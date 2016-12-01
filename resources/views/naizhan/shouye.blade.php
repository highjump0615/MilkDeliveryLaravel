@extends('naizhan.layout.master')
@section('content')
    @include('naizhan.theme.sidebar')
    <div id="page-wrapper" class="gray-bg dashbard-1">
        @include('naizhan.theme.header')
        <div class="row border-bottom">
            <ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">

                <li>
                    <a href=""><strong>首页</strong></a>
                </li>
            </ol>
        </div>
        <div class="ibox-content gray-bg vertical-align">
            <div class="col-lg-2"></div>
            <div class="col-lg-8">
                <?php $role_id = Auth::guard('naizhan')->user()->user_role_id;
                $role_pages = \App\Model\UserModel\UserPageAccess::where('user_role_id',$role_id)->get();?>
                @foreach($role_pages as $rp)
                        @if($rp->page_id == 120)
                            <div class="col-md-6">
                                <button class="btn btn-lg btn-success dim" onclick="location.href='xitong/yonghu'" type="button" style="width:100%; height: 100px;"><i class="fa fa-wrench"></i> 系统管理</button>
                            </div>
                        @endif
                    @if($rp->page_id == 84)
                            <div class="col-md-6">
                                <button class="btn btn-lg btn-success dim" onclick="location.href='dingdan/quanbuluru'" type="button" style="width:100%; height: 100px;"><i class="fa fa-check-circle"></i> 订单管理</button>
                            </div>
                    @endif
                    @if($rp->page_id == 92)
                            <div class="col-md-6">
                                <button class="btn btn-lg btn-success dim" onclick="location.href='naizhan/jibenziliao'" type="button" style="width:100%; height: 100px;"><i class="fa fa-bank"></i> 奶站管理</button>
                            </div>
                    @endif
                    @if($rp->page_id == 95)
                            <div class="col-md-6">
                                <button class="btn btn-lg btn-success dim" onclick="location.href='shengchan/jihuaguanli'" type="button" style="width:100%; height: 100px;"><i class="fa fa-tasks"></i> 生产与配送</button>
                            </div>
                    @endif
                    @if($rp->page_id == 100)
                            <div class="col-md-6">
                                <button class="btn btn-lg btn-success dim" onclick="location.href='tongji/naipinpeisongri'" type="button" style="width:100%; height: 100px;"><i class="fa fa-table"></i> 统计分析</button>
                            </div>
                    @endif
                    @if($rp->page_id == 105)
                            <div class="col-md-6">
                                <button class="btn btn-lg btn-success dim" onclick="location.href='kehu/kehudangan'" type="button" style="width:100%; height: 100px;"><i class="fa fa-user"></i> 客户管理</button>
                            </div>
                    @endif
                    @if($rp->page_id == 107)
                            <div class="col-md-6">
                                <button class="btn btn-lg btn-success dim" onclick="location.href='pingkuang/pingkuangshouhui'" type="button" style="width:100%; height: 100px;"><i class="fa fa-flask"></i> 瓶框管理</button>
                            </div>
                    @endif
                    @if($rp->page_id == 111)
                            <div class="col-md-6">
                                <button class="btn btn-lg btn-success dim" onclick="location.href='xiaoxi/zhongxin'" type="button" style="width:100%; height: 100px;"><i class="fa fa-hacker-news"></i> 消息中心</button>
                            </div>
                    @endif
                    @if($rp->page_id == 113 )
                            <div class="col-md-6">
                                <button class="btn btn-lg btn-success dim" onclick="location.href='caiwu/taizhang'" type="button" style="width:100%; height: 100px;"><i class="fa fa-cny"></i> 财务管理</button>
                            </div>
                    @endif
                @endforeach

            </div>
            <div class="col-lg-2"></div>
        </div>
    </div>
@endsection