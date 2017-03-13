

var count = $('#count').val();
var order_totals = [];
var retail_totals = [];
var drink_totals = [];
var group_totals = [];
var channel_totals = [];
var sum_totals = [];
var produced_totals = [];
var product_id = [];
var remain_amount = [];
var ordered_amount = [];
var delivered_total = [];

for(i = 0; i<count; i++){
    order_totals[i]=0;
    retail_totals[i] = 0;
    drink_totals[i] = 0;
    group_totals[i] = 0;
    channel_totals[i] = 0;
    sum_totals[i] = 0;
    produced_totals[i] = 0;
    product_id[i] = 0;
    remain_amount[i] = 0;
    ordered_amount[i] = 0;
    delivered_total[i] = 0;
}

$(document).ready(function(){
    get_product_id();
    calc_order();
    calc_retail();
    calc_drink();
    calc_group();
    calc_channel();

    // 获取实际配送数量
    $('#distribute td.delivered_sum').each(function(i) {
        var current_val = parseInt($(this).html());
        if(isNaN(current_val)){
            current_val = 0;
        }
        delivered_total[i]=current_val;
    });

    // 当前自营部分数量
    $('#distribute td.origin').each(function(i) {
        var current_val = parseInt($(this).html());
        if(isNaN(current_val)){
            current_val = 0;
            $(this).html('0');
        }
    });

    calc_total();
});

function get_product_id(){
    var $datarows = $('#distribute tr.product_tr');
    $datarows.each(function(i){
        product_id[i] = $(this).find('.product_id').attr('value');
    });
}

/**
 * 配送业务订单数量和可配送数量的合计
 */
function calc_order(){
    var $datarows = $('#distribute tr.product_tr');
    $datarows.each(function(i){
        // 总数量
        $(this).find('.order').each(function(){
            var current_val = parseInt($(this).html());
            if(isNaN(current_val)){
                current_val = 0;
            }
            order_totals[i]+=current_val;
        });

        // 订单数量
        var current_val = parseInt($(this).find('.ordered_amount').html());
        if(isNaN(current_val)){
            current_val = 0;
        }
        ordered_amount[i]=current_val;
    });

    // 显示总合计
    $('#distribute td.order_sum').each(function(i) {
        $(this).html(order_totals[i]);
    })
}

/**
 * 店内零售数量合计
 */
function calc_retail(){
    var current_val;

    // 总数量
    $('#distribute td.retail_sum').each(function(){
        current_val = parseInt($(this).html());
        if(isNaN(current_val)){
            current_val = 0;
            $(this).html('0');
        }
        retail_totals[i] = current_val;
    });
}

/**
 * 试饮赠品数量合计
 */
function calc_drink(){
    var current_val;

    // 总数量
    $('#distribute td.drink_sum').each(function(i){
        var current_val = parseInt($(this).html());
        if(isNaN(current_val)){
            current_val = 0;
            $(this).html('0');
        }
        drink_totals[i] += current_val;
    });
}

/**
 * 团购业务数量合计
 */
function calc_group(){
    var current_val;

    // 总数量
    $('#distribute td.group_sum').each(function(i){
        var current_val = parseInt($(this).html());
        if(isNaN(current_val)){
            current_val = 0;
            $(this).html('0');
        }
        group_totals[i]+=current_val;
    });
}

/**
 * 渠道业务数量合计
 */
function calc_channel(){

    var current_val;

    // 总数量
    $('#distribute td.channel_sum').each(function(i){
        var current_val = parseInt($(this).html());
        if(isNaN(current_val)){
            current_val = 0;
            $(this).html('0');
        }
        channel_totals[i]+=current_val;
    });
}

/**
 * 总数量合计
 */
function calc_total(){

    var $datarows = $('#distribute tr.product_tr');
    $datarows.each(function(i){
        // 订单配送量、自营配送量总合计
        $(this).find('.sum').each(function(){
            var current_val = parseInt($(this).html());
            if(isNaN(current_val)){
                current_val = 0;
            }
            sum_totals[i]+=current_val;
        });

        // 签收数量合计
        $(this).find('.produced').each(function(){
            var current_val = parseInt($(this).html());
            if(isNaN(current_val)){
                current_val = 0;
            }
            produced_totals[i]+=current_val;
        });
    });

    $('#distribute td.total_sum').each(function(i) {
//				if(produced_totals[i] < sum_totals[i]){
//					$(this).css("background-color","#ff0000");
//					$(this).css("color","#ffffff");
//				}
        $(this).html(sum_totals[i]);
    });

    // 当日库存剩余 = 当日奶站可出库数量 - 出库总计 - 可配送数量合计 + 配送业务实际配送数量
    $('#distribute td.remain_sum').each(function(i) {
        remain_amount[i] = produced_totals[i] - sum_totals[i]/* - order_totals[i] + delivered_total[i]*/;
        $(this).html(remain_amount[i]);
    });
}

