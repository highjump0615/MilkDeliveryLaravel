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

    /**
     * 获取所有价格模板
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function prices() {
        return $this->hasMany('App\Model\ProductModel\ProductPrice');
    }

    /**
     * 获取奶品的结算价格
     * @param $address
     * @return float
     */
    public function getSettlePrice($address)
    {
        $dPrice = 0;

        $product_price = ProductPrice::priceTemplateFromAddress($this->id, $address);
        if ($product_price) {
            $dPrice = $product_price->settle_price;
        }

        return $dPrice;
    }

    /**
     * 获取奶框信息
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function box() {
        return $this->belongsTo('App\Model\FactoryModel\FactoryBoxType', 'basket_spec');
    }
}
