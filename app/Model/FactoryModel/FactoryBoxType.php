<?php

namespace App\Model\FactoryModel;

use Illuminate\Database\Eloquent\Model;

class FactoryBoxType extends Model
{
    protected $table= 'mfboxtype';
    
    protected $fillable = [
        'name',
        'factory_id',
        'number',
        'is_deleted',
    ];

	public $timestamps = false;

    const FACTORY_BOX_TYPE_PREFIX = "BOX";

}
