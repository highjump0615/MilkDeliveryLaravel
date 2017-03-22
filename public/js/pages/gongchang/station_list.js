
$('button[data-action="delete_station"]').click(function (e) {
    e.preventDefault();
    e.stopPropagation();

    var sid = $(this).data('sid');
    $.confirm({
        icon: 'fa fa-warning',
        title: '删除奶站',
        text: '你会真的删除奶站吗？',
        confirmButton: "是",
        cancelButton: "不",
        confirmButtonClass: "btn-success",
        confirm: function () {
            delete_station(sid);
        },
        cancel: function () {
            return;
        }
    });
});

function delete_station(sid) {
    $.ajax({
        type: "post",
        url: API_URL + 'gongchang/xitong/naizhanzhanghao/delete_station',
        data: {
            'station_id': sid,
        },
        success: function (data) {
            console.log(data);
            if (data.status != "success") {
                alert('奶站删除失败');
            } else {
                alert("奶站已成功删除");
                location.reload();
            }
        },
        error: function (data) {
            console.log('Error:', data);
        }
    });

}

$(document).ready(function () {
    //Switchery
    var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
    elems.forEach(function (html) {
        var switchery = new Switchery(html);
    });
});

$('#origin_table tbody tr td:not(:nth-child(6))').on('click', function(e){
    e.preventDefault();
    e.stopPropagation();

    var tr = $(this).parent();
    var station_id = tr.data('sid');
    if (station_id)
    {
        var url = SITE_URL+"gongchang/xitong/naizhanzhanghao/tianjianaizhanzhanghu/zhanghuxiangqing-chakan/"+station_id;
        window.location.href = url;
    }
});

$('.js-switch').change(function (e) {

    e.preventDefault();
    e.stopPropagation();

    var checked = $(this).prop('checked');
    var sid = $(this).closest('tr').data('sid');

    $.ajax({
        type: "post",
        url: API_URL + 'gongchang/xitong/naizhanzhanghao/change_status_of_station',
        data: {
            'station_id': sid,
            'checked': checked
        },
        success: function (data) {
            console.log(data);
            if (data.status == "success") {

            } else {
                if (data.message) {
                    alert(data.message);
                }
            }
        },
        error: function (data) {
            console.log('Error:', data);
        }
    });
});

//Filter Function
$('button[data-action="show_selected"]').click(function () {

    var origin_table = $('#origin_table');
    var filter_table = $('#filter_table');
    var filter_table_tbody = $('#filter_table tbody');

    //get all selection
    var f_name = $('#filter_name').val().trim().toLowerCase();
    var f_type = $('#filter_type').val();
    var f_province = $('#filter_province').val().trim().toLowerCase();

    //show only rows in filtered table that contains the above field value
    var filter_rows = [];
    var i = 0;

    $('#origin_table').find('tbody tr').each(function () {
        var tr = $(this);
        o_name = tr.find('td.o_name').html().toString().toLowerCase();
        o_type = tr.find('td.o_type').data('id');
        o_province = tr.find('td.o_province').html().toString().toLowerCase();

        //customer
        if ((f_name != "" && o_name.includes(f_name)) || (f_name == "")) {
            tr.attr("data-show-1", "1");
        }
        else {
            tr.attr("data-show-1", "0")
        }

        if ((f_type != "" && o_type == f_type) || (f_type == "") || (f_type == undefined)) {
            tr.attr("data-show-2", "1");
        }
        else {
            tr.attr("data-show-2", "0")
        }

        if ((f_province != "none" && o_province.includes(f_province)) || (f_province == "none")) {
            tr.attr("data-show-3", "1");
        }
        else {
            tr.attr("data-show-3", "0")
        }

        if ((tr.attr("data-show-1") == "1" ) && (tr.attr("data-show-2") == "1") && (tr.attr("data-show-3") == "1")) {
            filter_rows[i] = $(tr)[0].outerHTML;
            i++;
        }
        else {
            //tr.addClass('hide');
        }
    });

    $(origin_table).hide();
    $(filter_table_tbody).empty();

    var length = filter_rows.length;

    var footable = $('#filter_table').data('footable');

    for (i = 0; i < length; i++) {
        var trd = filter_rows[i];
        footable.appendRow(trd);
    }

    $(filter_table).show();

});

//Export
$('button[data-action = "export_csv"]').click(function () {

    var od = $('#origin_table').css('display');
    var fd = $('#filter_table').css('display');

    var sendData = [];

    var i = 0;
    if (od != "none") {
        data_export('origin_table', 0, '', 0, 0);
    }
    else if (fd != "none") {
        data_export('filter_table', 0, '', 0, 0);
    }
});

//Print
$('button[data-action = "print"]').click(function () {

    var od = $('#origin_table').css('display');
    var fd = $('#filter_table').css('display');

    var printContents;
    if (od != "none") {
        printContent('origin_table');
    }
    else if (fd != "none") {
        printContent('filter_table');
    }
});

$('#origin_table .edit_station').on('click', function(e){

    e.preventDefault();
    e.stopPropagation();
    var station_id = $(this).data('sid');

    var url =SITE_URL +'/gongchang/xitong/naizhanzhanghao/naizhanxiugai/'+station_id;

    window.location.href = url;
});