$('.editable_amount').on('keyup',function(){
    for(i = 0; i<count; i++){
        order_totals[i]=0;
        sum_totals[i] = 0;
        produced_totals[i] = 0;
    }
    calc_order();
    calc_total();
});

function save_distribute() {
    var update_url = API_URL + 'naizhan/shengchan/peisongguanli/save_distribution';
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    });

    for(i = 0; i < count; i++){
        var id = $(this).attr('id');
        var station_id = $(this).attr('value');
        var formData = {
            product_id: product_id[i],
            retail: retail_totals[i],
            test_drink: drink_totals[i],
            group_sale: group_totals[i],
            channel_sale: channel_totals[i],
            remain: remain_amount[i]
        };
        console.log(formData);

        var type = "POST";

        $.ajax({
            type: type,
            url: update_url,
            data: formData,
            dataType: 'json',
            success: function (data) {
                console.log(data);
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
    }
    $(this).hide();
}

/**
 * 自动调配
 */
$(document).on('click','.auto_distribute',function(e){

    var changed_order_amount = [];

    $('#distribute .editable_amount').each(function(i) {
        var current_val = parseInt($(this).html());
        if(isNaN(current_val)){
            current_val = 0;
        }
        changed_order_amount[i] = current_val;
    });

    $('#changed_distribute tr:not(:first,:last)').each(function(){
        var j = 0;
        var id = $(this).attr('value');
        for(i = 0; i<count; i++){
            if(product_id[i]==id){
                j = i;
            }
        }

        // 变化后计划量比原计划量小
        if(parseInt($(this).find('td:eq(6)').text())>parseInt($(this).find('td:eq(7)').text())){
            // 变化后计划量直接填写修改后量
            $(this).find('td:eq(8)').html($(this).find('td:eq(7)').text());

            // 把差额添加到总变化量
            changed_order_amount[j]+=parseInt($(this).find('td:eq(6)').text())-parseInt($(this).find('td:eq(7)').text());
        }
    });

    $('#changed_distribute tr:not(:first,:last)').each(function(){
        var j = 0;
        var id = $(this).attr('value');
        for(i = 0; i<count; i++){
            if(product_id[i]==id){
                j = i;
            }
        }

        // 变化后计划量比原计划量大
        if(parseInt($(this).find('td:eq(6)').text())<parseInt($(this).find('td:eq(7)').text())){
            // 总变化量足够大，变化后计划量直接填写修改后量
            if(changed_order_amount[j] > parseInt($(this).find('td:eq(7)').text())-parseInt($(this).find('td:eq(6)').text())){
                $(this).find('td:eq(8)').html($(this).find('td:eq(7)').text());
                changed_order_amount[j] -= parseInt($(this).find('td:eq(7)').text())-parseInt($(this).find('td:eq(6)').text());
            }
            else{
                // 总变化量不够，变化后计划量填写剩余数量
                if(changed_order_amount[j] >= 0){
                    $(this).find('td:eq(8)').html(parseInt($(this).find('td:eq(6)').text())+changed_order_amount[j]);
                }
            }
        }

        // 满足了变化后计划量，转换状态
        if(parseInt($(this).find('td:eq(7)').text()) == parseInt($(this).find('td:eq(8)').text())){
            $(this).find('td:eq(11)').html('己调配');
        }
    })
});

/**
 * 生成配送列表
 */
$(document).on('click','.shengchan-peisong',function(e){

    // 防止二次点击，把按钮禁止
    $(this).attr('disabled', 'disabled');

    save_distribute();

    var update_url = API_URL + 'naizhan/shengchan/peisongguanli/save_changed_distribution';
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    });

    var table_info = [];
    var i = 0;

    $('#changed_distribute tr:not(:first,:last)').each(function(){
        var id = $(this).attr('id');
        if (id == null) {
            return;
        }

        var formData = {
            delivery_count: parseInt($(this).find('td:eq(8)').text()),
            comment:$(this).find('td:eq(12)').text(),
            id: id
        };

        table_info[i] = formData;
        i++;
    });

    var type = "PUT";

    $.ajax({
        type: type,
        url: update_url,
        contentType: 'json',
        processData: false,
        data: JSON.stringify(table_info),
        success: function (data) {
            console.log(data);
            window.location = SITE_URL + "naizhan/shengchan/jinripeisongdan";
        },
        error: function (data) {
            console.log('Error:', data);
        }
    });
});