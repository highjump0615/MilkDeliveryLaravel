<?php
namespace App\Model\DeliveryModel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MilkMan extends Model
{
    use SoftDeletes;

    protected $table= 'milkman';

    protected $fillable = [
        'name',
        'phone',
        'station_id',
        'number',
        'is_active',
    ];

    public function deliveryarea()
    {
    	return $this->hasMany('App\Model\DeliveryModel\MilkManDeliveryArea', 'milkman_id');
    }

    public function milkman_delivery_plan(){
        return $this->hasMany('App\Model\DeliveryModel\MilkManDeliveryPlan');
    }

    public function station(){
        return $this->belongsTo('App\Model\DeliveryModel\DeliveryStation');
    }
}
