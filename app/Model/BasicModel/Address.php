<?php

namespace App\Model\BasicModel;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $table = 'address';

    const LEVEL_PROVINCE = 1;
    const LEVEL_CITY = 2;
    const LEVEL_DISTRICT = 3;
    const LEVEL_STREET = 4;
    const LEVEL_VILLAGE = 5;

    protected $fillable = [
        'name',
        'level',
        'parent_id',
        'factory_id',
        'is_deleted',
        'is_active',
    ];

    public $timestamps = false;

  	protected $appends = [
  		'province',
  		'city',
  		'district',
  		'street',
  		//'sub_addresses',
        //'sub_active_addresses',
  		'sub_addresses_str',
        'full_address_name',
  	];

    const ADDRESS_ACTIVE = 1;
    const ADDRESS_INACTIVE = 0;

    public function setDelete()
    {
        $this->is_deleted = 1;
        $this->is_active = $this::ADDRESS_INACTIVE;
        $this->save();
    }

    public function setEnable()
    {
        if($this->level == 4)
        {
            $this->is_active = $this::ADDRESS_ACTIVE;
            $this->save();
        }
    }

    public function setDisable()
    {
     if($this->level == 4)
        {
            $this->is_active = $this::ADDRESS_INACTIVE;
            $this->save();
        }
    }

    public function getStreetAttribute(){
    	$street = null;

    	if($this->level == 5) {
    		$street_id = $this->parent_id;
    		$street = Address::find($street_id);
    	}

    	return $street;
    }

    public function getDistrictAttribute(){
    	
    	$district = null;

    	if($this->level == 4) {
    		$district_id = $this->parent_id;
    		$district = Address::find($district_id);
    	} else if($this->level == 5) {
    		$street = $this->street;
    		$district = Address::find($street->parent_id);
    	}

    	return $district;
    }

    public function getCityAttribute(){
    	$city = null;

    	if($this->level == 3) {
    		$city = Address::find($this->parent_id);
    	} else if($this->level > 3) {
    		$district = $this->district;
    		$city = Address::find($district->parent_id);
    	}
    	return $city;
    }

    public function getProvinceAttribute(){
    	$province = null;

    	if($this->level == 2) {
    		$province = Address::find($this->parent_id);
    	} else if($this->level > 2) {
    		$city = $this->city;
    		$province = Address::find($city->parent_id);
    	}

    	return $province;
    }

    public function getSubAddresses() {
    	$id = $this->id;

    	return Address::where('parent_id', $id)->get();
    }

    public function getSubActiveAddresses() {
        return Address::where('parent_id', $this->id)
            ->where('is_active', $this::ADDRESS_ACTIVE)
            ->where('is_deleted', 0)->get();
    }

    public function getSubAddressesStrAttribute() {
    	$id = $this->id;

    	$addrs =  Address::where('parent_id', $id)->get();

    	$names = array();
    	foreach($addrs as $a) {
    		array_push($names, $a->name);
    	}

    	return implode(', ', $names);
    }

    public function getSubAddressWithName($name) {
        $id = $this->id;

        return Address::where('parent_id', $id)->where('name', $name)->first();
    }

    public function getFullAddressNameAttribute()
    {
        $level = $this->level;

        if($level == 1) {
            return $this->name;
        }

        $province = $this->province->name;

        if($level == 2) {
            return $province.' '.$this->name;
        }

        $city = $this->city->name;

        if($level == 3) {
            return $province.' '.$city.' '.$this->name;
        }

        $district = $this->district->name;

        if($level == 4) {
            return $province.' '.$city.' '.$district.' '.$this->name;
        }

        $street = $this->street->name;
        return $province.' '.$city.' '.$district.' '.$street.' '.$this->name;
    }

    public function changeSubAddressName($origin_val, $new_val)
    {
    
        $origin_child_addr = $this->getSubAddressWithName($origin_val);

        if( strcasecmp($origin_val, $new_val) != 0 ){

            //find child whose name is new_val
            $same_new_addr = $this->getSubAddressWithName($new_val);

            if( $same_new_addr )
            {
                if($origin_child_addr->level != 4)
                {
                    //There is same address and that is not the street.
                    //give same_parent_id to origin's children
                    foreach($origin_child_addr->getSubAddresses() as $origin_child_child_addr)
                    {
                        $origin_child_child_addr->parent_id = $same_new_addr->id;
                        $origin_child_child_addr->save();
                    }
                    //delete origin child
                    $origin_child_addr->delete();

                    return $same_new_addr;
                } else {
                    return null;                    
                }

            } else {
                $origin_child_addr->name = $new_val;
                $origin_child_addr->save();
                return $origin_child_addr;
            }
        } else 
        {
            return $origin_child_addr;
        }
                
    }

    public static function addressObjFromName($address, $factory_id) {
        if($address == null || $address == "")
            return null;
            
        $addr = explode(" ",$address);

        $level = count($addr);

        if($level < 1)
            return null;

        $province_name = $addr[0];

        $province = Address::where('name', $province_name)->where('level', 1)
            ->where('factory_id', $factory_id)
            ->where('is_active', Address::ADDRESS_ACTIVE)
            ->where('is_deleted', 0)
            ->first();

        if($province == null)
            return null;

        if($level < 2)
            return $province;

        $city_name = $addr[1];

        $city = Address::where('name', $city_name)->where('level', 2)
            ->where('parent_id', $province->id)
            ->where('factory_id', $factory_id)
            ->where('is_active', Address::ADDRESS_ACTIVE)
            ->where('is_deleted', 0)
            ->first();


        if($city == null)
            return null;

        if($level < 3)
            return $city;

        $district_name = $addr[2];

        $district = Address::where('name', $district_name)->where('level', 3)
            ->where('parent_id', $city->id)
            ->where('factory_id', $factory_id)
            ->where('is_active', Address::ADDRESS_ACTIVE)
            ->where('is_deleted', 0)
            ->first();


        if($district == null)
            return null;

        if($level < 4)
            return $district;

        $street_name = $addr[3];

        $street = Address::where('name', $street_name)->where('level', 4)
            ->where('parent_id', $district->id)
            ->where('factory_id', $factory_id)
            ->where('is_active', Address::ADDRESS_ACTIVE)
            ->where('is_deleted', 0)
            ->first();

        if($street == null)
            return null;

        if($level < 5)
            return $street;

        $xiaoqi_name = $addr[4];

        $xiaoqi = Address::where('name', $xiaoqi_name)->where('level', 5)
            ->where('parent_id', $street->id)
            ->where('factory_id', $factory_id)
            ->where('is_active', Address::ADDRESS_ACTIVE)
            ->where('is_deleted', 0)
            ->first();

        return $xiaoqi;
    }

    /**
     * 获取该奶厂所属省级地址
     * @param $factoryId
     * @return mixed
     */
    public static function getProvinces($factoryId) {
        return Address::where('level', 1)
            ->where('factory_id', $factoryId)
            ->where('parent_id', 0)
            ->where('is_active', 1)
            ->where('is_deleted', 0)
            ->get();
    }

    /**
     * 获取上级地址
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent() {
        return $this->belongsTo('App\Model\BasicModel\Address', 'parent_id', 'id');
    }

    /**
     * 获取地址全名
     * @return mixed|string
     */
    public function getFullName() {
        $objAddress = $this;
        $strFullName = $this->name;

        // 获取上级地址
        do {
            $objAddress = $objAddress->parent;

            if ($objAddress) {
                $strFullName = $objAddress->name . " " . $strFullName;
            }
        }
        while ($objAddress);

        return $strFullName;
    }
}
