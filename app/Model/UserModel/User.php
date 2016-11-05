<?php

namespace App\Model\UserModel;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'password',
        'factory_id',
        'nick_name',
        'user_role_id',
        'status',
        'backend_type',
        'description',
        'last_session',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    public $timestamps = true;

    protected $hidden = [
        'password', 'remember_token',
    ];

    /*get Role_name from user_role_id*/
    protected $appends = [
        'current_role_name',
    ];

    public function getCurrentRoleNameAttribute(){
        $role_id = $this->user_role_id;
        $role_name = UserRole::where('id',$role_id)->get()->first();
        return $role_name;
    }


    public function role(){
        return $this->belongsTo('App\Model\UserModel\UserRole', 'user_role_id', 'id');
    }
    public function canAccess($page = null)
    {
        if(is_null($page)) return false;

        $url_parts = explode(".", $page);

        if(count($url_parts)>3) {
            for($i=3; $i<count($url_parts); $i++)
                unset($url_parts[$i]);
        }

        $page = implode('/', $url_parts);

//        echo "$page<br><br>";


        $page_urls = $this->role->pages;
        foreach($page_urls as $p) {
            if($page == $p->page_url) {
                return true;
            }
        }

        return false;
    }
}
