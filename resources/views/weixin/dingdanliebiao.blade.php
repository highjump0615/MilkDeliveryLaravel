@extends('weixin.layout.master')
@section('title','订单列表')
@section('css')
    <link href="<?=asset('plugins/switchery/switchery.min.css') ?>" rel="stylesheet">
@endsection
@section('content')

    <header>
        <a class="headl fanh" href="javascript:history.back();"></a>
        <h1>订单列表</h1>
    </header>

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
                    <span class="shsp-r"
                    >开启订单
                    <input type="checkbox" class="js-switch restart_order"
                           data-start-at="{{$o->getPauseStartAvailableDate()}}"
                           data-stop-from="{{$o->stop_at}}"
                           data-stop-to="{{$o->order_stop_end_date}}"
                           data-restart-at="{{$o->restart_at}}"
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
                        <span class="shsp-r"
                        >暂停订单
                        <input type="checkbox" class="js-switch stop_order"
                               data-start-at="{{$o->getPauseStartAvailableDate()}}"
                               data-end-at="{{$o->order_end_date}}"
                               data-stop-from="{{$o->stop_at}}"
                               data-stop-to="{{$o->order_stop_end_date}}"
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
                                <input class="input-sm form-control" id="stop_start" required name="start"
                                       type="date" value=""/>
                                <span class="input-group-addon">至</span>
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
                            <label class="align-center">暂停期间为 </label>
                            <label class="align-center" id="stop_period"></label>
                            <hr>
                            <label class="align-center  btn-block">选择开启的日期</label>
                            <div class="input-group">
                                <input required type="date" class="form-control" id="start_at"
                                       name="start_at" value="2018-04-12" /><span
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
        <p style="font-size: 15px; margin: 10px auto; width: 100%; text-align: center;">"没有订单"</p>
    @endforelse

    @include('weixin.layout.footer')
@endsection
@section('script')
    <!-- Switchery -->
    <script src="<?=asset('plugins/switchery/switchery.min.js') ?>"></script>
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


        $(document).on('change', '.stop_order', function () {
            var strModalSel = '#stop_order_modal';
            var checked = $(this).prop('checked');

            if (checked === true) {
                var order_id = $(this).data('order-id');

                // 订单停止范围
                var pause_avaliable_from = $(this).data('start-at');
                var pause_avaliable_to = $(this).data('end-at');

                // 当前暂停范围
                var stop_from = $(this).data('stop-from');
                var stop_to = $(this).data('stop-to');

                // 暂停开启日期
                $(strModalSel).find('#stop_start').attr('min', pause_avaliable_from);
                $(strModalSel).find('#stop_start').attr('max', pause_avaliable_to);

                // 暂停结束日期
                $(strModalSel).find('#stop_end').attr('min', pause_avaliable_from);

                init_stop_order_modal();

                $(strModalSel).find('#stop_start').val(stop_from);
                $(strModalSel).find('#stop_end').val(stop_to);

                $('#stop_order_id').val(order_id);
                $('#apply_stop').attr('data-order-id', order_id);
                $('#cancel_stop').attr('data-order-id', order_id);

                $(strModalSel).modal('show');

            } else {
                $(strModalSel).modal('hide');
            }
        });

        $(document).on('click', '#cancel_stop', function () {

            var order_id = $('#apply_stop').data('order-id');

            //init modal box
            init_stop_order_modal();
            $('#stop_order_modal').modal('hide');

            $('.stop_order[data-order-id = "' + order_id + '"]').trigger('click');
        });

        // 检查选择日期的合法性
        function checkStopDateValidation() {
            var inputStopFrom = '#stop_start';
            var inputStopTo = '#stop_end';

            var pause_avaliable_from = $(inputStopFrom).attr('min');
            var pause_avaliable_to = $(inputStopFrom).attr('max');
            var dateStopFrom = $(inputStopFrom).val();
            var dateStopTo = $(inputStopTo).val();

            if (dateStopFrom < pause_avaliable_from || dateStopFrom > pause_avaliable_to) {
                show_err_msg('暂停起始日期不在使用范围内');
                return false;
            }

            if (dateStopTo < dateStopFrom) {
                show_err_msg('暂停结束日期不得小于起始日期');
                return false;
            }

            return true;
        }

        $('#stop_order_modal_form').submit(function (e) {
            e.preventDefault();

            // Check validation
            if (!checkStopDateValidation()) {
                return;
            }

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

            var checked = $(this).prop('checked');
            if (checked === true) {
                var order_id = $(this).data('order-id');

                // 当前暂停范围
                var stop_from = $(this).data('stop-from');
                var stop_to = $(this).data('stop-to');

                // 订单停止范围
                var pause_avaliable_from = $(this).data('start-at');
                var restart_at = $(this).data('restart-at');

                // 暂停期间
                $('#stop_period').text(stop_from + ' ~ ' + stop_to);

                init_restart_order_modal();

                var selInputStartAt = '#start_at';
                $(selInputStartAt).attr('min', pause_avaliable_from);
                $(selInputStartAt).val(restart_at);

                $('#restart_order_id').val(order_id);
                $('#apply_restart').attr('data-order-id', order_id);
                $('#cancel_restart').attr('data-order-id', order_id);
                $('#restart_order_modal').modal('show');

            } else {
                $('#restart_order_modal').modal('hide');
            }
        });

        // 检查开启订单选择日期的合法性
        function checkRestartDateValidation() {
            var inputRestartFrom = '#start_at';
            var pause_avaliable_from = $(inputRestartFrom).attr('min');

            var dateRestart = $(inputRestartFrom).val();

            if (dateRestart < pause_avaliable_from) {
                show_err_msg('不能选择此日期');
                return false;
            }

            return true;
        }

        $('#restart_order_modal_form').submit(function (e) {

            e.preventDefault();

            // check validation
            if (!checkRestartDateValidation()) {
                return;
            }

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
