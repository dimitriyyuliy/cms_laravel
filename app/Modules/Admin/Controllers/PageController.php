<?php

namespace App\Modules\Admin\Controllers;

use App\Models\Main;
use App\Modules\Admin\Helpers\App as appHelpers;
use App\Modules\Admin\Helpers\DbSort;
use App\Modules\Admin\Helpers\Slug;
use App\Modules\Admin\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class PageController extends AppController
{
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
    public function index(Request $request)
    {
        $f = __FUNCTION__;
        Main::viewExists("{$this->viewPath}.{$this->view}.{$f}", __METHOD__);

        // Поиск. Массив гет ключей для поиска
        $queryArr = [
            'id',
            'parent_id',
            'title',
            'slug',
            'status',
            'sort',
        ];

        // Параметры Get запроса
        $get = request()->query();
        $col = $get['col'] ?? null;
        $cell = $get['cell'] ?? null;

        // Метод для поиска и сортировки запроса БД
        $values = DbSort::getSearchSort($queryArr, $get, $this->table, $this->model, $this->view, $this->perPage);

        /*$col = request()->query('col');
        $cell = request()->query('cell');


        // Значения по-умолчанию для сортировки
        $columnSort = 'id';
        $order = 'desc';

        // Если сессия сортировки не существует, то сохраним значения по-умолчанию
        if (!session()->exists("admin_sort.{$this->view}")) {
            session()->flash("admin_sort.{$this->view}.{$columnSort}", $order);
        }

        // Если передаётся через Get сортировка, то проверим есть ли такая колонка в таблице
        $get = request()->query();
        if ($get) {
            $columnSort = key($get);
            if (Schema::hasColumn($this->table, $columnSort)) {
                $order = $get[$columnSort];
                if ($order === 'asc' || $order === 'desc') {

                    // Удалим прошлое значение
                    session()->forget("admin_sort.{$this->view}");

                    // Сохраним новое
                    session()->flash("admin_sort.{$this->view}.{$columnSort}", $order);
                }
            }
        }


        // Если есть строка поиска
        if ($col && in_array($col, $queryArr) && $cell) {
            $values = $this->model::where($col, 'LIKE', "%{$cell}%")->orderBy($columnSort, $order)->paginate($this->perPage);

        // Иначе выборка всех элементов из БД
        } else {
            $values = $this->model::orderBy($columnSort, $order)->paginate($this->perPage);
        }*/

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

        $this->setMeta(__("{$this->lang}::a." . Str::ucfirst($f)));
        return view("{$this->viewPath}.{$this->view}.{$this->template}");
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
                'title' => 'required|string|max:190',
                'slug' => "required|string|unique:{$this->table}|max:190",
            ];
            $request->validate($rules);
            $data = $request->all();

            // Если нет body, то ''
            if (empty($data['body'])) {
                $data['body'] = '';
            }

            // Уникальный slug
            //$data['slug'] = Slug::checkRecursion($this->table, $data['slug']);

            $values = new Page();
            $values->fill($data);

            if ($values->save()) {

                // Удалить все кэши
                cache()->flush();

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

            $values = DB::table($this->table)->find((int)$id);

            // Записать в реестр parent_id
            if (!empty($values->parent_id)) {
                Main::set('parent_id', $values->parent_id);
            }

            // Потомки в массиве
            $getIdParents = appHelpers::getIdParents($values->id ?? null, $this->table);

            $this->setMeta(__("{$this->lang}::a.{$f}"));
            return view("{$this->viewPath}.{$this->view}.{$this->template}", compact('values', 'getIdParents'));
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
                'title' => 'required|string|max:190',
                'slug' => "required|string|unique:{$this->table},slug,{$id}|max:190",
            ];
            $request->validate($rules);
            $data = $request->all();

            $values = $this->model::find((int)$id);
            if ($values) {

                // Уникальный slug
                //$data['slug'] = Slug::checkRecursion($this->table, $data['slug'], null, $values->id);

                // Если нет сортировки, то по-умолчанию 500
                $data['sort'] = empty($data['sort']) ? 500 : $data['sort'];
                $values->fill($data);


                // Если данные не изменины
                $lastData = $this->model::find((int)$id)->toArray();
                $current = $values->toArray();
                if (!appHelpers::arrayDiff($lastData, $current)) {

                    // Сообщение об ошибке
                    return redirect()
                        ->route("admin.{$this->route}.edit", $values->id)
                        ->with('error', __("{$this->lang}::s.data_was_not_changed"));
                }

                if ($values->save()) {

                    // Удалить все кэши
                    cache()->flush();

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
                // Если есть потомки, то ошибка
                $getIdParents = appHelpers::getIdParents((int)$id, $this->table);
                if ($getIdParents) {
                    return redirect()
                        ->route("admin.{$this->route}.edit", $id)
                        ->with('error', __("{$this->lang}::s.remove_not_possible") . ', ' . __("{$this->lang}::s.there_are_nested") . ' #');
                }

                if ($values->delete()) {

                    // Удалить все кэши
                    cache()->flush();

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
            -with('error', __("{$this->lang}::s.something_went_wrong"));
    }
}
