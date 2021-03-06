<?php

namespace App\Modules\Admin\Controllers;

use App\Models\Main;
use App\Modules\Admin\Helpers\DbSort;
use App\Modules\Admin\Helpers\Img;
use App\Modules\Admin\Helpers\Slug;
use App\Modules\Admin\Models\User;
use App\Modules\Admin\Models\UserLastData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class UserController extends AppController
{
    public static $guardedLast = ['id', 'note', 'accept', 'email_verified_at', 'remember_token', 'created_at', 'updated_at'];


    public function __construct(Request $request)
    {
        parent::__construct($request);
        $class = $this->class = str_replace('Controller', '', class_basename(__CLASS__));
        $model = $this->model = '\App\\Modules\\Admin\\Models\\' . $this->class;
        $table = $this->table = with(new $model)->getTable();
        $route = $this->route = $request->segment(2);
        $view = $this->view = Str::snake($this->class);
        View::share(compact('class','model', 'table', 'route', 'view'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $f = __FUNCTION__;
        Main::viewExists("{$this->viewPath}.{$this->view}.{$f}", __METHOD__);
        //$values = $this->model::with('role')->orderBy('id', 'desc')->paginate($this->perPage);

        // Поиск. Массив гет ключей для поиска
        $queryArr = [
            'id',
            'name',
            'email',
            'tel',
            'role_id',
            'ip',
        ];

        // Параметры Get запроса
        $get = request()->query();
        $col = $get['col'] ?? null;
        $cell = $get['cell'] ?? null;

        // Метод для поиска и сортировки запроса БД
        $values = DbSort::getSearchSort($queryArr, $get, $this->table, $this->model, $this->view, $this->perPage);

        $this->setMeta(__("{$this->lang}::a." . Str::ucfirst($this->table)));
        return view("{$this->viewPath}.{$this->view}.{$f}", compact('values', 'queryArr', 'col', 'cell'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $f = __FUNCTION__;
        Main::viewExists("{$this->viewPath}.{$this->view}.{$this->template}", __METHOD__);

        // Статусы пользователей
        $statuses = config('admin.user_statuses');

        // Роли преобразуются в массив
        $roles_obj = DB::table('roles')->select('id', 'name')->get();
        $roles = [];
        if (!empty($roles_obj)) {
            foreach ($roles_obj as $v) {
                $roles[$v->id] = $v->name;
            }
        }

        // Если не Админ, то запишим id роли Админ
        $roleIdAdmin = !auth()->user()->isAdmin() ? auth()->user()->getRoleIdAdmin() : null;

        $this->setMeta(__("{$this->lang}::a." . Str::ucfirst($f)));
        return view("{$this->viewPath}.{$this->view}.{$this->template}", compact('roles', 'statuses', 'roleIdAdmin'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->isMethod('post')) {
            $rules = [
                'name' => 'required|string|max:190',
                'email' => "required|string|email|unique:{$this->table},email|max:190",
                'password' => 'required|string|min:6|same:password_confirmation',
                'role_id' => 'required|integer',
                //'tel' => 'required|string|max:190',
            ];
            $request->validate($rules);
            $data = $request->all();

            // Если не Админ выбирает роль Админ, то ошибка
            if (!auth()->user()->isAdmin() && $data['role_id'] == auth()->user()->getRoleIdAdmin()) {

                // Сообщение об ошибке
                return redirect()
                    ->back()
                    ->with('error', __("{$this->lang}::s.admin_choose_admin"));
            }

            // Если нет картинки
            if (empty($data['img'])) {
                $data['img'] = config("admin.img{$this->class}Default");
            }

            // Поле подтверждение пароля удаляется
            unset($data['password_confirmation']);

            // Если есть пароль, то он хэшируется
            if ($data['password']) {
                $data['password'] = Hash::make($data['password']);
            }

            $values = new User();
            $values->fill($data);

            if ($values->save()) {

                // Сообщение об успехе
                return redirect()
                    ->route("admin.{$this->route}.edit", $values->id)
                    ->with('success', __("{$this->lang}::s.created_successfully", ['id' => $values->id]));
            }
        }

        // Сообщение об ошибке
        Main::getError('Request', __METHOD__, null);
        return redirect()
            ->route("admin.{$this->route}.index")
            ->with('error', __("{$this->lang}::s.something_went_wrong"));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /*public function show($id)
    {
        //
    }*/

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if ((int)$id) {
            $f = __FUNCTION__;
            Main::viewExists("{$this->viewPath}.{$this->view}.{$this->template}", __METHOD__);

            $values = $this->model::with('role')->find((int)$id);
            if (!$values) {

                // Сообщение об ошибке
                Main::getError('Request', __METHOD__, null);
                return redirect()
                    ->route("admin.{$this->route}.index")
                    ->with('error', __("{$this->lang}::s.something_went_wrong"));
            }

            // Статусы пользователей
            $statuses = config('admin.user_statuses');

            // Роли преобразуются в массив
            $roles_obj = DB::table('roles')->select('id', 'name')->get();
            $roles = [];
            if (!empty($roles_obj)) {
                foreach ($roles_obj as $v) {
                    $roles[$v->id] = $v->name;
                }
            }


            // DROPZONE DATA
            // Передаём начальную часть названия для передаваемой картинки Dropzone JS
            $imgRequestName = $this->imgRequestName = Slug::cyrillicToLatin($values->name, 32);

            // ID элемента, для которого картинка Dropzone JS
            $imgUploadID = $this->imgUploadID = $values->id;

            // Если не Админ, то запишим id роли Админ
            $roleIdAdmin = !auth()->user()->isAdmin() ? auth()->user()->getRoleIdAdmin() : null;

            $this->setMeta(__("{$this->lang}::a.{$f}"));
            return view("{$this->viewPath}.{$this->view}.{$this->template}", compact('values', 'roles', 'statuses', 'imgRequestName', 'imgUploadID', 'roleIdAdmin'));
        }

        // Сообщение об ошибке
        Main::getError('Request', __METHOD__, null);
        return redirect()
            ->route("admin.{$this->route}.index")
            ->with('error', __("{$this->lang}::s.something_went_wrong"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if ((int)$id && $request->isMethod('put')) {
            $rules = [
                'name' => 'required|string|max:190',
                'email' => "required|string|email|unique:{$this->table},email,{$id}|max:190",
                'role_id' => 'required|integer',
                //'password' => 'same:password_confirmation',
                //'tel' => 'required|string|max:190',
            ];
            $request->validate($rules);
            $data = $request->all();

            // Если нет картинки
            if (empty($data['img'])) {
                $data['img'] = config("admin.img{$this->class}Default");
            }

            // Поле подтверждение пароля удаляется
            unset($data['password_confirmation']);

            // Поле пароль удаляется, т.к. оно меняет через JS
            unset($data['password']);

            // Если есть пароль, то он хэшируется
            /*if ($data['password']) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }*/

            $values = $this->model::find((int)$id);
            if ($values) {
                $values->fill($data);

                // Если не Админ выбирает роль Админ, то ошибка
                if (!auth()->user()->isAdmin() && $data['role_id'] == auth()->user()->getRoleIdAdmin()) {

                    // Сообщение об ошибке
                    return redirect()
                        ->back()
                        ->with('error', __("{$this->lang}::s.admin_choose_admin"));
                }

                // Если данные изменины
                if (isset($lastData['role'])) unset($lastData['role']);
                $lastDataNew = [];
                //$lastData = $this->model::with('role')->find((int)$id)->toArray();
                $lastData = $this->model::find((int)$id);
                if ($lastData && $lastData->toJson() === $values->toJson()) {

                    // В таблицу users_last_data запишутся предыдущие данные
                    $lastData = $lastData->toArray();
                    foreach ($lastData as $k => $v) {

                        // Исключаем не нужные поля
                        if (!in_array($k, self::$guardedLast)) {
                            $lastDataNew[$k] = $v;
                        }
                    }
                    $lastDataNew['user_id'] = $lastData['id'];

                    // Сохраняем данные
                    if ($lastDataNew) {
                        $last = new UserLastData();
                        $last->fill($lastDataNew);

                        if (!$last->save()) {

                            // Сообщение что-то пошло не так
                            $message = 'Error UserLastData save and in ' . __METHOD__;
                            Log::warning($message);
                        }
                    }

                } else {

                    // Сообщение об ошибке
                    return redirect()
                        ->route("admin.{$this->route}.edit", $values->id)
                        ->with('error', __("{$this->lang}::s.data_was_not_changed"));
                }

                if ($values->update()) {

                    // Если меняются данные текущего пользователя, то изменим их в объекте auth
                    if ($values->id === auth()->user()->id) {
                        $auth = auth()->user()->toArray();
                        if ($auth) {
                            unset($auth['img']); // Удалим из массива картинку, т.к. она меняется сразу при смене картинки
                            foreach ($auth as $authKey => $authValue) {
                                if (isset($data[$authKey]) && $data[$authKey] != $authValue) {
                                    auth()->user()->update([$authKey => $data[$authKey]]);
                                }
                            }
                        }
                    }

                    // Сообщение об успехе
                    return redirect()
                        ->route("admin.{$this->route}.edit", $values->id)
                        ->with('success', __("{$this->lang}::s.saved_successfully", ['id' => $values->id]));
                }
            }
        }

        // Сообщение об ошибке
        Main::getError('Request', __METHOD__, null);
        return redirect()
            ->route("admin.{$this->route}.index")
            ->with('error', __("{$this->lang}::s.something_went_wrong"));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if ((int)$id) {
            $values = $this->model::find((int)$id);

            if ($values) {
                $img = $values->img ?? null;

                // Если включен shop
                if (config('add.shop')) {

                    // Проверим есть ли заказы
                    $orders = DB::table('orders')->where('user_id', (int)$id)->get()->toArray();
                    if ($orders) {
                        $ordersPart = '';
                        foreach ($orders as $order) {
                            $ordersPart .= "#{$order->id} ,";
                        }
                        $ordersPart = rtrim($ordersPart, ' ,');

                        return redirect()
                            ->back()
                            ->with('error', __("{$this->lang}::s.user_has") . Str::lower(__("{$this->lang}::a.Orders")) . " {$ordersPart}");
                    }
                }

                if ($values->delete()) {

                    // Удалим картинку с сервера, кроме картинки по-умолчанию
                    Img::deleteImg($img, config("admin.img{$this->class}Default"));

                    // Сообщение об успехе
                    return redirect()
                        ->route("admin.{$this->route}.index")
                        ->with('success', __("{$this->lang}::s.removed_successfully", ['id' => $values->id]));
                }
            }
        }

        // Сообщение об ошибке
        Main::getError('Request', __METHOD__, null);
        return redirect()
            ->route("admin.{$this->route}.index")
            ->with('error', __("{$this->lang}::s.something_went_wrong"));
    }


    // Разлогинить пользователя
    public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        return redirect()->route('index');
    }
}
