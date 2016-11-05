<?php

namespace App\Model\BasicModel;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $fillable = [
        'name',
        'image_url',
    ];

    public $timestamps = false;
    //
}
