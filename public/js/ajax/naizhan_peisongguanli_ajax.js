

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
var changed_amount = [];
var ordered_amount = [];
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
    changed_amount[i] = 0;
    ordered_amount[i] = 0;
}

$(document).ready(function(){
    get_product_id();
    calc_order();
    calc_retail();
    calc_drink();
    calc_group();
    calc_channel();
    calc_total();
})

function get_product_id(){
    var $datarows = $('#distribute tr.product_id_tr');
    $datarows.each(function(){
        $(this).find('.product_id').each(function(i){
            var current_id = $(this).attr('value');
            product_id[i]=current_id;
        });
    })
}

function calc_order(){
    var $datarows = $('#distribute tr.order_tr');
    $datarows.each(function(){
        $(this).find('.order').each(function(i){
            var current_val = parseInt($(this).html());
            if(isNaN(current_val)){
                current_val = 0;
            }
            order_totals[i]+=current_val;
        });

        $(this).find('.ordered_amount').each(function(i){
            var current_val = parseInt($(this).html());
            if(isNaN(current_val)){
                current_val = 0;
            }
            ordered_amount[i]=current_val;
        });
    })
    $('#distribute td.order_sum').each(function(i) {
        $(this).html(order_totals[i]);
    })
}

function calc_retail(){
    var $datarows = $('#distribute tr.retail_tr');
    $datarows.each(function(){
        $(this).find('.retail').each(function(i){
            var current_val = parseInt($(this).html());
            if(isNaN(current_val)){
                current_val = 0;
            }
            retail_totals[i]+=current_val;
        });
    })
    $('#distribute td.retail_sum').each(function(i) {
        $(this).html(retail_totals[i]);
    })
}

function calc_drink(){
    var $datarows = $('#distribute tr.drink_tr');
    $datarows.each(function(){
        $(this).find('.drink').each(function(i){
            var current_val = parseInt($(this).html());
            if(isNaN(current_val)){
                current_val = 0;
            }
            drink_totals[i]+=current_val;
        });
    })
    $('#distribute td.drink_sum').each(function(i) {
        $(this).html(drink_totals[i]);
    })
}

function calc_group(){
    var $datarows = $('#distribute tr.group_tr');
    $datarows.each(function(){
        $(this).find('.group').each(function(i){
            var current_val = parseInt($(this).html());
            if(isNaN(current_val)){
                current_val = 0;
            }
            group_totals[i]+=current_val;
        });
    })
    $('#distribute td.group_sum').each(function(i) {
        $(this).html(group_totals[i]);
    })
}

function calc_channel(){
    var $datarows = $('#distribute tr.channel_tr');
    $datarows.each(function(){
        $(this).find('.channel').each(function(i){
            var current_val = parseInt($(this).html());
            if(isNaN(current_val)){
                current_val = 0;
            }
            channel_totals[i]+=current_val;
        });
    })
    $('#distribute td.channel_sum').each(function(i) {
        $(this).html(channel_totals[i]);
    })
}

function calc_total(){
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

    $('#distribute td.plan_sum').each(function(i) {
        $(this).html(produced_totals[i]-sum_totals[i]);
    })
    $('#distribute td.remain_as_order').each(function(i) {
        $(this).html(produced_totals[i]-not_ordered_totals[i]);
        changed_amount[i] = produced_totals[i]-not_ordered_totals[i]-ordered_amount[i];
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
})

$(document).on('click','#save_distribution',function(e){
    var update_url = API_URL + 'naizhan/shengchan/peisongguanli/save_distribution';
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    })

    for(i = 0; i < count; i++){
        var id = $(this).attr('id');
        var station_id = $(this).attr('value');
        var formData = {
            product_id:product_id[i],
            retail:retail_totals[i],
            test_drink:drink_totals[i],
            group_sale:group_totals[i],
            channel_sale:channel_totals[i]
        }
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
})

$(document).on('click','.auto_distribute',function(e){
    for(i = 0; i<count; i++){
        changed_amount[i] = produced_totals[i]-not_ordered_totals[i]-ordered_amount[i]
    }

    $('#changed_distribute tr:not(:first,:last)').each(function(){
        var j = 0;
        var id = $(this).attr('value');
        for(i = 0; i<count; i++){
            if(product_id[i]==id){
                j = i;
            }
        }
        if(parseInt($(this).find('td:eq(6)').text())>parseInt($(this).find('td:eq(7)').text())){
            $(this).find('td:eq(8)').html($(this).find('td:eq(7)').text());
            changed_amount[j]+=parseInt($(this).find('td:eq(6)').text())-parseInt($(this).find('td:eq(7)').text());
        }
    })
    $('#changed_distribute tr:not(:first,:last)').each(function(){
        var j = 0;
        var id = $(this).attr('value');
        for(i = 0; i<count; i++){
            if(product_id[i]==id){
                j = i;
            }
        }
        if(parseInt($(this).find('td:eq(6)').text())<parseInt($(this).find('td:eq(7)').text())){
            if(changed_amount[j]>parseInt($(this).find('td:eq(7)').text())-parseInt($(this).find('td:eq(6)').text())){
                $(this).find('td:eq(8)').html($(this).find('td:eq(7)').text());
                changed_amount[j]-=parseInt($(this).find('td:eq(7)').text())-parseInt($(this).find('td:eq(6)').text());
            }
            else{
                if(changed_amount[j]>0){
                    $(this).find('td:eq(8)').html(parseInt($(this).find('td:eq(6)').text())+changed_amount[j]);
                }
            }
        }
        if(parseInt($(this).find('td:eq(7)').text()) == parseInt($(this).find('td:eq(8)').text())){
            $(this).find('td:eq(11)').html('己调配');
        }
    })
})

$(document).on('click','.shengchan-peisong',function(e){
    var update_url = API_URL + 'naizhan/shengchan/peisongguanli/save_changed_distribution';
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    })

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
        }

        table_info[i] = formData;
        i++;
    })

    var type = "PUT";

    $.ajax({
        type: type,
        url: update_url,
        contentType: 'json',
        processData: false,
        data: JSON.stringify(table_info),
        success: function (data) {
            console.log(data);
            window.location = SITE_URL + "naizhan/shengchan/peisongliebiao";
        },
        error: function (data) {
            console.log('Error:', data);
        }
    });
})