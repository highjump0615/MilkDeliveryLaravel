@extends('naizhan.layout.master')
@section('css')
    <link href="<?=asset('css/plugins/chosen/chosen.css')?>" rel="stylesheet">
    <link href="<?=asset('css/plugins/select2/select2.min.css')?>" rel="stylesheet">
    <link href="<?=asset('css/plugins/iCheck/custom.css')?>" rel="stylesheet">
@endsection
@section('content')
    @include('naizhan.theme.sidebar')
    <div id="page-wrapper" class="gray-bg dashbard-1">
        @include('naizhan.theme.header')
        <div class="row border-bottom">
            <ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
                <li class="active">
                    <a>奶站管理</a>
                </li>
                <li class="active">
                    <strong>配送员管理</strong>
                </li>
            </ol>
        </div>

        <div class="row wrapper">
            <div class="wrapper-content">
                <div class="ibox-content" style="border:none;">
                    <div class="feed-element" style="border-bottom: 1px #e7eaec solid ; padding-bottom: 5px;">
                        <div class="col-md-3">
                            <label class="col-md-6 text-right" style="padding-top: 5px;">配送员名称:</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" id="filter_milkman" style="width:100%;">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="col-md-6 text-right" style="padding-top: 5px;">身份证号:</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" id="filter_number" style="width:100%;">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="col-md-4 text-right" style="padding-top: 5px;">区域:</label>
                            <div class="col-md-8">
                                <div class="input-group">
                                    <select id="filter_area" data-placeholder="" class="form-control"
                                            class="chosen-select" tabindex="2" style="width:100%;">
                                        <option value="">全部</option>
                                        @foreach($street as $s)
                                            <option value="{{$s}}">{{$s}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2" style="padding-top:5px;">
                            <button type="button" class="btn btn-success btn-m-d" data-action="show_selected">筛选
                            </button>
                            &nbsp;
                            <button class="btn btn-success btn-m-d btn-outline" data-action="print">打印</button>
                            <button class="btn btn-success btn-m-d btn-outline" data-action="export_csv">导出</button>
                        </div>
                    </div>
                </div>

                <form id="add_milkman" method="POST" enctype="multipart/form-data">
                    <div class="ibox">
                        <div class="feed-element">
                            <div class="col-md-5">
                                <label class="col-md-3" style="padding-top: 5px;">姓名:</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" id="milkman" style="width:100%;"
                                           onkeypress="checkname();">
                                </div>
                            </div>
                            <label id="name_alert" style="color: red; padding-top: 5px; display: none">(请输入姓名!)</label>
                        </div>
                        <div class="feed-element">
                            <div class="col-md-5">
                                <label class="col-md-3" style="padding-top: 5px;">电话:</label>
                                <div class="col-md-9">
                                    <input type="text" pattern="^1[345678][0-9]{9}$" id="phone" name="phone" class="form-control"
                                           oninvalid="this.setCustomValidity('手机号码格式不正确')"
                                           oninput="this.setCustomValidity('')"
                                           style="width:100%;">
                                </div>
                            </div>
                            <label id="phone_alert" style="color: red; padding-top: 5px; display: none">(请输入电话!)</label>
                        </div>
                        <div class="feed-element">
                            <div class="col-md-5">
                                <label class="col-md-3" style="padding-top: 5px;">身份证号:</label>
                                <div class="col-md-9">
                                    <input type="text" pattern="^(\d){15}|(\d{17}(\d|x|X))$"
                                           class="form-control" id="number" style="width:100%;"
                                           oninput="this.setCustomValidity('')"
                                           oninvalid="this.setCustomValidity('身份证号不符合')"/>
                                </div>
                            </div>
                            <label id="number_alert"
                                   style="color: red; padding-top: 5px; display: none">(请输入身份证号!)</label>
                        </div>
                        <div>
                            <div class="col-md-5">
                                <label class="col-md-3" style="padding-top: 5px;">配送街道:</label>
                                <div class="col-md-9">
                                    <select data-placeholder=" " id="area" class="chosen-select form-control" multiple
                                            style="height:35px;" onchange="hide_street_alert();">
                                        @foreach($street as $s)
                                            <option value="{{$s}}">{{$s}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <label class="col-md-offset-2 col-md-3" id="street_alert"
                                   style="color: red; padding-top: 5px; display: none">(请输入街道!)</label>
                        </div>
                    </div>

                    <div><br></div>
                    <div class="wrapper-content">
                        <div class="col-md-2">
                            <label class="control-label">&emsp;小区添加</label>
                        </div>
                        <div class="col-md-8">
                            <div class="wrapper-content">
                                <table id="xiaoqi_table" class="table footable table-bordered white-bg">
                                    <tbody>
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <ul class="pagination pull-right"></ul>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-2">
                        </div>
                    </div>
                    <label class="col-md-offset-2 col-md-10" id="xiaoqu_alert"
                           style="color: red; padding-top: 5px; display: none">(请输入身小区!)</label>
                    <div class="wrapper">
                        <div class="col-md-5"></div>
                        <div class="col-md-2">
                            <button class="btn btn-danger" id="save" type="submit"
                                    style="width:100%; padding-bottom: 10px;"><i class="fa fa-plus"></i> 保存并添加
                            </button>
                        </div>
                        <div class="col-md-5"></div>
                    </div>
                </form>

                <div class="ibox float-e-margins" style="padding-top: 20px;">
                    <div class="ibox-content" id="notification" style="padding-left: 20px; display: none;">
                        <p style="color: #ff0000">无法删除配送员!</p>
                    </div>
                    <div class="ibox-content">
                        <table id="peisongyuan" class="table footable table-bordered" data-page-size="10"
                               data-limit-navigation="5">
                            <thead>
                            <tr>
                                <th data-sort-ignore="true">顺序</th>
                                <th data-sort-ignore="true">姓名</th>
                                {{--<th data-sort-ignore="true">编号</th>--}}
                                <th data-sort-ignore="true">电话</th>
                                <th data-sort-ignore="true">身份证号</th>
                                <th data-sort-ignore="true">配送范围</th>
                                <th data-sort-ignore="true">小区</th>
                                <th data-sort-ignore="true">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $i = 0; ?>
                            @foreach($milkmans as $mm)
                                <?php $i++; ?>
                                <tr id="peisongyuan{{$mm->id}}">
                                    <td>{{$i}}</td>
                                    <td class="name">{{$mm->name}}</td>
                                    {{--<td>Y036521</td>--}}
                                    <td>{{$mm->phone}}</td>
                                    <td class="number">{{$mm->number}}</td>
                                    <td class="area"><a
                                                href="{{ url('/naizhan/naizhan/fanwei-chakan/'.$mm->id) }}">{{$mm->street}}</a>
                                    </td>
                                    <td>
                                        <a href="{{ url('/naizhan/naizhan/fanwei-chakan/'.$mm->id) }}">{{$mm->xiaoqi}}</a>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-success modify" value="{{$mm->id}}">修改</button>
                                        &nbsp;
                                        <button class="btn btn-sm btn-success delete" value="{{$mm->id}}">删除</button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="8">
                                    <ul class="pagination pull-right"></ul>
                                </td>
                            </tr>
                            </tfoot>
                        </table>

                        <table id="filtered_table" class="table footable table-bordered" style="display: none"
                               data-page-size="10" data-limit-navigation="5">
                            <thead>
                            <tr>
                                <th data-sort-ignore="true">顺序</th>
                                <th data-sort-ignore="true">姓名</th>
                                {{--<th data-sort-ignore="true">编号</th>--}}
                                <th data-sort-ignore="true">电话</th>
                                <th data-sort-ignore="true">身份证号</th>
                                <th data-sort-ignore="true">配送范围</th>
                                <th data-sort-ignore="true">小区</th>
                                <th data-sort-ignore="true">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="8">
                                    <ul class="pagination pull-right"></ul>
                                </td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
@section('script')
    <script src="<?=asset('js/plugins/chosen/chosen.jquery.js') ?>"></script>
    <script src="<?=asset('js/plugins/select2/select2.full.min.js')?>"></script>
    <script src="<?=asset('js/plugins/iCheck/icheck.min.js')?>"></script>
    <script src="<?=asset('js/global.js')?>"></script>
    <script src="<?=asset('js/pages/naizhan/peisongyuan_register.js')?>"></script>
    <script type="text/javascript">
        $(document).ready(function () {

//			var config = {
//				'.chosen-select'           : {},
//				'.chosen-select-deselect'  : {allow_single_deselect:true},
//				'.chosen-select-no-single' : {disable_search_threshold:10},
//				'.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
//				'.chosen-select-width'     : {width:"95%"}
//			}
//			for (var selector in config) {
//				$(selector).chosen(config[selector]);
//			}

            $(".chosen-select").chosen();
//			$('#area').multiSelect( {keepOrder: true });
        });
    </script>
@endsection