<?php

namespace App\Modules\Admin\Controllers;

use App\Models\Main;
use App\Modules\Admin\Helpers\DbSort;
use App\Modules\Admin\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class SettingController extends AppController
{
    private $titleNoEditArr;


    public function __construct(Request $request)
    {
        parent::__construct($request);
        $class = $this->class = str_replace('Controller', '', class_basename(__CLASS__));
        $model = $this->model = '\App\\Modules\\Admin\\Models\\' . $this->class;
        $table = $this->table = with(new $model)->getTable();
        $route = $this->route = $request->segment(2);
        $this->titleNoEditArr = Setting::titleNoEditArr() ?? [];
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
        //$values = DB::table($this->table)->orderBy('id', 'desc')->paginate($this->perPage);


        // Поиск. Массив гет ключей для поиска
        $queryArr = [
            'id',
            'title',
            'value',
            'section',
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
        $disabled = null;

        $this->setMeta(__("{$this->lang}::a." . Str::ucfirst($f)));
        return view("{$this->viewPath}.{$this->view}.{$this->template}", compact('disabled'));
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
                'title' => "required|string|unique:{$this->table}|max:190",
            ];
            $request->validate($rules);
            $data = $request->all();

            $values = new Setting();
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
            $title = $values->title;
            $disabled = in_array($title, $this->titleNoEditArr) ? 'disabled' : null;

            $this->setMeta(__("{$this->lang}::a.{$f}"));
            return view("{$this->viewPath}.{$this->view}.{$this->template}", compact('values', 'disabled'));
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
                'title' => "required|string|unique:{$this->table},title,{$id}|max:190",
            ];
            $request->validate($rules);
            $data = $request->all();

            $values = $this->model::find((int)$id);
            if ($values) {
                $values->fill($data);

                // Если данные не изменины
                $lastData = $this->model::find((int)$id);
                if ($lastData && $lastData->toJson() === $values->toJson()) {

                    // Сообщение об ошибке
                    return redirect()
                        ->route("admin.{$this->route}.edit", $values->id)
                        ->with('error', __("{$this->lang}::s.data_was_not_changed"));
                }

                if ($values->update()) {

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

            if ($values && $values->delete()) {

                // Удалить все кэши
                cache()->flush();

                // Сообщение об успехе
                return redirect()
                    ->route("admin.{$this->route}.index")
                    ->with('success', __("{$this->lang}::s.removed_successfully", ['id' => $values->id]));
            }
        }

        // Сообщение об ошибке
        Main::getError('Request', __METHOD__, null);
        return redirect()
            ->route("admin.{$this->route}.index")
            ->with('error', __("{$this->lang}::s.something_went_wrong"));
    }
}
