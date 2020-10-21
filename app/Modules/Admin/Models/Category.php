<?php

namespace App\Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';
    protected $guarded = ['id', 'created_at', 'updated_at']; // Запрещается редактировать


    public function parentId()
    {
        return $this->belongsTo(self::class);
    }

    // Связь многие ко многим
    public function products()
    {
        return $this->belongsToMany(Product::class, 'category_product');
    }
}
