<?php

namespace App\Model\BasicModel;

use Illuminate\Database\Eloquent\Model;
use App\Model\BasicModel\CityData;

class ProvinceData extends Model
{
    protected $table = 'province';

    protected $fillable = [
        'code',
        'name',
    ];

    public $timestamps = false;

  	protected $appends = [
  		'city',
  	];

  	public function getCityAttribute()
  	{
  		$city = null;

  		$city = CityData::where('provincecode', $this->code)->get();
      
  		return $city;
  	}
}

