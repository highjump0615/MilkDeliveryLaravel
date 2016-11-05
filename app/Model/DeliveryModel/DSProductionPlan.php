<?php

namespace App\Model\DeliveryModel;

use App\Model\ProductModel\Product;
use App\Model\ProductModel\ProductPrice;
use DateTime;
use Illuminate\Database\Eloquent\Model;

class DSProductionPlan extends Model
{
    const DSPRODUCTION_SENT_PLAN = 1;
    const DSPRODUCTION_PENDING_PLAN = 2;
    const DSPRODUCTION_PRODUCE_CANCEL = 3;
    const DSPRODUCTION_PASSED_PLAN = 4;
    const DSPRODUCTION_PRODUCE_FINNISHED = 5;
    const DSPRODUCTION_PRODUCE_SENT = 6;
    const DSPRODUCTION_PRODUCE_RECEIVED = 7;

    protected $table = 'dsproductionplan';

    public $timestamps = false;

    protected $fillable = [
        'station_id',
        'product_id',
        'order_count',
        'produce_start_at',
        'produce_end_at',
        'created_at',
        'retail',
        'test_drink',
        'group_sale',
        'channel_sale',
        'subtotal_count',
        'subtotal_money',
        'actual_count',
        'conform_count',
        'status',
        'comment',
        'changed_plan_count',
    ];

    protected $appends = [
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
}
