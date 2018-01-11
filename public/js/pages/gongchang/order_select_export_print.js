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
    });
});

//Pring
$('button[data-action = "print"]').click(function () {
    printContent('order_table', 0, '');
});


/*
 *Redirect Rule
 *
 * Default: xiangqing
 * all page: daishenhe-> daishenhe-dingdanxiangqing
 *           others -> xianqing
 * daishenhe -> daishenhe-dingdanxiangqing
 * weitonggueo -> xiangqing
 * zaipeisong page -> xiangqing
 * xudan page:  -> xudan
 * weiqinai page-> xiangqing
 * zanting page: -> xiangqing
 * weitongguo page-> xiangqing
 *
 */

var url1 = SITE_URL + 'gongchang/dingdan/dingdanluru/xiangqing/';
var url2 = SITE_URL + 'gongchang/dingdan/xudanliebiao/xudan/';
var url3 = SITE_URL + 'gongchang/dingdan/daishenhedingdan/daishenhe-dingdanxiangqing/';

$('body').on('click', '#order_table tbody tr', function () {
    var order_id = $(this).data('orderid');

    var status = $(this).data('status');

    //weiqinai, zanting
    var url = url1 + order_id;

    if (at_page == 'daishenhe' || at_page == "quanbu" && (status == "1" || status == "8"))
    {
        url = url3+order_id;
    }
    else if( at_page == "xudan")
    {
        url =url2+order_id;
    }

    window.location.href = url;

});