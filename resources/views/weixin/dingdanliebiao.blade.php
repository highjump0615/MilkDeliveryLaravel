@extends('weixin.layout.master')
@section('title','订单列表')
@section('css')
    <link href="<?=asset('css/plugins/added/switchery.css') ?>" rel="stylesheet">
@endsection
@section('content')

    <!-- 保存当前时间 -->
    <?php
    $s_timeCurrent = (new DateTime("now", new DateTimeZone('Asia/Shanghai')))->format('Y-m-d H:i:s');
    ?>

    <script language="JavaScript">
        var s_timeCurrent = "{{$s_timeCurrent}}";
    </script>

    <header>
        <a class="headl fanh" href="javascript:history.back();"></a>
        <h1>订单列表</h1>
    </header>

    @if(count($orders) == 0)
        <p style="font-size: 15px; margin: 10px auto; width: 100%; text-align: center;">"没有订单"</p>
    @else
        @forelse($orders as $o)
            <div class="ordsl">
                <div class="ordnum">
                    <span>订单日期: {{$o->ordered_at}}</span>
                    <label>订单号：{{$o->number}}</label>
                    <br>
                    <label>状态: {{$o->status_name}}</label>
                </div>
                @forelse($o->order_products as $op)
                    <a href="{{url('/weixin/dingdanxiangqing?order='.$o->id)}}">
                        <div class="ordtop clearfix">
                            <img class="ordpro" src="<?=asset('img/product/logo/' . $op->product->photo_url1)?>">
                            <div class="ord-r">
                                {{$op->product_name}}
                                <br>
                                单价：{{$op->product_price}}
                                <br>
                                订单数量：{{$op->total_count}}

                            </div>
                            <div class="ordye">
                                @if($o->status == \App\Model\OrderModel\Order::ORDER_PASSED_STATUS ||
                                    $o->status == \App\Model\OrderModel\Order::ORDER_ON_DELIVERY_STATUS)
                                    开始日期: {{$op->start_at}}&emsp;
                                @endif
                                金额：{{$op->total_amount}}元
                            </div>
                        </div>
                    </a>
                @empty
                    <div>没有订单项目</div>
                @endforelse

                <div class="ordshz">
                    @if($o->isStopped())
                        <span class="shsp-r">
                    开启订单
                    <input type="checkbox" class="js-switch restart_order"
                           data-stop-start-at="{{$o->stop_at}}"
                           data-stop-end-at="{{$o->restart_at}}"
                           data-order-id="{{$o->id}}"/>
                    </span>
                    @else
                        @if($o->status == \App\Model\OrderModel\Order::ORDER_ON_DELIVERY_STATUS || $o->status == \App\Model\OrderModel\Order::ORDER_FINISHED_STATUS )
                            <span class="shsp">
                            <a href="{{url('/weixin/show_xuedan?order='.$o->id.'&&type='.$type)}}">续单</a>
                    </span>
                        @endif

                        @if($o->status == \App\Model\OrderModel\Order::ORDER_FINISHED_STATUS)
                            <span class="shsp">
                        <a href="{{url('/weixin/dingdanpingjia?order='.$o->id)}}">评价</a>
                    </span>
                        @endif

                        @if($o->status == \App\Model\OrderModel\Order::ORDER_PASSED_STATUS ||
                            $o->status == \App\Model\OrderModel\Order::ORDER_NOT_PASSED_STATUS ||
                            $o->status == \App\Model\OrderModel\Order::ORDER_ON_DELIVERY_STATUS)
                            <span class="shsp">
                            @if(isset($type) && ($type !="none"))
                                    <a href="{{url('/weixin/dingdanxiugai?order='.$o->id.'&&type='.$type.'&&start=yes')}}">修改</a>
                                @else
                                    <a href="{{url('/weixin/dingdanxiugai?order='.$o->id.'&&start=yes')}}">修改</a>
                                @endif
                    </span>
                        @endif
                        @if($o->status == \App\Model\OrderModel\Order::ORDER_ON_DELIVERY_STATUS)
                            <span class="shsp-r">
                    暂停订单
                    <input type="checkbox" class="js-switch stop_order"
                           data-start-at="{{$o->start_at}}"
                           data-end-at="{{$o->order_end_date}}"
                           data-order-id="{{$o->id}}"/>
                    </span>
                        @endif
                    @endif
                </div>
            </div>

            <div id="stop_order_modal" class="animated modal fade" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-md">
                    <div class="modal-content">
                        <form id="stop_order_modal_form" method="POST" style="padding:0;">
                            <div class="modal-body">
                                <label style="margin-bottom: 30px;">选择暂停的日期</label>
                                <div class="input-group col-md-12">
                                    {{--<input type="text" required class="input-sm form-control"--}}
                                    {{--name="start" id="stop_start"/>--}}
                                    <input class="input-sm form-control" id="stop_start" required name="start"
                                           type="date" value=""/>
                                    <span class="input-group-addon">至</span>
                                    {{--<input type="text" id="stop_end" required class="input-sm form-control"--}}
                                    {{--name="end"/>--}}
                                    <input class="input-sm form-control" id="stop_end" required name="end" type="date"
                                           value=""/>
                                </div>
                                <input type="hidden" id="stop_order_id" name="order_id" value=""/>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-white" id="apply_stop">确定</button>
                                <button type="button" class="btn btn-white" id="cancel_stop">取消</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div id="restart_order_modal" class="animated modal fade" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-sm">
                    <div class="modal-content">
                        <form id="restart_order_modal_form" method="POST" style="padding:0;">
                            <div class="modal-body">
                                <label class="align-center btn-block">暂停的日期</label>
                                <label class="align-center  btn-block" id="stop_period"></label>
                                <hr>
                                <label class="align-center  btn-block" style="margin-top: 30px;">选择开启的日期</label>
                                <div class="input-group">
                                    {{--<input required type="text" class="form-control" id="start_at"--}}
                                    {{--name="start_at"><span--}}
                                    {{--class="input-group-addon"><i class="fa fa-calendar"></i></span>--}}
                                    <input required type="date" class="form-control" id="start_at"
                                           name="start_at"><span
                                            class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                </div>
                                <input type="hidden" name="order_id" id="restart_order_id" value=""/>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-white" id="apply_restart">确定</button>
                                <button type="button" class="btn btn-white" id="cancel_restart">取消</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @empty
        @endforelse
    @endif

    @include('weixin.layout.footer')
