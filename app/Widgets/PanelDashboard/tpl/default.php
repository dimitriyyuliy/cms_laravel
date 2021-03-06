<?php

use Illuminate\Support\Facades\Route;
use App\Models\Main;


$lang = lang();
$route = Main::get('c');
$id = (int)Main::get('id');
$editLink = $id && Route::has("admin.{$route}.edit") ? route("admin.{$route}.edit", $id) : null;
$img = config('add.img', 'img');

?>
<div class="panel-dashboard">
    <a href="<?= session()->get('back_link_admin', route('admin.main')); ?>" class="panel-dashboard__icons" title="<?= __("{$lang}::a.Dashboard"); ?>">
        <?php

            /*
             <i class="fas fa-tachometer-alt"></i>
             */

            ?>
        <svg class="panel-dashboard__tachometer">
            <use xlink:href="<?= asset("{$img}/svg/dashboard_sprite.svg#tachometer-alt"); ?>"></use>
        </svg>
    </a>
    <?php


    if ($editLink): ?>
        <a href="<?= $editLink; ?>" class="panel-dashboard__icons" target="_blank" title="<?= __("{$lang}::a.edit"); ?>">
            <?php

            /*
             <i class="fas fa-edit"></i>
             */

            ?>
            <svg class="panel-dashboard__edit">
                <use xlink:href="<?= asset("{$img}/svg/dashboard_sprite.svg#edit"); ?>"></use>
            </svg>
        </a>
    <?php endif; ?>
</div>
