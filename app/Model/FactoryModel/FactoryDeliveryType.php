<?php

namespace App\Model\FactoryModel;

use Illuminate\Database\Eloquent\Model;
use App\Model\DeliveryModel\DeliveryType;

class FactoryDeliveryType extends Model
{
    protected $table = 'mfdeliverytype';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'delivery_type',
        'factory_id',
        'is_active',
    ];

    const FACTORY_DELIVERY_TYPE_ACTIVE = 1 ;
    const FACTORY_DELIVERY_TYPE_INACTIVE = 0 ;

    protected $appends = [
    	'name',
    ];

    public function getNameAttribute()
    {
    	return DeliveryType::find($this->delivery_type)->name;
    }
}
