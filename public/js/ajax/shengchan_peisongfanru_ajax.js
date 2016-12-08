var current_row_number;


$(document).on('change','#milkman_name',function(){
    var milkman_id = $('#milkman_name option:selected').val();
    var current_date = $('#search_date').val();
    window.location.href = SITE_URL+"naizhan/shengchan/peisongfanru?milkman_id="+milkman_id+"&current_date="+current_date+"";
});

$(document).on('change','#date_select',function(){
    var milkman_id = $('#milkman_name option:selected').val();
    var current_date = $('#search_date').val();
    window.location.href = SITE_URL+"naizhan/shengchan/peisongfanru?milkman_id="+milkman_id+"&current_date="+current_date+"";
});

$(document).on('click','#save',function(){
    confirmdelivery();
    savebottlebox();
    $(this).hide();
});

function savebottlebox() {

    var url = API_URL + 'naizhan/shengchan/peisongfanru/bottleboxsave';
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    });

    var milkman_id = $('#current_milkman_id').val();
    $('#refund_bottle tr:not(:first)').each(function () {
        var bottle_type = $(this).attr('id');
        var count = parseInt($(this).find('td:eq(1)').text());

        var form_data = {
            milkman_id: milkman_id,
            bottle_type: bottle_type,
            count: count
        };

        var type = "POST";

        $.ajax({
            type: type,
            url: url,
            data: form_data,
            dataType:'json',
            success: function (data) {
                console.log(data);
            },
            error:function (data) {
                console.log('Error:',data);
            }
        });
    })
}

function confirmdelivery() {
    var url = API_URL + 'naizhan/shengchan/peisongfanru/confirmdelivery';
    var milkman_id = $('#current_milkman_id').val();

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    });

    var table_info = [];
    var i = 0;
    $('#delivery_table tr.order_info').each(function () {
        var order_id = $(this).attr('id');
        var oder_product_id = $(this).find('.delivered_count').attr('id');
        var delivered_product_count = $(this).find('.delivered_count').text();
        var delivery_type = $(this).attr('ordertype');
        var report = $(this).find('.report').text();
        if(isNaN(parseInt(delivered_product_count)) || delivered_product_count == ''){
            delivered_product_count = 0;
        }
        var formData = {
            milkman_id: milkman_id,
            order_product_id: oder_product_id,
            delivered_count: delivered_product_count,
            delivery_type: delivery_type,
            report: report,
            order_id:order_id,
        };
        table_info[i] = formData;
        i++;
    });
    var send_type = "POST";

    $.ajax({
        type: send_type,
        url: url,
        contentType: 'json',
        processData: false,
        data: JSON.stringify(table_info),
        success: function (data) {
            console.log(data);
            location.reload();
        },
        error:function (data) {
            console.log('Error:',data);
        }
    });
}

$('#date_select .input-group.date').datepicker({
    todayBtn: "linked",
    keyboardNavigation: false,
    forceParse: false,
    calendarWeeks: false,
    autoclose: true
});

$('button[data-action = "print"]').click(function () {

    var sendData = [];

    var printContents;

    printContents = document.getElementById("delivered_info").outerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;

    window.print();
    document.body.innerHTML = originalContents;
    location.reload();
});

$('#return').click(function () {
    window.location = SITE_URL + "naizhan/shengchan/peisongliebiao";
})