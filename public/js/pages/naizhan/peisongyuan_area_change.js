//MultiSelect
$(document).ready(function () {

    $('.js-multiselect1').multiselect({
        left: "#js_multiselect_from_1",
        right: '#js_multiselect_to_1',
        rightAll: '#js_right_All_1',
        rightSelected: '#js_right_Selected_1',
        leftSelected: '#js_left_Selected_1',
        leftAll: '#js_left_All_1',
        moveUp: '#multiselect_move_up',
        moveDown: '#multiselect_move_down'

    });

    $('.js-multiselect2').multiselect({
        left: "#js_multiselect_from_2",
        right: '#js_multiselect_to_2',
        rightAll: '#js_right_All_2',
        rightSelected: '#js_right_Selected_2',
        leftSelected: '#js_left_Selected_2',
        leftAll: '#js_left_All_2',

    });
});

//Modify Xiaoqus
$(document).on('click', '[data-action="modify_xiaoqu"]', function () {

    var street_id = $(this).data('street-id');
    var street_name = $(this).data('street-name');

    $('#selected_street_to_change').val(street_name);
    $('#street_id_to_change').val(street_id);

    //get used xiaoqu and show them in right panel
    var left_select = $('select#js_multiselect_from_1');
    $(left_select).empty();

    var right_select = $('select#js_multiselect_to_1');
    $(right_select).empty();

    $.each(used_obj[street_id][1], function (xiaoqu_id, xiaoqu_name) {
        var option = '<option value="' + xiaoqu_id + '">' + xiaoqu_name + '</option>';
        $(right_select).append(option);
    });

    $.each(avail_obj[street_id][1], function (xiaoqu_id, xiaoqu_name) {
        if (!(used_obj[street_id][1]).hasOwnProperty(xiaoqu_id)) {
            var option = '<option value="' + xiaoqu_id + '">' + xiaoqu_name + '</option>';
            $(left_select).append(option);
        }
    });

    $('#change_modal_form').modal('show');
});
//Change Xiaoqu of station's delivery area
$('#submit_change_form').on('click', function () {

    var milkman_id = $('#milkman_id').val();
    var street_id_to_change = $('#street_id_to_change').val();

    //make the elements in right list to be selected
    $('select#js_multiselect_to_1 > option').prop('selected', true);

    var sendData = $('#change_xiaoqu_form').serializeArray();
    sendData.push({'name': "milkman_id", 'value': milkman_id});
    sendData.push({'name': "street_id_to_change", 'value': street_id_to_change});

    $('select#js_multiselect_to_1 > option').prop('selected', false);

    // 查看是否存在to[]
    var bExist = false;
    for (var idx in sendData) {
        if (sendData[idx]['name'] == 'to[]') {
            bExist = true;
            break;
        }
    }

    // 没有选择小区信息
    if (!bExist) {
        return;
    }

    $.ajax({
        type: "POST",
        url: API_URL + 'naizhan/naizhan/fanwei-chakan/modifyPeisongyuanArea',
        data: sendData,
        success: function (data) {
            console.log(data);
            location.reload();
        },
        error: function (data) {
            console.log(data);
        },
    })
});

//Delete Delivery Area of Station
$(document).on('click', '[data-action="delete_xiaoqu"]', function () {
    var button = $(this);

    $.confirm({
        icon: 'fa fa-warning',
        title: '删除配送范围',
        text: '你会真的删除配送范围吗？',
        confirmButton: "是",
        cancelButton: "不",
        confirmButtonClass: "btn-success",
        confirm: function () {
            delete_delivery_area(button);
        },
        cancel: function () {
            return;
        }
    });
});
function delete_delivery_area(button) {
    var street_id = $(button).data('street-id');
    var milkman_id = $('#milkman_id').val();

    $.ajax({
        type: "POST",
        url: API_URL + 'naizhan/naizhan/fanwei-chakan/deleteDeliveryArea',
        data: {
            'street_id': street_id,
            'milkman_id': milkman_id,
        },
        success: function (data) {
            console.log(data);
            location.reload();
        },
        error: function (data) {
            console.log(data);
        },
    })
}

$(document).on('click', '#add_address', function () {

    //add street list to the select
    $('#add_street_list').empty();

    $.each(avail_obj, function (street_id, street) {

        if (used_obj.hasOwnProperty(street_id)) {
//                    var option = '<option disabled value="' + street_id + '">' + street[0] + '<i class="fa fa-asterisk">*</i></option>';
            return true;    // continue
        } else {
            var option = '<option value="' + street_id + '">' + street[0] + '</option>';
        }
        $('#add_street_list').append(option);
    });

    var street_id = $('#add_street_list').val();
    if(!street_id){
        $('#errmsg').show();
        setTimeout(function() { $("#errmsg").hide(); }, 5000);
        return;
    }

    //get used xiaoqu and show them in right panel
    var left_select = $('select#js_multiselect_from_2');
    $(left_select).empty();

    var right_select = $('select#js_multiselect_to_2');
    $(right_select).empty();

    $.each(avail_obj[street_id][1], function (xiaoqu_id, xiaoqu_name) {
        var option = '<option value="' + xiaoqu_id + '">' + xiaoqu_name + '</option>';
        $(left_select).append(option);
    });

    $('#add_modal_form').modal('show');
});

$(document).on('click', '#add-street', function () {
    $('#add_modal_form').modal('show');
});

$(document).on('change', '#add_street_list', function () {
    var street_id = $(this).val();

    //get used xiaoqu and show them in right panel
    var left_select = $('select#js_multiselect_from_2');
    $(left_select).empty();

    var right_select = $('select#js_multiselect_to_2');
    $(right_select).empty();

    $.each(avail_obj[street_id][1], function (xiaoqu_id, xiaoqu_name) {
        var option = '<option value="' + xiaoqu_id + '">' + xiaoqu_name + '</option>';
        $(left_select).append(option);
    });

});