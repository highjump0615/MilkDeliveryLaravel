<?php

namespace App\Model\ProductModel;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    protected $table= 'productcategory';
    
    protected $fillable = [
        'name',
        'factory_id',
        'is_deleted',
    ];

	public $timestamps = false;

}
