$(document).ready(function () {
    $('table.footable').footable({});
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    });
});

function endofweek() {
    var curr = new Date; // get current date
    var first = curr.getDate() - curr.getDay(); // First day is the day of the month - the day of the week
    var last = first + 6; // last day is the first day + 6
    var lastday = new Date(curr.setDate(last));
    var result =  lastday.getFullYear()+"-"+ (lastday.getMonth() + 1) + "-"
        + lastday.getDate();
    return result;
}

function startofweek() {
    var curr = new Date; // get current date
    var first = curr.getDate() - curr.getDay(); // First day is the day of the month - the day of the week
    var firstday = new Date(curr.setDate(first));
    var result = firstday.getFullYear()+ "-"+(firstday.getMonth() + 1) + "-"
        + firstday.getDate();
    return result;
}


function show_dismissable_warning_msg(msg){

    $.notify(msg, 'warn', {'autoHide': false});
}

function show_dismissable_info_msg(msg){

    $.notify(msg, 'info', {'autoHide': false});
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


function endofmonth() {
    var curr = new Date; // get current date
    var lastday = new Date(curr.setDate(30));
    var result = lastday.getFullYear()+"-"+(lastday.getMonth() + 1) + "-"
        + lastday.getDate();
    return result;
}

function startofmonth() {
    var curr = new Date; // get current date
    var firstday = new Date(curr.setDate(1));
    var result =firstday.getFullYear()+ "-" +(firstday.getMonth() + 1) + "-"
        + firstday.getDate();
    return result;
}