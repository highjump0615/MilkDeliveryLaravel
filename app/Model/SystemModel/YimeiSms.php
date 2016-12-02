<?php

namespace App\Model\SystemModel;

use Illuminate\Database\Eloquent\Model;

class YimeiSms extends Model
{
    protected $table = 'yimeisms';
    protected $fillable = [
        'name',
        'value',
    ];
    protected $appends = [
    ];
}
