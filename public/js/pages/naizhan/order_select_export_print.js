$(document).ready(function () {
    $('#data_range_select .input-daterange').datepicker({
        keyboardNavigation: false,
        forceParse: false,
        autoclose: true,
        calendarWeeks: false,
        clearBtn: true,
    });

    $('.single_date').datepicker({
        todayBtn: false,
        keyboardNavigation: false,
        forceParse: false,
        calendarWeeks: false,
        autoclose: true,
        clearBtn:true,
        startDate: new Date(),
    });
});

//Export
$('button[data-action = "export_csv"]').click(function () {

    var od = $('#order_table').css('display');
    var fd = $('#filter_table').css('display');

    if (od != "none") {
        data_export('order_table', 0, '', 0, 0);
    }
    else if (fd != "none") {
        data_export('filter_table', 0, '', 0, 0);
    }
});

//Pring
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


/*
 *Redirect Rule
 *
 * Default: xiangqing
 * all page: -> xianqing
 * daishenhe -> xiangqing
 * weitonggueo -> xiangqing
 * zaipeisong page -> xiangqing
 * xudan page:  -> xudan
 * weiqinai page-> xiangqing
 * zanting page: -> xiangqing
 * weitongguo page-> xiangqing
 *
 */

var url1 = SITE_URL + 'naizhan/dingdan/xiangqing/';
var url2 = SITE_URL + 'naizhan/dingdan/luruxudan/';

$('body').on('click', '#order_table tbody tr', function () {
    var order_id = $(this).data('orderid');
    var url = url1 + order_id;

    if( typeof at_page !== 'undefined' && at_page == "xudan")
    {
        url =url2+order_id;
    }

    window.location.href = url;

});

$('body').on('click', ('#filter_table tbody tr'), function () {

    var order_id = $(this).data('orderid');

    var url = url1 + order_id;

    if( typeof at_page !== 'undefined' && at_page == "xudan")
    {
        url =url2+order_id;
    }

    window.location.href = url;
});
