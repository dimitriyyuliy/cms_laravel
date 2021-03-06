<?php

$modules = 'Modules'; // Внимание, не меняйте название папки, т.к. после этого необходимо поменять все namespace!
//$admin = is_file(__DIR__ . '/admin.php') ? include('admin.php') : null;


return [

    /*
    |--------------------------------------------------------------------------
    | Modules settings
    |--------------------------------------------------------------------------
    */

    'folder' => $modules,
    'path' => app_path($modules),
    'path_file' => "app/{$modules}",
    'namespace' => "App\\{$modules}",

    // Папка админки
    'admin' => 'Admin',

    // Папка с видами
    'views' => 'views',

    // Папка с переводами
    'lang' => 'lang',

    'modules' => [
        'Admin' => [
            'routes' => true,
            'webpack' => true,
        ],
        'Form' => [
            'routes' => true,
            'webpack' => false,
        ],
        'Shop' => [
            'routes' => true,
            'webpack' => false,
        ],
        'Auth' => [
            'routes' => true,
            'webpack' => false,
        ],
    ],

];
