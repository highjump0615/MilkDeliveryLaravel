
var street = [];

$(document).ready(function () {
    $(".chosen-select").chosen();

    verifyPhone();
    verifyIdNum();
});

/**
 * 点击每行配送员信息跳转到配送范围页面，操作列除外
 */
$('.tbl_data tbody tr td:not(:nth-child(7))').on('click', function(e){
    e.preventDefault();
    e.stopPropagation();

    var nId =$(this).parent().data('target');
    window.location = SITE_URL+'naizhan/naizhan/fanwei-chakan/'+nId;
});

$(document).on('change', '#area', function () {
    street = [];
    $('#xiaoqi_table').html("");

    $('#area :selected').each(function (i, selected) {
        street[i] = $(selected).text();
    });

    if(street.length == 0)
        return;


    var url = API_URL + 'naizhan/naizhan/peisongyuan/getXiaoqi';
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    });

    var form_data = {
        street: street
    };

    console.log(street);

    var type = "GET";

    $.ajax({
        type: type,
        url: url,
        data: form_data,
        dataType:'json',
        success: function (data) {
            console.log(data);
            var role = '';
            for(var key in data) {
                var value = data[key];
                role +='<tr><td class="col-sm-3">'+key+'</td><td class="col-sm-9" style="text-align: left">';
                for(i=0;i<value.length; i++){
                    role += '<div class="col-sm-3" style="padding-bottom:5px;">';
                    role += '<label><input type="checkbox"  class="i-checks" name="checkboxlist" value="'+key+" "+value[i]+'">'+' '+''+value[i]+'</label></div>';
                }
                role += '</td></tr>';
            }
            $('#xiaoqi_table').append(role);
        },
        error:function (data) {
            console.log('Error:',data);
        }
    });
});

function hide_street_alert() {
    $('#street_alert').hide();
}

$('#add_milkman').submit(function(e){
    e.preventDefault();

    var name = $('#milkman').val();
    var phone = $('#phone').val();
    var number = $('#number').val();

    checkname();
    if(name == ''){
        $('#name_alert').show();
        return;
    }

    checkphone();
    if(phone == ''){
        $('#phone_alert').show();
        return;
    }

    checknumber();
    if(number == ''){
        $('#number_alert').show();
        return;
    }

    $('#area :selected').each(function (i, selected) {
        street[i] = $(selected).text();
    });

    if(street.length <1){
        $('#street_alert').show();
        return;
    }
    var checkValues = $('input[name=checkboxlist]:checked').map(function()
    {
        return $(this).val();
    }).get();

    if(checkValues.length < 1){
        $('#xiaoqu_alert').show();
        return;
    }
    var url = API_URL + 'naizhan/naizhan/peisongyuan/savePeisongyuan';

    var xiaoqi_val = [];
    for(i = 0; i < checkValues.length; i++){
        var xiaoqi = checkValues[i].split(" ");
        xiaoqi_val[i] = xiaoqi[1];
    }

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    });

    var form_data = {
        name: name,
        phone: phone,
        number: number,
        street: street,
        xiaoqi: checkValues,
    };

    var button = $('#save');
    $(button).prop('disabled', true);

    $.ajax({
        type: "POST",
        url: url,
        data: form_data,
        dataType:'json',
        success: function (data) {
            var current_last_row_number = document.querySelector('#peisongyuan tr:last-child td:first-child').textContent;
            var last_row_number = parseInt(current_last_row_number,10)+1;
            if(isNaN(parseInt(current_last_row_number,10)))
                last_row_number = 1;
            var role = '<tr class="row-hover-light-blue" data-target="'+data+'" id="peisongyuan'+data+'">';
            role += '<td>'+last_row_number+'</td><td>'+name+'</td><td>'+phone+'</td><td>'+number+'</td>';
            role += '<td>'+street+'</td>';
            role += '<td>'+xiaoqi_val+'</td>';
            role += '<td><button class="btn btn-sm btn-success modify" data-toggle="modal" href="#modal-form" value="'+data+'">修改</button>&nbsp;';
            role += '<button class="btn btn-sm btn-success delete disabled" value="'+data+'">删除</button></td>';
            $('#peisongyuan').append(role);
            $('#xiaoqu_alert').hide();

            $('#milkman').val('');
            $('#phone').val('');
            $('#number').val('');
            $('#xiaoqi_table').html("");

            // 清空配送街道输入框
            $("#area").val('').trigger("chosen:updated");

            $(button).prop('disabled', false);
        },
        error:function (data) {
            console.log('Error:',data);
            $(button).prop('disabled', false);
        }
    });
});


$(document).on('click','.modify',function(){
    var milkman_id = $(this).val();

    // 设置修改对话框的内容
    var tr = $(this).parent().parent();

    // 名称
    $('#edit_name').val($(tr).find('td:eq(1)').html());
    // 电话
    $('#edit_phone').val($(tr).find('td:eq(2)').html());
    // 身份证号
    $('#edit_idcard').val($(tr).find('td:eq(3)').html());

    // id
    $('#edit_id').val(milkman_id);

});

