$(document).ready(function(){


    //display modal form for creating new task
    $('#btn-add').click(function(){
        $('#btn-save').val("add");
        $('#frmroles').trigger("reset");
        $('#myModal').modal('show');
    });

	
    //create new role / update existing role
    $("#btn-save").click(function (e) {

        var url = API_URL + 'naizhan/xitong/juese';
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        })

        e.preventDefault();

        var formData = {
            name: $('#role').val(),
            backend_type: $('#type').val(),
        }

        console.log(formData);

        //used to determine the http verb to use [add=POST], [update=PUT]
        var state = $('#btn-save').val();

        var type = "POST"; //for creating new resource
        var role_id = $('#role_id').val();;
        var my_url = url;

        if (state == "update"){
            type = "PUT"; //for updating existing resource
            my_url += '/' + role_id;
        }

        $.ajax({

            type: type,
            url: my_url,
            data: formData,
            dataType: 'json',
            success: function (data) {
                //console.log(data);

                var role = '<tr id="role' + data.id + '" class="clickable-row gradeX" idnumber="'+data.id+'"><td>' + data.name + '</td>';
                role += '<td><button class="btn btn-md btn-success delete-role" id="role'+data.id+'" value="' + data.id + '">删除</button></td></tr>';


                console.log(role);
                console.log("state:"+state);
                if (state == "add"){ //if user added a new record
                    $('#roles-list').append(role);
                    //console.log('adding to role list');
                }else{ //if user updated an existing record
                    $("#role" + role_id).replaceWith( role );
                }

                $('#frmroles').trigger("reset");

                $('#myModal').modal('hide')
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
    });
});

$(document).on('click','.delete-role',function(e){
    e.preventDefault();
    e.stopPropagation();

    var role_id = $(this).val();
    $.confirm({
        icon: 'fa fa-warning',
        title: '角色管理',
        text:'您要删除角色吗？',
        confirmButton: "是",
        cancelButton: "不",
        confirmButtonClass: "btn-success",
        confirm: function () {
            deleteRole(role_id);
        },
        cancel: function () {
            return;
        }
    });
});

function deleteRole(role_id){
    var url = API_URL + 'naizhan/xitong/juese';
    $.ajax({
        type: "DELETE",
        url: url + '/' + role_id,
        success: function (data) {
            console.log("delete:"+data);
            $("#role" + role_id).remove();
        },
        error: function (data) {
            console.log('Error:', data);
            var message = document.getElementById('alertMessage');
            message.style.color="#ff6666";
            message.innerHTML="正在使用角色!";
        }
    });
}