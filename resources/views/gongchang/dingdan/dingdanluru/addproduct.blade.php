<tr id="first_data" class="one_product">
    <!-- 日期 -->
    @if (!empty($order_product))
    <input type="hidden" name="created_at" value="{{$order_product->created_at}}" />
    @endif

    <!-- 奶品 -->
    <td>
        <select required class="form-control order_product_id"
                name="order_product_id[]"
                style="height:34px;">
            @if (isset($products))
                @foreach ($products as $product)
                    @if (isset($order_product) && $order_product->product_id == $product->id)
                        <option value="{{$product->id}}" selected>{{$product->simple_name}}</option>
                    @else
                        <option value="{{$product->id}}">{{$product->simple_name}}</option>
                    @endif
                @endforeach
            @else
                <option value="none">这家工厂没有注册的产品</option>
            @endif
        </select>
    </td>
    <!-- 订单类型 -->
    <td>
        <select required class="form-control factory_order_type"
                name="factory_order_type[]">
            @if (isset($factory_order_types))
                @foreach ($factory_order_types as $fot)
                    @if (isset($order_product) && $order_product->order_type == $fot->order_type)
                        <option value="{{$fot->order_type}}" selected
                                data-content="{{$fot->id}}">{{$fot->order_type_name}}</option>
                    @else
                        <option value="{{$fot->order_type}}"
                                data-content="{{$fot->id}}">{{$fot->order_type_name}}</option>
                    @endif
                @endforeach
            @else
                <option value="none">没有订单类型</option>
            @endif
        </select>
    </td>
    <!-- 数量 -->
    <td>
        <div>
            <?php
                // 默认数量
                $nCount = 30;

                if (isset($order_product)) {
                    if (!$is_edit && isset($order)) {
                        $nCount = $order_product->total_count;
                    }
                    else {
                        $nCount = $order_product->getRemainCount();
                    }
                }
            ?>
            <input required name="one_product_total_count[]"
                   class="one_product_total_count form-control"
                   type="number"
                   min="1"
                   value="{{$nCount}}"
                   style="padding-left: 2px;"/>
            <select class="one_product_total_count_select control hidden form-control">
                @if(isset($products_count_on_fot))
                    @foreach($products_count_on_fot as $pcof)
                        @if (isset($order_product) && $order_product->order_type == $pcof['fot'])
                            <option data-otid="{{$pcof['fot']}}" selected
                                    value="{{$pcof['pcfot']}}">{{$pcof['pcfot']}}</option>
                        @else
                            <option data-otid="{{$pcof['fot']}}"
                                    value="{{$pcof['pcfot']}}">{{$pcof['pcfot']}}</option>
                        @endif
                    @endforeach
                @endif
            </select>
        </div>
    </td>
    <!-- 起送日期 -->
    <td>
        <div class="input-group date single_date">
            <input required type="text" class="form-control start_at" name="start_at[]"
                   @if (isset($order) && !$is_edit) value="{{$order->order_end_date}}"
                   @elseif (isset($order_product)) value="{{$order_product->start_at}}" @endif />
            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
        </div>
    </td>
    <!-- 配送规则 -->
    <td>
        <select required class="form-control order_delivery_type " name="order_delivery_type[]">
            @if (isset($order_delivery_types))
                @foreach ($order_delivery_types as $odt)
                    @if (isset($order_product) && $order_product->delivery_type == $odt->delivery_type)
                        <option value="{{$odt->delivery_type}}" selected
                                data-content="{{$odt->id}}">{{$odt->name}}</option>
                    @else
                        <option value="{{$odt->delivery_type}}"
                                data-content="{{$odt->id}}">{{$odt->name}}</option>
                    @endif
                @endforeach
            @else
                <option value="">没有配送规则</option>
            @endif
        </select>
    </td>
    <!-- 每次数量 -->
    <td>
        <div class="bottle_number">
            <input type="number" min="1" required name="order_product_count_per[]"
                   class="form-control order_product_count_per"
                   @if (isset($order_product))
                    value="{{$order_product->count_per_day}}"
                   @else
                    value="1"
                   @endif
                   style="display:inline-block;">
        </div>
    </td>
    <!-- 配送日期选择 -->
    <td>
        <!-- 天天送、隔日送就不显示选择日期的 -->
        <div class="calendar_show" style="@if (isset($order_product) && $order_product->delivery_type < 3 ) display: none; @endif">
            <div class="input-group date picker">
                <input type="text" class="form-control delivery_dates" name="delivery_dates[]"
                       value="@if (isset($order_product)) {{$order_product->custom_order_dates}} @endif" />
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
            </div>
        </div>
    </td>
    <!-- 单数 -->
    <td>
        <label class="control-label product_count_per_day" style="padding-top: 7px;">
            <input type="text" required name="avg[]" class="avg" readonly value="1.0"/>
        </label>
    </td>
    <!-- 金额 -->
    <td>
        <label class="control-label total_amount_per_product" style="padding-top: 7px;">
            <input type="text" required name="one_p_amount[]" class="one_p_amount" readonly/>
        </label>
    </td>
    <!-- 删除按钮 -->
    <td>
        <button type="button" class="remove_one_product"><i class="fa fa-trash-o" aria-hidden="true"></i>
        </button>
    </td>
</tr>