$(document).on('click','.delete',function(){
    var milkman_id = $(this).val();
    $.confirm({
        icon: 'fa fa-warning',
        title: '配送员管理',
        text:'您要删除配送员交货区吗？',
        confirmButton: "是",
        cancelButton: "不",
        confirmButtonClass: "btn-success",
        confirm: function () {
            deletefun(milkman_id);
        },
        cancel: function () {
            return;
        }
    });
});

function deletefun(milkman_id) {
    var url = API_URL + 'naizhan/naizhan/peisongyuan/deletePeisongyuan';
    $.ajax({
        type: "DELETE",
        url: url + '/' + milkman_id,
        success: function (data) {
            console.log(data);
            $("#peisongyuan" + milkman_id).remove();
            $('#notification').hide();
        },
        error: function (data) {
            console.log('Error:', data);
            $('#notification').show();
        }
    });
}

//Filter Function
$('button[data-action="show_selected"]').click(function () {

    var view_table = $('#peisongyuan');
    var filter_table = $('#filtered_table');
    var filter_table_tbody = $('#filtered_table tbody');

    //get all selection
    var f_name = $('#filter_milkman').val().trim().toLowerCase();
    var f_number = $('#filter_number').val().trim().toLowerCase();
    var f_area = $('#filter_area').val().trim().toLowerCase();

    //show only rows in filtered table that contains the above field value
    var filter_rows = [];
    var i = 0;

    $('#peisongyuan').find('tbody tr').each(function () {
        var tr = $(this);

        milkman_name = $(this).find('td.name').text().toLowerCase();
        milkman_number = $(this).find('td.number').text().toLowerCase();
        milkman_area = $(this).find('td.area').text().toLowerCase();

        //customer
        if ((f_name != "" && milkman_name.includes(f_name)) || (f_name == "")) {
            tr.attr("data-show-1", "1");
        } else {
            tr.attr("data-show-1", "0")
        }

        if ((f_number != "" && milkman_number.includes(f_number)) || (f_number == "")) {
            tr.attr("data-show-2", "1");
        } else {
            tr.attr("data-show-2", "0")
        }

        if ((f_area != "" && milkman_area.includes(f_area)) || (f_area == "")) {
            tr.attr("data-show-3", "1");
        } else {
            tr.attr("data-show-3", "0")
        }

        if ((tr.attr("data-show-1") == "1" ) && (tr.attr("data-show-2") == "1") && (tr.attr("data-show-3") == "1")) {
            //tr.removeClass('hide');
            $(tr).find('td:eq(0)').html(i+1);
            filter_rows[i] = $(tr)[0].outerHTML;
            i++;
            //filter_rows += $(tr)[0].outerHTML;

        } else {
            //tr.addClass('hide');
        }
    });

    $(view_table).hide();
    $(filter_table_tbody).empty();

    var length = filter_rows.length;

    var footable = $('#filtered_table').data('footable');

    for (i = 0; i < length; i++) {
        var trd = filter_rows[i];
        footable.appendRow(trd);
    }

    $(filter_table).show();

});

$('button[data-action = "print"]').click(function () {

    var od = $('#peisongyuan').css('display');
    var fd = $('#filtered_table').css('display');

    var printContents;
    if (od != "none") {
        printContent('peisongyuan', 0, '');
    }
    else if (fd != "none") {
        printContent('filtered_table', 0, '');
    }
});

$('button[data-action = "export_csv"]').click(function () {

    var sendData = [];

    var i = 0;
    //send order data
    $('#peisongyuan thead tr').each(function () {
        var tr = $(this);
        var trdata = [];

        var j = 0;
        $(tr).find('th').each(function () {
            var td = $(this);
            var td_data = td.html().toString().trim();
            td_data =td_data.split("<");
            // if (td_data.includes('span') || td_data.includes('button') || td_data.includes('href'))
            //     td_data = "";
            trdata[j] = td_data[0];
            j++;
        });
        sendData[i] = trdata;
        i++;
    });

    $('#peisongyuan tbody tr').each(function () {
        var tr = $(this);
        var trdata = [];

        var j = 0;
        $(tr).find('td').each(function () {
            var td = $(this);
            var td_data = td.html().toString().trim();
            if (td_data.includes('span') || td_data.includes('button') || td_data.includes('href'))
                td_data = "";
            trdata[j] = td_data;
            j++;
        });
        sendData[i] = trdata;
        i++;
    });

    var send_data = {"data": sendData};
    console.log(send_data);

    $.ajax({
        type: 'POST',
        url: API_URL + "export",
        data: send_data,
        success: function (data) {
            console.log(data);
            if (data.status == 'success') {
                var path = data.path;
                location.href = path;
            }
        },
        error: function (data) {
            //console.log(data);
        }
    })
});

function checkname() {
    $('#name_alert').hide();
}

function checkphone() {
    $('#phone_alert').hide();
}

function checknumber() {
    $('#number_alert').hide();
}