<?php

namespace App\Model\DeliveryModel;

use Illuminate\Database\Eloquent\Model;

use App\Model\ProductModel\ProductCategory;

class DeliveryType extends Model
{
    protected $table= 'deliverytype';

    protected $fillable = [
        'name',
        'is_active',
    ];

    public $timestamps = false;

    const DELIVERY_TYPE_EVERY_DAY = 1;
    const DELIVERY_TYPE_EACH_TWICE_DAY = 2;
    const DELIVERY_TYPE_WEEK = 3;
    const DELIVERY_TYPE_MONTH = 4;
}
