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