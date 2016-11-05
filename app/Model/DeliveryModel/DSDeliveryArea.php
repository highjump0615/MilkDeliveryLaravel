<?php

namespace App\Model\DeliveryModel;

use Illuminate\Database\Eloquent\Model;

class DSDeliveryArea extends Model
{
    protected $table = "dsdeliveryarea";
    public $timestamps = false;

    protected $fillable =[
        'address',
        'station_id',
    ];

    protected $appends = [
        // 地址名称
        'province_name',
        'city_name',
        'district_name',
        'street_name',
        'village_name'
    ];

    public function staion()
    {
    	return $this->belongsTo('App\Model\DeliveryModel\DeliveryStation');
    }

    /**
     * 获取地址各级名称
     * @return string
     */
    private function getAddressPartName($level) {
        if ($this->address) {
            $address = explode(' ', $this->address);
            if (count($address) >= $level + 1)
                return $address[$level];
            else
                return "";
        }
    }

    /**
     * 获取地址省名称
     * @return string
     */
    public function getProvinceNameAttribute()
    {
        return $this->getAddressPartName(0);
    }

    /**
     * 获取地址城市级名称
     * @return string
     */
    public function getCityNameAttribute()
    {
        return $this->getAddressPartName(1);
    }

    /**
     * 获取地址区名称
     * @return string
     */
    public function getDistrictNameAttribute()
    {
        return $this->getAddressPartName(2);
    }

    /**
     * 获取地址区名称
     * @return string
     */
    public function getStreetNameAttribute()
    {
        return $this->getAddressPartName(3);
    }

    /**
     * 获取地址区名称
     * @return string
     */
    public function getVillageNameAttribute()
    {
        return $this->getAddressPartName(4);
    }
}
