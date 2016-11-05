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
            <dd class="pa2t"><span>2016-06-02</span>已配送——1瓶</dd>
            <dd class="pa2t"><span>2016-06-02</span>已配送——1瓶</dd>
            <dd class="pa2t"><span>2016-06-02</span>已配送——1瓶</dd>
        </dl>
    </div>
    <div class="he50"></div>
    <div class="dnsbt clearfix">
        <a class="tjord" href="javascript:void(0);">修改单日计划</a>
    </div>

@endsection
@section('script')
    <script src="<?=asset('weixin/js/jquery-1.10.1.min.js')?>"></script>
    <script src="<?=asset('weixin/js/moment.min.js')?>"></script>
    <script src="<?=asset('weixin/js/fullcalendar.min.js')?>"></script>
    <script type="text/javascript">
        $(function () {
            $('#calendar').fullCalendar({
                header: {
                    left: 'prev',
                    center: 'title',
                    right: 'next'
                },
                firstDay: 0,
                editable: true,
                events: [
                    {
                        title: '2',
                        start: '2016-09-01',
                        className: 'ypsrl'

                    },
                    {
                        //title: '2',
                        start: '2016-09-01',
                        rendering: 'background',
                        color: '#e1e1e1'
                    }, {
                        title: '2',
                        start: '2016-09-28',
                        //className:'ypsrl'

                    },
                    {
                        //title: '2',
                        start: '2016-09-28',
                        rendering: 'background',
                        color: '#00a040'
                    },
                    {
                        title: '5',
                        start: '2016-09-29',
                    },
                    {
                        //title: '5',
                        start: '2016-09-29',
                        rendering: 'background',
                        color: '#00a040'
                    },
                    {
                        title: '3',
                        start: '2016-09-30',
                    },
                    {
                        //title: '3',
                        start: '2016-09-30',
                        rendering: 'background',
                        color: '#00a040'
                    }
                ]
            });

        });
    </script>

    <script>

        $(".addSubtract .add").click(function () {
            $(this).prev().val(parseInt($(this).prev().val()) + 1);
        });
        $(".addSubtract .subtract").click(function () {
            if (parseInt($(this).next().val()) > 10) {
                $(this).next().val(parseInt($(this).next().val()) - 1);
                $(this).removeClass("subtractDisable");
            }
            if (parseInt($(this).next().val()) <= 10) {
                $(this).addClass("subtractDisable");
            }
        });
    </script>
@endsection
