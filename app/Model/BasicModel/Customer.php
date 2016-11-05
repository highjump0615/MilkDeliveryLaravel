<?php

namespace App\Model\BasicModel;

use Illuminate\Database\Eloquent\Model;
use App\Factory;
use App\Model\DeliveryModel\DSDeliveryArea;


class Customer extends Model
{
    public $table = 'customer';

    protected $fillable = [
        'name',
        'phone',
        'address',
        'station_id',
        'status',
        'milkman_id',
        'factory_id',
        'remain_amount',
    ];

    protected $appends =[
        'province',
        'city',
        'district',
        'street',
        'xiaoqu',
        'sub_addr',
    ];

    public function Order()
    {
        return $this->hasMany('App\Model\OrderModel\Order');
    }

    public function getProvinceAttribute()
    {
        $address = $this->address;
        if($address)
        {
            $addr_array = explode(' ', $address);
            if(count($addr_array) > 0)
                return $addr_array[0];
            else
                return "";
        } else
            return "";
    }

    public function getCityAttribute()
    {
        $address = $this->address;
        if($address)
        {
            $addr_array = explode(' ', $address);
            if(count($addr_array) > 1)
                return $addr_array[1];
            else
                return "";
        } else
            return "";
    }

    public function getDistrictAttribute()
    {
        $address = $this->address;
        if($address)
        {
            $addr_array = explode(' ', $address);
            if(count($addr_array) > 2)
                return $addr_array[2];
            else
                return "";
        } else
            return "";
    }

    public function getStreetAttribute()
    {
        $address = $this->address;
        if($address)
        {
            $addr_array = explode(' ', $address);
            if(count($addr_array) > 3)
                return $addr_array[3];
            else
                return "";
        } else
            return "";
    }

    public function getXiaoquAttribute()
    {
        $address = $this->address;
        if($address)
        {
            $addr_array = explode(' ', $address);
            if(count($addr_array) > 4)
                return $addr_array[4];
            else
                return "";
        } else
            return "";
    }

    public function getSubAddrAttribute()
    {
        $address = $this->address;
        if($address)
        {
            $addr_array = explode(' ', $address);
            if(count($addr_array) > 5)
            {
                $length = count($addr_array);
                if($length == 6)
                    return $addr_array[5];
                else{
                    $sub_addr="";
                    for($i=5; $i< $length; $i++)
                    {
                        $sub_addr .=$addr_array[$i];
                    }

                    return $sub_addr;
                }
            }
            else
                return "";
        } else
            return "";
    }

    public function getMainAddrAttribute()
    {
        $main_addr = str_replace($this->sub_addr, " ", $this->address);
        return $main_addr;
    }



}
