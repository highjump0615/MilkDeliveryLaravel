var g_streetsPrev = new Array();

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

/**
 * 选择街道有变化
 */
$(document).on('change', '#area', function () {

    var tblVillage = $('#xiaoqi_table');
    var streets = $(this).find('option:selected');

    var length = streets.length;

    // 添加街道 & 小区
    var i, j;

    for (i = 0; i < length; i++) {
        var id = streets.eq(i).val();
        if (id == undefined) {
            continue;
        }

        // 查看是否已选择的街道
        for (j = 0; j < g_streetsPrev.length; j++) {
            var streetPrev = g_streetsPrev[j];
            if (streetPrev.id == id) {
                break;
            }
        }

        if (j < g_streetsPrev.length) {
            // 已添加的，跳过
            continue;
        }

        // 获取该街道的小区
        var dataString = {"street_id": id};
        $.ajax({
            type: "GET",
            url: API_URL + 'naizhan/naizhan/peisongyuan/getXiaoqi',
            data: dataString,
            success: function (data) {
                console.log(data);

                // 获取街道名称
                var strStreet = '';
                for (i = 0; i < length; i++) {
                    var id = streets.eq(i).val();
                    if (id == data.streetId) {
                        strStreet = streets.eq(i).html();
                        break;
                    }
                }

                // 添加小区
                var villages = data.deliveryArea;
                var role = '<tr id="area' + data.streetId + '"><td class="col-sm-3">' + strStreet + '</td><td class="col-sm-9" style="text-align: left">';

                for (j = 0; j < villages.length; j++) {
                    var village = villages[j];
                    role += '<div class="col-sm-3" style="padding-bottom:5px;">';
                    role += '<label><input type="checkbox"  class="i-checks" name="checkboxlist" value="' + village[0] + '">';
                    role += '<span>' + ' ' + village[1] + '</span></label>';
                    role += '</div>';
                }
                role += '</td></tr>';

                tblVillage.append(role);

                // 添加到主数组
                var street = {id: data.streetId};
                g_streetsPrev.push(street);
            },
            error:function (data) {
                console.log('Error:', data);
            }
        });
    }

    // 删除已取消的街道
    for (i = 0; i < g_streetsPrev.length; i++) {
        id = g_streetsPrev[i].id;

        // 查看是否已取消的街道
        for (j = 0; j < length; j++) {
            if (streets.eq(j).val() == id) {
                break;
            }
        }

        if (j < length) {
            // 没有取消，跳过
            continue;
        }

        // 删除该街道内容
        $('#area' + id).remove();

        // 从主数组删除
        g_streetsPrev.splice(i, 1);
    }

    // 如果没有选择街道，则不用显示小区信息
    if (length == 0) {
        tblVillage.html("");
    }
});

function hide_street_alert() {
    $('#street_alert').hide();
}

/**
 * 添加配送员提交
 */
$('#add_milkman').submit(function(e){
    e.preventDefault();

    var name = $('#milkman').val();
    var phone = $('#phone').val();
    var number = $('#number').val();
    var street = [];

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

    var areaInput = $('input[name=checkboxlist]:checked');
    var checkValues = areaInput.map(function()
    {
        return $(this).val();
    }).get();

    if(checkValues.length < 1){
        $('#xiaoqu_alert').show();
        return;
    }

    var url = API_URL + 'naizhan/naizhan/peisongyuan/savePeisongyuan';

    //
    // 收集奶站名称
    //
    var xiaoqi_val = areaInput.map(function() {
        return $(this).siblings('span').html();
    }).get();

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    });

    var form_data = {
        name: name,
        phone: phone,
        number: number,
        xiaoqi: checkValues
    };

    var button = $('#save');
    button.prop('disabled', true);
    button.html('正在添加...');

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

            restoreSaveButton();
        },
        error:function (data) {
            console.log('Error:',data);

            restoreSaveButton();
        }
    });
});

/**
 * 恢复保存按钮
 */
function restoreSaveButton() {
    var button = $('#save');

    button.prop('disabled', false);
    button.html('<i class="fa fa-plus"></i> 保存并添加');
}

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