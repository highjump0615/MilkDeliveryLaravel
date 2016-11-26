<?php

namespace App\Model\ProductionModel;

use Illuminate\Database\Eloquent\Model;

use App\Model\ProductModel\ProductCategory;


class FactoryProductionPlan extends Model
{
    const FACTORY_PRODUCE_PLAN_SENT = 1;
    const FACTORY_PRODUCE_STARTED = 2;
    const FACTORY_PRODUCE_FINNISHED = 3;
    const FACTORY_PRODUCE_CANCELED = 4;
    
    protected $table= 'mfproductionplan';

    public $timestamps = false;

    protected $fillable = [
        'factory_id',
        'product_id',
        'count',
        'start_at',
        'end_at',
        'status',
    ];
}
