var strShowUrl = 'gongchang/xitong/juese';

$(document).on('click','.clickable-row', function (e) {

    var message = document.getElementById('alertMessage');
    message.style.color="#ff6666";
    message.innerHTML="";

    $(this).addClass('active').siblings().removeClass('active');

    var row = $(this);
    role_id=row.attr('idnumber');

    window.location.href = SITE_URL + strShowUrl + '/' + role_id;
});