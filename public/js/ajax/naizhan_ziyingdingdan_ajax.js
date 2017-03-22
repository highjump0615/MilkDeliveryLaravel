
var address_message = document.getElementById('address_message');
var message = document.getElementById('alert_message');

$(document).on('click','#but_print',function(e){
    printContent('delivery_milk');
});

$(document).on('click','#save',function(e){
    var url = API_URL + 'naizhan/shengchan/ziyingdingdan/save';
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    });

    e.preventDefault();

    var count = 0;
    $('#delivery_milk #user').each(function(){
        count++;
    });

    // 没有保存的数据，退出
    if (count <= 0) {
        return;
    }

    var order_count = 0;
    var urlTo = SITE_URL + "naizhan/shengchan/jinripeisongdan";

    // 反应正在保存状态
    $(this).attr('disabled', 'disabled');
    $(this).html('正在保存...');

    $('#delivery_milk #user').each(function(){
        order_count++;

        var product_name = $(this).find('td:eq(4)').text();
        var product_id = $(this).find('td:eq(4)').attr('value');
        var product_name_array = product_name.split(',');
        var product_id_array = product_id.split(',');

        for(i=0; i<product_name_array.length-1; i++){
            var product_count = product_name_array[i].split('*');
            product_name_array[i] = product_count[1];
        }

        var formData = {
            customer_name: $(this).find('td:eq(3)').text(),
            address: $(this).find('td:eq(2)').attr('value'),
            type:$(this).find('td:eq(1)').attr('value'),
            phone: $(this).find('td:eq(5)').text(),
            milkman_id: $(this).find('td:eq(6)').attr('value'),
            deliver_time: $(this).find('td:eq(7)').attr('value'),
            comment: $(this).find('td:eq(8)').html(),
            product_id: product_id_array,
            product_count: product_name_array,
        };

        var type = "POST"; //for creating new resource

        $.ajax({
            type: type,
            url: url,
            data: formData,
            dataType: 'json',
            success: function (data) {
                //console.log(data);
                if (count == order_count) {
                    // 跳转到今日配送单
                    // window.location.href = urlTo;

                    // 刷新
                    location.reload();
                }
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
    });
});


$(document).on('click','#add',function(){

    var type = $('#type option:selected').text();
    var type_val = $('#type option:selected').val();
    
    var address = $('#current_district').html()+ $('#address4 option:selected').text()+$('#address5 option:selected').text()+$('#address6').val();
    var address_val = $('#addr_district').val()+' '+$('#address4 option:selected').text()+' '+$('#address5 option:selected').text()+' '+$('#address6').val();

    var customer_name = $('#customer_name').val();

    if(customer_name == ''){
        $('#name_alert').show();
        return;
    }

    // 手机号码验证
    var phone_number = $('#phone_number').val();
    var regExPhone = new RegExp('^1[345678][0-9]{9}$');

    if (!regExPhone.test(phone_number)){
        $('#phone_alert').show();
        return;
    }


    var milkman_name = $('#milkman_name option:selected').text();
    var milkman_id = $('#milkman_name option:selected').val();

    var time = $('#time option:selected').text();
    var time_val = $('#time option:selected').val();

    var product = '';
    var product_id = '';

    var current_last_row_number = 0;
    if(document.querySelector('#delivery_milk tr:last-child td:first-child') != null){
        current_last_row_number = document.querySelector('#delivery_milk tr:last-child td:first-child').textContent;
    }
    var last_row_number = parseInt(current_last_row_number,10)+1;
    if (isNaN(last_row_number)) {
        // 默认序号是1
        last_row_number = 1;
    }

    var total_order_count = 0;
    var error_code = 0;
    $('#product_deliver tr').each(function(){
        var id = $(this).attr('id');
        if($('#amount'+id+'').val()!=''){
            var rest_val = parseInt($('#rest_amount'+id+'').text());
            var current_val = 0;
            if(!isNaN(parseInt($('#amount'+id+'').val()))){
                current_val = Math.min(parseInt($('#amount'+id+'').val()), rest_val);
                total_order_count += current_val;
            }

            // 更新上面表的剩余库存
            if(current_val > 0) {
                $('#rest_amount' + id + '').html(rest_val - current_val);
            }
            else{
                error_code = 1;
                return;
            }

            // 更新上面表的出库
            var prevVal = 0;
            if (type_val == 2) {        // 团购
                prevVal = parseInt($('#group'+id+'').html());
                $('#group'+id+'').html(prevVal + current_val);
            }
            else if (type_val == 3) {   // 渠道
                prevVal = parseInt($('#channel'+id+'').html());
                $('#channel'+id+'').html(prevVal + current_val);
            }
            else if (type_val == 4) {   // 试饮
                prevVal = parseInt($('#test'+id+'').html());
                $('#test'+id+'').html(prevVal + current_val);
            }
            else {                      // 零售
                prevVal = parseInt($('#retail'+id+'').html());
                $('#retail'+id+'').html(prevVal + current_val);
            }

            product +=$('#amount'+id+'').attr('name')+'*'+current_val+','
            product_id +=id+',';
        }
    });

    if(error_code == 1){
        message.innerHTML="(剩余量不足以交付!)";
        return;
    }
    if(total_order_count == 0){
        message.innerHTML="(输入配送内容!)";
        return;
    }

    // 隐藏添加提示行
    showAddNotice(false);

    // 添加数据
    var role = '<tr id="user"><td>'+last_row_number+'</td><td value="'+type_val+'">'+type+'</td><td value="'+address_val+'">'+address+'</td>';
    role+='<td>'+customer_name+'</td><td value="'+product_id+'">'+product+'</td>';
    role+='<td>'+phone_number+'</td><td value="'+milkman_id+'">'+milkman_name+'</td><td value="'+time_val+'">'+time+'</td><td contenteditable="true" class="editfill"></td></tr>';
    $('#delivery_milk').append(role);

    // 清空输入框
    $('#address6').val('');
    $('#customer_name').val('');
    $('#phone_number').val('');

    $('#product_deliver tr').each(function(){
        var id = $(this).attr('id');
        $('#amount'+id+'').val('');
    });
});


$('#phone_number').on('input', function() {
    $('#phone_alert').hide();
    // do something
});

$('#customer_name').on('input', function() {
    $('#name_alert').hide();
    // do something
});

$('.street_list').on("change", function () {

    var street = $(this).find('option:selected').val();
    address_message.innerHTML="";
    var xiaolist = $(this).parent().find('.xiaoqu_list');

    xiaolist.empty();
    if (street == "") {
        address_message.innerHTML="(不是从列表中选择得到街道名称和编号!)";
        return;
    }
    else {
        address_message.innerHTML="";
    }

    var dataString = {"street_name": street};

    $.ajax({
        type: "GET",
        url: API_URL + "naizhan/shengchan/ziyingdingdan/getXiaoqu",
        data: dataString,
        success: function (data) {
            console.log("xiaoqus:" + data.xiaoqus);

            var xiaoqus = data.xiaoqus;
            var xiaodata;

            for (j = 0; j < xiaoqus.length; j++) {
                var xiaoqu = xiaoqus[j];
                xiaodata = '<option value = "' + xiaoqu + '">' + xiaoqu + '</option>';
                xiaolist.append(xiaodata);
            }
        },
        error: function (data) {
            console.log(data);
        }
    });
});

$(document).on('click','#plan_cancel',function () {
    $('#delivery_milk #user').each(function () {
        $(this).remove();
    });
    // $('#delivery_milk').tbody.remove();

    // 跳转到前一页
    // parent.history.back();

    showAddNotice(true);
});

$(document).ready(function(){
//			$('#produced_milk tr:not(:first)').each(function(){
//				var test_drink=0;
//				var group_sale=0;
//				var channel_sale=0;
//				if(!isNaN(parseInt($(this).find('td:eq(2)').text()))){
//					test_drink = parseInt($(this).find('td:eq(2)').text());
//				}
//				if(!isNaN(parseInt($(this).find('td:eq(3)').text()))){
//					group_sale = parseInt($(this).find('td:eq(3)').text());
//				}
//				if(!isNaN(parseInt($(this).find('td:eq(4)').text()))){
//					channel_sale = parseInt($(this).find('td:eq(4)').text());
//				}
//				$(this).find('td:eq(5)').html(test_drink+group_sale+channel_sale);
//			})

    if ($('.street_list').val() != "none")
        $('.street_list').trigger('change');

    showAddNotice(true);
});

function showAddNotice(bShow) {
    if (bShow) {
        $('#delivery_milk #tr_add_notice').show();
    }
    else {
        $('#delivery_milk #tr_add_notice').hide();
    }
}