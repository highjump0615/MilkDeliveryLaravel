<form method="get" id="form_filter" class="form-horizontal no-top-padding">
<div class="feed-element">
    <div class="vertical-align">
        {{-- 收件人 --}}
        <div class="item">
            <label class="control-label">收货人:</label>
            <div class="input-data">
                <input type="text"
                       name="customer"
                       placeholder=""
                       class="form-control"
                       @if (!empty($customer)) value="{{$customer}}" @endif>
            </div>
        </div>
        {{-- 电话 --}}
        <div class="item">
            <label class="control-label">电话:</label>
            <div class="input-data">
                <input type="text"
                       name="phone"
                       placeholder=""
                       class="form-control"
                       @if (!empty($phone)) value="{{$phone}}" @endif>
            </div>
        </div>
        {{-- 奶站 --}}
        <div class="item">
            <label class="control-label">奶站:</label>
            <div class="input-data">
                <select class="chosen-select form-control"
                        style="height:35px;"
                        name="station"
                        tabindex="2">
                    <option value="0"></option>
                    @if (isset($factory) and ($factory->deliveryStations))
                        @foreach($factory->deliveryStations as $st)
                            <option value="{{$st->id}}"
                                    @if (!empty($station) && $station == $st->id) selected @endif>
                                {{$st->name}}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
        {{-- 订单性质 --}}
        <div class="item">
            <label class="control-label">订单性质:</label>
            <div class="input-data">
                <select class="chosen-select form-control"
                        name="property"
                        style="height:35px;"
                        tabindex="2">
                    <option value="0"></option>
                    @if (isset($order_properties))
                        @foreach($order_properties as $orderproperty)
                            <option value="{{$orderproperty->id}}"
                                    @if (!empty($property) && $property == $orderproperty->id) selected @endif>
                                {{$orderproperty->name}}
                            </option>
                        @endforeach
                    @else
                        <option value="1">新单</option>
                        <option value="2">续单</option>
                    @endif
                </select>
            </div>
        </div>
        {{-- 单据号 --}}
        <div class="item">
            <label class="control-label">单据号:</label>
            <div class="input-data">
                <input type="text"
                       name="receipt"
                       placeholder=""
                       class="form-control"
                       @if (!empty($receipt)) value="{{$receipt}}" @endif>
            </div>
        </div>
    </div>
</div>
<div class="feed-element">
    <div class="vertical-align">
        {{-- 订单编号 --}}
        <div class="item">
            <label class="control-label">订单编号:</label>
            <div class="input-data">
                <input type="text"
                       name="number"
                       placeholder=""
                       class="form-control"
                       @if (!empty($number)) value="{{$number}}" @endif>
            </div>
        </div>
        {{-- 征订员 --}}
        <div class="item">
            <label class="control-label">征订员:</label>
            <div class="input-data">
                <input id="filter_order_checker"
                       name="checker"
                       type="text"
                       placeholder=""
                       class="form-control"
                       @if (!empty($checker)) value="{{$checker}}" @endif>
            </div>
        </div>
        {{-- 订单类型 --}}
        <div class="item">
            <label class="control-label">订单类型:</label>
            <div class="input-data">
                <select data-placeholder=""
                        name="type"
                        class="chosen-select form-control"
                        style="height:35px;"
                        tabindex="2">
                    <option value="0"></option>
                    @if ( isset($factory) and ($factory->factory_order_types) )
                        @foreach($factory->factory_order_types as $fot)
                            <option value="{{$fot->order_type}}"
                                    @if (!empty($type) && $type == $fot->order_type) selected @endif>
                                {{$fot->order_type_name}}
                            </option>
                        @endforeach
                    @else
                        <option value=1>月单</option>
                        <option value=2>季单</option>
                        <option value=3>半年单</option>
                    @endif
                </select>
            </div>
        </div>
        {{-- 支付类型 --}}
        <div class="item">
            <label class="control-label">支付:</label>
            <div class="input-data">
                <select data-placeholder=""
                        name="ptype"
                        class="chosen-select form-control"
                        style="height:35px;"
                        tabindex="2">
                    <option value="0"></option>
                    @if ( isset($payment_types))
                        @foreach($payment_types as $pt)
                            <option value="{{$pt->id}}"
                                    @if (!empty($ptype) && $ptype == $pt->id) selected @endif>
                                {{$pt->name}}
                            </option>
                        @endforeach
                    @else
                        <option value="3">微信</option>
                        <option value="1">现金</option>
                        <option value="2">奶卡</option>
                    @endif
                </select>
            </div>
        </div>
        {{-- 地址 --}}
        <div class="item">
            <label class="control-label">地址:</label>
            <div class="input-data">
                <input type="text"
                       name="address"
                       placeholder=""
                       class="form-control"
                       @if (!empty($address)) value="{{$address}}" @endif>
            </div>
        </div>
    </div>
