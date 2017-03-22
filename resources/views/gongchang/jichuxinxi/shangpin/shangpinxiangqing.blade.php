@extends('gongchang.layout.master')

@section('css')

    <link href="<?=asset('css/plugins/chosen/chosen.css') ?>" rel="stylesheet">

    <style>

        .image-preview {
            height: 100px;
            width: 100px;
            position: relative;
            overflow: hidden;
            background-color: #ffffff;
            margin-left: 10px;
            color: #ecf0f1;
            border: 1px solid gray;
            display: inline-block;
            background-size: contain;
            background-repeat: no-repeat;
            background-image: url('{{ URL::asset('js/plugins/imgupload/add_new.png') }}');
        }

        .image-preview input {
            line-height: 200px;
            font-size: 200px;
            position: absolute;
            opacity: 0;
            z-index: 10;
        }

        .image-preview label {
            position: absolute;
            z-index: 5;
            opacity: 0;
            cursor: pointer;
            background-color: #bdc3c7;
            width: 200px;
            height: 50px;
            font-size: 20px;
            line-height: 50px;
            text-transform: uppercase;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            margin: auto;
            text-align: center;
        }

        div.file-panel {
            position: absolute;
            height: 0;
            filter: progid:DXImageTransform.Microsoft.gradient(GradientType=0, startColorstr='#80000000', endColorstr='#80000000') \0;
            background: rgba(0, 0, 0, 0.5);
            width: 100%;
            top: 0;
            left: 0;
            overflow: hidden;
            z-index: 300;
        }

        div.file-panel span.cancel {
            background-position: -48px -24px;
        }

        div.file-panel span {
            width: 24px;
            height: 24px;
            display: inline;
            float: right;
            text-indent: -9999px;
            overflow: hidden;
            margin: 5px 1px 1px;
            cursor: pointer;
            background: url('{{ URL::asset('js/plugins/imgupload/icons.png') }}') no-repeat;
        }

        #update_price_bt {
            display: none;
        }

    </style>
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
                    <strong>牛奶奶品详情</strong>
                </li>
            </ol>
        </div>

        @if(!isset($product))
            <div class="row border-bottom">
                <div class="col-lg-12" style="background-color:white">
                    <div class="ibox float-e-margins">
                        <div class="ibox-content">
                            <p>对不起, 目前的产品不能在数据库中找到</p>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="row border-bottom">
                <div class="col-lg-12" style="background-color:white">
                    <div class="ibox float-e-margins">
                        <div class="ibox-content">
                            <form class="form-horizontal">
                                <div class="form-group">
                                    <label class="col-md-2 control-label">商品名称 : </label>
                                    <div class="col-md-2">
                                        <input type="text" required id="product_name" name="product-name"
                                               value="{{$product->name}}">
                                    </div>


                                    <label class="col-md-2 control-label">商品简称 : </label>
                                    <div class="col-md-2">
                                        <input type="text" required id="product_simple_name" name="product-simple-name"
                                               value="{{$product->simple_name}}">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-2 control-label">上传图片 : </label>
                                    <div id="imageset" class="col-md-8 imageset">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-2 control-label">牛奶 : </label>

                                    <div class="col-md-2">
                                        <select class="form-control" name="property" id="milk_type">
                                            @for($i =1; $i <= 3; $i++)
                                                @if($product->property == $i)
                                                    <option value="{{ $i }}"
                                                            selected>{{ App\Model\ProductModel\Product::propertyName($i) }}</option>
                                                @else
                                                    <option value="{{ $i }}">{{ App\Model\ProductModel\Product::propertyName($i) }}</option>
                                                @endif
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
                                                    @if($categories[$i]->id == $product->category)
                                                        <option value="{{$categories[$i]->id}}"
                                                                selected>{{ $categories[$i]->name }}</option>
                                                    @else
                                                        <option value="{{$categories[$i]->id}}">{{ $categories[$i]->name }}</option>
                                                    @endif
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
                                                  id="product_introduction"
                                                  name="product-introduction">{{$product->introduction}}</textarea>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-2 control-label">规格 : </label>

                                    <div class="col-md-2">
                                        @if(isset($bottle_types) && count($bottle_types) > 0)
                                            <select class="form-control" id="bottle_type" name="bottle-type">
                                                @for($i=0; $i < count($bottle_types); $i++)
                                                    @if($bottle_types[$i]->id == $product->bottle_type)
                                                        <option value="{{$bottle_types[$i]->id}}"
                                                                selected>{{$bottle_types[$i]->name}}</option>
                                                    @else
                                                        <option value="{{$bottle_types[$i]->id}}">{{$bottle_types[$i]->name}}</option>
                                                    @endif

                                                @endfor
                                            </select>
                                        @else
                                            <select class="form-control" id="bottle_type" name="bottle-type">
                                                <option value="none">没有规格</option>
                                            </select>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-2 control-label">保质期 : </label>

                                    <div class="col-md-2">
                                        <div class="col-md-10">
                                            <input class="form-control" name="guarantee-period" id="guarantee_period"
                                                   value="{{$product->guarantee_period}}">
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
                                                  id="guarantee_req">{{$product->guarantee_req}}</textarea>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-2 control-label">配料 : </label>

                                    <div class="col-md-6">
                                        <textarea class="form-control" placeholder="天然有机生牛乳" style="padding:10px;"
                                                  name="material" id="material">{{$product->material}}</textarea>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-2 control-label">生产周期 : </label>

                                    <div class="col-md-2">
                                        <select class="form-control" name="production-time" id="production_period">
                                            @if( $product->production_period  == "24")
                                                <option value="24" selected>24小时</option>
                                            @else
                                                <option value="24">24小时</option>
                                            @endif
                                            @if( $product->production_period == "48")
                                                <option value="48" selected>48小时</option>
                                            @else
                                                <option value="48">48小时</option>
                                            @endif
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
                                                    @if($product->basket_spec == $product_basket_specs[$i]->id)
                                                        <option value="{{$product_basket_specs[$i]->id}}"
                                                                selected>{{$product_basket_specs[$i]->name}}</option>
                                                    @else
                                                        <option value="{{$product_basket_specs[$i]->id}}">{{$product_basket_specs[$i]->name}}</option>
                                                    @endif
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
                                        @if($product->bottle_back_factory == "1")
                                            <input type="checkbox" class="js-switch" checked name="depot-need"
                                                   id="depot_need"/>
                                        @else
                                            <input type="checkbox" class="js-switch" name="depot-need" id="depot_need"/>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-2 control-label"> 价格模板 : </label>
                                    <div>

                                        @if(isset($price_template))
                                            <div id="product_area_set_result">
                                                <div class="col-md-12 form-group">
                                                    <div class="tabs-container col-md-8 col-md-offset-2">
                                                        <ul class="nav nav-tabs closeable-tabs">
                                                            @for($i = 0; $i<$new_count; $i++)
                                                                @if($i==0)
                                                                    <li class="active">
                                                                        <a data-toggle="tab" aria-expanded="true"
                                                                           href="#tab-{{$i+1}}">{{$price_template[$i]->template_name}}</a>
                                                                        <a class="close-tab"><i class="fa fa-times"></i></a>
                                                                    </li>
                                                                @else
                                                                    <li class="">
                                                                        <a data-toggle="tab" aria-expanded="false"
                                                                           href="#tab-{{$i+1}}">{{$price_template[$i]->template_name}}</a>
                                                                        <a class="close-tab"><i class="fa fa-times"></i></a>
                                                                    </li>
                                                                @endif
                                                            @endfor
                                                        </ul>
                                                        <div class="tab-content">
                                                            @for($i = 0; $i<$new_count; $i++)
                                                                <div id="tab-{{$i+1}}" class="tab-pane @if($i == 0) active @endif">
                                                                    <div class="panel-body">
                                                                        @foreach($price_template[$i]['sales_area_array'] as $province=>$array1)
                                                                            @foreach($array1 as $city=>$districts)
                                                                                <div class="col-md-3">
                                                                                    <label><span
                                                                                                class="province_sp">{{$province}}</span>&nbsp;&nbsp;<span
                                                                                                class="city_sp">{{$city}}</span></label>
                                                                                </div>
                                                                                <div class="col-md-7">
                                                                                    <label>包含分区:&nbsp;
                                                                            <span class="district_sp">
                                                                            {{$districts}}
                                                                        </span></label><br>
                                                                                </div>
                                                                                <div class="col-md-2 text-right">
                                                                                    <button type="button"
                                                                                            class="btn btn-success btn-outline"
                                                                                            data-action="edit_template">
                                                                                        <i class="fa fa-pencil"></i>修改
                                                                                    </button>
                                                                                </div>
                                                                            @endforeach
                                                                        @endforeach
                                                                        <br>
                                                                        <input type="hidden" class="price_tp_id"
                                                                               value="{{$price_template[$i]->id}}"/>
                                                                        <div class="col-md-offset-3 col-md-9">
                                                                            {{--<label style="display:none;">模板名称:&nbsp;--}}
                                                                            {{--<span class="name_sp">{{$price_template[$i]->template_name}}</span></label><br>--}}
                                                                            <input type="hidden" class="name_sp"
                                                                                   value="{{$price_template[$i]->template_name}}">
                                                                            <label>零售价:&nbsp; <span
                                                                                        class="retail_sp">{{$price_template[$i]->retail_price}}</span>元</label><br>
                                                                            <label>月单:&nbsp; <span
                                                                                        class="month_sp">{{$price_template[$i]->month_price}}</span>元</label><br>
                                                                            <label>季单:&nbsp; <span
                                                                                        class="season_sp">{{$price_template[$i]->season_price}}</span>元</label><br>
                                                                            <label>半年单:&nbsp; <span
                                                                                        class="half_year_sp">{{$price_template[$i]->half_year_price}}</span>元</label><br>
                                                                            <label>结算价:&nbsp; <span
                                                                                        class="settle_sp">{{$price_template[$i]->settle_price}}</span>元</label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endfor
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div id="product_area_set_result" class="col-md-12">
                                                <div class="col-md-12 form-group">
                                                    <div class="tabs-container col-md-8 col-md-offset-2">
                                                        <ul class="nav nav-tabs closeable-tabs">
                                                        </ul>
                                                        <div class="tab-content">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div id="product_area_set">
                                    <div class="form-group" style="margin-bottom: 15px !important;">
                                        <label class="col-md-3 control-label">模板名称 </label>
                                        <div class="col-md-2">
                                            <input type="text" id="template_name" class="form-control col-md-2">
                                        </div>

                                    </div>
                                    <div class="form-group area_select" style="margin-bottom: 15px !important;">
                                        <label class="col-md-3 control-label" style="padding-top: 7px;">销售区域:</label>
                                        <div class="col-md-2">
                                            <select class="form-control province_list">
                                                @if (isset($provinces))
                                                    @for ($i = 0; $i < count($provinces); $i++)
                                                        @if($i == 0)
                                                            <option value="{{$provinces[$i]->name}}"
                                                                    selected>{{$provinces[$i]->name}}</option>
                                                        @else
                                                            <option value="{{$provinces[$i]->name}}"
                                                            >{{$provinces[$i]->name}}</option>
                                                        @endif
                                                    @endfor
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <select class="form-control city_list">
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <select data-placeholder=""
                                                    class="form-control chosen-select district_list"
                                                    multiple style="width: 100%;">
                                            </select>
                                        </div>

                                    </div>
                                    <div class="form-group" style="margin-bottom: 15px !important;">
                                        <label class="col-md-3 control-label">零售价 </label>
                                        <div class="col-md-2">
                                            <input type="number" min="0.1" step="any" id="retail_price"
                                                   class="form-control col-md-2">
                                        </div>
                                        <label class="control-label">元</label>
                                    </div>

                                    <div class="form-group" style="margin-bottom: 15px !important;">
                                        <label class="col-md-3 control-label">月单 </label>
                                        <div class="col-md-2">
                                            <input type="number" min="0.1" step="any" id="month_price"
                                                   class="form-control col-md-2">
                                        </div>
                                        <label class="control-label">元</label>
                                    </div>

                                    <div class="form-group" style="margin-bottom: 15px !important;">
                                        <label class="col-md-3 control-label">季单 </label>
                                        <div class="col-md-2">
                                            <input type="number" min="0.1" step="any" id="season_price"
                                                   class="form-control col-md-2">
                                        </div>
                                        <label class="control-label">元</label>
                                    </div>

                                    <div class="form-group" style="margin-bottom: 15px !important;">
                                        <label class="col-md-3 control-label">半年单</label>
                                        <div class="col-md-2">
                                            <input type="number" min="0.1" step="any" id="half_year_price"
                                                   class="form-control col-md-2">
                                        </div>
                                        <label class="control-label">元</label>
                                    </div>
                                    <div class="form-group" style="margin-bottom: 15px !important;">
                                        <label class="col-md-3 control-label">结算价 </label>
                                        <div class="col-md-2">
                                            <input type="number" min="0.1" step="any" id="settle_price"
                                                   class="form-control col-md-2">
                                        </div>
                                        <label class="control-label col-md-1" style="padding:0; text-align: left;">元</label>

                                        <div class="form-group col-md-6" style="text-align:left;">
                                            <button type="button" id="add_price_bt" class="btn btn-outline btn-success"
                                                    style="margin-right: 10px;" onclick="add_price_template()"><i
                                                        class="fa fa-plus"></i>添加价格模板
                                            </button>
                                            <button type="button" id="update_price_bt"
                                                    class="btn btn-outline btn-success"
                                                    style="margin-right: 10px;">
                                                <i class="fa fa-save"></i>修改价格模板
                                            </button>
                                            <button type="button" class="btn btn-outline btn-success"
                                                    onclick="init_price_template()">取消
                                            </button>
                                        </div>

                                    </div>

                                </div>

                                <div class="form-group col-md-12" style="margin-bottom: 30px;">
                                    <div class="row" style="margin-bottom: 15px;">
                                        <label class="col-md-2 control-label">产品详情 : </label>
                                    </div>
                                    <div class="col-md-10" style="margin:0 auto; float: none;" id="ueditor_div">
                                        <script id="editor" type="text/plain"
                                                style="width: 100%;height:500px;"></script>
                                    </div>
                                </div>
                            </form>
                            <div class="form-group" style="margin-bottom: 30px;">
                                <div class="col-md-offset-4 col-md-4">
                                    <div class="col-md-6">
                                        <button class="btn btn-success btn-md" onclick="update_product()"
                                                style="width: 100%;">更新
                                        </button>
                                        <!--button class="btn btn-success btn-md" onclick="save_temp()" style="width: 100%;">保存</button-->
                                    </div>
                                    <div class="col-md-6">
                                        <button class="btn btn-success btn-md" onclick="cancel_update_product()"
                                                style="width:100%;">取消
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@section('script')

    <!-- Chosen -->
    <script src="<?=asset('js/plugins/chosen/chosen.jquery.js') ?>"></script>

    <!-- UE Editor -->
    <script type="text/javascript" charset="utf-8" src="<?=asset('ueditor/ueditor.config.js')?>"></script>
    <script type="text/javascript" charset="utf-8" src="<?=asset('ueditor/ueditor.all.js')?>"></script>
    <script type="text/javascript" charset="utf-8" src="<?=asset('ueditor/lang/zh-cn/zh-cn.js')?>"></script>

    <!--upload preview-->
    <script type="text/javascript" src="<?=asset('js/plugins/imgupload/jquery.uploadPreview.js')?>"></script>
    <script type="text/javascript" src="<?=asset('js/pages/gongchang/naipin_common.js')?>"></script>

    <script defer>

        var update_page = true;

        var init_tab_count = "{{$new_count}}";
        var names = [];
        var ue, current_product_id, current_product_name;
        ue = UE.getEditor('editor');
        var price_temp_changed = false;

        function show_ue_content(ue_data, ue) {
            if (ue_data == "")
                return;
            var data1 = ue_data[0].data.toString();
            ue.ready(function () {
                ue.setContent(data1, false);
            });
        }

        function show_product_images(pid) {
            if (!pid)
                return;
            var file1 = "{{$file1}}";
            var file2 = "{{$file2}}";
            var file3 = "{{$file3}}";
            var file4 = "{{$file4}}";

            var empty_data = '<div class="image-preview col-md-2" data-attached="0"  data-changed = "0" >\
                        <label for="image-upload" class="image-label">选择文件</label>\
                        <input type="file" name="logoimage[]" class="image-upload"/>\
                        <div class="file-panel" style="height: 0px;">\
                            <span class="cancel">删除</span>\
                        </div>\
                    </div>';

            var previous_data = '';

            if (file1 != "") {
                previous_data += '<div class="image-preview col-md-2" style="background-image:url(' + file1 + ')" data-attached="1" origin-data-attached = "1" data-changed = "0" >\
                        <label for="image-upload" class="image-label">选择文件</label>\
                        <input type="file" name="logoimage[]" class="image-upload"/>\
                        <div class="file-panel" style="height: 0px;">\
                            <span class="cancel">删除</span>\
                        </div>\
                    </div>';
            } else {
                previous_data += empty_data;
            }

            if (file2 != "") {
                previous_data += '<div class="image-preview col-md-2" style="background-image:url(' + file2 + ')" data-attached="1"  origin-data-attached = "1"  data-changed = "0" >\
                        <label for="image-upload" class="image-label">选择文件</label>\
                        <input type="file" name="logoimage[]" class="image-upload"/>\
                        <div class="file-panel" style="height: 0px;">\
                            <span class="cancel">删除</span>\
                        </div>\
                    </div>';
            } else if (file1 != "") {
                previous_data += empty_data;
            }

            if (file3 != "") {
                previous_data += '<div class="image-preview col-md-2" style="background-image:url(' + file3 + ')" data-attached="1"  origin-data-attached = "1"   data-changed = "0" >\
                        <label for="image-upload" class="image-label">选择文件</label>\
                        <input type="file" name="logoimage[]" class="image-upload"/>\
                        <div class="file-panel" style="height: 0px;">\
                            <span class="cancel">删除</span>\
                        </div>\
                    </div>';
            } else if (file2 != "") {
                previous_data += empty_data;
            }

            if (file4 != "") {
                previous_data += '<div class="image-preview col-md-2" style="background-image:url(' + file4 + ')" data-attached="1"  origin-data-attached = "1"  data-changed = "0" >\
                        <label for="image-upload" class="image-label">选择文件</label>\
                        <input type="file" name="logoimage[]" class="image-upload"/>\
                        <div class="file-panel" style="height: 0px;">\
                            <span class="cancel">删除</span>\
                        </div>\
                    </div>';
            } else if (file3 != "") {
                previous_data += empty_data;
            }

            $('#imageset').append(previous_data);

        }

        $(window).load(function () {
            current_product_id = "{{$product->id}}";

            current_product_name = "{{$product->name}}";

            show_product_images(current_product_id);

            var ue_data = $.parseHTML("{{$product->uecontent}}");
            var ue = UE.getEditor('editor');

            show_ue_content(ue_data, ue);
        });

    </script>
    <script type="text/javascript" src="<?=asset('js/pages/gongchang/naipin_show.js')?>"></script>
@endsection
