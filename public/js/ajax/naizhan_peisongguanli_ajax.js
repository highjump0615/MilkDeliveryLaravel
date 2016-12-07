

var count = $('#count').val();
var order_totals = [];
var retail_totals = [];
var drink_totals = [];
var group_totals = [];
var channel_totals = [];
var sum_totals = [];
var produced_totals = [];
var not_ordered_totals = [];
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
    not_ordered_totals[i] = 0;
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

    calc_total();
});

function get_product_id(){
    var $datarows = $('#distribute tr.product_id_tr');
    $datarows.each(function(){
        $(this).find('.product_id').each(function(i){
            var current_id = $(this).attr('value');
            product_id[i]=current_id;
        });
    })
}

/**
 * 配送业务订单数量和可配送数量的合计
 */
function calc_order(){
    var $datarows = $('#distribute tr.order_tr');
    $datarows.each(function(){
        // 总数量
        $(this).find('.order').each(function(i){
            var current_val = parseInt($(this).html());
            if(isNaN(current_val)){
                current_val = 0;
            }
            order_totals[i]+=current_val;
        });

        // 订单数量
        $(this).find('.ordered_amount').each(function(i){
            var current_val = parseInt($(this).html());
            if(isNaN(current_val)){
                current_val = 0;
            }
            ordered_amount[i]=current_val;
        });
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
    var retail_count = [];

    // 总数量
    $('#distribute td.retail_sum').each(function(){
        current_val = parseInt($(this).html());
        if(isNaN(current_val)){
            current_val = 0;
            $(this).html('0');
        }
        retail_totals[i] = current_val;
    });

    // 当前数量
    $('#distribute td.retail_origin').each(function(i) {
        current_val = parseInt($(this).html());
        if(isNaN(current_val)){
            current_val = 0;
            $(this).html('0');
        }
        retail_count[i] = current_val;
    });

    // 差量
    $('#distribute td.retail_diff').each(function(i) {
        $(this).html(retail_totals[i] - retail_count[i]);
    });
}

/**
 * 试饮赠品数量合计
 */
function calc_drink(){
    var current_val;
    var drink_count = [];

    // 总数量
    $('#distribute td.drink_sum').each(function(i){
        var current_val = parseInt($(this).html());
        if(isNaN(current_val)){
            current_val = 0;
            $(this).html('0');
        }
        drink_totals[i] += current_val;
    });

    // 当前数量
    $('#distribute td.drink_origin').each(function(i) {
        current_val = parseInt($(this).html());
        if(isNaN(current_val)){
            current_val = 0;
            $(this).html('0');
        }
        drink_count[i] = current_val;
    });

    // 差量
    $('#distribute td.drink_diff').each(function(i) {
        $(this).html(drink_totals[i] - drink_count[i]);
    });
}

/**
 * 团购业务数量合计
 */
function calc_group(){
    var current_val;
    var group_count = [];

    // 总数量
    $('#distribute td.group_sum').each(function(i){
        var current_val = parseInt($(this).html());
        if(isNaN(current_val)){
            current_val = 0;
            $(this).html('0');
        }
        group_totals[i]+=current_val;
    });

    // 当前数量
    $('#distribute td.group_origin').each(function(i) {
        current_val = parseInt($(this).html());
        if(isNaN(current_val)){
            current_val = 0;
            $(this).html('0');
        }
        group_count[i] = current_val;
    });

    // 差量
    $('#distribute td.group_diff').each(function(i) {
        $(this).html(group_totals[i] - group_count[i]);
    });
}

/**
 * 渠道业务数量合计
 */
function calc_channel(){

    var current_val;
    var channel_count = [];

    // 总数量
    $('#distribute td.channel_sum').each(function(i){
        var current_val = parseInt($(this).html());
        if(isNaN(current_val)){
            current_val = 0;
            $(this).html('0');
        }
        channel_totals[i]+=current_val;
    });

    // 当前数量
    $('#distribute td.channel_origin').each(function(i) {
        current_val = parseInt($(this).html());
        if(isNaN(current_val)){
            current_val = 0;
            $(this).html('0');
        }
        channel_count[i] = current_val;
    });

    // 差量
    $('#distribute td.channel_diff').each(function(i) {
        $(this).html(channel_totals[i] - channel_count[i]);
    });
}

/**
 * 总数量合计
 */
function calc_total(){
    // 订单配送量、自营配送量总合计
    var $datarows = $('#distribute tr.sum_tr');
    $datarows.each(function(){
        $(this).find('.sum').each(function(i){
            var current_val = parseInt($(this).html());
            if(isNaN(current_val)){
                current_val = 0;
            }
            sum_totals[i]+=current_val;
        });
    });

    // 签收数量合计
    var $produced_rows = $('#distribute tr.produced_tr');
    $produced_rows.each(function(){
        $(this).find('.produced').each(function(i){
            var current_val = parseInt($(this).html());
            if(isNaN(current_val)){
                current_val = 0;
            }
            produced_totals[i]+=current_val;
        });
    });

    // 自营配送量合计
    var $not_ordered_rows = $('#distribute tr.sum_tr:not(:first)');
    $not_ordered_rows.each(function(){
        $(this).find('.sum').each(function(i){
            var current_val = parseInt($(this).html());
            if(isNaN(current_val)){
                current_val = 0;
            }
            not_ordered_totals[i]+=current_val;
        });
    });

    $('#distribute td.total_sum').each(function(i) {
//				if(produced_totals[i] < sum_totals[i]){
//					$(this).css("background-color","#ff0000");
//					$(this).css("color","#ffffff");
//				}
        $(this).html(sum_totals[i]);
    });

    // 计划差异 = 签收数量合计 - 总合计
    $('#distribute td.plan_sum').each(function(i) {
        $(this).html(produced_totals[i]-sum_totals[i]);
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
        retail_totals[i] = 0;
        drink_totals[i] = 0;
        group_totals[i] = 0;
        channel_totals[i] = 0;
        sum_totals[i] = 0;
        produced_totals[i] = 0;
        not_ordered_totals[i] = 0;
    }
    calc_order();
    calc_retail();
    calc_drink();
    calc_group();
    calc_channel();
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

    $('#distribute .order_tr .editable_amount').each(function(i) {
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