<?php

namespace App\Model\BasicModel;

use Illuminate\Database\Eloquent\Model;
use App\Model\BasicModel\DistrictData;

class CityData extends Model
{
    protected $table = 'city';

    protected $fillable = [
        'code',
        'name',
        'provincecode',
    ];

    public $timestamps = false;

  	protected $appends = [
  		'district',
  	];

  	public function getDistrictAttribute()
  	{
  		$district = null;
  		$district = DistrictData::where('citycode', $this->code)->get();
  		return $district;
  	}
}