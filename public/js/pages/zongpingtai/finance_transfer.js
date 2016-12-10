/**
 * Created by Administrator on 16/12/10.
 */

$(document).ready(function () {
    $('#all_check .iCheck-helper').css('display', 'none');

    $('#all_check').click(function () {

        var od = $('#order_table').css('display');

        var check = $(this).find('.i-checks')[0];
        var checked = $(check).prop('checked');
        if (checked) {
            $('#order_table input.i-checks').each(function () {
                $(this).prop("checked", true);
                $(this).closest('.icheckbox_square-green').addClass('checked');
            });

            if (od == "none") {
                $('#filter_table input.i-checks').each(function () {
                    $(this).prop("checked", true);
                    $(this).closest('.icheckbox_square-green').addClass('checked');
                });
            }

        } else {
            $('#order_table input.i-checks').each(function () {
                $(this).prop("checked", false);
                $(this).closest('.icheckbox_square-green').removeClass('checked');
            });

            if (od == "none") {
                $('#filter_table input.i-checks').each(function () {
                    $(this).prop("checked", true);
                    $(this).closest('.icheckbox_square-green').removeClass('checked');
                });
            }
        }
    });

    $('.icheckbox_square-green').click(function () {

        var check = $(this).find('.i-checks')[0];
        var checked = $(check).prop('checked');

        var od = $('#order_table').css('display');

        if (checked) {
            $('#order_table input.i-checks').each(function () {
                $(this).prop("checked", true);
                $(this).addClass('checked');
            });

            if (od == "none") {
                $('#filter_table input.i-checks').each(function () {
                    $(this).prop("checked", true);
                    $(this).addClass('checked');
                });
            }

        } else {
            $('#order_table input.i-checks').each(function () {
                $(this).prop("checked", false);
                $(this).removeClass('checked');
            });

            if (od == "none") {
                $('#filter_table input.i-checks').each(function () {
                    $(this).prop("checked", true);
                    $(this).removeClass('checked');
                });
            }
        }
    });

    $('#insert_modal_form').submit(function (e) {
        e.preventDefault();
        var sendData = $(this).serializeArray();
        console.log(sendData);

        $.ajax({
            type: 'POST',
            url: API_URL + 'zongpingtai/caiwu/zhangwujiesuan/zhangdanzhuanzhang/complete_trans',
            data: sendData,
            success: function (data) {
                if (data.status = "success") {
                    show_success_msg(" 转账账单成功");

                    $('#insert_modal_form').modal('hide');
                    window.location.href = SITE_URL + "zongpingtai/caiwu/zhangwujiesuan/lishizhuanzhangjiru/" + data.factory;
                }
            },
            error: function (data) {
                console.log(data);
            }
        })

    });

    $('button[data-action="insert_modal"]').click(function () {
        var trans_id_list = [];
        //get selected trans id

        var od = $('#order_table').css('display');
        if (od != "none") {
            $('#order_table input.i-checks').each(function () {
                var checked = $(this).prop("checked");
                if (checked) {
                    var sid = $(this).data('station-id');

                    $('#order_table tbody tr td.o_station[data-sid="' + sid + '"]').each(function () {
                        var tid = $(this).data('trsid');
                        trans_id_list.push(tid);
                    });
                }
            });
        } else {
            $('#filter_table input.i-checks').each(function () {
                var checked = $(this).prop("checked");
                if (checked) {
                    var sid = $(this).data('station-id');
                    $('#filter_table tbody tr td.o_station[data-sid="' + sid + '"]').each(function () {
                        var tid = $(this).data('trsid');
                        trans_id_list.push(tid);
                    });
                }
            });
        }

        if (trans_id_list.length == 0) {
            return;
        }

        $('#insert_modal').modal('show');

        var sendData = {'tids': trans_id_list};
        console.log(sendData);

        $.ajax({
            type: "POST",
            url: API_URL + 'zongpingtai/caiwu/zhangwujiesuan/zhangdanzhuanzhang/get_trans_data',
            data: sendData,
            success: function (data) {
                console.log(data);
                if (data.status == "success") {
                    var trs_list = data.trs;

                    var ptable = $('#insert_modal_table');
                    var ptbody = $('#insert_modal_table tbody');
                    $(ptbody).empty();

                    var footable = $(ptable).data('footable');

                    for (var i = 0; i < trs_list.length; i++) {
                        var trs = trs_list[i];
                        var trd = '<tr><td>' + (i + 1) + '</td><td>' + trs[0] + '<input type="hidden" name="delivery_station_id[]" value="' + trs[5] + '">' + '<input type="hidden" name="trs_ids[]" value="' + trs[4] + '"></td><td>' + trs[1] + '</td><td>' + trs[2] + '</td>';
                        trd += '<td>' + trs[3] + '<input type="hidden" name="total_amount[]" value="'+trs[3]+'"></td>';
                        trd += '<td><input type="number" required min="0" step="0.01" name="real_input[]" type="text"/></td><td><input required  name="trans_number[]" type="text"/></td><td><input name="comment[]" type="text"/></td>';
                        footable.appendRow(trd);
                    }
                }

            },
            error: function (data) {
                console.log(data);
            }
        });
    });

    $('button[data-action = "print_modal_data"]').click(function () {
        printContent('print_modal_table', 0, '');
    });

    $('button[data-action="print_modal"]').click(function () {

        var trans_id_list = [];
        //get selected trans id

        var od = $('#order_table').css('display');
        if (od != "none") {
            $('#order_table input.i-checks').each(function () {
                var checked = $(this).prop("checked");
                if (checked) {
                    var sid = $(this).data('station-id');

                    $('#order_table tbody tr td.o_station[data-sid="' + sid + '"]').each(function () {
                        var tid = $(this).data('trsid');
                        trans_id_list.push(tid);
                    });
                }
            });
        } else {
            $('#filter_table input.i-checks').each(function () {
                var checked = $(this).prop("checked");
                if (checked) {
                    var sid = $(this).data('station-id');
                    $('#filter_table tbody tr td.o_station[data-sid="' + sid + '"]').each(function () {
                        var tid = $(this).data('trsid');
                        trans_id_list.push(tid);
                    });
                }
            });
        }

        if (trans_id_list.length == 0) {
            return;
        }

        $('#print_modal').modal('show');

        var sendData = {'tids': trans_id_list};
        console.log(sendData);

        $.ajax({
            type: "POST",
            url: API_URL + 'zongpingtai/caiwu/zhangwujiesuan/zhangdanzhuanzhang/get_trans_data',
            data: sendData,
            success: function (data) {
                console.log(data);
                if (data.status == "success") {
                    var trs_list = data.trs;

                    var ptable = $('#print_modal_table');
                    var ptbody = $('#print_modal_table tbody');
                    $(ptbody).empty();

                    var footable = $(ptable).data('footable');

                    console.log(footable);
                    for (var i = 0; i < trs_list.length; i++) {
                        var trs = trs_list[i];
                        var trd = '<tr><td>' + (i + 1) + '</td><td>' + trs[0] + '</td><td>' + trs[1] + '</td><td>' + trs[2] + '</td><td>' + trs[3] + '</td>';
                        trd += '<td></td><td></td><td></td>';
                        footable.appendRow(trd);
                    }
                }

            },
            error: function (data) {
                console.log(data);
            }
        });
    });

    $('button[data-action="show_selected"]').click(function () {

        var order_table = $('#order_table');
        var filter_table = $('#filter_table');
        var filter_table_tbody = $('#filter_table tbody');

        var f_station = $('#filter_station').val();

        //show only rows in filtered table that contains the above field value
        var filter_rows = [];
        var i = 0;

        $('#order_table').find('tbody tr').each(function () {
            var tr = $(this);
            o_station = tr.find('td.o_station').data('sid');

            if (f_station == "none" || f_station == o_station) {
                tr.attr('data-show-1', '1');
            } else {
                tr.attr('data-show-1', '0');
            }

            if (tr.attr("data-show-1") == "1") {
                //tr.removeClass('hide');
                filter_rows[i] = $(tr)[0].outerHTML;
                i++;
                //filter_rows += $(tr)[0].outerHTML;
            }
            else {
                //tr.addClass('hide');
            }
        });

        $(order_table).hide();
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

        var od = $('#order_table').css('display');
        var fd = $('#filter_table').css('display');

        var sendData = [];

        var i = 0;
        if (od != "none") {
            //send order data
            $('#order_table thead tr').each(function () {
                var tr = $(this);
                var trdata = [];

                var j = 0;
                $(tr).find('th').each(function () {
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

            $('#order_table tbody tr').each(function () {
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


        } else if (fd != "none") {
            //send filter data
            $('#filter_table thead tr').each(function () {
                var tr = $(this);
                var trdata = [];

                var j = 0;
                $(tr).find('th').each(function () {
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

            $('#filter_table tbody tr').each(function () {
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

        } else {
            return;
        }

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

    //Print Table Data
    $('button[data-action = "print"]').click(function () {

        var od = $('#order_table').css('display');
        var fd = $('#filter_table').css('display');

        if (od != "none") {
            printContent('order_table', 0, '');
        }
        else if (fd != "none") {
            printContent('filter_table', 0, '');
        }
    });

});

