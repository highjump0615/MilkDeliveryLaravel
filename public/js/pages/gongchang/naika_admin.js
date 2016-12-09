
var quantity = 0;
var start_number = 0;
var end_number = 0;

var carddata;

$(function(){
    $('#csv_file_upload_btn').click(function(){
        $('#csv_file_upload_input').click();
    });

    $('#csv_file_upload_input').change(function(){
        console.log('uploading');
        $('#upload-form').submit();
    });
});

/**
 * 更新数量和金额
 */
function calcCountCost() {
    var i;
    var nCount = 0;
    var nCost = 0;
    var strStartNum = $('#start_number').val();
    var strEndNum = $('#end_number').val();

    var nStartIndex = -1, nEndIndex = -1;

    // 现决定范围
    for (i = 0; i < carddata.length; i++) {
        if (carddata[i].number == strStartNum) {
            nStartIndex = i;
        }
        if (carddata[i].number == strEndNum) {
            nEndIndex = i;
            break;
        }
    }

    // 计算数量和金额
    if (nStartIndex >= 0 && nEndIndex >= 0 && nEndIndex >= nStartIndex) {
        for (i = nStartIndex; i <= nEndIndex; i++) {
            nCount++;
            nCost += carddata[i].balance;
        }
    }

    $('#quantity').val(nCount);
    $('#total_amount').html(nCost);
}

/**
 * 获取奶卡信息
 */
function getCardInfo() {
    var url = API_URL + 'gongchang/get_naika_info'
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    })
    // var balance = parseInt($('#balance_value option:selected').text());
    var dataString = {'balance': 0};
    $.ajax({
        type: "GET",
        url: url,
        data: dataString,
        success: function (data) {
            console.log(data);

            carddata = data;

            quantity = data.length;
            if(quantity != 0){
                // $('#total_amount').html(quantity * balance);
                start_number = data[0].number;
                end_number = data[quantity-1].number;
                $('#start_number').val(start_number);
                $('#end_number').val(end_number);
                calcCountCost();
                // $('#quantity').prop('disabled',false);
                // $('#total_amount').prop('disabled',false);
                // $('#start_number').prop('disabled',false);
                // $('#end_number').prop('disabled',false);
                $('#max_quantity').val(quantity);
                $('#submit').show();
            }
            // else {
            //     $('#quantity').val(quantity);
            //     $('#total_amount').html(quantity * balance);
            //     start_number = data.start_number;
            //     end_number = data.end_number;
            //     $('#start_number').val(start_number);
            //     $('#end_number').val(parseInt(start_number) + parseInt(quantity));
            //     $('#quantity').prop('disabled',true);
            //     $('#total_amount').prop('disabled',true);
            //     $('#start_number').prop('disabled',true);
            //     $('#end_number').prop('disabled',true);
            //     $('#submit').hide();
            // }
        },
        error: function (data) {
            console.log(data);
        }
    });
}

$(document).ready(function () {
});

$(document).on('click','#but_sell',function () {
    getCardInfo();
});

// $(document).on('change','#balance_value',function () {
//     var url = API_URL + 'gongchang/get_naika_info'
//     $.ajaxSetup({
//         headers: {
//             'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
//         }
//     })
//     var balance = parseInt($('#balance_value option:selected').text());
//     var dataString = {'balance': balance};
//     $.ajax({
//         type: "GET",
//         url: url,
//         data: dataString,
//         success: function (data) {
//             console.log(data);
//             quantity = data.count;
//             if(quantity != 0){
//                 $('#quantity').val(quantity);
//                 $('#total_amount').html(quantity * balance);
//                 start_number = data.start_number;
//                 end_number = data.end_number;
//                 $('#start_number').val(start_number);
//                 $('#end_number').val(parseInt(start_number) + parseInt(quantity)-1);
//                 $('#quantity').prop('disabled',false);
//                 $('#total_amount').prop('disabled',false);
//                 $('#start_number').prop('disabled',false);
//                 $('#end_number').prop('disabled',false);
//                 $('#max_quantity').val(quantity);
//                 $('#submit').show();
//             }
//             else {
//                 $('#quantity').val(quantity);
//                 $('#total_amount').html(quantity * balance);
//                 start_number = data.start_number;
//                 end_number = data.end_number;
//                 $('#start_number').val(start_number);
//                 $('#end_number').val(parseInt(start_number) + parseInt(quantity));
//                 $('#quantity').prop('disabled',true);
//                 $('#total_amount').prop('disabled',true);
//                 $('#start_number').prop('disabled',true);
//                 $('#end_number').prop('disabled',true);
//                 $('#submit').hide();
//             }
//         },
//         error: function (data) {
//             console.log(data);
//         }
//     });
// })

