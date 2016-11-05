<?php

namespace App\Model\BasicModel;

use Illuminate\Database\Eloquent\Model;

class DSType extends Model
{
    protected $table = "dstype";

    public $timestamps = false;

    protected $fillable =[
        'name',
    ];
}
