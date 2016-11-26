<?php

namespace App\Model\ProductModel;

use App\Model\FactoryModel\FactoryBottleType;
use Illuminate\Database\Eloquent\Model;

use App\Model\ProductModel\ProductCategory;


class Product extends Model
{
    protected $table= 'products';
    
    protected $fillable = [
    	'name',
        'simple_name',
        'category',
    	'introduction',
        'property',
    	'spec',
    	'guarantee_period',
    	'guarantee_req',
        'material',
    	'production_period',
    	'basket_spec',
    	'bottle_back_factory',
    	'bottle_no',
    	'photo_url1',
    	'photo_url2',
    	'photo_url3',
    	'photo_url4',
        'status',
        'bottle_type',
        'uecontent',
        'series_no',
        'factory_id',
        'is_deleted',
    ];

    protected $appends = [
    	'category_name',
        'bottle_type_name',
        'settle_price'
    ];

//	public $timestamps = false;

    const PRODUCT_PROPERTY_PURE_MILK = 1;
    const PRODUCT_PROPERTY_YOGURT = 2;
    const PRODUCT_PROPERTY_KOUWEI = 3;

    public static function propertyName($id) {
        if($id == 1)
            return "鲜奶";
        else if($id == 2)
            return "酸奶";
        else
            return "口味";
    }

    const PRODUCT_STATUS_ACTIVE =1;
    const PRODUCT_STATUS_INACTIVE =0;

    public function getCategoryNameAttribute(){
        $category_name = '';
        $category_id = $this->category;
        if(ProductCategory::find($category_id))
            $category_name = ProductCategory::find($category_id)->name;
        return $category_name;
    }

    public function getBottleTypeNameAttribute(){
        $bottle_type = FactoryBottleType::find($this->bottle_type);
        if($bottle_type)
            return $bottle_type->name;
    }

    public function getSettlePriceAttribute()
    {
        //Todo
        //ProductPrice::where('product_id', $this->id)->where('sales_area', )
         return 6.8;
    }
}
