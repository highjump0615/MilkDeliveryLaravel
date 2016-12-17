@extends('weixin.layout.master')
@section('title','订单日计划修改')
@section('css')
    <link href="<?=asset('weixin/css/fullcalendar.min.css')?>" rel="stylesheet"/>
@endsection

@section('content')
    <header>
        @if(isset($from))
            <a class="headl fanh" href="{{url('weixin/gerenzhongxin')}}"></a>
        @else
            <a class="headl fanh" href="javascript:history.back()"></a>
        @endif

        <h1>我的订单计划</h1>

    </header>

    <div class="ordrxg">
        <div id='calendar'></div>

        <label class="pa2t" style="margin-top: 20px;">订单记录：</label>
        <dl class="ordjl">
            <table class="table table-bordered" data-page-size="10" id="internalActivities">
                <thead>
                <tr>
                    <!--<th data-sort-ignore="true">序号</th>-->
                    <th data-sort-ignore="true">配送时间</th>
                    <th data-sort-ignore="true">状态</th>
                    <th data-sort-ignore="true">奶品</th>
                    <th data-sort-ignore="true">数量</th>
                </tr>
                </thead>
                <tbody>
                @if(isset($plans))
                    <?php $i = 0;?>
                    @foreach($plans as $plan)
                        <tr data-planid="{{$plan['plan_id']}}" data-date="{{$plan['deliver_at']}}">
                            <!--<td>{{$i+1}}</td>-->
                            <td>{{$plan['deliver_at']}}</td>
                            <td>{{$plan['status_name']}}</td>
                            <td>{{$plan['product_name']}}</td>
                            @if($plan['status'] == \App\Model\DeliveryModel\MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_FINNISHED )
                                <td>{{$plan['delivered_count']}}</td>
                            @else
                                <td>
                                    <label> {{$plan['changed_plan_count']}}</label>
                                </td>
                            @endif
                        </tr>
                        <?php $i++; ?>
                    @endforeach
                @endif

                </tbody>
            </table>

            <!--<button class="tjord" id="seeMoreRecords">More</button>-->
    </dl>
    </div>
    <div class="he50"></div>
    <div class="dnsbt clearfix">
        <button class="tjord" id="change_one_day">修改单日计划</button>
    </div>

@endsection
@section('script')
    <script src="<?=asset('weixin/js/fullcalendar.min.js')?>"></script>

    <script type="text/javascript">

        var today = "{{$today}}";
        var trs = $("#internalActivities tr");
//        var btnMore = $("#seeMoreRecords");
        var trsLength = trs.length;
        var currentIndex = 10;

        var edit_min_date = "{{$edit_min_date}}";

        trs.hide();


        $(function () {
            $('#calendar').fullCalendar({
                header: {
                    left: 'prev',
                    center: 'title',
                    right: 'next'
                },
                now: today,
                firstDay: 0,
                editable: false,
                events: [
                        @foreach($plans as $p)
                    {
                        title: "{{$p->product_simple_name}} {{$p->changed_plan_count}}",
                        start: '{{$p->deliver_at}}',
                        @if($p->isEditAvailable())
                        className: 'ypsrl editable',
                        selectable: true,
                        color: '#00cc00',
                        @else
                        className: 'ypsrl noteditable',
                        selectable: false,
                        color:'#a82828',
                        @endif
                        textColor: 'white',
                    },
                    @endforeach
                ],
                viewRender: function (view, element) {
                    var start_date = view.intervalStart;

                    var cal_year = start_date.format('YYYY');
                    var cal_month = start_date.format('MM');

                    $(trs).each(function(){
                        var tr = $(this);
                        var date_str = tr.data('date');

                        if(date_str != undefined) {
                            var date_obj = new Date(date_str);

                            var year = date_obj.getUTCFullYear();

                            var month = date_obj.getUTCMonth() + 1;;

                            if(cal_year == year && cal_month == month) {
                                tr.show();
                            } else {
                                tr.hide();
                            }

                        }


                    });
                }
            });

            $(document).on('click', '#calendar table thead tr td.fc-day-top', function () {

                if(!edit_min_date)
                    return;

                $('#calendar table thead tr td').each(function () {
                    $(this).removeClass('selected');
                });

                var day_td = $(this);
                if($(day_td).hasClass('fc-past'))
                        return;

                var date = $(day_td).data('date');

                if(date < edit_min_date)
                        return;

                $(day_td).addClass('selected');
            });


            $('#change_one_day').click(function () {
                var day_td = $('#calendar table thead tr').find('td.selected');

                if ($(day_td).length > 0) {
                    if (!($(day_td).hasClass('editable'))) {
                        var date = $(day_td).data('date');
                        window.location = SITE_URL + "weixin/danrixiugai?date=" + date;
                    } else {
                        show_warning_msg('修改单日计划不可能');
                    }
                } else {
                    show_info_msg('选择要更改日期');
                }
            });
        });
    </script>
@endsection
