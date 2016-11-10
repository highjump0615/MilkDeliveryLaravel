var current_row_number;
var user_id;

// You can change the class of your Switchery checkboxes
// by changing the document.querySelectorAll() argument.
// For example: If we want to use the class name ios7-switch,
// i.e. <input type="checkbox" class="ios7-switch" />
// we update the following line to
// ...document.querySelectorAll('.ios7-switch')...
var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
// This loop is needed so that we can have
// more than one Switchery checkbox
elems.forEach(function(html) {
    var switchery = new Switchery(html);
});
/*            var elem = document.querySelector('.js-switch');
 var switchery = new Switchery(elem, { color: '#1AB394' });
 */

function checkpassword()
{
    var pass1 = document.getElementById('password');
    var pass2 = document.getElementById('password2');
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
    document.getElementById('userconfirmMessage').innerHTML="";
    document.getElementById('passwordMessage').innerHTML="";
    document.getElementById('confirmMessage').innerHTML="";
    document.getElementById('password2').style.backgroundColor = "#ffffff";
}

$(document).ready(function(){


    $('.footable').footable();
    //display modal form for creating new task
    $('#btn-add').click(function(){
        clearhistory();
        //$('#myModal').modal('show');
    });

	
    //create new role / update existing role
    $("#btn-save").click(function (e) {

        var url = API_URL + 'naizhan/xitong/yonghu';
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        })

        e.preventDefault();

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
            my_url += '/' + user_id;
            last_row_number = current_row_number;
        }
        if($('#username').val()=='' || $('#password').val()!=$('#password2').val() ||$('#password').val() == ''){
            if($('#username').val()==''){
                var user_confirmmessage = document.getElementById('userconfirmMessage');
                user_confirmmessage.style.color="#ff6666";
                user_confirmmessage.innerHTML="输入用户名!";
                $('#modal-form').modal('show');
                return;
            }
            if($('#password').val() == ''){
                var password_message = document.getElementById('passwordMessage');
                password_message.style.color="#ff6666";
                password_message.innerHTML="输入密码名!";
                $('#modal-form').modal('show');
                return;
            }
            if($('#password').val()!=$('#password2').val()){
                var password_message = document.getElementById('confirmMessage');
                password_message.style.color="#ff6666";
                password_message.innerHTML="确认密码!";
                $('#modal-form').modal('show');
                return;
            }
        }
        else {
            var formData = {
                name: $('#username').val(),
                password: $('#password').val(),
                status: $('#status').val(),
                backend_type: 3,
                station_id: $('#current_station_id').val(),
                user_role_id: $('#permission').val(),
            }

            $.ajax({

                type: type,
                url: my_url,
                data: formData,
                dataType: 'json',
                success: function (data) {
                    if(data.is_exist == 1){
                        var user_confirmmessage = document.getElementById('userconfirmMessage');
                        user_confirmmessage.style.color="#ff6666";
                        user_confirmmessage.innerHTML="用户名已存在!";
                        return;
                    }

                    //console.log(data);
                    var current_status = null;
                    if(data.status == 1)
                        current_status = 'checked';
                    var role = '<tr id="user'+data.id+'"><td>'+last_row_number+'</td><td>'+data.name+'</td><td>'+data.current_role_name.name+'</td>';
                    role+='<td></td><td><input id="status'+data.id+'" type="checkbox" class="js-switch" '+current_status+' /></td>';
                    role+='<td><button class="btn btn-sm btn-success update-user" data-toggle="modal" href="#modal-form" value="'+data.id+'">修改</button>&nbsp;<button  class="btn btn-sm btn-success delete-user" value="'+data.id+'">删除</button></td></tr>';

                    console.log(role);
                    console.log("state:"+state);
                    if (state == "add"){ //if user added a new record
                        $('#user-list').append(role);
                        //console.log('adding to role list');
                    }else{ //if user updated an existing record
                        $("#user" + user_id).replaceWith( role );
                    }

                    $('#frmroles').trigger("reset");

                    $('#modal-form').modal('hide');
                    var elem = document.querySelector('#status'+data.id+'');
                    var switchery = new Switchery(elem);
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
    $('.modal-spinner-frame').show();
    user_id = $(this).val();
    var current_user_id = $(this).attr('user_role');
    current_row_number = $(this).closest('tr').find('td:first').text();
    var url = API_URL + 'naizhan/xitong/yonghu/' +user_id;
    console.log(url);
    $.get(url, function (data) {
        //success data
        console.log(data);
        $('#username').val(data.name);
        $('#password').val(data.password);
        $('#status').val(data.status);
        $('#permission').val(data.user_role_id);
        $('#btn-save').val("update");

        $('.modal-spinner-frame').hide();
        if(current_user_id == 1){
            $('#permission_info').hide();
        }
        else{
            $('#permission_info').show();
        }
    })
});


$(document).on('click','.delete-user',function(){
    var user_id = $(this).val();
    $.confirm({
        icon: 'fa fa-warning',
        title: '用户管理',
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
    var url = API_URL + 'naizhan/xitong/yonghu';
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

$(document).on('change','.changeStatus',function (e) {
    clearhistory();
    var url = API_URL + 'naizhan/xitong/yonghu/changeStatus';
    var id = $(this).val();
    var status = 0;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    })

    e.preventDefault();

    var type = "POST"; //for creating new resource
    if($(this).is(':checked')){
        status = 1;
    }
    else {
        status = 0;
    }

    var formData = {
        id: id,
        status: status,
    }

    $.ajax({
        type: type,
        url: url,
        data: formData,
        dataType: 'json',
        success: function (data) {
            //console.log(data);
        },
        error: function (data) {
            console.log('Error:', data);
        }
    });
});
