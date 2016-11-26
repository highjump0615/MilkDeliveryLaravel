<?php

namespace App\Model\ProductModel;

use Illuminate\Database\Eloquent\Model;

class ProductPrice extends Model
{
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

	public $timestamps = false;

    public static function priceTemplateFromAddress($product_id, $target_address) {
        
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

        $candidates = ProductPrice::where('product_id', $product_id)->where('sales_area', 'like', '%'.$district.'%')->get();

        foreach($candidates as $pp) {
            $addresses = explode(',', $pp->sales_area);
            foreach($addresses as $address) {
                $addr_parts = explode(' ', $address);

                if($count >=3 && $tar_addrs[0] == $addr_parts[0] && $tar_addrs[1] == $addr_parts[1] && $tar_addrs[2] == $addr_parts[2] ) {
                    return $pp;
                } else if($count == 2 && $tar_addrs[0] == $addr_parts[0] && $tar_addrs[1] == $addr_parts[1]){
                    return $pp;
                }
            }
        }

        return null;
    }

}
