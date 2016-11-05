@extends('weixin.layout.master')
@section('title','天天送')
@section('css')
    <link rel="stylesheet" href="css/themes/base/jquery-ui.css"/>
    <link href='css/fullcalendar.min.css' rel='stylesheet'/>
    <link rel="stylesheet" href="css/swiper.min.css">
@endsection
@section('content')

    <header>
        <a class="headl fanh" href="javascript:void(0)"></a>
        <h1>产品详情</h1>

    </header>
    <div class="bann">
        <div class="swiper-container">
            <div class="swiper-wrapper">
                <div class="swiper-slide"><img class="bimg" src="images/bann.jpg"></div>
                <div class="swiper-slide"><img class="bimg" src="images/bann.jpg"></div>
                <div class="swiper-slide"><img class="bimg" src="images/bann.jpg"></div>
            </div>
            <!-- Add Pagination -->
            <div class="swiper-pagination"></div>
        </div>
    </div>
    <div class="protop">
        <h2>巴氏鲜奶 200ML</h2>
        <p>巴氏鲜奶指采用巴氏灭菌法加工的牛奶。巴氏灭菌法是根据对耐高
            温性极强的结核菌热致死曲线和乳质中最易受热影响的奶油分离性
            热</p>
        <table class="prodz" width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td>月单</td>
                <td class="dzmon">￥3.7</td>
            </tr>
            <tr>
                <td>季单</td>
                <td class="dzmon">￥3.5</td>
            </tr>
            <tr>
                <td height="16">半年单</td>
                <td class="dzmon">￥3.2</td>
            </tr>
        </table>
    </div>
    <div class="dnsl pa2t">
        <div class="dnsli clearfix">
            <div class="dnsti">订奶数量：</div>
        <span class="addSubtract">
                  <a class="subtract" href="javascript:;">-</a>
                    <input type="text" value="10" style="ime-mode: disabled;">
                    <a class="add" href="javascript:;">+</a></span>（瓶）
        </div>
        <div class="dnsli clearfix">
            <div class="dnsti">配送规则：</div>
            <select class="dnsel" name="" onChange="javascript:dnsel_changed(this.value)">
                <option selected value="dnsel_item0">天天送</option>
                <option value="dnsel_item1">隔日送</option>
                <option value="dnsel_item2">按周送</option>
                <option value="dnsel_item3">随心送</option>
            </select>
            <div class="clear"></div>
        </div>
        <!-- combo box change -->
        <!-- 天天送 -->
        <div class="dnsli clearfix dnsel_item" id="dnsel_item0">
            <div class="dnsti">每天配送数量：</div>
        <span class="addSubtract">
                  <a class="subtract" href="javascript:;">-</a>
                    <input type="text" value="1" style="ime-mode: disabled;">
                    <a class="add" href="javascript:;">+</a></span>（瓶）
        </div>
        <!--隔日送 -->
        <div class="dnsli clearfix dnsel_item" id="dnsel_item1">
            <div class="dnsti">每天配送数量：</div>
        <span class="addSubtract">
              <a class="subtract" href="javascript:;">-</a>
                <input type="text" value="1" style="ime-mode: disabled;">
                <a class="add" href="javascript:;">+</a></span>（瓶）
        </div>
        <!-- 按周规则 -->
        <div class="dnsli clearfix dnsel_item" id="dnsel_item2">
            <table class="psgzb" width="" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <th scope="col">周一</th>
                    <th scope="col">周二</th>
                    <th scope="col">周三</th>
                    <th scope="col">周四</th>
                    <th scope="col">周五</th>
                    <th scope="col">周六</th>
                    <th scope="col">周日</th>
                </tr>
                <tr height="55px">
                    <td>
                        <div><p>1</p></div>
                        <div><p>cls</p></div>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>
                        <div><p>5</p></div>
                        <div><p>cls</p></div>
                    </td>
                    <td>
                        <div><p>5</p></div>
                        <div><p>cls</p></div>
                    </td>
                </tr>
            </table>
        </div>
        <!-- 随心送 -->
        <div class="dnsel_item" id="dnsel_item3">
            <div id='calendar'></div>
        </div>

        <div class="dnsli clearfix">
            <div class="dnsti">起送时间：</div>
            <input class="qssj" name="" id="datepicker"><!--<input class="qssj" name="" type="date">-->
        </div>
        <div class="dnsall">
            <div class="dnsts">
                订购天数：<span>16天</span>
                <a class="cxsd" href="javascript:void(0);">重新设定</a>
            </div>
            <p>规格：200ML</p>
            <p>保质期：5天</p>
            <p>储藏条件：0-5℃</p>
            <p>包装：玻璃瓶</p>
            <p>配料：有机生牛乳</p>
        </div>
    </div>
    <div class="dnxx">
        <div class="dnxti"><strong>详细介绍</strong>
            <span>DETAILED INTRODUCTION</span>
        </div>
        <div class="nnadv">
            精选内蒙古草原有机牧场自然好牛奶发酵
        </div>
        <dl class="dnsdl clearfix">
            <dt>营养丰富</dt>
            <dd>在原味酸奶的基础上添加"维C之王"的好几山东撒
                发生的都擦碘伏。
            </dd>
        </dl>
        <dl class="dnsdl clearfix dnsdl2">
            <dt>有机牛奶</dt>
            <dd>在原味酸奶的基础上添加"维C之王"的好几山东撒
                发生的都擦碘伏。
            </dd>
        </dl>
        <div class="nnadv2">
            精选内蒙古草原有机牧场自然好牛奶发酵
        </div>

        <div class="nntj pa2t">
            <p><span>条件一：</span>源于有机农业生产体系</p>
            <p><span>条件二：</span>种植、养殖全部过程遵循自然规律、生态规律严禁使用
                化肥。农药。无刺激生长调节剂、催奶剂、食品添加剂
                等人工合成的化学物质</p>
            <p><span>条件二：</span>种植、养殖全部过程遵循自然规律、生态规律严禁使用
                化肥。农药。无刺激生长调节剂、催奶剂、食品添加剂
                等人工合成的化学物质</p>
            <p><span>条件二：</span>种植、养殖全部过程遵循自然规律、生态规律严禁使用
                化肥。农药。无刺激生长调节剂、催奶剂、食品添加剂
                等人工合成的化学物质</p>
        </div>

        <div class="nntip"><p>种植、养殖<span>全部过程遵循</span>自然规律、生态规律严禁使用
                化肥。农药。无刺激生长调节剂、催奶剂、食品添加剂
                等人工合成的化学物质</p>
            <img class="bimg" src="images/bann.jpg">
        </div>

    </div>
    <div class="sppj pa2t">
        <div class="sppti">商品评价</div>
        <ul class="sppul">
            <li>
                <div class="spnum"><span class="spstart"><i></i><i></i><i></i><i></i><i></i></span>137*******125</div>
                <div class="pjxx">
                    牛奶配送人员很守时，每天按时配送，也很贴心的提醒我家里哈登三角符
                    可见哈登哈哈客和卡号的好多号喝酒肯定很
                </div>

            </li>
            <li>
                <div class="spnum"><span class="spstart"><i></i><i></i><i></i><i></i><i class="stno"></i></span>137*******125
                </div>
                <div class="pjxx">
                    牛奶配送人员很守时，每天按时配送，也很贴心的提醒我家里哈登三角符
                    可见哈登哈哈客和卡号的好多号喝酒肯定很
                </div>

            </li>
        </ul>
    </div>
    <div class="he50"></div>
    <div class="dnsbt clearfix">
        <a class="dnsb1" href="javascript:void(0);"><span>50瓶</span>/补选</a>
        <a class="dnsb2" href="审核后订单详情.html">立即订购</a>
    </div>
