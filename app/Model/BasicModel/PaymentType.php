<?php

namespace App\Model\BasicModel;

use Illuminate\Database\Eloquent\Model;

class PaymentType extends Model
{
    public $table = 'paymenttype';

    public $timestamps = false;

    protected $fillable = [
        'name',
    ];

    const PAYMENT_TYPE_MONEY_NORMAL =1;
    const PAYMENT_TYPE_CARD =2;
    const PAYMENT_TYPE_WECHAT =3;
}
