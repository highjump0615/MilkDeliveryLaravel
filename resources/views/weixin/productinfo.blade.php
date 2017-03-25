<?php

// 订单数量
$nCountTotal = 30;
if (!empty($wop)) {
    $nCountTotal = $wop->total_count;
}

// 每次数量
$nCountEach = 1;
if (!empty($wop) && !empty($wop->count_per_day)) {
    $nCountEach = $wop->count_per_day;
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
        <tr>
            <td>季单</td>
            <td class="dzmon">￥{{$season_price}}</td>
        </tr>
        <tr>
            <td>半年单</td>
            <td class="dzmon">￥{{$half_year_price}}</td>
        </tr>
    </table>
</div>

<div class="dnsl pa2t">
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

    <div class="dnsli clearfix">
        <div class="dnsti">订单类型：</div>
        <select class="dnsel" id="order_type">
            @if (isset($factory_order_types))
                @foreach ($factory_order_types as $fot)
                    @if (!empty($wop) && $fot->order_type == $wop->order_type)
                        <option value="{{$fot->order_type}}" data-content="{{$fot->order_count}}" selected>{{$fot->order_type_name}}</option>
                    @else
                        <option value="{{$fot->order_type}}" data-content="{{$fot->order_count}}">{{$fot->order_type_name}}</option>
                    @endif
                @endforeach
            @endif
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

    <div class="dnsli clearfix">
        <div class="dnsti">配送规则：</div>
        <select class="dnsel" id="delivery_type" onChange="javascript:dnsel_changed(this.value)">
            <option value="dnsel_item0"
                    data-value="{{\App\Model\DeliveryModel\DeliveryType::DELIVERY_TYPE_EVERY_DAY}}">天天送
            </option>
            <option value="dnsel_item1"
                    data-value="{{\App\Model\DeliveryModel\DeliveryType::DELIVERY_TYPE_EACH_TWICE_DAY}}">隔日送
            </option>
            <option value="dnsel_item2"
                    data-value="{{\App\Model\DeliveryModel\DeliveryType::DELIVERY_TYPE_WEEK}}">按周送
            </option>
            <option value="dnsel_item3"
                    data-value="{{\App\Model\DeliveryModel\DeliveryType::DELIVERY_TYPE_MONTH}}">随心送
            </option>
        </select>
        <div class="clear"></div>
    </div>

    <!-- combo box change -->
    <!-- 天天送 -->
    <div class="dnsli clearfix dnsel_item" id="dnsel_item0" style="display:none;">
        <div class="dnsti">每天配送数量：</div>
        <span class="minusplus">
            <a class="minus" href="javascript:;">-</a>
            <input type="text" class="deliver_count_per_day" value="{{$nCountEach}}" style="ime-mode: disabled;">
            <a class="plus" href="javascript:;">+</a>
        </span>（瓶）
    </div>

    <!--隔日送 -->
    <div class="dnsli clearfix dnsel_item" id="dnsel_item1" style="display:none;">
        <div class="dnsti">每天配送数量：</div>
        <span class="minusplus">
            <a class="minus" href="javascript:;">-</a>
            <input type="text" class="deliver_count_per_day" value="{{$nCountEach}}" style="ime-mode: disabled;">
            <a class="plus" href="javascript:;">+</a>
        </span>（瓶）
    </div>

    <!-- 按周规则 -->
    <div class="dnsli clearfix dnsel_item" id="dnsel_item2" style="display: none;">
        <table class="psgzb" width="" border="0" cellspacing="0" cellpadding="0" id="week">
        </table>
    </div>

    <!-- 随心送 -->
    <div class="dnsel_item" id="dnsel_item3" style="display: none;">
        <div class="calender">
            <div class="selectmouth">
                <p style="text-align:right" class="lastmonth" onclick="lastmonth()">< < <</p>
                <p><input type="text" class="selectdate" value="2017年3月" readonly=readonly /></p>
                <p class="nextmonth" onclick="nextmonth()">> > ></p>
            </div>
            <table class="data_table" cellspacing="0px">
                <thead>
                <tr>
                    <td>日</td><td>一</td><td>二</td><td>三</td><td>四</td><td>五</td><td>六</td>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="dnsli clearfix">
        <div class="ordrq">起送时间：<input class="qssj" id="start_at" name="start_at" type="date" value=""/></div>
    </div>

    <div class="dnsall">

        @if (!empty($showDayCount))
            <div class="dnsts">
                订购天数：
                <span id="order_day_num">
                    @if (isset($order_day_num)) {{$order_day_num}} @endif
                </span> 天
                <a class="cxsd" href="javascript:void(0);">重新设定</a>
            </div>
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
