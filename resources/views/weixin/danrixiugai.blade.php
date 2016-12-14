@extends('weixin.layout.master')
@section('title','修改单日')
@section('css')

@endsection
@section('content')
    <header>
        <a class="headl fanh" href="javascript:history.back()"></a>
        <h1>修改单日</h1>
    </header>
    <div class="ordall pa2">
        金额：￥{{$total_amount_on_date}}
    </div>
    <div class="ordrq">所选日期：<input class="qssj" id="plan_date" name="" type="date" value="{{$date}}"></div>

    <div class="dncon">
        <ul class="dnpro dnpro2">
            @forelse($plans as $plan)
                <li class="clearfix one_plan">
                    <img class="ordpro" src="<?=asset('/img/product/logo/'.$plan->plan_product_image)?>">
                    <p>{{$plan->product_name}}</p>
                    <div class="ordye">金额：{{$plan->plan_price}}元</div>
                    <span class="minusplus">
                        <a class="minus" href="javascript:;">-</a>
                        <input type="text" value="{{$plan->changed_plan_count}}" style="ime-mode: disabled;"/>
                        <a class="plus" href="javascript:;">+</a>
                    </span>
                    <input type="hidden" class="change_plan_count" data-planid="{{$plan->id}}" data-origin = "{{$plan->changed_plan_count}}"/>
                </li>
            @empty

            @endforelse
        </ul>
    </div>
    <div class="he50"></div>
    <div class="dnsbt clearfix">
        <button class="tjord tjord3" id="change_plans_on_date">提交</button>
    </div>

@endsection
@section('script')
    <script type="text/javascript">
        $('#plan_date').change(function(){
            var date = $(this).val();
            window.location= SITE_URL+"weixin/danrixiugai?date="+date;
        });

        $('#change_plans_on_date').click(function(){

            var sendData = [];
            $('li.one_plan').each(function(){
                var changed = parseInt($(this).find('span.minusplus input').val());
                var origin = parseInt($(this).find('input.change_plan_count').data('origin'));
                var plan_id = $(this).find('input.change_plan_count').data('planid');

                if(changed != origin)
                {
                    sendData.push([plan_id, origin, changed]);
                }
            });

            if(sendData.length > 0)
            {
                console.log(sendData);

                $.ajax({
                    type: "POST",
                    url: SITE_URL + "/weixin/api/change_delivery_plan_for_one_date",
                    data: {'plans_data':sendData},
                    success: function (data) {
                        console.log(data);
                        if (data.status == "success") {
                            show_success_msg('修改单日成功');
//                            location.reload();
                            location.href = SITE_URL + "weixin/dingdanrijihua";
                        } else {
                            show_warning_msg('剩余奶品数量不足，修改失败');
                            console.log(data.messages);
                        }
                    },
                    error: function (data) {
                        console.log(data);
                        show_warning_msg('剩余奶品数量不足，修改失败');
                    }
                });
            }

        });

        $(".plus").click(function () {
            $(this).prev().val(parseInt($(this).prev().val()) + 1);
            if(parseInt($(this).prev().val()) >1 )
            {
                $(this).parent().find('.minus').removeClass("minusDisable");
            }
        });
        $(".minus").click(function () {
            if (parseInt($(this).next().val()) > 1) {
                $(this).next().val(parseInt($(this).next().val()) - 1);
                $(this).removeClass("minusDisable");
            }
            if (parseInt($(this).next().val()) <= 1) {
                $(this).addClass("minusDisable");
            }
        });

    </script>
@endsection