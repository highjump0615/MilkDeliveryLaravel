<?php

namespace App\Model\UserModel;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    const ZONGPINGTAI = 1;
    const GONGCHANG = 2;
    const NAIZHAN = 3;

    protected $fillable = [
        'backend_type',
        'parent_page',
        'name',
        'order_no',
        'page_url',
        'page_ident',
        'icon_name'
    ];

    protected $appends = [
        'sub_pages',
    ];

    public function getSubPagesAttribute(){
        $pages = Page::where('parent_page', $this->id)->orderby('order_no')->get();
        return $pages;
    }
}
