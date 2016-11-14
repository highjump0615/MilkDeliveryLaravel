@extends('weixin.layout.master')
@section('title','订单日计划修改')
@section('css')
    <link rel="stylesheet" href="<?=asset('weixin/css/swiper.min.css')?>">
    <link href="<?=asset('weixin/css/fullcalendar.min.css')?>" rel="stylesheet"/>
@endsection

@section('content')
    <header>
        <a class="headl fanh" href="javascript:history.back()"></a>
        <h1>我的订单计划</h1>

    </header>

    <div class="ordrxg">
        <div id='calendar'></div>

        <dl class="ordjl">
            <dt class="pa2t">订单记录：</dt>
            <?php $fn_count = 0;?>
            @forelse($plans as $plan)
                @if($plan->status == \App\Model\DeliveryModel\MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_FINNISHED)
                    <dd class="pa2t"><span>{{$plan->deliver_at}}</span>已配送——{{$plan->delivered_count}}瓶</dd>
                    <?php $fn_count++;?>
                @endif
            @empty
                <p class="plan_result">没有配送计划</p>
            @endforelse
            @if($fn_count == 0)
                <p class="plan_result">没有已配送</p>
            @endif
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

        $(function () {
            $('#calendar').fullCalendar({
                header: {
                    left: 'prev',
                    center: 'title',
                    right: 'next'
                },
                firstDay: 0,
                editable: false,
                events: [
                        @foreach($plans as $p)
                    {
                        title: "{{$p->product_name}} {{$p->changed_plan_count}}",
                        start: '{{$p->deliver_at}}',
                        className: 'ypsrl',
                        textColor: '#00cc00'

                    },
                    @endforeach
                ],
            });

            $(document).on('click', '#calendar table thead tr td.fc-day-top', function(){
                var day_td= $(this);
                var date = $(day_td).data('date');

                $('#calendar table thead tr td').each(function(){
                    $(this).removeClass('selected');
                });
                $(day_td).addClass('selected');
            });


            $('#change_one_day').click(function(){
                var day_td = $('#calendar table thead tr').find('td.selected');

                if($(day_td).length >0){
                    if(! ($(day_td).hasClass('fc-past')) )
                    {
                        var date= $(day_td).data('date');
                        window.location= SITE_URL+"weixin/danrixiugai?date="+date;
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
