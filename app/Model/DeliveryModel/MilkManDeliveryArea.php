<?php
namespace App\Model\DeliveryModel;

use Illuminate\Database\Eloquent\Model;

class MilkManDeliveryArea extends Model
{
    protected $table= 'milkmandeliveryarea';

    protected $fillable = [
        'milkman_id',
        'address',
        'order',
    ];

    public $timestamps = false;

    public function milkman()
    {
    	return $this->belongsTo('App\Model\DeliveryModel\MilkMan');
    }
}
