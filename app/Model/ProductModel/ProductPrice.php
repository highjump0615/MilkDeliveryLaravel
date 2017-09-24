<?php

namespace App\Model\ProductModel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductPrice extends Model
{
    use SoftDeletes;

    protected $table= 'productprice';
    
    protected $fillable = [
    	'product_id',
        'template_name',
        'sales_area',
        'retail_price',
        'month_price',
        'season_price',
        'half_year_price',
        'settle_price',
    ];

    /**
     * 根据地区获取价格模板
     * @param $product_id
     * @param $target_address
     * @param null $date
     * @return null
     */
    public static function priceTemplateFromAddress($product_id, $target_address, $date = null) {
        
        $tar_addrs = explode(" ", $target_address);

        $count = count($tar_addrs);

        if( $count < 2 )
            return null;

        if($count == 2)
        {
            $district = $tar_addrs[0]." ".$tar_addrs[1];
        } else if($count >= 3)
        {
            $district = $tar_addrs[0]." ".$tar_addrs[1]." ".$tar_addrs[2];
        }

        $queryPrice = ProductPrice::withTrashed()
            ->where('product_id', $product_id)
            ->where('sales_area', 'like', '%'.$district.'%')
            ->orderBy('created_at', 'desc');

        // 日期限制
        if (!empty($date)) {
            $queryPrice->where('created_at', '<', $date);
        }

        $candidates = $queryPrice->first();
        if (!empty($candidates)) {
            $addresses = explode(',', $candidates->sales_area);
            foreach($addresses as $address) {
                $addr_parts = explode(' ', $address);

                if($count >=3 && $tar_addrs[0] == $addr_parts[0] && $tar_addrs[1] == $addr_parts[1] && $tar_addrs[2] == $addr_parts[2] ) {
                    return $candidates;
                } else if($count == 2 && $tar_addrs[0] == $addr_parts[0] && $tar_addrs[1] == $addr_parts[1]){
                    return $candidates;
                }
            }
        }

        return null;
    }

}
