

    var url = API_URL + 'naizhan/xitong/juese';
    var rows = $('#roles-list tr');

    $(document).on('click','.clickable-row', function (e) {
        rows.removeClass('highlight');
        var row = $(this);
        role_id=row.attr('idnumber');
        console.log("*******role_id:"+role_id);
        var type = "GET"; //for get Permisstion table
        var data = {id:$('#role_id').val()};

        $.ajax({

            type: type,
            url: url + '/' + role_id,
            data: data,
            dataType: 'json',
            success: function (data) {
                console.log(data);

                role='<table class="table tree table-inverse" id="permissionTable"><tbody><tr data-node="treetable-650__1" data-pnode="">';
                // role+='<td width="30%" class="average-score sm-text color-gray"><input type="checkbox" class="i-checks" name="input[]"> 全选</td>';
                // role+='<td class="average-score sm-text color-gray"><input type="checkbox" class="i-checks" name="input[]"> 查看</td>';
                // role+='<td class="average-score sm-text color-gray"><input type="checkbox" class="i-checks" name="input[]"> 修改</td>';
                // role+='<td class="average-score sm-text color-gray"><input type="checkbox" class="i-checks" name="input[]"> 新增</td>';
                // role+='<td class="average-score sm-text color-gray"><input type="checkbox" class="i-checks" name="input[]"> 删除</td>';
                // role+='<td class="average-score sm-text color-gray"></td>';
                // role+='<td class="average-score sm-text color-gray"><input type="checkbox" class="i-checks" name="input[]"> 全选</td></tr>';
                role+='<input type="hidden" name="roleId" value="'+data.id+'">'

                for(i = 0; i < data.page_access_list.length; i++)
                {
                    var k=i+2;
                    role+='<tr class="gray-bg">';
                    role+='<td colspan="7" class="competency sm-text">';
                    var acccess_check = null;
                    if(data.page_access_list[i].access == true){
                        acccess_check = "Checked";
                    }
                    var Page_order = i+2;
                    //console.log(acccess_check);
                    role+='<div class="checkbox checkbox-primary"><input type="checkbox" class="" id="parenticheck'+Page_order+'" name="input'+data.page_access_list[i].id+'" '+acccess_check+'>';
                    role+='<label for="parenticheck'+Page_order+'">'+data.page_access_list[i].name+'</label></div></td>';
                    role+='</tr>';
                    for(j = 0; j < data.page_access_list[i].pages.length; j++)
                    {
                        var subpage_access_check = null;
                        if(data.page_access_list[i].pages[j].access == true){
                            subpage_access_check = "Checked";
                        }
                        var l = j+1;
                        role+='<tr>';
                        role+='<td width="30%" class="average-score sm-text color-gray">';
                        role+='<div class="checkbox checkbox-primary">&emsp;<input type="checkbox" class="childicheck'+Page_order+'" id="child'+data.page_access_list[i].pages[j].id+'" name="input'+data.page_access_list[i].pages[j].id+'" '+subpage_access_check+'>';
                        role+='<label for="child'+data.page_access_list[i].pages[j].id+'">'+data.page_access_list[i].pages[j].name+'</label></div></td>';
                        // role+='<td class="average-score sm-text color-gray"><input type="checkbox" class="i-checks"> 查看</td>';
                        // role+='<td class="average-score sm-text color-gray"><input type="checkbox" class="i-checks"> 修改</td>';
                        // role+='<td class="average-score sm-text color-gray"><input type="checkbox" class="i-checks"> 新增</td>';
                        // role+='<td class="average-score sm-text color-gray"><input type="checkbox" class="i-checks"> 删除</td>';
                        // role+='<td class="average-score sm-text color-gray"></td>';
                        // role+='<td class="average-score sm-text color-gray"><input type="checkbox" class="i-checks"> 全选</td></tr>';
                    }
                }

                role+='</tbody>';
                role+='</table>';


                //console.log(role);
                $('#permissionTable').trigger("reset");
                $("#permissionTable").replaceWith( role );


                $('#parenticheck1').change(function () {
                    if($(this).is(':checked')){
                        $('.childicheck1').prop('checked',true);
                    }
                    else {
                        $('.childicheck1').prop('checked',false);
                    }
                })
                $('.childicheck1').change(function () {
                    if($(this).is(':checked')){
                        $('#parenticheck1').prop('checked',true);
                    }
                    else {
                        var i = 0;
                        $(this).parent().parent().parent().parent().find('.childicheck1').each(function () {
                            if($(this).is(':checked')){
                                i++;
                            }
                        })
                        if(i==0){
                            $('#parenticheck1').prop('checked',false);
                        }
                    }
                })

                $('#parenticheck2').change(function () {
                    if($(this).is(':checked')){
                        $('.childicheck2').prop('checked',true);
                    }
                    else {
                        $('.childicheck2').prop('checked',false);
                    }
                })
                $('.childicheck2').change(function () {
                    if($(this).is(':checked')){
                        $('#parenticheck2').prop('checked',true);
                    }
                    else {
                        var i = 0;
                        $(this).parent().parent().parent().parent().find('.childicheck2').each(function () {
                            if($(this).is(':checked')){
                                i++;
                            }
                        })
                        if(i==0){
                            $('#parenticheck2').prop('checked',false);
                        }
                    }
                })
                $('#parenticheck3').change(function () {
                    if($(this).is(':checked')){
                        $('.childicheck3').prop('checked',true);
                    }
                    else {
                        $('.childicheck3').prop('checked',false);
                    }
                })
                $('.childicheck3').change(function () {
                    if($(this).is(':checked')){
                        $('#parenticheck3').prop('checked',true);
                    }
                    else {
                        var i = 0;
                        $(this).parent().parent().parent().parent().find('.childicheck3').each(function () {
                            if($(this).is(':checked')){
                                i++;
                            }
                        })
                        if(i==0){
                            $('#parenticheck3').prop('checked',false);
                        }
                    }
                })

                $('#parenticheck4').change(function () {
                    if($(this).is(':checked')){
                        $('.childicheck4').prop('checked',true);
                    }
                    else {
                        $('.childicheck4').prop('checked',false);
                    }
                })
                $('.childicheck4').change(function () {
                    if($(this).is(':checked')){
                        $('#parenticheck4').prop('checked',true);
                    }
                    else {
                        var i = 0;
                        $(this).parent().parent().parent().parent().find('.childicheck4').each(function () {
                            if($(this).is(':checked')){
                                i++;
                            }
                        })
                        if(i==0){
                            $('#parenticheck4').prop('checked',false);
                        }
                    }
                })

                $('#parenticheck5').change(function () {
                    if($(this).is(':checked')){
                        $('.childicheck5').prop('checked',true);
                    }
                    else {
                        $('.childicheck5').prop('checked',false);
                    }
                })
                $('.childicheck5').change(function () {
                    if($(this).is(':checked')){
                        $('#parenticheck5').prop('checked',true);
                    }
                    else {
                        var i = 0;
                        $(this).parent().parent().parent().parent().find('.childicheck5').each(function () {
                            if($(this).is(':checked')){
                                i++;
                            }
                        })
                        if(i==0){
                            $('#parenticheck5').prop('checked',false);
                        }
                    }
                })
                $('#parenticheck6').change(function () {
                    if($(this).is(':checked')){
                        $('.childicheck6').prop('checked',true);
                    }
                    else {
                        $('.childicheck6').prop('checked',false);
                    }
                })
                $('.childicheck6').change(function () {
                    if($(this).is(':checked')){
                        $('#parenticheck6').prop('checked',true);
                    }
                    else {
                        var i = 0;
                        $(this).parent().parent().parent().parent().find('.childicheck6').each(function () {
                            if($(this).is(':checked')){
                                i++;
                            }
                        })
                        if(i==0){
                            $('#parenticheck6').prop('checked',false);
                        }
                    }
                })
                $('#parenticheck7').change(function () {
                    if($(this).is(':checked')){
                        $('.childicheck7').prop('checked',true);
                    }
                    else {
                        $('.childicheck7').prop('checked',false);
                    }
                })
                $('.childicheck7').change(function () {
                    if($(this).is(':checked')){
                        $('#parenticheck7').prop('checked',true);
                    }
                    else {
                        var i = 0;
                        $(this).parent().parent().parent().parent().find('.childicheck7').each(function () {
                            if($(this).is(':checked')){
                                i++;
                            }
                        })
                        if(i==0){
                            $('#parenticheck7').prop('checked',false);
                        }
                    }
                })

                $('#parenticheck8').change(function () {
                    if($(this).is(':checked')){
                        $('.childicheck8').prop('checked',true);
                    }
                    else {
                        $('.childicheck8').prop('checked',false);
                    }
                })
                $('.childicheck8').change(function () {
                    if($(this).is(':checked')){
                        $('#parenticheck8').prop('checked',true);
                    }
                    else {
                        var i = 0;
                        $(this).parent().parent().parent().parent().find('.childicheck8').each(function () {
                            if($(this).is(':checked')){
                                i++;
                            }
                        })
                        if(i==0){
                            $('#parenticheck8').prop('checked',false);
                        }
                    }
                })
                $('#parenticheck9').change(function () {
                    if($(this).is(':checked')){
                        $('.childicheck9').prop('checked',true);
                    }
                    else {
                        $('.childicheck9').prop('checked',false);
                    }
                })
                $('.childicheck9').change(function () {
                    if($(this).is(':checked')){
                        $('#parenticheck9').prop('checked',true);
                    }
                    else {
                        var i = 0;
                        $(this).parent().parent().parent().parent().find('.childicheck9').each(function () {
                            if($(this).is(':checked')){
                                i++;
                            }
                        })
                        if(i==0){
                            $('#parenticheck9').prop('checked',false);
                        }
                    }
                })
                $('#parenticheck10').change(function () {
                    if($(this).is(':checked')){
                        $('.childicheck10').prop('checked',true);
                    }
                    else {
                        $('.childicheck10').prop('checked',false);
                    }
                })
                $('.childicheck10').change(function () {
                    if($(this).is(':checked')){
                        $('#parenticheck10').prop('checked',true);
                    }
                    else {
                        var i = 0;
                        $(this).parent().parent().parent().parent().find('.childicheck10').each(function () {
                            if($(this).is(':checked')){
                                i++;
                            }
                        })
                        if(i==0){
                            $('#parenticheck10').prop('checked',false);
                        }
                    }
                })



                $('.i-checks').iCheck({
                    checkboxClass: 'icheckbox_square-green',
                    radioClass: 'iradio_square-green',
                });

                $('.table').treeTable();
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
    });