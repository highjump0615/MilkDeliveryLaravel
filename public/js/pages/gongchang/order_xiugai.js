/**
 * Created by Administrator on 10/3/2016.
 */


//Change Customer info
$('#customer_form').on("submit", function (e) {
    e.preventDefault();

    var submit_button= $(this).find('button[type="submit"]');

    var changed_custom = false;

    $('.addr_info').each(function () {

        var origin = $(this).data('origin');
        var current = $(this).val();

        if (origin != current) {
            changed_custom = true;
        }
    });

    if (!changed_custom) {
        show_info_msg("没有任何改变");
        return;
    }

    var customer_data = $('#customer_form').serializeArray();

    //get xiaoqu id and include in sendData
    var xiaoqu_id = $('#xiaoqu option:selected').data('xiaoqu-id');
    customer_data.push({'name': 'c_xiaoqu_id', 'value': xiaoqu_id});

    $(submit_button).prop('disabled', true);
    $('#customer_spinner').show().css('display', 'inline-block');

    console.log(customer_data);


    $.ajax({
        type: "POST",
        url: API_URL + "gongchang/dingdan/dingdanxiugai/change_customer",
        data: customer_data,
        success: function (data) {
            console.log(data);

            if (data.status == 'success') {
                $('#customer_spinner').hide();
                $(submit_button).prop('disabled', false);

                show_success_msg('客户更改成功');
                //location.reload();
                // var url = SITE_URL+"gongchang/dingdan/daishenhedingdan";
                // window.location.replace(url);

                //reset station info
                $('#station_name').val(data.station_name);
                $('#station_name').attr('data-stationid', data.station_id);

                station_id = data.station_id;
                milkman_id = data.milkman_id;

                //reset milkman info
                if(data.milkman_name)
                    $('#milkman_name').val(data.milkman_name);

                if(data.milkman_phone)
                    $('#milkman_phone').val(data.milkman_phone);

                //reset product price
                trigger_factory_order_type_change();


            } else {
                $('#customer_spinner').hide();
                $(submit_button).prop('disabled', false);
                if (data.message)
                    show_info_msg(data.message);
            }

        },
        error: function (data) {
            console.log(data);
            show_err_msg('客户更改失败');
            $('#customer_spinner').hide();
            $(submit_button).prop('disabled', false);
        }
    });
});

//change order product

$('#product_table :input').change(function(){

    $('#product_table').attr('data-changed', true);
});

$('#order_submit').click(function(e){

    e.preventDefault();

    //check empty of one product calendar and count_per_day
    var empty_tr = false;
    $('#product_table tbody tr').each(function(){
        if(check_input_empty_for_one_product(this))
            empty_tr = true;
    })
    if (empty_tr)
    {
        show_warning_msg('请填写产品的所有字段');
        return;
    }

    var pass = true;
    //Check the product total amount
    $('.one_product .one_p_amount').each(function(){
        if(!$(this).val())
            pass = false;
    });

    if(!pass)
    {
        show_warning_msg("产品价格不计算")
        return;
    }

    //check all amount is bigger than intial order amount
    var real_total  = parseFloat($('.updated_total_sp').html()) ;
    var origin_total = current_order_total;

    if(real_total > origin_total) {
        show_warning_msg('所有产品价格均超过初始订单金额。');
        return;
    }

    $('#order_form').submit();

})


$('#order_form').on('submit', function(e){

    e.preventDefault();
    var submit_button= $(this).find('button[type="submit"]');

    var current_order_total = parseFloat($('.current_total_sp').html());
    var updated_order_total = parseFloat($('.updated_total_sp').html());
    var sendData = $(this).serializeArray();

    sendData.push({'name': 'milkman_id', 'value': milkman_id});
    sendData.push({'name': 'station_id', 'value': station_id});
    sendData.push({'name':'current_order_total', 'value':current_order_total});
    sendData.push({'name':'updated_order_total', 'value':updated_order_total});

    console.log(sendData);
    $('#order_spinner').show().css('display', 'inline-block');
    $(submit_button).prop('disabled', true);



    $.ajax({
        type: "POST",
        url: API_URL + "gongchang/dingdan/dingdanxiugai/change_order_info",
        data: sendData,
        success: function (data) {
            console.log(data);
            $('#order_spinner').hide();
            $(submit_button).prop('disabled', false);

            if (data.status == 'success') {
                show_success_msg('订单修改成功');

                var url = SITE_URL+"gongchang/dingdan/daishenhedingdan";
                window.location.replace(url);
            } else {
                if (data.message)
                    alert(data.message);

            }

        },
        error: function (data) {
            console.log(data);
            $('#order_spinner').hide();
            $(submit_button).prop('disabled', false);

        }
    });

});