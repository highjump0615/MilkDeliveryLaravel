<?php

namespace App\Model\ReviewModel;

use App\Model\BasicModel\Customer;
use App\Model\OrderModel\Order;
use Illuminate\Database\Eloquent\Model;
use DateTime;
use DateTimeZone;

class Review extends Model
{
    CONST REVIEW_STATUS_WAITTING = 1;
    CONST REVIEW_STATUS_ISOLATION = 2;
    CONST REVIEW_STATUS_PASSED = 3;
    protected $table = "reviews";

    public $timestamps = false;

    protected $fillable =[
        'customer_id',
        'order_id',
        'mark',
        'content',
        'status',
        'created_at',
    ];

    protected $appends = [
        'tel_number'
    ];

    public function getTelNumberAttribute()
    {
        $customer = Customer::find($this->customer_id);
        if($customer)
        {
            $phone=$customer->phone;
            $length = strlen($phone);
            $asterisk = "";
            for($i = 0; $i<$length-6; $i++)
            {
                $asterisk .= "*";
            }

            $tel_number = substr_replace($phone, $asterisk, 3, $length-6);
            return $tel_number;

        } else
        {
            return "122*****112";
        }
    }

}
