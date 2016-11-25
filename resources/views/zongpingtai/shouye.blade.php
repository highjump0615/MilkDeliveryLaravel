@extends('zongpingtai.layout.master')
@section('content')
    @include('zongpingtai.theme.sidebar')
    <div id="page-wrapper" class="gray-bg dashbard-1">
        @include('zongpingtai.theme.header')
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
                <?php $role_id = Auth::guard('zongpingtai')->user()->user_role_id;
                $role_pages = \App\Model\UserModel\UserPageAccess::where('user_role_id',$role_id)->get();?>
                @foreach($role_pages as $rp)
                    @if($rp->page_id == 59)
                        <div class="col-lg-6">
                            <button class="btn btn-lg btn-success dim" onclick="location.href='xitong/chakanrizhi'" type="button" style="width:100%; height: 100px;"><i class="fa fa-tasks"></i> 系统管理</button>
                        </div>
                    @endif
                    @if($rp->page_id == 68)
                        <div class="col-lg-6">
                            <button class="btn btn-lg btn-success dim" onclick="location.href='yonghu/guanliyuanzhongxin'" type="button" style="width:100%; height: 100px;"><i class="fa fa-group"></i> 用户管理</button>
                        </div>
                    @endif
                    @if($rp->page_id == 72)
                        <div class="col-lg-6">
                            <button class="btn btn-lg btn-success dim" onclick="location.href='caiwu/zhanghuguanli'" type="button" style="width:100%; height: 100px;"><i class="fa fa-cny"></i> 财务管理</button>
                        </div>
                    @endif
                    @if($rp->page_id == 75)
                        <div class="col-lg-6">
                            <button class="btn btn-lg btn-success dim" onclick="location.href='kehu/kehuliebiao'" type="button" style="width:100%; height: 100px;"><i class="fa fa-user"></i> 客户管理</button>
                        </div>
                    @endif
                    @if($rp->page_id == 77)
                        <div class="col-lg-6">
                            <button class="btn btn-lg btn-success dim" onclick="location.href='tongji/naipinpeisong'" type="button" style="width:100%; height: 100px;"><i class="fa fa-table"></i> 统计分析</button>
                        </div>
                    @endif
                @endforeach
            </div>
            <div class="col-lg-2"></div>
        </div>
    </div>
@endsection