function isNumber(evt) {
    $('#quantity_alert').hide();
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    var quantity =$('#quantity').val();
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    }
    if(parseInt(quantity)<0){
        return false;
    }
    return true;
}

// $(document).on('change','#quantity',function () {
//     var current_quantity = $(this).val();
//     if(parseInt($(this).val())>$('#max_quantity').val()){
//         $('#quantity_alert').show();
//         $(this).html(current_quantity);
//     }
//     else {
//         $('#quantity_alert').hide();
//         var balance = parseInt($('#balance_value option:selected').text());
//         var quantity = parseInt($(this).val());
//         $('#total_amount').html(quantity * balance);
//         var start_number = parseInt($('#start_number').val());
//         var end_number = quantity + start_number-1;
//         $('#end_number').val(end_number);
//     }
// })

$(document).on('keyup','#start_number',function () {
    // var quantity = parseL($(this).val());
    // var start_number = parseLong($('#start_number').val());
    // var end_number = quantity + start_number-1;
    // $('#end_number').val(end_number);
    calcCountCost();
});

$(document).on('keyup','#end_number',function () {
    calcCountCost();
});

//Filter Function
$(document).on('click','#search',function () {

    var card_table = $('#cardTable');
    var filter_table = $('#filteredTable');
    var filter_table_tbody = $('#filteredTable tbody');

    //get all selection
    var f_cardnumber = $('#card_number').val().trim().toLowerCase();
    var f_balance = $('#balance').val().trim().toLowerCase();
    var f_recipient = $('#recipient').val().trim().toLowerCase();

    //show only rows in filtered table that contains the above field value
    var filter_rows = [];
    var i = 0;

    $('#cardTable').find('tbody tr').each(function () {
        var tr = $(this);

        cardnumber = $(this).find('td.number').text().toLowerCase();
        balance = $(this).find('td.balance').text().toLowerCase();
        recipient = $(this).find('td.recipient').text().toLowerCase();

        //customer
        if ((f_cardnumber != "" && cardnumber.includes(f_cardnumber)) || (f_cardnumber == "")) {
            tr.attr("data-show-1", "1");
        } else {
            tr.attr("data-show-1", "0")
        }

        if ((f_balance != "" && balance.includes(f_balance)) || (f_balance == "")) {
            tr.attr("data-show-2", "1");
        } else {
            tr.attr("data-show-2", "0")
        }

        if ((f_recipient != "" && recipient.includes(f_recipient)) || (f_recipient == "")) {
            tr.attr("data-show-3", "1");
        } else {
            tr.attr("data-show-3", "0")
        }

        if ((tr.attr("data-show-1") == "1" ) && (tr.attr("data-show-2") == "1") && (tr.attr("data-show-3") == "1")) {
            //tr.removeClass('hide');
            // if($(tr)[0].cells.length==10){
            //     $(tr).find('td:eq(0)').remove();
            // }
            $(tr).find('td:eq(0)').html(i+1);
            filter_rows[i] = $(tr)[0].outerHTML;
            i++;
            //filter_rows += $(tr)[0].outerHTML;

        } else {
            //tr.addClass('hide');
        }
    });

    $(card_table).hide();
    $(filter_table_tbody).empty();

    var length = filter_rows.length;

    var footable = $('#filteredTable').data('footable');

    for (i = 0; i < length; i++) {
        var trd = filter_rows[i];
        footable.appendRow(trd);
    }
    $(filter_table).show();

});

$('button[data-action = "print"]').click(function () {

    var od = $('#cardTable').css('display');
    var fd = $('#filteredTable').css('display');

    if (od != "none") {
        printContent('cardTable', gnUserTypeFactory, '奶卡管理');
    }
    else if (fd != "none") {
        //print filter data
        printContent('filteredTable', gnUserTypeFactory, '奶卡管理');
    }
});

$('#submit').click(function (event) {
    $('#card_number_alert').hide();
    if($('#name').val() == ''){
        $('#name_alert').show();
        event.preventDefault();
    }
    if(parseInt($('#quantity').val())>$('#max_quantity').val()){
        $('#quantity_alert').show();
        $('#quantity').html($('#max_quantity').val());
        event.preventDefault();
    }
})
// $(document).on('click','#submit',function () {
//
// })

function hide_alert() {
    $('#name_alert').hide();
}