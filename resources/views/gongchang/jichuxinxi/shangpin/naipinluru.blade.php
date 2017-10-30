@extends('gongchang.layout.master')

@section('css')

    <!-- Multi Select Combo -->
    <link href="<?=asset('css/plugins/chosen/chosen.css') ?>" rel="stylesheet">

    @include('gongchang.jichuxinxi.shangpin.style')

@endsection

@section('content')
    @include('gongchang.theme.sidebar')
    <div id="page-wrapper" class="gray-bg dashbard-1">
        @include('gongchang.theme.header')
        <div class="row border-bottom">
            <ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
                <li>
                    <a href="">基础信息管理</a>
                </li>
                <li>
                    <a href={{URL::to('/gongchang/jichuxinxi/shangpin')}}>商品管理</a>
                </li>
                <li>
                    <strong>奶品录入</strong>
                </li>
            </ol>
        </div>

        <div class="row border-bottom">
            <div class="col-lg-12" style="background-color:white">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        <form class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">商品名称 : </label>
                                <div class="col-sm-2">
                                    <input type="text" required id="product_name" name="product-name">
                                </div>
                                <label class="col-sm-2 control-label">商品简称 : </label>
                                <div class="col-sm-2">
                                    <input type="text" required id="product_simple_name" name="product-simple-name">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-2 control-label">上传图片 : </label>
                                <div id="imageset" class="col-md-8 imageset">
                                    <div class="image-preview col-md-2" data-attached="0">
                                        <label for="image-upload" class="image-label">选择文件</label>
                                        <input type="file" name="logoimage[]" class="image-upload"/>
                                        <div class="file-panel" style="height: 0px;">
                                            <span class="cancel">删除</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-2 control-label">牛奶 : </label>

                                <div class="col-md-2">
                                    <select class="form-control" name="property" id="milk_type">
                                        @for($i =1; $i <= 3; $i++)
                                            <option value="{{ $i }}">{{ App\Model\ProductModel\Product::propertyName($i) }}</option>
                                        @endfor
                                    </select>

                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-2 control-label">商品分类 : </label>

                                <div class="col-md-2">
                                    @if (isset($categories))
                                        <select class="form-control" name="product-category" id="product_category">
                                            @for($i =0; $i < count($categories); $i++)
                                                <option value="{{ $categories[$i]->id }}">{{ $categories[$i]->name }}</option>
                                            @endfor
                                        </select>
                                    @else
                                        <select class="form-control" name="product-category" id="product_category">
                                            <option selected value="none">没有活动分类.</option>
                                        </select>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-2 control-label">简介 : </label>

                                <div class="col-md-6">
                                    <textarea placeholder="" class="form-control" style="padding:10px;"
                                              id="product_introduction" name="product-introduction"></textarea>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-2 control-label">规格 : </label>

                                <div class="col-md-2">
                                    @if(isset($bottle_types) && count($bottle_types) > 0)
                                        <select class="form-control" id="bottle_type" name="bottle_type">
                                            @for($i=0; $i < count($bottle_types); $i++)
                                                <option value="{{$bottle_types[$i]->id}}">{{$bottle_types[$i]->name}}</option>
                                            @endfor
                                        </select>
                                    @else
                                        <select class="form-control" id="bottle_type" name="bottle_type">
                                            <option value="none">没有规格</option>
                                        </select>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-2 control-label">保质期 : </label>

                                <div class="col-md-2">
                                    <div class="col-md-10">
                                        <input class="form-control" name="guarantee-period" id="guarantee_period">
                                        </input>
                                    </div>
                                    <div class="col-md-2" style="padding: 5px 0;">
                                        天
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-2 control-label">储存条件 : </label>

                                <div class="col-md-6">
                                    <textarea class="form-control" style="padding:10px;" name="guarantee-spec"
                                              id="guarantee_req"></textarea>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-2 control-label">配料 : </label>

                                <div class="col-md-6">
                                    <textarea class="form-control" placeholder="天然有机生牛乳" style="padding:10px;"
                                              name="material" id="material"></textarea>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-2 control-label">生产周期 : </label>

                                <div class="col-md-2">
                                    <select class="form-control" name="production-time" id="production_period">
                                        <option value="24">24小时</option>
                                        <option value="48">48小时</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-2 control-label">奶筐规格 : </label>
                                <div class="col-md-2">
                                    @if(isset($product_basket_specs) && count($product_basket_specs) > 0)
                                        <select class="form-control" id="product_basket_spec"
                                                name="product-basket-spec">
                                            @for($i=0; $i < count($product_basket_specs); $i++)
                                                <option value="{{$product_basket_specs[$i]->id}}">{{$product_basket_specs[$i]->name}}</option>
                                            @endfor
                                        </select>
                                    @else
                                        <select class="form-control" id="product_basket_spec"
                                                name="product-basket-spec">
                                            <option value="none">没有奶筐规格</option>
                                        </select>
                                    @endif

                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-2 control-label">是否需要返厂 : </label>
                                <div class="col-md-2">
                                    <input type="checkbox" class="js-switch" checked name="depot-need" id="depot_need"/>
                                </div>
                            </div>

                            <div class="form-group" style="margin-bottom: 15px !important;">
                                <label class="col-md-2 control-label"> 价格模板 : </label>
                                <div>
                                    <div id="product_area_set_result" class="col-md-12">
                                        <div class="col-md-12  form-group">
                                            <div class="tabs-container col-md-8 col-md-offset-2">
                                                <ul class="nav nav-tabs closeable-tabs">
                                                </ul>
                                                <div class="tab-content">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            @include('gongchang.jichuxinxi.shangpin.template')

                            <div class="form-group" style="margin-bottom: 30px;">
                                <div class="row" style="margin-bottom: 15px;">
                                    <label class="col-md-2 control-label">产品详情 : </label>
                                </div>
                                <div class="col-md-10" style="margin:0 auto; float: none;" id="ueditor_div">
                                    <script id="editor" type="text/plain" style="width: 100%;height:500px;"></script>
                                </div>
                            </div>
                        </form>

                        <div class="form-group" style="margin-bottom: 30px;">
                            <div class="col-md-offset-4 col-md-4">
                                <div class="col-md-6">
                                    <button class="btn btn-success btn-md" onclick="insert_product()"
                                            style="width: 100%;">保存
                                    </button>
                                    <!--button class="btn btn-success btn-md" onclick="save_temp()" style="width: 100%;">保存</button-->
                                </div>
                                <div class="col-md-6">
                                    <button class="btn btn-success btn-md" onclick="cancel_add_product()"
                                            style="width:100%;">取消
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')

    <!-- Chosen -->
    <script src="<?=asset('js/plugins/chosen/chosen.jquery.js') ?>"></script>

    <!-- UE Editor -->
    <script type="text/javascript" charset="utf-8" src="<?=asset('ueditor/ueditor.config.js')?>"></script>
    <script type="text/javascript" charset="utf-8" src="<?=asset('ueditor/ueditor.all.js')?>"></script>
    <!--建议手动加在语言，避免在ie下有时因为加载语言失败导致编辑器加载失败-->
    <!--这里加载的语言文件会覆盖你在配置项目里添加的语言类型，比如你在配置项目里配置的是英文，这里加载的中文，那最后就是中文-->
    <script type="text/javascript" charset="utf-8" src="<?=asset('ueditor/lang/zh-cn/zh-cn.js')?>"></script>

    <!--upload preview-->
    <script type="text/javascript" src="<?=asset('js/plugins/imgupload/jquery.uploadPreview.js')?>"></script>

    <script type="text/javascript" src="<?=asset('js/pages/gongchang/naipin_common.js?170830')?>"></script>
    <script type="text/javascript" src="<?=asset('js/pages/gongchang/naipin_show.js?170830')?>"></script>
    <script type="text/javascript" src="<?=asset('js/pages/gongchang/naipin_insert.js?170930')?>"></script>
@endsection	