@endsection
@section('script')


    <script src="js/ui/jquery-ui.js"></script>
    <script src="js/datepicker.js"></script>
    <script src='js/moment.min.js'></script>
    <script src='js/fullcalendar.min.js'></script>

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
                        start: '2016-09-28',
                        type: 'count',

                    },
                    {
                        title: 'cls',
                        start: '2016-09-28',
                        type: 'clear',

                    },
                    {
                        start: '2016-09-28',
                        rendering: 'background',
                        color: '#00a040',
                        type: 'render',
                    }
                ],
                dayClick: function (date, jsEvent, view) {
                    var events = $('#calendar').fullCalendar('clientEvents');
                    var calCountEvent = null;
                    for (var i = 0; i < events.length; i++) {
                        if (moment(date).isSame(moment(events[i].start))) {
                            if (events[i].type == "count") {
                                calCountEvent = events[i];
                                break;
                            }
                        }
                    }
                    if (calCountEvent == null) {
                        var countEvent = new Object();
                        countEvent.start = date;
                        countEvent.title = '1';
                        countEvent.type = 'count';

                        var clearEvent = new Object();
                        clearEvent.start = date;
                        clearEvent.title = 'cls';
                        clearEvent.type = 'clear';

                        var addEventSource = [
                            {
                                title: '1',
                                start: date,
                                type: 'count',

                            },
                            {
                                title: 'cls',
                                start: date,
                                type: 'clear',

                            },
                            {
                                start: date,
                                rendering: 'background',
                                color: '#00a040',
                                type: 'render',
                            }
                        ];

                        $('#calendar').fullCalendar('addEventSource', addEventSource);
                    }
                },
                eventClick: function (calEvent, jsEvent, view) {
                    if (calEvent.type == "count") {
                        calEvent.title = parseInt(calEvent.title) + 1;
                        $('#calendar').fullCalendar('updateEvent', calEvent);
                    }
                    else if (calEvent.type == "clear") {
                        var events = $('#calendar').fullCalendar('clientEvents');
                        var calCountEvent;
                        var renderEvent;
                        for (var i = 0; i < events.length; i++) {
                            if (moment(calEvent.start).isSame(moment(events[i].start))) {
                                if (events[i].type == "count") {
                                    calCountEvent = events[i];
                                }
                                else if (events[i].type == "render") {
                                    renderEvent = events[i];
                                }
                            }
                        }
                        $('#calendar').fullCalendar('removeEvents', calEvent._id);
                        $('#calendar').fullCalendar('removeEvents', calCountEvent._id);
                        $('#calendar').fullCalendar('removeEvents', renderEvent._id);
                    }
                }

            });
            $("table.psgzb td > div").click(function(){
                if($(this).is(":first-child"))
                {
                    $(this).children().html(parseInt($(this).children().html())+1);
                }
                else
                {
                    $(this).parent().html("");
                }
                return false;
            });
            $("table.psgzb td").click(function(){
                if($(this).children().length != 2)
                {
                    $(this).html("<div><p>1</p></div><div><p>cls</p></div>");
                    $(this).children().click(function(){
                        if($(this).is(":first-child"))
                        {
                            $(this).children().html(parseInt($(this).children().html())+1);
                        }
                        else
                        {
                            $(this).parent().html("");
                        }
                        return false;
                    });
                }
            });
            dnsel_changed("dnsel_item0");
        });
    </script>


    <!-- Swiper JS -->
    <script src="js/swiper.min.js"></script>

    <script>
        function dnsel_changed(id) {
            $(".dnsel_item").css("display", "none");
            $("#" + id).css("display", "block");
        }
    </script>
    <script>
        var swiper = new Swiper('.swiper-container', {
            pagination: '.swiper-pagination',
            paginationClickable: true,
            spaceBetween: 30,
        });
    </script>
    <script>

        $(".addSubtract .add").click(function () {
            $(this).prev().val(parseInt($(this).prev().val()) + 1);
        });
        $(".addSubtract .subtract").click(function () {
            if (parseInt($(this).next().val()) > 1) {
                $(this).next().val(parseInt($(this).next().val()) - 1);
                $(this).removeClass("subtractDisable");
            }
            if (parseInt($(this).next().val()) <= 1) {
                $(this).addClass("subtractDisable");
            }
        });
    </script>

@endsection



