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
        'order_id',
        'mark',
        'content',
        'status',
        'created_at',
    ];

    public function addReview($order_id,$marks,$content){
        $current_datetime = new DateTime("now",new DateTimeZone('Asia/Shanghai'));
        $current_datetime_str = $current_datetime->format('Y-m-d H:i:s');
        $review = new $this;
        $review->mark = $marks;
        $review->content = $content;
        $review->order_id = $order_id;
        $review->customer_id = Order::find($order_id)->customer_id;
        $review->created_at = $current_datetime_str;
        $review->status = $this::REVIEW_STATUS_WAITTING;
        $review->save();
    }
    //
}
