var user_id;
var limit_money = parseFloat($('#init_business_credit_amount').val()*0.9,2) + parseFloat($('#business_credit_balance').val(),2);
var used_money;

/**
 * 计算提交计划相关的数量和金额
 */
function setPlanData() {
    $('tr:not(:first)').each(function(){
        // 自营数量合计
        var sum = 0;
        var price = $(this).find('.current_price').val();
        var ordered_price = $(this).find('.ordered_price').val();
        $(this).find('.sales_val').each(function(){
            var plan_val = $(this).text();

            if(!isNaN(plan_val)&& plan_val.length!==0){
                sum+=parseFloat(plan_val,2);
            }
        })

        var ordered_count = parseInt($(this).find('.ordered_count').text());

        // 自营项目数量
        var selfuse_count = sum - ordered_count;
        $('.total_count',this).html(selfuse_count);

        // 自营项目金额合计
        var total_price = selfuse_count * price;
        $('.total_price',this).html(total_price.toFixed(2));

        // 订单数量合计
        $('.total_count_all',this).html(sum);

        // 全部金额
        total_price += parseFloat(ordered_price,2);
        $('.total_price_all',this).html(total_price.toFixed(2));
    });

    $('tr:last td:not(:first)').text(function(i){
        var t = 0;
        $(this).parent().prevAll().find('td:nth-child('+(i+5)+')').each(function(){
            t += parseFloat($(this).text(),2)||0;
        });
        return Number(t.toFixed(2));
    })
}

$(document).ready(function() {
    $('.footable').footable();

    setPlanData();
});

$(document).on('keyup','.sales_val',function(){
    setPlanData();
});

$(document).on('click','.confirm_submit',function(e){
    used_money = $('#total_amount').text() - $('#total_ordered_money').val();
    var status = 1;
    if(used_money > parseFloat(limit_money,2)){
        $.confirm({
            icon: 'fa fa-warning',
            title: '信用余额不足',
            text:'你的钱不足以生产这些产品! 信用商业钱超过。你想发送计划？',
            confirmButton: "是",
            cancelButton: "不",
            confirmButtonClass: "btn-success",
            confirm: function () {
                send_plan();
            },
            cancel: function () {
            }
        });
        status = 2;
    }else {
        send_plan();
    }
});

function send_plan() {
    used_money = $('#total_amount').text() - $('#total_ordered_money').val();
    var status = 1;
    if(used_money > parseFloat(limit_money,2)){
        status = 2;
    }
    var url = API_URL + 'naizhan/shengchan/tijiaojihua/store';

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    });

    $('.confirm_submit').prop("disabled",true);

    var table_info = [];
    var i = 0;
    $('tr:not(:first,:last)').each(function(){
        var id = $(this).find('#product_id').val();

        if($('#subtotal_count'+id+'').text() != 0) {
            var formData = {
                status: status,
                product_id: $('#current_id' + id + '').val(),
                order_count: $('#ordered_count' + id + '').text(),
                retail: $('#retail' + id + '').text(),
                test_drink: $('#test_drink' + id + '').text(),
                group_sale: $('#group' + id + '').text(),
                channel_sale: $('#channel' + id + '').text(),
                subtotal_count: $('#subtotal_count' + id + '').text(),
                subtotal_money: $('#subtotal_price' + id + '').text(),
            };
            table_info[i] = formData;
            i++;
        }
    });

    var type = "POST"; //for creating new resource
    var my_url = url;

    $.ajax({
        type: type,
        url: my_url,
        contentType: 'json',
        processData: false,
        data: JSON.stringify(table_info),
        success: function (data) {
            $('.confirm_submit').hide();
            $('.modify').show();
            $('#balance').replaceWith('<label id="balance" class="col-lg-3" style="font-size:20px; color:white; background-color:#ff0000;">自营业务余额：'+data.business_balance+'</label>')
            console.log(data);
        },
        error: function (data) {
            console.log('Error:', data);
        }
    });
}

$(document).on('click','.modify',function(e){
    used_money = $('#total_amount').text() - $('#total_ordered_money').val();
    var status = 1;
    if(used_money > parseFloat(limit_money,2)){
        $.confirm({
            icon: 'fa fa-warning',
            title: '警报',
            text:'你的钱不足以生产这些产品! 信用商业钱超过。你想发送计划？',
            confirmButton: "是",
            cancelButton: "不",
            confirmButtonClass: "btn-success",
            confirm: function () {
                modify_plan();
                return;
            },
            cancel: function () {
                return;
            }
        });
        status = 2;
    }else {
        modify_plan();
    }
})

function modify_plan() {
    used_money = $('#total_amount').text() - $('#total_ordered_money').val();
    var status = 1;
    if(used_money > parseFloat(limit_money,2)){

        status = 2;
    }
    var url = API_URL + 'naizhan/shengchan/tijiaojihua/modify';

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    })


    $('.modify').prop("disabled",true);

    var table_info = [];
    var i = 0;
    $('tr:not(:first,:last)').each(function(){
        var id = $(this).find('#product_id').val();

        if($('#subtotal_count'+id+'').text() != 0) {
            var formData = {
                status: status,
                product_id: $('#current_id' + id + '').val(),
                order_count: $('#ordered_count' + id + '').text(),
                retail: $('#retail' + id + '').text(),
                test_drink: $('#test_drink' + id + '').text(),
                group_sale: $('#group' + id + '').text(),
                channel_sale: $('#channel' + id + '').text(),
                subtotal_count: $('#subtotal_count' + id + '').text(),
                subtotal_money: $('#subtotal_price' + id + '').text(),
            }
            table_info[i] = formData;
            i++;
        }
    })

    var type = "POST"; //for creating new resource
    var my_url = url;

    $.ajax({

        type: type,
        url: my_url,
        contentType: 'json',
        processData: false,
        data: JSON.stringify(table_info),
        success: function (data) {
            $('.confirm_submit').hide();
            $('.modify').prop("disabled",false);
            $('#balance').replaceWith('<label id="balance" class="col-lg-3" style="font-size:20px; color:white; background-color:#ff0000;">自营业务余额：'+data.business_balance+'</label>')
            console.log(data);
        },
        error: function (data) {
            console.log('Error:', data);
        }
    });
}