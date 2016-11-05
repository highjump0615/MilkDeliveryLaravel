<?php

namespace App\Model\DeliveryModel;

use Illuminate\Database\Eloquent\Model;
use App\Model\ProductModel\Product;
use Auth;

class DSDeliveryPlan extends Model
{
    protected $table = "dsdeliveryplan";

    public $timestamps = false;

    protected $fillable =[
        'station_id',
        'deliver_at',
        'product_id',
        'retail',
        'test_drink',
        'group_sale',
        'channel_sale',
        'created_at',
    ];

    protected $appends =[
      'product_name',
    ];

    public function getProductNameAttribute(){
        $product_id = $this->product_id;
        $product_name = Product::find($product_id);
        if($product_name == null)
            return null;
        else
            return $product_name->name;
    }
    //
}
