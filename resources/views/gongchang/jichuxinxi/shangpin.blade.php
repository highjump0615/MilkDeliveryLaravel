@extends('gongchang.layout.master')
@section('css')
    <style>
        div.row > div.ibox-content:first-child {
            border-top: none;
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
                    <a href=""><strong>商品管理</strong></a>
                </li>
            </ol>
        </div>

        <div class="row white-bg" style="padding: 10px;">
            <div class="ibox-content">
                <div class="feed-element">
                    <div class="col-md-3">
                        <label>牛奶名称:</label>
                        <input type="text" id="sel_product_name">
                    </div>
                    <div class="col-md-3">
                        <label>编号:</label>
                        <input type="text" id="sel_product_series_no">
                    </div>
                    <div class="col-md-4">
                        <label class="col-md-4 text-right">分类:</label>
                        <div class="col-md-7">
                            <select name="product-category" id="sel_product_category" style="width: 100%; height: 30px;">
                                @if (isset($categories))
                                    <option selected value="none"></option>
                                    @for($i =0; $i < count($categories); $i++)
                                        <option value="{{ $categories[$i]->id }}">{{ $categories[$i]->name }}</option>
                                    @endfor
                                @else
                                    <option selected value="none">没有活动分类.</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-success btn-md btn-outline" data-action="show_selected">筛选</button>
                        <button type="button" class="btn btn-success btn-m btn-outline" data-action="print">打印</button>
                    </div>
                </div>
                <div class="feed-element">
                    <button class="btn  btn-success btn-outline"
                            onclick="window.location='{{URL::to('/gongchang/jichuxinxi/shangpin/naipinluru')}}'"
                            type="button"><i class="fa fa-plus"></i> 添加商品
                    </button>
                    <button class="btn btn-outline btn-success" data-action="add_category_modal" type="button"><i class="fa fa-plus"></i> 添加分类
                    </button>
                    <button class="btn btn-outline btn-success"
                            onclick="window.location='{{URL::to('/gongchang/jichuxinxi/shangpin/shangpincanshushezhi')}}'"
                            type="button"><i class="fa fa-setting"></i> 商品参数设置
                    </button>
                </div>
            </div>

            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    <div class="table-responsive"></div>
                    <table class="footable table table-bordered" data-page-size="10" id="product_table"  data-limit-navigation="5">
                        <thead>
                        <tr>
                            <th data-sort-ignore="true">序号</th>
                            <th data-sort-ignore="true">图片</th>
                            <th data-sort-ignore="true">名称</th>
                            <th data-sort-ignore="true">简称</th>
                            <th data-sort-ignore="true">编号</th>
                            <th data-sort-ignore="true">规格</th>
                            <th data-sort-ignore="true">所属分类</th>
                            <th data-sort-ignore="true">操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(isset($products) && count($products))
                            @for($i=0; $i < count($products); $i++)
                                <tr class="row-hover-light-blue" data-target="{{$products[$i]->id}}" data-show-1="1" data-show-2="1"
                                    data-show-3="1">
                                    <td>{{$i+1}}</td>
                                    <td>
                                        @if($products[$i]->photo_url1)
                                            <img src="{{asset('img/product/logo/'.$products[$i]->photo_url1)}}"
                                                 class="img-responsive" width="100px;"/>
                                        @elseif ($products[$i]->photo_url2)
                                            <img src="{{asset('img/product/logo/'.$products[$i]->photo_url2)}}"
                                                 class="img-responsive" width="100px;"/>
                                        @elseif ($products[$i]->photo_url3)
                                            <img src="{{asset('img/product/logo/'.$products[$i]->photo_url3)}}"
                                                 class="img-responsive" width="100px;"/>
                                        @elseif ($products[$i]->photo_url4)
                                            <img src="{{asset('img/product/logo/'.$products[$i]->photo_url4)}}"
                                                 class="img-responsive" width="100px;"/>
                                        @else
                                            <img src="<?=asset('img/piao/niunai.png') ?>" class="img-responsive"
                                                 width="100px;"/>
                                        @endif
                                    </td>
                                    <td class="pname">{{$products[$i]->name}}</td>
                                    <td>{{$products[$i]->simple_name}}</td>
                                    <td class="series">{{$products[$i]->series_no}}</td>
                                    <td>{{$products[$i]->bottle_type_name}}</td>
                                    <td class="category"
                                        data-value="{{$products[$i]->category}}">{{$products[$i]->category_name}}</td>
                                    <td>
                                        @if($products[$i]->status)
                                            <button type="button" class="btn btn-success btn-sm"
                                                    data-action="disable_product"
                                                    data-target="{{$products[$i]->id}}">下架
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-sm" data-action="disable_product"
                                                    data-target="{{$products[$i]->id}}">上架
                                            </button>
                                        @endif

                                        <button type="button" class="btn btn-success btn-sm" disabled
                                                data-action="delete_product" data-target="{{$products[$i]->id}}">删除
                                        </button>
                                    </td>
                                </tr>
                            @endfor
                        @endif
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="9">
                                <ul class="pagination pull-right"></ul>
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div id="category_modal" class="modal fade" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="row">
                                <div class="feed-element">
                                    <label class="col-lg-3" style="padding-top:5px;">分类名称</label>
                                    <div class="col-lg-7">
                                        <input type="text" id="cname_added" style="height:35px; width:100%;">
                                    </div>
                                    <div class="col-lg-2">
                                        <button type="button" class="btn btn-success add_cbt">添加</button>
                                    </div>
                                </div>
                                <div class="ibox-content">
                                    <table class="footable table table-bordered" id="category_table" data-page-size="5"  data-limit-navigation="5" >
                                        <thead>
                                        <tr class="row-hover-light-blue">
                                            <th class="col-sm-3" data-sort-ignore="true">序号</th>
                                            <th class="col-sm-6" data-sort-ignore="true">分类名称</th>
                                            <th class="col-sm-3" data-sort-ignore="true">编辑</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @if(isset($categories))
                                            @for($i=0; $i< count($categories); $i++)
                                                <tr class="ctr">
                                                    <td>{{$i+1}}</td>
                                                    <td data-id="{{$categories[$i]->id}}" class="cname"
                                                        data-changed="0">{{$categories[$i]->name}}</td>
                                                    <td>
                                                        <button class="btn btn-success btn-sm edit_cbt">修改</button>
                                                        <button class="btn btn-success btn-sm save_cbt"
                                                                style="display: none;">保存
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endfor
                                        @endif
                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <td colspan="3">
                                                <ul class="pagination pull-right"></ul>
                                            </td>
                                        </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <p>请按修改后保存按钮...</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-white" data-action="update_category">确定</button>
                            <button type="button" class="btn btn-white" data-dismiss="modal">取消</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')

    <script type="text/javascript">

        $('#product_table tbody tr').on('click', function(e){
            e.preventDefault();
            e.stopPropagation();
            var product_id =$(this).data('target');
            var url = SITE_URL+'gongchang/jichuxinxi/shangpin/shangpinxiangqing/'+product_id;
            window.location = url;
        });

        function same_check_category_name(name) {

            var same_check = false;
            $('td.cname').each(function () {

                //compare with not changed td
                if (($(this).data("changed") == 0) && ($(this).html() == name)) {
                    same_check = true;
                    return false;
                } else if (($(this).data("changed") == 1) && (($(this).html() == name) || ($(this).find('input').val() == name) )) {
                    same_check = true;
                    return false;
                }
            });

            if (same_check) {
                show_info_msg("同一个分类名称存在");
                return true;
            }

            //check in db because of inactive category
//            var senddata = {'category_name_to_add': name};
//            $.ajax({
//                type: 'GET',
//                url: API_URL + 'gongchang/jichuxinxi/shangpin/check_same_category',
//                data: senddata,
//                success: function (data) {
//                    if (data.status == 'success') {
//                        return false;
//                    } else {
//                        alert('同一分类名称存在');
//                        return true;
//                    }
//                },
//                error: function (data) {
//                    alert('同时检查同一类别，发生错误. 请稍后再试');
//                    return true;
//                }
//            });
        }

        //Print Table Data
        $('button[data-action = "print"]').click(function () {
            var printContents = document.getElementById("product_table").outerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            location.reload();
        });

        $('.add_cbt').click(function () {

            var cname_added = $('#cname_added').val();

            //check empty
            if (cname_added == "")
                return;

            if (same_check_category_name(cname_added))
                return;
            //save added category
            var senddata = {'category_name_to_add': cname_added};
            $.ajax({
                type: 'POST',
                url: API_URL + 'gongchang/jichuxinxi/shangpin/add_category',
                data: senddata,
                success: function (data) {
                    console.log("category_add_result: " + data);
                    if (data.status == "success") {
                        var category_id = data.added_category_id;
                        var count = $('tr.ctr').length + 1;

                        var add_data = '<tr class="ctr"><td>' + count + '</td><td data-id="' + category_id + '" class="cname" origin_value="'+cname_added+'" data-changed="0">' + cname_added + '</td>\
										<td><button class="btn btn-success btn-sm edit_cbt">修改</button>\
											<button class="btn btn-success btn-sm save_cbt" style="display: none;">保存</button>\
										</td>\
									 </tr>';
                        var footable = $('#category_table').data('footable');
                        footable.appendRow(add_data);
                        show_success_msg('成功添加新分类');
                    } else {
                        show_warning_msg(data.message);
                    }
                },
                error: function (data) {
                    console.log(data);
                    show_err_msg("同时增加新的分类，错误发生. 请稍后再试.");
                }
            });
        });

        $(document).on('click', '.edit_cbt', function () {
            $(this).hide();
            var tr = $(this).closest('tr.ctr');
            tr.find('.save_cbt').show();
            var td = tr.find('td.cname');
            var name_to_edit = td.html();
            td.attr('origin_value', name_to_edit);
            var input_data = '<input type="text" class="edit_cinput" value="' + name_to_edit + '"/>';
            td.html(input_data);
        });

        $(document).on('click', '.save_cbt', function () {

            var tr = $(this).closest('tr.ctr');
            var td = tr.find('td.cname');
            var updated_name = td.find("input").val();

            //check whether same category name exists
            if (same_check_category_name(updated_name))
                return;

            td.html(updated_name);
            $(this).hide();
            tr.find('.edit_cbt').show();


            if (updated_name != td.attr('origin_value')) {
                td.attr("data-changed", '1');
            } else {
                td.attr("data-changed", '0');
            }
        });

        $('button[data-action="update_category"]').click(function () {
            //find changed td and get origin value/changed value/category_id
            var changed_category = [];
            $('td[data-changed="1"]').each(function () {
                changed_category.push({
                    "ccid": $(this).attr('data-id'),
                    "origin_value": $(this).attr('origin_value'),
                    "new_value": $(this).html()
                });
            });
            if(changed_category.length == 0)
            {
                $('#category_modal').modal("hide");
                return;
            }


            //update category
            $.ajax({
                type: 'POST',
                url: API_URL + 'gongchang/jichuxinxi/shangpin/update_category',
                data: {
                    'changed_category':changed_category,
                },
                success: function (data) {
                    console.log(data);
                    if (data.status == "success") {
                        console.log('success');
                    } else {
                        show_warning_msg(data.message);
                    }
                },
                error: function (data) {
                    console.log(data);
                }

            });

            $('#category_modal').modal("hide");
        });

        $('button[data-action = "delete_product"]').click(function (e) {
            e.preventDefault();
            e.stopPropagation();

            var button = $(this);

            $.confirm({
                icon: 'fa fa-warning',
                title: '删除地址',
                text: '你会真的删除地址吗？',
                confirmButton: "是",
                cancelButton: "不",
                confirmButtonClass: "btn-success",
                confirm: function () {
                    delete_product(button);
                },
                cancel: function () {
                    return;
                }
            });
        });

        function delete_product(button) {

            var product_id = $(button).data('target');
            var senddata = {'product_id': product_id};
            $.ajax({
                type: 'POST',
                url: API_URL + 'gongchang/jichuxinxi/shangpin/delete_product',
                data: senddata,
                success: function (data) {
                    console.log(data);
                    if (data.status == "success") {
                        //remove current row
                        $('tr[data-target="' + product_id + '"]').remove();
                        //$(this).closest('tr').remove();
                    } else {
                        show_warning_msg(data.message);
                    }
                },
                error: function (data) {
                    console.log(data);
                }

            });
        };

        $('button[data-action="disable_product"]').click(function (e) {
            e.preventDefault();
            e.stopPropagation();

            var product_id = $(this).data('target');
            var bt = $(this);
            var senddata;
            var flag = false;
            if ($(this).hasClass('btn-success'))
                flag = true;

            if (flag)
                senddata = {'product_id': product_id, 'action': 'disable'};
            else
                senddata = {'product_id': product_id, 'action': 'enable'};

            $.ajax({
                type: 'POST',
                url: API_URL + 'gongchang/jichuxinxi/shangpin/disable_product',
                data: senddata,
                success: function (data) {
                    console.log(data);
                    if (data.status == "success") {
                        if (flag) {
                            bt.html('上架');
                            bt.removeClass('btn-success');
                        } else {
                            bt.html('下架');
                            bt.addClass('btn-success');
                        }

                    } else {
                        show_warning_msg(data.message);
                    }
                },
                error: function (data) {
                    console.log(data);
                }

            });
        });

        //Filter Function
        $('button[data-action="show_selected"]').click(function () {
            //get all selection
            var spn = $('#sel_product_name').val().trim().toLowerCase();
            var ssn = $('#sel_product_series_no').val().trim().toLowerCase();
            var scn = $('#sel_product_category').val().trim();

            //show only rows that contains the above field value

            $('#product_table tbody tr').each(function () {
                var tr = $(this);
                pn = tr.find('td.pname').html().toString().toLowerCase();
                sn = tr.find('td.series').html().toString().toLowerCase();
                cn = tr.find('td.category').attr("data-value");

                if ((spn != "" && pn.includes(spn)) || (spn == "")) {
                    tr.attr("data-show-1", "1");
                } else {
                    tr.attr("data-show-1", "0")
                }

                if ((ssn != "" && sn.includes(ssn)) || (ssn == "")) {
                    tr.attr("data-show-2", "1");
                } else {
                    tr.attr("data-show-2", "0")
                }

                if ((scn != "none" && cn == scn) || (scn == "none")) {
                    tr.attr("data-show-3", "1");
                } else {
                    tr.attr("data-show-3", "0")
                }

                if ((tr.attr("data-show-1") == "1" ) && (tr.attr("data-show-2") == "1") && (tr.attr("data-show-3") == "1")) {
                    tr.removeClass('hide');
                } else {
                    tr.addClass('hide');
                }
            });

        });

        $('button[data-action="add_category_modal"]').click(function(){
            $('#cname_added').val('');
            $('#category_modal').modal('show');
        });

    </script>
@endsection