
var Guanli_user_id;
var current_row_number;

function checkpassword()
{
    var pass1 = document.getElementById('pass1');
    var pass2 = document.getElementById('pass2');
    var message = document.getElementById('confirmMessage');
    var trueColor = "#66cc66";
    var falseColor = "#ff6666";

    if(pass1.value == pass2.value){
        pass2.style.backgroundColor=trueColor;
        message.style.color=trueColor;
        message.innerHTML="正确密码!";
    }
    else{
        pass2.style.backgroundColor=falseColor
        message.style.color=falseColor;
        message.innerHTML="不正确密码!";
    }
}

function  hide_user_alert() {
    $('#userconfirmMessage').hide();
}

function hide_password_alert() {
    $('#passwordMessage').hide();
}

function clearhistory() {
    $('#btn-save').val("add");
    $('#permission_info').show();
    $('#modal-form').trigger('reset');
    $('#username').val("");
    $('#description').val("");
    $('#pass1').val("");
    $('#pass2').val("");
    $('#confirmMessage').html("");
    document.getElementById('pass2').style.backgroundColor="#ffffff";
    document.getElementById('confirmMessage').innerHTML="";
    document.getElementById('userconfirmMessage').innerHTML="";
    document.getElementById('passwordMessage').innerHTML="";
}

$(document).ready(function(){

    //display modal form for creating new task
    $('#btn-add').click(function(){
        clearhistory();
        //$('#modal-form').modal('show');
    });

    $('.footable').footable();
    //create new role / update existing role
    $("#btn-save").click(function (e) {

        var url = API_URL + 'zongpingtai/yonghu/guanliyuanzhongxin';
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        });

        var password = $('#pass1').val();
        var confirmpass = $('#pass2').val();
        var username = $('#username').val();

        if(username == '' || password == ''|| password!=confirmpass)
        {
            if(username == ''){
                var user_confirmmessage = document.getElementById('userconfirmMessage');
                user_confirmmessage.style.color="#ff6666";
                user_confirmmessage.innerHTML="输入用户名!";
                $('#modal-form').modal('show');
                return;
            }
            if(password == ''){
                var passwordMessage = document.getElementById('passwordMessage');
                passwordMessage.style.color="#ff6666";
                passwordMessage.innerHTML="输入密码!";
                $('#modal-form').modal('show');
                return;
            }
            if(password!=confirmpass){
                var passwordMessage = document.getElementById('confirmMessage');
                passwordMessage.style.color="#ff6666";
                passwordMessage.innerHTML="确认密码!";
                $('#modal-form').modal('show');
                return;
            }
        }
        else{
            e.preventDefault();

            var formData = {
                name: $('#username').val(),
                password: $('#pass1').val(),
                description:$('#description').val(),
                status: $('#status').val(),
                backend_type: $('#backend_type').val(),
                user_role_id: $('#permission').val(),
            };

            console.log(formData);

            //used to determine the http verb to use [add=POST], [update=PUT]
            var state = $('#btn-save').val();

            var type = "POST"; //for creating new resource
            var my_url = url;
            var current_last_row_number = document.querySelector('table tr:last-child td:first-child').textContent;
            var last_row_number = parseInt(current_last_row_number,10)+1;
            if(isNaN(parseInt(current_last_row_number,10)))
                last_row_number = 1;
            if (state == "update"){
                type = "PUT"; //for updating existing resource
                my_url += '/' + Guanli_user_id;
                last_row_number = current_row_number;
            }

            $.ajax({
                type: type,
                url: my_url,
                data: formData,
                dataType: 'json',
                success: function (data) {
                    //console.log(data);
                    if(data.is_exist == 1){
                        var user_confirmmessage = document.getElementById('userconfirmMessage');
                        user_confirmmessage.style.color="#ff6666";
                        user_confirmmessage.innerHTML="用户名已存在!";
                        return;
                    }
                    var current_status = null;
                    if(data.status == 1)
                        current_status = '<i class="fa fa-check"></i>';
                    else
                        current_status = '<i class="fa fa-times"></i>';

                    var role = '<tr id="user'+data.id+'"><td>'+last_row_number+'</td><td>'+data.name+'</td><td>'+data.current_role_name.name+'</td>';
                    role+='<td>'+current_status+'</td><td></td>';
                    if(data.user_role_id == 100)
                        role+='<td><button type="button" data-toggle="modal" class="btn btn-success update-user" href="#modal-form" value="' + data.id + '">编辑</button></td></tr>';
                    else {
                        role += '<td><button type="button" data-toggle="modal" class="btn btn-success update-user" href="#modal-form" value="' + data.id + '">编辑</button>&nbsp;';

                        role += '<a type="button" class="btn btn-success"  href="juese/' + data.user_role_id + '"  value="' + data.id + '">查看操作权限</a>&nbsp;';
                        if (data.status == 1)
                            role += '<button type="button" class="btn btn-success stop-user" value="' + data.id + '">禁止用户</button>&nbsp;';
                        else
                            role += '<button type="button" class="btn btn-success start-user" value="' + data.id + '">允许用户</button>&nbsp;';
                        role += '<button type="button" class="btn btn-success delete-user" value="' + data.id + '">删除用户</button></td></tr>';
                    }
                    console.log(role);
                    console.log("state:"+state);
                    if (state == "add"){ //if user added a new record
                        $('#user-list').append(role);
                        //console.log('adding to role list');
                    }else{ //if user updated an existing record
                        $("#user" + Guanli_user_id).replaceWith( role );
                    }


                    $('#modal-form').trigger('reset');

                    $('#modal-form').modal('hide');

                },
                error: function (data) {
                    console.log('Error:', data);
                }
            });
        }

    });
});

