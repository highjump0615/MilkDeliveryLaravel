<?php

// 每次数量
$nCountEach = 1;

// 编辑订单页面
if (!empty($wop) && !empty($wop->count_per_day)) {
    $nCountEach = $wop->count_per_day;
}

// 奶品修改页面
if (!empty($current_count_per_day)) {
    $nCountEach = $current_count_per_day;
}

?>

<div class="dnsli clearfix">

<!-- 奶品修改页面 -->
@if (!empty($current_custom_order_dates))
    <input type="hidden" id="custom_order_dates" value="{{$current_custom_order_dates}}">
@endif

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