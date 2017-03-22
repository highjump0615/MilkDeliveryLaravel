
function checkusername()
{
    document.getElementById('userconfirmMessage').innerHTML="";
}

function clearalert() {
    document.getElementById('passwordMessage').innerHTML="";
}

function clearhistory() {
    $('#btn-save').val("add");
    $('#modal-form').trigger("reset");
    $('#username').val("");
    $('#password').val("");
    $('#password2').val("");
    $('#confirmMessage').html("");
    $('#nickname').val("");
    document.getElementById('userconfirmMessage').innerHTML="";
    document.getElementById('passwordMessage').innerHTML="";
    document.getElementById('confirmMessage').innerHTML="";
    document.getElementById('password2').style.backgroundColor = "#ffffff";
}

$(document).ready(function(){

    $('.kv-fa').rating({
        theme: 'krajee-fa',
        filledStar: '<i class="fa fa-star"></i>',
        emptyStar: '<i class="fa fa-star-o"></i>'
    });

    $('#data_5 .input-daterange').datepicker({
        keyboardNavigation: false,
        forceParse: false,
        autoclose: true
    });

    $('.rating,.kv-gly-star,.kv-gly-heart,.kv-uni-star,.kv-uni-rook,.kv-fa,.kv-fa-heart,.kv-svg,.kv-svg-heart').on('change', function () {
        console.log('Rating selected: ' + $(this).val());
    });

    $('.footable').footable();

});

$(document).on('click','.pass',function(){
    var review_id = $(this).val();
    var url = API_URL + 'gongchang/pingjia/pingjialiebiao/pass';
    var formData = {
        id: review_id,
    }
    $.ajax({
        type: "POST",
        url: url,
        data: formData,
        dataType: 'json',
        success: function (data) {
            console.log(data);
            var detail = SITE_URL + '/gongchang/pingjia/pingjiaxiangqing/' + data.id;
            var role1 = '<td>通过</td>';
            var role2='<td><a href="'+detail+'" class="btn btn-success btn-sm">查看</a>&nbsp;';
            role2+='<button data-toggle="modal" href="#modal-form" class="btn btn-success btn-sm modify" value="'+data.id+'">修改</button>&nbsp;';
            role2+='<button class="btn btn-success btn-sm remove" value="'+data.id+'">删除</button></td>';

            $("#review" + review_id).find('td:eq(3)').replaceWith(role1);
            $("#review" + review_id).find('td:eq(4)').replaceWith(role2);
        },
        error: function (data) {
            console.log('Error:', data);
        }
    });
});

$(document).on('click','.isolate',function(){
    var review_id = $(this).val();
    var url = API_URL + 'gongchang/pingjia/pingjialiebiao/isolate';
    var formData = {
        id: review_id,
    }
    $.ajax({
        type: "POST",
        url: url,
        data: formData,
        dataType: 'json',
        success: function (data) {
            console.log(data);
            var detail = SITE_URL + '/gongchang/pingjia/pingjiaxiangqing/' + data.id;
            var role1 = '<td>屏蔽</td>';
            var role2='<td><a href="'+detail+'" class="btn btn-success btn-sm">查看</a>&nbsp;';
            role2+='<button class="btn btn-success btn-sm pass" value="'+data.id+'">通过</button>&nbsp;';
            role2+='<button class="btn btn-success btn-sm remove" value="'+data.id+'">删除</button></td>';

            $("#review" + review_id).find('td:eq(3)').replaceWith(role1);
            $("#review" + review_id).find('td:eq(4)').replaceWith(role2);
        },
        error: function (data) {
            console.log('Error:', data);
        }
    });
});

$(document).on('click','.remove',function(){
    var review_id = $(this).val();
    $.confirm({
        icon: 'fa fa-warning',
        title: '评价管理',
        text:'您要删除客户评价吗？',
        confirmButton: "是",
        cancelButton: "不",
        confirmButtonClass: "btn-success",
        confirm: function () {
            deleteReview(review_id);
        },
        cancel: function () {
            return;
        }
    });
});

function deleteReview(review_id) {
    var url = API_URL + 'gongchang/pingjia/pingjialiebiao/remove';
    $.ajax({
        type: "DELETE",
        url: url + '/' + review_id,
        success: function (data) {
            console.log(data);
            $("#review" + review_id).remove();
        },
        error: function (data) {
            console.log('Error:', data);
        }
    });
}

$(document).on('click','.modify',function(){
    var review_id = $(this).val();
    var url = API_URL + 'gongchang/pingjia/pingjialiebiao/current_info/' +review_id;
    console.log(url);
    $.get(url, function (data) {
        //success data
        console.log(data);
        $('#current_id').val(data.id);
        // $('#mark').attr("value",data.mark);
        $('#content').val(data.content);
        // $('#current_rate.filled-stars').css("width",data.mark*20+'%');
        var role='<div id="current_rate" class="col-md-9"><input id="mark" type="text" class="kv-fa rating-loading" value="'+data.mark+'" data-size="xs" title=""></div>';
        $('#current_rate').replaceWith(role);
        $('.kv-fa').rating({
            theme: 'krajee-fa',
            filledStar: '<i class="fa fa-star"></i>',
            emptyStar: '<i class="fa fa-star-o"></i>'
        });
        $('#modal-form').modal('show');
    })
});

$(document).on('click','#save',function(){
    var review_id = $('#current_id').val();
    var mark = $('#mark').val();
    var content = $('#content').val();
    var url = API_URL + 'gongchang/pingjia/pingjialiebiao/modify';
    var formData = {
        id: review_id,
        mark: mark,
        content: content,
    }
    $.ajax({
        type: "POST",
        url: url,
        data: formData,
        dataType: 'json',
        success: function (data) {
            console.log(data);
            var detail = SITE_URL + '/gongchang/pingjia/pingjiaxiangqing/' + data.id;
            var content = data.content.substring(0,10)+'...';
            var role='<td><input type="text" class="kv-fa rating-loading" value="'+data.mark+'" data-size="xs" title="" readonly>';
            role+='<a href="'+detail+'">'+content+'</a></td>';

            $("#review" + review_id).find('td:eq(1)').replaceWith(role);
            $('#modal-form').modal('hide');
            $('.kv-fa').rating({
                theme: 'krajee-fa',
                filledStar: '<i class="fa fa-star"></i>',
                emptyStar: '<i class="fa fa-star-o"></i>'
            });
        },
        error: function (data) {
            console.log('Error:', data);
        }
    });
});

$(document).on('click','#search',function () {
    var status = $('#status option:selected').val();
    var start_date = $('#start_date').val();
    var end_date = $('#end_date').val();
    window.location.href = SITE_URL+"milk/public/gongchang/pingjia/pingjialiebiao/?status="+status+"&start_date="+start_date+"&end_date="+end_date+"";
})