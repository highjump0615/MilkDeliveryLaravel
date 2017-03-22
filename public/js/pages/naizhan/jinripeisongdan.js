/**
 * Created by Administrator on 2/5/17.
 */
$(document).ready(function(){
    /**
     * 计算配送统计的合计
     */
    $('.delivery_amount tr:not(:first)').each(function(){
        var plan_sale=0;
        var test_drink=0;
        var group_sale=0;
        var channel_sale=0;
        var store_sale=0;

        if(!isNaN(parseInt($(this).find('td:eq(1)').text()))){
            plan_sale = parseInt($(this).find('td:eq(1)').text());
        }
        if(!isNaN(parseInt($(this).find('td:eq(2)').text()))){
            test_drink = parseInt($(this).find('td:eq(2)').text());
        }
        if(!isNaN(parseInt($(this).find('td:eq(3)').text()))){
            group_sale = parseInt($(this).find('td:eq(3)').text());
        }
        if(!isNaN(parseInt($(this).find('td:eq(4)').text()))){
            channel_sale = parseInt($(this).find('td:eq(4)').text());
        }
        if(!isNaN(parseInt($(this).find('td:eq(5)').text()))){
            store_sale = parseInt($(this).find('td:eq(5)').text());
        }
        $(this).find('td:eq(6)').html(plan_sale + test_drink + group_sale + channel_sale + store_sale);
    })
});

$(document).on('change','#milkman_name',function () {
    $('.milkman_plans').hide();
    var currentMilkman = $('#milkman_name option:selected').val();
    $('#milkman'+currentMilkman+'').show();
});

$('button[data-action = "print"]').click(function () {

    var sendData = [];

    var printContents;

    printContents = document.getElementById("deliver_info").outerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;

    window.print();
    document.body.innerHTML = originalContents;
    location.reload();
});

$('#return').click(function () {
    window.location = SITE_URL + "naizhan/shengchan/peisongliebiao";
});