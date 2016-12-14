function set_current_menu() {
    //set active for second menu
    for (var i = 0; i < 4; i++) {
        var current_li = $('.menu ul li:eq(' + i + ')');
        
        if (current_menu == i) {
            current_li.addClass('curr');
            current_li.find('a').addClass("curr");
        } else {
            current_li.removeClass('curr');
            current_li.find('a').removeClass("curr");
        }

    }
}

function show_warning_msg(msg){

    $.notify(msg, 'warn');
}

function show_err_msg(msg){

    $.notify(msg, 'error');

}

function show_success_msg(msg){
    $.notify(msg, 'success');

}

function show_info_msg(msg){

    $.notify(msg, 'info');

}

$(".addSubtract .add").click(function () {
    $(this).prev().val(parseInt($(this).prev().val()) + 1);
});
$(".addSubtract .subtract").click(function () {
    if (parseInt($(this).next().val()) > 10) {
        $(this).next().val(parseInt($(this).next().val()) - 1);
        $(this).removeClass("subtractDisable");
    }
    if (parseInt($(this).next().val()) <= 10) {
        $(this).addClass("subtractDisable");
    }
});

$(".addSubtract.deliver_plan_as .subtract").click(function () {
    if (parseInt($(this).next().val()) > 1) {
        $(this).next().val(parseInt($(this).next().val()) - 1);
        $(this).removeClass("subtractDisable");
    }
    if (parseInt($(this).next().val()) <= 1) {
        $(this).addClass("subtractDisable");
    }
});



$(".plus").click(function () {
    $(this).prev().val(parseInt($(this).prev().val()) + 1);
    if(parseInt($(this).prev().val()) >1 )
    {
        $(this).parent().find('.minus').removeClass("minusDisable");
    }
});
$(".minus").click(function () {
    if (parseInt($(this).next().val()) > 1) {
        $(this).next().val(parseInt($(this).next().val()) - 1);
        $(this).removeClass("minusDisable");
    }
    if (parseInt($(this).next().val()) <= 1) {
        $(this).addClass("minusDisable");
    }
});