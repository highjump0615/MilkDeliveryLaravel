@extends('weixin.layout.master')
@section('title','产品详情')
@section('css')
    <link rel="stylesheet" href="<?=asset('weixin/css/fullcalendar.min.css')?>">
    <link rel="stylesheet" href="<?=asset('weixin/css/pages/freeorder_calendar.css')?>">
@endsection

@section('content')

    <header>
        @if(isset($previous) && $previous == "none")
            <a class="headl fanh" href="{{url('weixin/shangpinliebiao')}}"></a>
        @else
            <a class="headl fanh" href="javascript:history.back();"></a>
        @endif
        <h1>产品详情</h1>

    </header>
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
        <input type="hidden" id="product_id" value="{{$product->id}}">

        <div class="dnsli clearfix">
            <div class="dnsti">订单类型：</div>
            <select class="dnsel" id="order_type">
                @if (isset($factory_order_types))
                    @foreach ($factory_order_types as $fot)
                        <option value="{{$fot->order_type}}" data-content="{{$fot->order_count}}">{{$fot->order_type_name}}</option>
                    @endforeach
                @endif
            </select>
            <div class="clear"></div>
        </div>
        <div class="dnsli clearfix">
            <div class="dnsti">订奶数量：</div>
                 <span class="minusplus">
                  <a class="minus" href="javascript:;">-</a>
                  <input type="text" min="1" id="total_count" value="30" style="ime-mode: disabled;">
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
        <div class="dnsli clearfix dnsel_item" id="dnsel_item0">
            <div class="dnsti">每天配送数量：</div>
            <span class="minusplus">
                <a class="minus" href="javascript:;">-</a>
                <input type="text" value="1" style="ime-mode: disabled;">
                <a class="plus" href="javascript:;">+</a>
            </span>（瓶）
        </div>

        <!--隔日送 -->
        <div class="dnsli clearfix dnsel_item" id="dnsel_item1">
            <div class="dnsti">每天配送数量：</div>
            <span class="minusplus">
                <a class="minus" href="javascript:;">-</a>
                <input type="text" value="1" style="ime-mode: disabled;">
                <a class="plus" href="javascript:;">+</a>
            </span>（瓶）
        </div>

        <!-- 按周规则 -->
        <div class="dnsli clearfix dnsel_item" id="dnsel_item2">
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
            {{--<div class="dnsti">起送时间：</div>--}}
            {{--<div class="input-group datepicker">--}}
                {{--<input type="text" required class="" name="start_at" id="start_at">--}}
                {{--<span><i class="fa fa-calendar"></i></span>--}}
            {{--</div>--}}
            <div class="ordrq">起送时间：<input class="qssj" id="start_at" name="start_at" type="date" value=""/></div>
        </div>

        <div class="dnsall">
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
        <div id="uecontent">{{$product->uecontent}}</div>
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

    @if(!isset($order_id))
    <div class="dnsbt clearfix">
        <button id="make_order" class="dnsb1"><i class="fa fa-check-circle"></i> 立即订购</button>
        <button id="submit_order" class="dnsb2"><i class="fa fa-cart-plus"></i> 加入购物车</button>
    </div>
    @elseif (isset($order_id))
        <div class="dnsbt clearfix">
            <button id="add_order" data-order-id="{{$order_id}}" class="dnsb2"><i class="fa fa-plus-circle"></i> 加入订单</button>
        </div>
    @endif

@endsection
@section('script')
    <script src="<?=asset('weixin/js/showfullcalendar.js')?>"></script>
    <script src="<?=asset('weixin/js/myweek.js')?>"></script>

    <script type="text/javascript">
    var errimg= "<?=asset('weixin/images/sb.png') ?>";
    var gap_day = 3;
    @if(!empty($gap_day))
        gap_day = parseInt("{{$gap_day}}");
    @endif
    var today = new Date("{{$today}}");
    var type = '';
    @if (!empty($type))
        type = "{{$type}}";
    @endif
    </script>

    <script type="text/javascript" src="<?=asset('js/pages/order/order_bottle.js') ?>"></script>
    <script src="<?=asset('weixin/js/freeorder_calendar.js')?>"></script>
    <script src="<?=asset('weixin/js/pages/tianjiadingdan.js')?>"></script>
@endsection