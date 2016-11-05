<?php

namespace App\Model\DeliveryModel;

use Illuminate\Database\Eloquent\Model;

use App\Model\ProductModel\ProductCategory;

class DSPaymentCalcType extends Model
{
    protected $table= 'dspaymentcalctypes';

    protected $fillable = [
        'name',
        'is_active',
    ];

    public $timestamps = false;
}