@endsection
@section('script')
    <!-- Switchery -->
    <script src="<?=asset('js/plugins/added/switchery.js') ?>"></script>
    <script>
        //Create Switchery
        if (Array.prototype.forEach) {
            var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
            elems.forEach(function (html) {
                var switchery = new Switchery(html);
            });
        } else {
            var elems = document.querySelectorAll('.js-switch');
            for (var i = 0; i < elems.length; i++) {
                var switchery = new Switchery(elems[i]);
            }
        }

        Date.prototype.toISOString = function () {
            return this.getUTCFullYear() + '-' + pad(this.getUTCMonth() + 1) + '-' + pad(this.getUTCDate());
        };


        // 解析当前服务器的时间 (2014-08-12 09:25:24)
        var time = s_timeCurrent.replace(/-/g, ':').replace(' ', ':');
        time = time.split(':');
        dateToday = new Date(time[0], (time[1] - 1), time[2], time[3], time[4], time[5]);

        var firstm = new Date(dateToday.getFullYear(), dateToday.getMonth(), 1);
        var lastm = new Date(dateToday.getFullYear(), dateToday.getMonth() + 1, 0);

        /*
         * Stop Order
         */

        function init_stop_order_modal() {
            $('#stop_order_id').val('');
            $('#stop_start').val('');
            $('#stop_end').val('');
            $('#apply_stop').removeAttr('data-order-id');
            $('#cancel_stop').removeAttr('data-order-id');
        }

        function pad(number) {
            var r = String(number);
            if (r.length === 1) {
                r = '0' + r;
            }
            return r;
        }

        $(document).on('change', '.stop_order', function () {

            var order_id = $(this).data('order-id');
            var checked = $(this).prop('checked');

            var start_at = $(this).data('start_at');
            var end_at = $(this).data('end_at');

            if (checked == true) {
                var order_start_at = start_at;

                var order_start_date = new Date(order_start_at);
                var order_end_date = new Date(end_at);

                var stop_start_able_date = new Date();
                //        stop_start_able_date.setDate(today.getDate()+gap_day);

                if (order_start_date > stop_start_able_date) {
                    stop_start_able_date = order_start_date;
                }

//                //set the stop modal 's start stop date val as orders's start date at least.
//                $('#stop_order_modal').find('.input-daterange #stop_start').datepicker({
//                    keyboardNavigation: false,
//                    forceParse: false,
//                    autoclose: true,
//                    calendarWeeks: false,
//                    clearBtn: true,
//                    minDate: stop_start_able_date,
//                    maxDate: order_end_date,
//                });
//
//                $('#stop_order_modal').find('.input-daterange #stop_end').datepicker({
//                    keyboardNavigation: false,
//                    forceParse: false,
//                    autoclose: true,
//                    calendarWeeks: false,
//                    clearBtn: true,
//                    minDate: stop_start_able_date,
//                });

                Date.prototype.toISOString = function () {
                    return this.getUTCFullYear() + '-' + pad(this.getUTCMonth() + 1) + '-' + pad(this.getUTCDate());
                };

                var min_stop_start = stop_start_able_date.toISOString();
                var max_stop_start = order_end_date.toISOString();
                var min_stop_end = stop_start_able_date.toISOString();

                $('#stop_order_modal').find('#stop_start').attr('min', min_stop_start);
                $('#stop_order_modal').find('#stop_start').attr('max', max_stop_start);

                $('#stop_order_modal').find('#stop_end').attr('min', min_stop_end);

                init_stop_order_modal();

                $('#stop_order_id').val(order_id);
                $('#apply_stop').attr('data-order-id', order_id);
                $('#cancel_stop').attr('data-order-id', order_id);
                $('#stop_order_modal').modal('show');

            } else {
                $('#stop_order_modal').modal('hide');
            }
        });

        $(document).on('click', '#cancel_stop', function () {

            var order_id = $('#apply_stop').data('order-id');

            //init modal box
            init_stop_order_modal();
            $('#stop_order_modal').modal('hide');

            $('.stop_order[data-order-id = "' + order_id + '"]').trigger('click');
        });

        $('#stop_order_modal_form').submit(function (e) {

            e.preventDefault();

            var submit = $(this).find('button[type="submit"]');
            $(submit).prop('disabled', true);

            var sendData = $(this).serializeArray();


            $.ajax({
                type: "POST",
                url: API_URL + "gongchang/dingdan/stop_order_in_gongchang",
                data: sendData,
                success: function (data) {
                    console.log(data);

                    if (data.status == 'success') {
                        var stop_start_date = data.stop_start;
                        var stop_end_date = data.stop_end;

                        show_success_msg("停止订单成功: " + stop_start_date + " ~ " + stop_end_date);
                        location.reload();

                    } else {
                        if (data.message)
                            show_warning_msg("停止订单失败: " + data.message);
                        else
                            show_warning_msg("停止订单失败.");

                        $('#stop_order_modal').modal('hide');
                        $(submit).prop('disabled', false);

                        var order_id = $('#apply_stop').data('order-id');
                        $('.stop_order[data-order-id = "' + order_id + '"]').trigger('click');
                    }
                },
                error: function (data) {
                    console.log(data);
                }
            });
        });

        /*
         * Restart Order
         */

        function init_restart_order_modal() {
            $('#restart_order_id').val('');

            $('#start_at').val('');
            $('#apply_restart').removeAttr('data-order-id');
            $('#cancel_restart').removeAttr('data-order-id');
        }

        $(document).on('click', '#cancel_restart', function () {

            var order_id = $(this).data('order-id');

            //init modal box
            init_restart_order_modal();
            $('#restart_order_modal').modal('hide');

            $('.restart_order[data-order-id = "' + order_id + '"]').trigger('click');
        });

        $(document).on('change', '.restart_order', function () {

            var order_id = $(this).data('order-id');
            var checked = $(this).prop('checked');

            var stop_at = $(this).data('stop_at');

            if (checked == true) {
                var today = new Date();

                var order_start_at = start_at;
                var order_start_date = new Date(order_start_at);

                var restart_able_date = new Date();
                if (order_start_date > restart_able_date) {
                    restart_able_date = order_start_date;
                }
                //        if(gap_day)
                //            restart_able_date.setDate(today.getDate()+gap_day);
                //        else
                //        {
                //            gap_day = 3; //default
                //            restart_able_date.setDate(today.getDate()+3);
                //        }

//                $('#restart_order_modal').find('#start_at').datepicker({
//                    keyboardNavigation: false,
//                    forceParse: false,
//                    autoclose: true,
//                    calendarWeeks: false,
//                    clearBtn: true,
//                    minDate: restart_able_date,
//                });

                restart_able_date = restart_able_date.toISOString();
                $('#start_at').attr('min', restart_able_date);

                init_restart_order_modal();

                $('#restart_order_id').val(order_id);
                $('#apply_restart').attr('data-order-id', order_id);
                $('#cancel_restart').attr('data-order-id', order_id);
                $('#restart_order_modal').modal('show');

            } else {
                $('#restart_order_modal').modal('hide');
            }
        });

        $('#restart_order_modal_form').submit(function (e) {

            e.preventDefault();

            var submit = $(this).find('button[type="submit"]');
            $(submit).prop('disabled', true);
            var sendData = $(this).serializeArray();
            console.log(sendData);

            $.ajax({
                type: "POST",
                url: API_URL + "gongchang/dingdan/restart_order",
                data: sendData,
                success: function (data) {
                    console.log(data);
                    if (data.status == 'success') {
                        $('#restart_order_modal').modal('hide');
                        $(submit).prop('disabled', true);

                        show_success_msg("开启订单成功");
                        location.reload();

                    } else {
                        $('#restart_order_modal').modal('hide');
                        $(submit).prop('disabled', false);
                        if(data.message)
                            show_warning_msg("开启订单失败. " + data.message);
                        else
                            show_warning_msg("开启订单失败. ");

                        var order_id = $('#apply_restart').data('order-id');
                        $('.restart_order[data-order-id = "' + order_id + '"]').trigger('click');
                    }
                },
                error: function (data) {
                    console.log(data);
                    $(submit).prop('disabled', true);
                }
            });
        });


    </script>
@endsection
