<?php

namespace App\Model\UserModel;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    const USER_STATUS_ACTIVE = 1;
    const USER_STATUS_INACTIVE = 0;

    /** backend type */
    const USER_BACKEND_ADMIN = 1;
    const USER_BACKEND_FACTORY = 2;
    const USER_BACKEND_STATION = 3;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'password',
        'factory_id',
        'station_id',
        'nick_name',
        'user_role_id',
        'status',
        'backend_type',
        'description',
        'last_session',
        'last_used_ip',
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

    /**
     * 用户类型
     * @return string
     */
    public function getBackendTypeName() {
        $result = '总平台';

        if ($this->backend_type == 2) {
            $result = '奶厂';
        }
        else if ($this->backend_type == 3) {
            $result = '奶站';
        }

        return $result;
    }

    /**
     * 是否超级管理员
     * @param $type
     * @return bool
     */
    public function isSuperUser($type) {
        $role = UserRole::where('backend_type', $type)->get()->first();
        return ($this->user_role_id == $role->id);
    }
}