$(document).on('click','.update-user',function(){
    clearhistory();
    Guanli_user_id = $(this).val();
    var current_user_id = $(this).attr('user_role');
    current_row_number = $(this).closest('tr').find('td:first').text();
    var url = API_URL + 'zongpingtai/yonghu/guanliyuanzhongxin/' +Guanli_user_id;
    console.log(url);
    $.get(url, function (data) {
        //success data
        console.log(data);
        $('#username').val(data.name);
        $('#pass1').val(data.password);
        $('#pass2').val(data.password);
        $('#description').val(data.description);
        $('#status').val(data.status);
        $('#permission').val(data.user_role_id);
        $('#btn-save').val("update");
    })
    if(current_user_id == 100){
        $('#permission_info').hide();
    }
    else{
        $('#permission_info').show();
    }
});

/*Make Status inactive*/
$(document).on('click','.stop-user',function(e) {
    var user_id = $(this).val();
    current_row_number = $(this).closest('tr').find('td:first').text();
    var url = API_URL + 'zongpingtai/yonghu/guanliyuanzhongxin/changeStatus/' +user_id;
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    })

    e.preventDefault();
    var formData = {
        status: 0,
    }

    var type = "PUT"; //for creating new resource
    $.ajax({

        type: type,
        url: url,
        data: formData,
        dataType: 'json',
        success: function (data) {
            var role = '<tr id="user'+data.id+'"><td>'+current_row_number+'</td><td>'+data.name+'</td><td>'+data.current_role_name.name+'</td>';
            role+='<td><i class="fa fa-times"></i></td><td></td>';
            role+='<td><button type="button" data-toggle="modal" class="btn btn-success update-user" href="#modal-form" value="'+data.id+'">编辑</button>&nbsp;';
            role+='<button type="button" data-toggle="modal" class="btn btn-success" href="juese">查看操作权限</button>&nbsp;';
            role+='<button type="button" class="btn btn-success start-user" value="'+data.id+'">允许用户</button>&nbsp;';
            role+='<button type="button" class="btn btn-success delete-user" value="'+data.id+'">删除用户</button></td></tr>';

            $("#user" + user_id).replaceWith( role );
        },
        error: function (data) {
            console.log('Error:', data);
        }
    });
});


$(document).on('click','.start-user',function() {
    var user_id = $(this).val();
    current_row_number = $(this).closest('tr').find('td:first').text();
    var url = API_URL + 'zongpingtai/yonghu/guanliyuanzhongxin/changeStatus/' +user_id;
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    })

    var formData = {
        status: 1,
    }

    var type = "PUT"; //for creating new resource
    $.ajax({

        type: type,
        url: url,
        data: formData,
        dataType: 'json',
        success: function (data) {
            var role = '<tr id="user'+data.id+'"><td>'+current_row_number+'</td><td>'+data.name+'</td><td>'+data.current_role_name.name+'</td>';
            role+='<td><i class="fa fa-check"></i></td><td></td>';
            role+='<td><button type="button" data-toggle="modal" class="btn btn-success update-user" href="#modal-form" value="'+data.id+'">编辑</button>&nbsp;';
            role+='<button type="button" data-toggle="modal" class="btn btn-success" href="juese">查看操作权限</button>&nbsp;';
            role+='<button type="button" class="btn btn-success stop-user" value="'+data.id+'">禁止用户</button>&nbsp;';
            role+='<button type="button" class="btn btn-success delete-user" value="'+data.id+'">删除用户</button></td></tr>';

            $("#user" + user_id).replaceWith( role );
        },
        error: function (data) {
            console.log('Error:', data);
        }
    });
});


$(document).on('click','.delete-user',function(){
    var user_id = $(this).val();
    $.confirm({
        icon: 'fa fa-warning',
        title: '管理员中心',
        text:'您要删除用户帐户吗？',
        confirmButton: "是",
        cancelButton: "不",
        confirmButtonClass: "btn-success",
        confirm: function () {
            deleteUser(user_id);
        },
        cancel: function () {
            return;
        }
    });
});

function deleteUser(user_id) {
    var url = API_URL + 'zongpingtai/yonghu/guanliyuanzhongxin';
    $.ajax({

        type: "DELETE",
        url: url + '/' + user_id,
        success: function (data) {
            console.log(data);
            $("#user" + user_id).remove();
        },
        error: function (data) {
            console.log('Error:', data);
        }
    });
}