</div>
<div class="feed-element">
    <div class="vertical-align">
        @if ($showState)
        {{-- 订单状态 --}}
        <div class="item">
            <label class="control-label">订单状态:</label>
            <div class="input-data">
                <select data-placeholder=""
                        name="status"
                        class="chosen-select form-control"
                        style="height:35px;"
                        tabindex="2">
                    <option value="0"></option>
                    <option value="{{\App\Model\OrderModel\Order::ORDER_NEW_WAITING_STATUS}}"
                            @if (!empty($status) && $status == \App\Model\OrderModel\Order::ORDER_NEW_WAITING_STATUS) selected @endif>
                        待审核
                    </option>
                    <option value="{{\App\Model\OrderModel\Order::ORDER_ON_DELIVERY_STATUS}}"
                            @if (!empty($status) && $status == \App\Model\OrderModel\Order::ORDER_ON_DELIVERY_STATUS) selected @endif>
                        在配送
                    </option>
                    <option value="{{\App\Model\OrderModel\Order::ORDER_STOPPED_STATUS}}"
                            @if (!empty($status) && $status == \App\Model\OrderModel\Order::ORDER_STOPPED_STATUS) selected @endif>
                        暂停
                    </option>
                    <option value="{{\App\Model\OrderModel\Order::ORDER_FINISHED_STATUS}}"
                            @if (!empty($status) && $status == \App\Model\OrderModel\Order::ORDER_FINISHED_STATUS) selected @endif>
                        已完成
                    </option>
                    <option value="{{\App\Model\OrderModel\Order::ORDER_NEW_NOT_PASSED_STATUS}}"
                            @if (!empty($status) && $status == \App\Model\OrderModel\Order::ORDER_NEW_NOT_PASSED_STATUS) selected @endif>
                        未通过
                    </option>
                    <option value="{{\App\Model\OrderModel\Order::ORDER_CANCELLED_STATUS}}"
                            @if (!empty($status) && $status == \App\Model\OrderModel\Order::ORDER_CANCELLED_STATUS) selected @endif>
                        退订
                    </option>
                </select>
            </div>
        </div>
        @endif
    </div>

    {{-- 下单日期 --}}
    <div class="item double" id="data_range_select">
        <label class="control-label">下单日期:</label>
        <div class="input-daterange input-group" id="datepicker">
            <input type="text"
                   class="input-sm form-control"
                   name="start"
                   @if (!empty($start)) value="{{$start}}" @endif/>
            <span class="input-group-addon">至</span>
            <input type="text"
                   id="filter_order_end_date"
                   class="input-sm form-control"
                   name="end"
                   @if (!empty($end)) value="{{$end}}" @endif/>
        </div>
    </div>

    @if ($showEndDate)
    <div class="item" id="date_select">
        {{-- 到期日期 --}}
        <label class="control-label">到期日期:</label>
        <div class="input-data input-group date col-lg-2 single_date">
            <input type="text"
                   class="form-control"
                   name="end_date"
                   disabled
                   @if (!empty($endDate)) value="{{$endDate}}" @endif/>
            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
        </div>
    </div>
    @endif
</div>
<!-- 隐藏submit，以便回车键就执行筛选 -->
<input type="submit" style="display: none;">
</form>