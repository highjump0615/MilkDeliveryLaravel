<?php

namespace App\Model\DeliveryModel;

use Illuminate\Database\Eloquent\Model;

class DSType extends Model
{
    protected $table= 'dstype';

    protected $fillable = [
        'name',
        'is_active',
    ];

    public $timestamps = false;
}
