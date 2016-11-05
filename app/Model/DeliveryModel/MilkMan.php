<?php
namespace App\Model\DeliveryModel;

use Illuminate\Database\Eloquent\Model;

class MilkMan extends Model
{
    protected $table= 'milkman';

    protected $fillable = [
        'name',
        'phone',
        'station_id',
        'number',
        'is_active',
    ];

    public $timestamps = false;

    public function deliveryarea()
    {
    	return $this->hasMany('App\Model\DeliveryModel\MilkManDeliveryArea');
    }

    public function milkman_delivery_plan(){
        return $this->hasMany('App\Model\DeliveryModel\MilkManDeliveryPlan');
    }

    public function station(){
        return $this->belongsTo('App\Model\DeliveryModel\DeliveryStation');
    }
}
