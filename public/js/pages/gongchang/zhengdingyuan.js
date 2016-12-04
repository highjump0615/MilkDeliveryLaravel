/**
 * Created by Administrator on 16/12/4.
 */

var gnCheckerId;

$(document).ready(function() {

    verifyPhone();

    $(document).on('click', '.update-checker', function () {
        console.log('update checker modal dialog');

        gnCheckerId = $(this).val();
        current_row_number = $(this).closest('tr').find('td:first').text();
        var url = API_URL + 'gongchang/jichuxinxi/zhengdingyuan/' + gnCheckerId;

        $.get(url, function (data) {
            //success data
            console.log(data);
            $('#checker_name').val(data.name);
            $('#checker_phone').val(data.phone);
            $('#checker_number').val(data.number);
            if(data.station_id != null){
                $('#checker_station').val(data.station_id);
            }else {
                $('#checker_station').val(-1);
            }

            $('#checker_modal').modal("show");
        });
    });
});

function deleteChecker(checker_id) {
    var url = API_URL + 'gongchang/jichuxinxi/zhengdingyuan';
    $.ajax({
        type: "DELETE",
        url: url + '/' + checker_id,
        success: function (data) {
            console.log(data);
            $("#checker" + checker_id).remove();
        },
        error: function (data) {
            console.log('Error:', data);
        }
    });
}

$(document).on('click', '.delete-checker', function () {
    var checker_id = $(this).val();
    $.confirm({
        icon: 'fa fa-warning',
        title: '征订员删除',
        text:'您要删除征订员吗？',
        confirmButton: "是",
        cancelButton: "不",
        confirmButtonClass: "btn-success",
        confirm: function () {
            deleteChecker(checker_id);
        },
        cancel: function () {
        }
    });
});

$('#post_update_checker').submit(function (e) {

    e.preventDefault();

    console.log("updating checker");
    $.ajax({
        type: 'POST',
        url: API_URL + 'gongchang/jichuxinxi/zhengdingyuan/' + gnCheckerId,
        data: {
            'name': $('#checker_name').val(),
            'phone': $('#checker_phone').val(),
            'number': $('#checker_number').val(),
            'station': $('#checker_station').val()
        },
        success: function (data) {
            console.log(data);

            var role = '';
            role += '<tr id="checker' + data.id + '">';
            role += '<td>' + data.name + '</td>';
            role += '<td>' + data.number + '</td>';
            role += '<td>' + data.phone + '</td>';
            role += '<td>' + data.danwei_name + '</td>';
            role += '<td>';
            role += '<button class="btn btn-sm btn-success update-checker" value="' + data.id + '">修改</button>';
            role += '&emsp;'; role += '<button class="btn btn-sm btn-success delete-checker" value="' + data.id + '">删除</button>';
            role += '</td>';
            role += '</tr>';

            $("#checker" + gnCheckerId).replaceWith( role );
            $('#checker_modal').modal('hide');
        },
        error: function (data) {
            console.log(data);
        }
    });
});

//Print Table Data
$('button[data-action = "print"]').click(function () {

    var printContents;
    printContents = document.getElementById("origin_table").outerHTML;

    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
    location.reload();
});

$('button[data-action="show_selected"]').click(function () {

    console.log("filtering");

    var origin_table = $('#origin_table');
    var checker_filter_table = $('#checker_filter_table');
    var checker_filter_table_tbody = $('#checker_filter_table tbody');

    //get all selection
    var f_name = $('#filter_name').val().trim().toLowerCase();
    var f_number = $('#filter_number').val().trim().toLowerCase();
    var f_station = $('#filter_station').val();

    //show only rows in filtered table that contains the above field value
    var filter_rows = [];
    var i = 0;

    origin_table.find('tbody tr').each(function () {
        var tr = $(this);

        var o_checker_name = tr.find('td.o_checker_name').html().toString().toLowerCase();
        var o_station = tr.find('td.o_checker_station').html().toString().toLowerCase();
        var o_number = tr.find('td.o_checker_number').html().toString().toLowerCase();

        //customer
        if ((f_name != "" && o_checker_name.includes(f_name)) || (f_name == "")) {
            tr.attr("data-show-1", "1");
        } else {
            tr.attr("data-show-1", "0")
        }

        if ((f_number != "" && o_number.includes(f_number)) || (f_number == "")) {
            tr.attr("data-show-2", "1");
        } else {
            tr.attr("data-show-2", "0")
        }

        if ((f_station != "none" && o_station.includes(f_station)) || (f_station == "none")) {
            tr.attr("data-show-3", "1");
        } else {
            tr.attr("data-show-3", "0")
        }


        if ((tr.attr("data-show-1") == "1" ) && (tr.attr("data-show-2") == "1") && (tr.attr("data-show-3") == "1")) {

            console.log("---" + $(tr)[0]);
            filter_rows[i] = $(tr)[0].outerHTML;
            console.log('here?');
            i++;


        } else {
            //tr.addClass('hide');
        }


    });
    console.log('there?');
    origin_table.hide();
    checker_filter_table_tbody.empty();

    var length = filter_rows.length;

    var footable = checker_filter_table.data('footable');

    for (i = 0; i < length; i++) {
        var trd = filter_rows[i];
        footable.appendRow(trd);
    }

    checker_filter_table.show();
});
