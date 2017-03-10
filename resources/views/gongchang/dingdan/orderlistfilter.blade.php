
<div class="feed-element">
    <div class="vertical-align">
        <label class="col-md-1 control-label">收货人:</label>
        <div class="col-md-2"><input type="text" placeholder="" class="form-control" value=""
                                     id="filter_customer"></div>
        <label class="col-md-1 control-label">电话:</label>
        <div class="col-md-2"><input type="text" placeholder="" class="form-control" value=""
                                     id="filter_phone"></div>
        <label class="col-md-1 control-label">奶站:</label>
        <div class="col-md-2">
            <select id="filter_station" class="chosen-select form-control" style="height:35px;"
                    tabindex="2">
                <option value="none"></option>
                @if (isset($factory) and ($factory->deliveryStations))
                    @foreach($factory->deliveryStations as $station)
                        <option value="{{$station->name}}">{{$station->name}}</option>
                    @endforeach
                @endif
            </select>
        </div>
        <label class="col-md-1 control-label">订单性质:</label>
        <div class="col-md-2">
            <select id="filter_delivery_property" class="chosen-select form-control"
                    style="height:35px;" tabindex="2">
                <option value="none"></option>
                @if (isset($order_properties))
                    @foreach($order_properties as $orderproperty)
                        <option value="{{$orderproperty->name}}">{{$orderproperty->name}}</option>
                    @endforeach
                @else
                    <option value="1">新单</option>
                    <option value="2">续单</option>
                @endif
            </select>
        </div>
    </div>
</div>
<div class="feed-element">
    <div class="vertical-align">
        <label class="col-md-1 control-label">订单编号:</label>
        <div class="col-md-2"><input type="text" placeholder="" class="form-control" value=""
                                     id="filter_number"></div>
        <label class="col-md-1 control-label">征订员:</label>
        <div class="col-md-2"><input id="filter_order_checker" type="text" placeholder=""
                                     class="form-control" value=""></div>
        <label class="col-md-1 control-label">订单类型:</label>
        <div class="col-md-2">
            <select data-placeholder="" id="filter_term_kind" class="chosen-select form-control"
                    style="height:35px;" tabindex="2">
                <option value="none"></option>
                @if ( isset($factory) and ($factory->factory_order_types) )
                    @foreach($factory->factory_order_types as $fot)
                        <option value="{{$fot->order_type_name}}">{{$fot->order_type_name}}</option>
                    @endforeach
                @else
                    <option value="月单">月单</option>
                    <option value="季单">季单</option>
                    <option value="半年单">半年单</option>
                @endif
            </select>
        </div>
        <label class="col-md-1 control-label">支付:</label>
        <div class="col-md-2">
            <select data-placeholder="" id="filter_payment_type" class="chosen-select form-control"
                    style="height:35px;" tabindex="2">
                <option value="none"></option>
                @if ( isset($payment_types))
                    @foreach($payment_types as $pt)
                        <option value="{{$pt->name}}">{{$pt->name}}</option>
                    @endforeach
                @else
                    <option value="微信">微信</option>
                    <option value="现金">现金</option>
                    <option value="奶卡">奶卡</option>
                @endif
            </select>
        </div>
    </div>
</div>
<div class="feed-element">

    @if ($showState)
    <div class="vertical-align">
        <label class="col-md-1 control-label">订单状态:</label>
        <div class="col-md-2">
            <select data-placeholder="" id="filter_status" class="chosen-select form-control"
                    style="height:35px;" tabindex="2">
                <option value="none"></option>
                <option value="{{\App\Model\OrderModel\Order::ORDER_NEW_WAITING_STATUS}}">待审核</option>
                <option value="{{\App\Model\OrderModel\Order::ORDER_ON_DELIVERY_STATUS}}">在配送</option>
                <option value="{{\App\Model\OrderModel\Order::ORDER_STOPPED_STATUS}}">暂停</option>
                <option value="{{\App\Model\OrderModel\Order::ORDER_FINISHED_STATUS}}">已完成</option>
                <option value="{{\App\Model\OrderModel\Order::ORDER_NEW_NOT_PASSED_STATUS}}">未通过</option>
                <option value="{{\App\Model\OrderModel\Order::ORDER_CANCELLED_STATUS}}">退订</option>
            </select>
        </div>
    </div>
    @endif

    <div class="col-md-6 feed-element form-group" id="data_range_select">
        <label class="col-md-3 control-label">下单日期:</label>
        <div class="input-daterange input-group col-md-8" id="datepicker">
            <input type="text" id="filter_order_start_date" class="input-sm form-control" name="start"/>
            <span class="input-group-addon">至</span>
            <input type="text" id="filter_order_end_date" class="input-sm form-control" name="end"/>
        </div>
    </div>

    @if ($showEndDate)
    <div class="form-group" id="date_select">
        <label class="col-lg-1 control-label" style="padding-top:5px;">到期日期:</label>
        <div class="input-group date col-lg-2 single_date">
            <input type="text" class="form-control" id="end_date" /><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
        </div>
    </div>
    @endif
</div>