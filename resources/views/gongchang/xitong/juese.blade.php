@extends('gongchang.layout.master')
@section('css')
    <link href="<?=asset('css/plugins/added/build.css') ?>" rel="stylesheet">

    <style type="text/css">
        /*.treetable-expanded > td:first-child,*/
        /*.treetable-collapsed > td:first-child {*/
            /*padding-left: 2em;*/
        /*}*/

        /*.treetable-expanded > td:first-child > .treetable-expander,*/
        /*.treetable-collapsed > td:first-child > .treetable-expander {*/
            /*top: 0.05em;*/
            /*position: relative;*/
            /*margin-left: -1.5em;*/
            /*margin-right: 0.25em;*/
        /*}*/

        /*.treetable-expanded .treetable-expander,*/
        /*.treetable-expanded .treetable-expander {*/
            /*width: 1em;*/
            /*height: 1em;*/
            /*cursor: pointer;*/
            /*position: relative;*/
            /*display: inline-block;*/
        /*}*/

        /*.treetable-depth-1 > td:first-child {*/
            /*padding-left: 3em;*/
        /*}*/

        /*.treetable-depth-2 > td:first-child {*/
            /*padding-left: 4.5em;*/
        /*}*/

        /*.treetable-depth-3 > td:first-child {*/
            /*padding-left: 6em;*/
        /*}*/

        table, th, td {
            text-align: left;
        }

        tr {
            cursor: default;
        }
        .highlight {
            background: #737373;
            color: #ffffff;
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
                    <a href="">系统管理</a>
                </li>
                <li>
                    <a href={{URL::to('/gongchang/xitong/yonghu')}}><strong>用户管理</strong></a>
                </li>
                <li>
                    <a href=""><strong>角色管理</strong></a>
                </li>
            </ol>
        </div>

        <div class="row">
            <div class="wrapper-content">

                <!--Role_name table-->
            <div class="ibox col-md-offset-1 col-md-3" style="padding-top: 30px;">
                <a data-toggle="modal" class="btn btn-success dim" href="#myModal" type="button"><i
                            class="fa fa-plus"></i> 添加角色</a>

                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>角色列表</h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <input type="hidden" id="user_role_id" value="{{$role_id}}">
                        <input type="hidden" id="#backend_type" value="2">
                        <table class="table table-bordered" id="roles-list">
                            <tbody>
                            @foreach($role_name as $rn)
                            <tr id="role{{$rn->id}}" class="clickable-row gradeX @if ($role_id == $rn->id) active @endif" idnumber="{{$rn->id}}" style="height: 50px;">
                                <td>{{$rn->name}}</td>
                                @if($rn->id == 1)
                                    <td class="center">不可删</td>
                                @else
                                    <td class="center" ><button class="btn btn-md btn-success delete-role" id="delete_role{{$rn->id}}" value="{{$rn->id}}">删除</button></td>
                                @endif
                            </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            </tfoot>
                        </table>
                        <div class="col-sm-9"><span id="alertMessage" class="alertMessage"></span></div>
                    </div>
                </div>
            </div>

            <!--Add Rolename Modalbox-->
            <div id="myModal" class="modal fade" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form id="frmroles" name="frmroles">
                        <div class="modal-body">
                            <div class="row">
                                &nbsp;
                                <div class="col-lg-12">
                                    <label class="col-lg-3">角色名称 :</label>

                                    <div class="col-lg-9">
                                        <input type="text" style="width:100%;" id="role" required name="role_name">
                                        <input type="hidden" style="width:100%;" id="type" required name="backend_type" value="2">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-white" id="btn-save" value="add" data-dismiss="modal">确定</button>
                            <button type="button" class="btn btn-white" data-dismiss="modal">取消</button>
                            <input type="hidden" id="role_id" name="role_id" value="0">
                        </div>
                        </form>
                    </div>
                </div>
            </div>
            <!--Permission table-->
            <div class="col-lg-5" style="padding-top: 65px;">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>权限设定</h5>

                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                        </div>
                    </div>
                    <form action="{{ url('api/gongchang/xitong/juese/store/')}}" role="form" method="post">
                        {{ csrf_field() }}
                    <div class="ibox-content">
                        <table class="table tree table-inverse" id="permissionTable">
                            <tbody>
                            {{--<tr data-node="treetable-650__1" data-pnode="">--}}
                                {{--<td width="30%" class="average-score sm-text color-gray"><input type="checkbox"--}}
                                                                                                {{--class="i-checks"> 全选--}}
                                {{--</td>--}}
                                {{--<td class="average-score sm-text color-gray"><input type="checkbox" class="i-checks"> 查看--}}
                                {{--</td>--}}
                                {{--<td class="average-score sm-text color-gray"><input type="checkbox" class="i-checks"> 修改--}}
                                {{--</td>--}}
                                {{--<td class="average-score sm-text color-gray"><input type="checkbox" class="i-checks"> 新增--}}
                                {{--</td>--}}
                                {{--<td class="average-score sm-text color-gray"><input type="checkbox" class="i-checks"> 删除--}}
                                {{--</td>--}}
                                {{--<td class="average-score sm-text color-gray"></td>--}}
                                {{--<td class="average-score sm-text color-gray"><input type="checkbox" class="i-checks"> 全选--}}
                                {{--</td>--}}
                            {{--</tr>--}}
                            <input type="hidden" name="roleId" value="{{$role_id}}">
                            <?php
                            $i = 1;
                            ?>
                            @foreach($pages as $p)
                                <?php
                                $i++;
                                $j = 0;
                                ?>
                                <tr class="gray-bg">
                                    <td colspan="7" class="competency sm-text">
                                        <div class="checkbox checkbox-primary">
                                            <input type="checkbox" class=""  id="parenticheck{{$i}}" name="input{{$p->id}}" @foreach($access_pages as $ap) @if($ap->page_id == $p->id) Checked @endif @endforeach>
                                            <label for="parenticheck{{$i}}">{{$p->name}}</label>
                                        </div>
                                    </td>

                                </tr>
                                @foreach($p->sub_pages as $s)
                                    <?php
                                    $j++;
                                    ?>
                                    <tr>
                                        <td width="30%" class="average-score sm-text color-gray">
                                            <div class="checkbox checkbox-primary">
                                            &emsp;<input type="checkbox" class="childicheck{{$i}}" id="child{{$s->id}}"
                                                                                                        name="input{{$s->id}}"@foreach($access_pages as $ap) @if($ap->page_id == $s->id) Checked @endif @endforeach>
                                            <label for="child{{$s->id}}">{{$s->name}}</label>
                                            </div>
                                        {{--</td>--}}
                                        {{--<td class="average-score sm-text color-gray"><input type="checkbox"--}}
                                                                                            {{--class="i-checks"> 查看--}}
                                        {{--</td>--}}
                                        {{--<td class="average-score sm-text color-gray"><input type="checkbox"--}}
                                                                                            {{--class="i-checks"> 修改--}}
                                        {{--</td>--}}
                                        {{--<td class="average-score sm-text color-gray"><input type="checkbox"--}}
                                                                                            {{--class="i-checks"> 新增--}}
                                        {{--</td>--}}
                                        {{--<td class="average-score sm-text color-gray"><input type="checkbox"--}}
                                                                                            {{--class="i-checks"> 删除--}}
                                        {{--</td>--}}
                                        {{--<td class="average-score sm-text color-gray"></td>--}}
                                        {{--<td class="average-score sm-text color-gray"><input type="checkbox"--}}
                                                                                            {{--class="i-checks"> 全选--}}
                                        {{--</td>--}}
                                    </tr>
                                @endforeach
                            @endforeach
                            </tbody>
                        </table>
                        <div class="col-md-offset-5 col-md-3">
                            <button type="submit"  id="save_change" class="btn btn-success btn-md" @if($role_id == 1) style="display: none" @endif>保存</button>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
        </div>
    </div>
@endsection

@section('script')
    <meta name="_token" content="{!! csrf_token() !!}" />

    <!--Apply Ajax for Role_name table-->
    <script type="text/javascript" src="<?=asset('js/ajax/jueseajax.js') ?>"></script>
    <!--Apply Ajax for Permission table-->
    <script type="text/javascript" src="<?=asset('js/ajax/juesepermission.js') ?>"></script>
    <script>
        $(document).ready(function () {
            var role_id = $('#user_role_id').val();
            $('#role'+role_id+'').addClass('active').siblings().removeClass('active');
            $(document).bind('selectstart dragstart', function (e) {
                e.preventDefault;
                return false;
            })
        });

//        $(function () {
//            var checkAll = $('input.all');
//            var checkboxes = $('input.check');
//
//            $('input').iCheck();
//
//            checkAll.on('ifChecked ifUnchecked', function(event) {
//                if (event.type == 'ifChecked') {
//                    checkboxes.iCheck('check');
//                } else {
//                    checkboxes.iCheck('uncheck');
//                }
//            });
//
//            checkboxes.on('ifChanged', function(event){
//                if(checkboxes.filter(':checked').length == checkboxes.length) {
//                    checkAll.prop('checked', 'checked');
//                } else {
//                    checkAll.removeProp('checked');
//                }
//                checkAll.iCheck('update');
//            });
//        });

        $('#parenticheck1').change(function () {
            if($(this).is(':checked')){
                $('.childicheck1').prop('checked',true);
            }
            else {
                $('.childicheck1').prop('checked',false);
            }
        })
        $('.childicheck1').change(function () {
            if($(this).is(':checked')){
                $('#parenticheck1').prop('checked',true);
            }
            else {
                var i = 0;
                $(this).parent().parent().parent().parent().find('.childicheck1').each(function () {
                    if($(this).is(':checked')){
                        i++;
                    }
                })
                if(i==0){
                    $('#parenticheck1').prop('checked',false);
                }
            }
        })

        $('#parenticheck2').change(function () {
            if($(this).is(':checked')){
                $('.childicheck2').prop('checked',true);
            }
            else {
                $('.childicheck2').prop('checked',false);
            }
        })
        $('.childicheck2').change(function () {
            if($(this).is(':checked')){
                $('#parenticheck2').prop('checked',true);
            }
            else {
                var i = 0;
                $(this).parent().parent().parent().parent().find('.childicheck2').each(function () {
                    if($(this).is(':checked')){
                        i++;
                    }
                })
                if(i==0){
                    $('#parenticheck2').prop('checked',false);
                }
            }
        })
        $('#parenticheck3').change(function () {
            if($(this).is(':checked')){
                $('.childicheck3').prop('checked',true);
            }
            else {
                $('.childicheck3').prop('checked',false);
            }
        })
        $('.childicheck3').change(function () {
            if($(this).is(':checked')){
                $('#parenticheck3').prop('checked',true);
            }
            else {
                var i = 0;
                $(this).parent().parent().parent().parent().find('.childicheck3').each(function () {
                    if($(this).is(':checked')){
                        i++;
                    }
                })
                if(i==0){
                    $('#parenticheck3').prop('checked',false);
                }
            }
        })

        $('#parenticheck4').change(function () {
            if($(this).is(':checked')){
                $('.childicheck4').prop('checked',true);
            }
            else {
                $('.childicheck4').prop('checked',false);
            }
        })
        $('.childicheck4').change(function () {
            if($(this).is(':checked')){
                $('#parenticheck4').prop('checked',true);
            }
            else {
                var i = 0;
                $(this).parent().parent().parent().parent().find('.childicheck4').each(function () {
                    if($(this).is(':checked')){
                        i++;
                    }
                })
                if(i==0){
                    $('#parenticheck4').prop('checked',false);
                }
            }
        })

        $('#parenticheck5').change(function () {
            if($(this).is(':checked')){
                $('.childicheck5').prop('checked',true);
            }
            else {
                $('.childicheck5').prop('checked',false);
            }
        })
        $('.childicheck5').change(function () {
            if($(this).is(':checked')){
                $('#parenticheck5').prop('checked',true);
            }
            else {
                var i = 0;
                $(this).parent().parent().parent().parent().find('.childicheck5').each(function () {
                    if($(this).is(':checked')){
                        i++;
                    }
                })
                if(i==0){
                    $('#parenticheck5').prop('checked',false);
                }
            }
        })
        $('#parenticheck6').change(function () {
            if($(this).is(':checked')){
                $('.childicheck6').prop('checked',true);
            }
            else {
                $('.childicheck6').prop('checked',false);
            }
        })
        $('.childicheck6').change(function () {
            if($(this).is(':checked')){
                $('#parenticheck6').prop('checked',true);
            }
            else {
                var i = 0;
                $(this).parent().parent().parent().parent().find('.childicheck6').each(function () {
                    if($(this).is(':checked')){
                        i++;
                    }
                })
                if(i==0){
                    $('#parenticheck6').prop('checked',false);
                }
            }
        })
        $('#parenticheck7').change(function () {
            if($(this).is(':checked')){
                $('.childicheck7').prop('checked',true);
            }
            else {
                $('.childicheck7').prop('checked',false);
            }
        })
        $('.childicheck7').change(function () {
            if($(this).is(':checked')){
                $('#parenticheck7').prop('checked',true);
            }
            else {
                var i = 0;
                $(this).parent().parent().parent().parent().find('.childicheck7').each(function () {
                    if($(this).is(':checked')){
                        i++;
                    }
                })
                if(i==0){
                    $('#parenticheck7').prop('checked',false);
                }
            }
        })

        $('#parenticheck8').change(function () {
            if($(this).is(':checked')){
                $('.childicheck8').prop('checked',true);
            }
            else {
                $('.childicheck8').prop('checked',false);
            }
        })
        $('.childicheck8').change(function () {
            if($(this).is(':checked')){
                $('#parenticheck8').prop('checked',true);
            }
            else {
                var i = 0;
                $(this).parent().parent().parent().parent().find('.childicheck8').each(function () {
                    if($(this).is(':checked')){
                        i++;
                    }
                })
                if(i==0){
                    $('#parenticheck8').prop('checked',false);
                }
            }
        })
        $('#parenticheck9').change(function () {
            if($(this).is(':checked')){
                $('.childicheck9').prop('checked',true);
            }
            else {
                $('.childicheck9').prop('checked',false);
            }
        })
        $('.childicheck9').change(function () {
            if($(this).is(':checked')){
                $('#parenticheck9').prop('checked',true);
            }
            else {
                var i = 0;
                $(this).parent().parent().parent().parent().find('.childicheck9').each(function () {
                    if($(this).is(':checked')){
                        i++;
                    }
                })
                if(i==0){
                    $('#parenticheck9').prop('checked',false);
                }
            }
        })
        $('#parenticheck10').change(function () {
            if($(this).is(':checked')){
                $('.childicheck10').prop('checked',true);
            }
            else {
                $('.childicheck10').prop('checked',false);
            }
        })
        $('.childicheck10').change(function () {
            if($(this).is(':checked')){
                $('#parenticheck10').prop('checked',true);
            }
            else {
                var i = 0;
                $(this).parent().parent().parent().parent().find('.childicheck10').each(function () {
                    if($(this).is(':checked')){
                        i++;
                    }
                })
                if(i==0){
                    $('#parenticheck10').prop('checked',false);
                }
            }
        })
        $('#parenticheck11').change(function () {
            if($(this).is(':checked')){
                $('.childicheck11').prop('checked',true);
            }
            else {
                $('.childicheck11').prop('checked',false);
            }
        })
        $('.childicheck11').change(function () {
            if($(this).is(':checked')){
                $('#parenticheck11').prop('checked',true);
            }
            else {
                var i = 0;
                $(this).parent().parent().parent().parent().find('.childicheck11').each(function () {
                    if($(this).is(':checked')){
                        i++;
                    }
                })
                if(i==0){
                    $('#parenticheck11').prop('checked',false);
                }
            }
        })
        $('#parenticheck12').change(function () {
            if($(this).is(':checked')){
                $('.childicheck12').prop('checked',true);
            }
            else {
                $('.childicheck12').prop('checked',false);
            }
        })
        $('.childicheck12').change(function () {
            if($(this).is(':checked')){
                $('#parenticheck12').prop('checked',true);
            }
            else {
                var i = 0;
                $(this).parent().parent().parent().parent().find('.childicheck12').each(function () {
                    if($(this).is(':checked')){
                        i++;
                    }
                })
                if(i==0){
                    $('#parenticheck12').prop('checked',false);
                }
            }
        })
    </script>
@endsection