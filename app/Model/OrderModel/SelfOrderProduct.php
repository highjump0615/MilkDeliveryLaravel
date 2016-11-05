<?php

namespace App\Model\OrderModel;

use Illuminate\Database\Eloquent\Model;

class SelfOrderProduct extends Model
{
    protected $table = "selforderproduct";

    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'count',
        'price'
    ];

    public function product(){
        return $this->belongsTo('App\Model\ProductModel\Product');
    }
    //
}
