<?php

namespace App\Model\UserModel;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{

    const USERROLE_BACKEND_TYPE_ZONGPINGTAI = 1;
    const USERROLE_BACKEND_TYPE_GONGCHANG = 2;
    const USERROLE_BACKEND_TYPE_NAIZHAN = 3;

    const USERROLE_ZONGPINGTAI_TOTAL_ADMIN = 100;
    const USERROLE_GONGCHANG_TOTAL_ADMIN = 1;
    const USERROLE_NAIZHAN_TOTAL_ADMIN = 200;
    
    protected $fillable = [
        'id',
        'name',
        'backend_type',
    ];

    public $timestamps = false;

    protected $table = 'userroles';

    protected $appends = [
        'page_access_list',
    ];
    public function getPageAccessListAttribute(){
        $role_id = $this->id;

        $pages = Page::where('backend_type', $this->backend_type)->where('parent_page', 0)->orderby('order_no')->get();

        foreach($pages as $p) {
            $page_access = UserPageAccess::where('user_role_id', $role_id)->where('page_id', $p->id)->get()->first();

            if($page_access == null)
                $p["access"] = false;
            else
                $p["access"] = true;

            $sub_pages = $p->sub_pages;
            foreach($sub_pages as $s) {
                $page_access = UserPageAccess::where('user_role_id', $role_id)->where('page_id', $s->id)->get()->first();

                if($page_access == null)
                    $s["access"] = false;
                else
                    $s["access"] = true;
            }

            $p["pages"] = $sub_pages;
        }

        return $pages;
    }

    public function pages(){
        return $this->belongsToMany('App\Model\UserModel\Page', 'userpageaccess', 'user_role_id', 'page_id');
    }
}
