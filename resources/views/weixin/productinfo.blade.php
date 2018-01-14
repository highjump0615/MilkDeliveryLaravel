<?php

// 订单数量
$nCountTotal = 30;
if (!empty($wop)) {
    $nCountTotal = $wop->total_count;
}

?>

<div class="bann">
    <div class="swiper-container">
        <div class="swiper-wrapper">
            @if($file1)
                <div class="swiper-slide"><img class="bimg" src="{{$file1}}"></div>
            @endif
            @if($file2)
                <div class="swiper-slide"><img class="bimg" src="{{$file2}}"></div>
            @endif
            @if($file3)
                <div class="swiper-slide"><img class="bimg" src="{{$file3}}"></div>
            @endif
            @if($file4)
                <div class="swiper-slide"><img class="bimg" src="{{$file4}}"></div>
            @endif
        </div>
        <!-- Add Pagination -->
        <div class="swiper-pagination"></div>
    </div>
</div>

<div class="protop">
    <h3>{{$product->name}}</h3>
    <p>{{$product->introduction}}</p>
    <table class="prodz" width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td>月单</td>
            <td class="dzmon">￥{{$month_price}}</td>
        </tr>
    </table>
</div>

<div class="dnsl pa2t">
<!-- 编辑订单页面 -->
@if (!empty($wop))
    <input type="hidden" id="wechat_order_product_id" value="{{$wop->id}}">
    @if (!empty($wop->custom_order_dates))
        <input type="hidden" id="custom_order_dates" value="{{$wop->custom_order_dates}}">
    @endif
@endif
    <input type="hidden" id="product_id" value="{{$product->id}}">
@if (!empty($group_id))
    <input type="hidden" id="group_id" value="{{$group_id}}"/>
@endif

    @if (!empty($showOrderInfo))
    <div class="dnsli clearfix">
        <div class="dnsti">订单类型：</div>
        <select class="dnsel" id="order_type">
        <option value="1" data-content="30" selected>月单</option>
           <!--  @if (isset($factory_order_types))
                @foreach ($factory_order_types as $fot)
                    @if (!empty($wop) && $fot->order_type == $wop->order_type)
                        <option value="{{$fot->order_type}}" data-content="{{$fot->order_count}}" selected>{{$fot->order_type_name}}</option>
                    @else
                        <option value="{{$fot->order_type}}" data-content="{{$fot->order_count}}">{{$fot->order_type_name}}</option>
                    @endif
                @endforeach
            @endif -->
        </select>
        <div class="clear"></div>
    </div>
    <div class="dnsli clearfix">
        <div class="dnsti">订奶数量：</div>
        <span class="minusplus product_total_count">
            <a class="minus" href="javascript:;">-</a>
            <input type="text" min="1" id="total_count" value="{{$nCountTotal}}" style="ime-mode: disabled;">
            <a class="plus" href="javascript:;">+</a>
        </span>（瓶）
    </div>

    @include('weixin.productdeliverytype')

    <div class="dnsli clearfix">
        <div class="ordrq">起送时间：<input class="qssj" id="start_at" name="start_at" type="date" value=""/></div>
    </div>
    @endif

    <div class="dnsall">

        @if (!empty($showOrderInfo))
        @if (!empty($showDayCount))
            <div class="dnsts">
                订购天数：
                <span id="order_day_num">
                    @if (isset($order_day_num)) {{$order_day_num}} @endif
                </span> 天
                <a class="cxsd" href="javascript:void(0);">重新设定</a>
            </div>
        @endif
        @endif

        <div class="dnsli clearfix">
            <div class="dnsti">规格：</div>
            <div class="dnsti-r">{{$product->bottle_type_name}}</div>
        </div>
        <div class="dnsli clearfix">
            <div class="dnsti">保质期：</div>
            <div class="dnsti-r">{{$product->guarantee_period}}天</div>
        </div>
        <div class="dnsli clearfix">
            <div class="dnsti">储藏条件：</div>
            <div class="dnsti-r">{{$product->guarantee_req}}</div>
        </div>
        <div class="dnsli clearfix">
            <div class="dnsti">配料：</div>
            <div class="dnsti-r">{{$product->material}}</div>
        </div>
    </div>

</div>
<div class="dnxx">
    <div class="dnxti"><strong>详细介绍</strong>
        <span>DETAILED INTRODUCTION</span>
    </div>
    <div id="uecontent"></div>
</div>
<div class="sppj pa2t">
    <div class="sppti">商品评价</div>

    @if(isset($reviews))
        <ul class="sppul">
            @forelse($reviews as $review)
                <li>
                    <div class="spnum"><span class="spstart">@for($i=0; $i<$review->mark; $i++)<i></i>@endfor</span>
                        <p>{{$review->tel_number}}</p>
                    </div>
                    <div class="pjxx">
                        {{$review->content}}
                    </div>
                </li>
            @empty
                <p style="text-align: center;">没有评价</p>
            @endforelse
        </ul>
    @endif
</div>

<div class="he50"></div>
