<?php
namespace App\Model\DeliveryModel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MilkManDeliveryArea extends Model
{
    use SoftDeletes;

    protected $table= 'milkmandeliveryarea';

    protected $fillable = [
        'milkman_id',
        'address',
        'order',
        'deliveryarea_id',
    ];

    public function milkman()
    {
    	return $this->belongsTo('App\Model\DeliveryModel\MilkMan');
    }

    public function deliveryArea() {
        return $this->belongsTo('App\Model\DeliveryModel\DSDeliveryArea', 'deliveryarea_id')->withTrashed();
    }
}
