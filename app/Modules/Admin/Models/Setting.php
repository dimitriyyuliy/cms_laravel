<?php

namespace App\Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at'];


    // Возвращает массив названий настроек, название которых нельзя изменить из панели управления
    public static function titleNoEditArr() {
        return [
            'name',
            'admin_email',
            'email',
            'tel',
            'date_format',
            'change_key',
            'access_ip',
        ];
    }
}
