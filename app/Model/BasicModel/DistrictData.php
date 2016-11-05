<?php

namespace App\Model\BasicModel;

use Illuminate\Database\Eloquent\Model;

class DistrictData extends Model
{
    protected $table = 'district';

    protected $fillable = [
        'code',
        'name',
        'citycode',
    ];

    public $timestamps = false;
}
