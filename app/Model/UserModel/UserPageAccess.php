<?php

namespace App\Model\UserModel;

use Illuminate\Database\Eloquent\Model;

class UserPageAccess extends Model
{
    protected $fillable = [
        'page_id',
        'user_role_id',
    ];

    public $timestamps = false;

    protected $table = 'userpageaccess';
}
