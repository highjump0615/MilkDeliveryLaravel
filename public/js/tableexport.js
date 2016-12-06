
function data_export(tablename) {

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

    var send_data = {"data": sendData};

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
function printContent(strId) {

    // 打印
    $('#' + strId).print();
}