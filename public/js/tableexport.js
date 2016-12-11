
function data_export(tablename, usertype, pagename) {

    var sendData = [];

    var i = 0;

    //send order data
    $('#' + tablename + ' thead tr').each(function () {
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

    $('#' + tablename + ' tbody tr').each(function () {
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

    //
    // 添加系统日志
    //
    var send_data = {
        'data': sendData,
        'usertype': usertype,
        'page': pagename
    };

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
    });
}

/**
 * 打印
 * @param strId
 */
function printContent(strId, usertype, pagename) {

    // 打印
    $('#' + strId).print();

    // 没有用户类型，退出
    if (usertype <= 0) {
        return;
    }

    //
    // 添加系统日志
    //
    var send_data = {
        'usertype': usertype,
        'page': pagename
    };

    $.ajax({
        type: 'POST',
        url: API_URL + "printlog",
        data: send_data,
        success: function (data) {
        },
        error: function (data) {
        }
    });
}