<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BannedIp extends Model
{
    protected $table = 'banned_ip';
    protected $guarded = ['id', 'created_at', 'updated_at'];

}
