<?php

namespace App\Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'menu';
    protected $guarded = ['id'];

    public function parentId()
    {
        return $this->belongsTo(self::class);
    }

    // Связь один к одному
    public function menuName()
    {
        return $this->belongsTo(MenuName::class);
    }
}
