<?php

// Данный трейт даёт создать только один экхемпляр класса.

namespace App\Helpers\Services;


trait TSingleton
{
    private static $instance;

    public static function instance()
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }
}
