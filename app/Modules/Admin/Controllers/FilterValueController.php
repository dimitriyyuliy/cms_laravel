<?php

namespace App\Modules\Admin\Controllers;

use App\Models\Main;
use App\Modules\Admin\Helpers\App as appHelpers;
use App\Modules\Admin\Helpers\DbSort;
use App\Modules\Admin\Models\FilterValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class FilterValueController extends AppController
{
    private $parentTable = 'filter_groups';
    private $parentRoute = 'filter-group';
    private $belongsTable = 'filter_products';
    private $belongsRoute = 'product';


    public function __construct(Request $request)
    {
        parent::__construct($request);

        $parentTable = $this->parentTable;
        $parentRoute = $this->parentRoute;
        $belongsTable = $this->belongsTable;
        $belongsRoute = $this->belongsRoute;
        $class = $this->class = str_replace('Controller', '', class_basename(__CLASS__));
        $model = $this->model = '\App\\Modules\\Admin\\Models\\' . $this->class;
        $table = $this->table = with(new $model)->getTable(); // Получаем название таблицы
        $route = $this->route = $request->segment(2); // Получаем сегмент из url
        $view = $this->view = Str::snake($this->class); // Преобразуем в foo_bar
        View::share(compact('class','model', 'table', 'route', 'view', 'parentTable', 'parentRoute', 'belongsTable', 'belongsRoute'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Если через Get передаётся значение, то записывается в куку текущее меню
        $queryValue = $request->query('value');
        if ($queryValue) {
            return redirect()->back()->withCookie("{$this->view}_id", $request->query('value'), config('admin.cookie'));
        }

        $currentParentId = $request->cookie("{$this->view}_id");
        $countParent = DB::table($this->parentTable)->count();

        if (!$currentParentId && $countParent) {
            $currentParent = DB::table($this->parentTable)->first();
            $currentParentId = $currentParent->id;

            return redirect()->back()->withCookie("{$this->view}_id", $currentParentId, config('admin.cookie'));
        }

        $f = __FUNCTION__;
        Main::viewExists("{$this->viewPath}.{$this->view}.{$f}", __METHOD__);

        $parentValues = null;
        $values = null;

        // Поиск. Массив гет ключей для поиска
        $queryArr = [
            'id',
            'title',
            'sort',
        ];

        // Параметры Get запроса
        $get = request()->query();
        $col = $get['col'] ?? null;
        $cell = $get['cell'] ?? null;

        // Если в родительской таблице нет элементов, то ничего нельзя добавить
        $parentCount = DB::table($this->parentTable)->count();

        if ($parentCount > 0) {
            $parentValues = DB::table($this->parentTable)->select('id', 'title')->get();

            // Метод для поиска и сортировки запроса БД
            $values = DbSort::getSearchSort($queryArr, $get, $this->table, $this->model, $this->view, $this->perPage, 'parent_id', $currentParentId);
        }

        $this->setMeta(__("{$this->lang}::a." . Str::ucfirst($this->view)));
        return view("{$this->viewPath}.{$this->view}.{$f}", compact('parentValues', 'values', 'queryArr', 'col', 'cell', 'currentParentId', 'parentCount'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $f = __FUNCTION__;
        Main::viewExists("{$this->viewPath}.{$this->view}.{$this->template}", __METHOD__);

        $countParent = DB::table($this->parentTable)->count();
        $queryCookie = $request->cookie("{$this->view}_id");

        // Записывается в куку текущее меню
        if ($countParent && !$queryCookie) {
            return redirect()->back()->withCookie("{$this->view}_id", $request->query('value'), config('admin.cookie'));
        }

        $currentParentId = $request->cookie("{$this->view}_id");
        if (!$currentParentId) {
            $current_menu = DB::table($this->parentTable)->first();
            $currentParentId = $current_menu && $current_menu->count() > 0 ? $current_menu->id : null;
        }
        $parentValues = DB::table($this->parentTable)->find($currentParentId);

        // Если в родительской таблице нет элементов, то ничего нельзя добавить и поэтому не показываем в виде форму добавления
        $values = DB::table($this->parentTable)->count();

        $this->setMeta(__("{$this->lang}::a." . Str::ucfirst($f)));
        return view("{$this->viewPath}.{$this->view}.{$this->template}", compact('currentParentId', 'values', 'parentValues'));
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
                'value' => "required|string|max:190",
            ];
            $request->validate($rules);
            $data = $request->all();

            $values = new FilterValue();
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

            $currentParentId = null;
            $values = null;
            $parentValues = null;

            // Если в родительской таблице нет элементов, то ничего нельзя добавить
            $parentCount = DB::table($this->parentTable)->count();

            if ($parentCount > 0) {
                $currentParentId = request()->cookie("{$this->view}_id") ?: 1;
                $parentValues = DB::table($this->parentTable)->find($currentParentId);
                $values = DB::table($this->table)->find((int)$id);
            }

            // Записать в реестр parent_id
            if (!empty($values->parent_id)) {
                Main::set('parent_id', $values->parent_id);
            }

            // Потомки в массиве
            $getIdParents = appHelpers::getIdParents($values->id ?? null, $this->belongsTable, 'value_id');

            $this->setMeta(__("{$this->lang}::a.{$f}"));
            return view("{$this->viewPath}.{$this->view}.{$this->template}", compact('values', 'getIdParents', 'currentParentId', 'parentValues'));
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
                'value' => "required|string|max:190",
            ];
            $request->validate($rules);
            $data = $request->all();

            // Если нет сортировки, то по-умолчанию 500
            $data['sort'] = empty($data['sort']) ? 500 : $data['sort'];

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

            if ($values) {

                // Если есть родители, то ошибка
                $getIdParents = appHelpers::getIdParents((int)$id, $this->belongsTable, 'value_id');
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
            ->with('error', __("{$this->lang}::s.something_went_wrong"));
    }
}
