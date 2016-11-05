<?php
/**
 * Created by PhpStorm.
 * User: rise
 * Date: 9/15/2016
 * Time: 6:32 AM
 */

namespace App\Model\DeliveryModel;

use Illuminate\Database\Eloquent\Model;

use App\Model\ProductModel\ProductCategory;
use App\Model\DeliveryModel\DeliveryType;

class FactoryDeliveryType extends Model
{
    protected $table= 'mfdeliverytype';

    protected $fillable = [
        'delivery_type',
        'factory_id',
        'is_active',
    ];

    protected $appends = [
    	'name',
    ];
    public $timestamps = false;


    public function getNameAttribute()
    {
    	$dt = DeliveryType::where('id', $this->delivery_type)->get()->first();
    	return $dt->name;
    }
}
