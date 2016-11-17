<?php

namespace App\Model\ReviewModel;

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
        'product_id',
        'order_id',
        'mark',
        'content',
        'status',
        'created_at',
    ];
}
