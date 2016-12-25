$(document).ready(function () {

    $('.footable').footable();

    $('button[data-action = "insert_modal"]').click(function () {

        $('#insert_modal').modal();
        if ($('#insert_province').val()) {
            $('#insert_province').trigger('change');
        }

        $('#xiaoqu-insert').tagsinput('removeAll');
        $('#insert_modal input[name="street"]').val('');
    });


    $('#insert_province').change(function () {

        var current_province = $('#insert_province option:selected').val();
        $('#insert_city').empty();
        $('#insert_city').append('<option value="none_city"></option>');
        if (current_province == "none_province") {
            return;
        }

        $('#modal-spinner-frame-insert').show();

        var dataString = {'province': current_province};

        $.ajax({
            type: "GET",
            url: API_URL + "province_to_city",
            data: dataString,
            success: function (data) {
                if (data.status == "success") {
                    cities = data.city;

                    for (i = 0; i < cities.length; i++) {
                        city = cities[i];

                        if (i == 0) {
                            citydata = '<option value="' + city.name + '" selected>' + city.name + '</option>';
                        } else {
                            citydata = '<option value="' + city.name + '">' + city.name + '</option>';
                        }
                        $('#insert_city').append(citydata);
                    }
                    $('#insert_city').trigger('change');
                }
                $('#modal-spinner-frame-insert').hide();
            },
            error: function (data) {
                console.log(data);
                $('#modal-spinner-frame-insert').hide();
            }
        })
    });

    $('#insert_city').change(function () {

        var current_province = $('#insert_province option:selected').val();

        var current_city = $('#insert_city option:selected').val();
        $('#insert_district').empty();

        if (current_city == "none_city") {
            return;
        }

        $('#modal-spinner-frame-insert').show();

        var dataString = {'city': current_city, 'province': current_province};
        console.log(dataString);

        $.ajax({
            type: "GET",
            url: API_URL + "city_to_district",
            data: dataString,
            success: function (data) {
                if (data.status == "success") {
                    districts = data.district;

                    for (var i = 0; i < districts.length; i++) {
                        district = districts[i];

                        districtdata = '<option value="' + district.name + '">' + district.name + '</option>';
                        $('#insert_district').append(districtdata);
                    }
                }
                $('#modal-spinner-frame-insert').hide();
            },
            error: function (data) {
                console.log(data);
                $('#modal-spinner-frame-insert').hide();
            }
        })
    });


    $('button[data-action = "update_modal"]').click(function () {
        //get contents of same row
        var tr = $(this).closest('tr');
        var province = tr.find(".street_td").data('province');
        var city = tr.find(".street_td").data('city');
        var district = tr.find(".street_td").data('district');
        var street = tr.find(".street_td").data('content');
        var xiaoqu = tr.find(".xiaoqu_td").data('content');

        console.log(xiaoqu);

        $('#update_modal').find('[name = "province"]').val(province);
        $('#update_modal').find('[name = "city"]').val(city);
        $('#update_modal').find('[name = "district"]').val(district);

        $('#update_modal').find('[name = "street"]').val(street);
        $('#update_modal').find('[name = "origin_street"]').val(street);


        $('#update_modal').find('[name = "xiaoqu"]').val(xiaoqu);
        var array = xiaoqu.split(',');

        $('#xiaoqu-update').tagsinput('removeAll');

        for (var i=0; i<array.length; i++) {
            $('#xiaoqu-update').tagsinput('add', array[i]);
        }

        $('#update_modal').find('[name = "origin_xiaoqu"]').val(xiaoqu);

        $('#update_modal').modal();
    });

    $('button[data-action = "update_addr"]').click(function (e) {
        e.preventDefault();

        var modal = $("#update_modal");

        var province = modal.find('[name = "province"]').val();
        var city = modal.find('[name = "city"]').val();
        var district = modal.find('[name = "district"]').val();

        var street = modal.find('[name = "street"]').val();
        var origin_street = modal.find('[name = "origin_street"]').val();

        var xiaoqu = modal.find('[name = "xiaoqu"]').val();
        var origin_xiaoqu = modal.find('[name = "origin_xiaoqu"]').val();

        if ((street == origin_street) && (xiaoqu == origin_xiaoqu)) {
            modal.modal("hide");
            return;
        }

        var dataString = {
            'province': province,
            'city': city,
            'district': district,
            'street': street,
            'xiaoqu': xiaoqu,
            'origin_street': origin_street,
            'origin_xiaoqu': origin_xiaoqu
        };

        console.log(dataString);

        $.ajax({
            type: "POST",
            url: API_URL + "gongchang/jichuxinxi/dizhiku/update",
            data: dataString,
            success: function (data) {
                console.log(data);
                if (data.status == "success") {
                    modal.modal("hide");
                    location.reload();

                } else {
                    if(data.message)
                    {
                        show_warning_msg(data.message);
                    }
                }
            },
            error: function (data) {
                console.log(data);
                alert(data.message);
            }
        })
    });


    $('button[data-action = "insert_addr"]').click(function (e) {

        e.preventDefault();

        var modal = $("#insert_modal");

        var province = modal.find('[name = "province"]').val();
        var city = modal.find('[name = "city"]').val();
        var district = modal.find('[name = "district"]').val();
        var street = modal.find('[name = "street"]').val();
        var xiaoqu = modal.find('[name = "xiaoqu"]').val();

        if(street == "")
            return;

        if(xiaoqu == "")
            return;

        var dataString = {'province': province, 'city': city, 'district': district, 'street': street, 'xiaoqu': xiaoqu};
        console.log(dataString);

        $.ajax({
            type: "POST",
            url: API_URL + "gongchang/jichuxinxi/dizhiku/store",
            data: dataString,
            success: function (data) {
                console.log(data);
                if (data.status == "success") {
                    modal.modal("hide");
                    location.reload();
                }
            },
            error: function (data) {
                if(data.message)
                {
                    show_warning_msg(data.message);
                }
            }
        })
    });

    $('button[data-action = "delete"]').click(function () {

        var button = $(this);

        $.confirm({
            icon: 'fa fa-warning',
            title: '删除地址',
            text: '你会真的删除地址吗？',
            confirmButton: "是",
            cancelButton: "不",
            confirmButtonClass: "btn-success",
            confirm: function () {
                delete_address(button);
            },
            cancel: function () {
                return;
            }
        });
    });

    function delete_address(button) {
        var tr = $(button).closest('tr');

        var province = tr.find(".street_td").data('province');
        var city = tr.find(".street_td").data('city');
        var district = tr.find(".street_td").data('district');
        var street = tr.find(".street_td").data('content');
        var xiaoqu = tr.find(".xiaoqu_td").data('content');

        var dataString = {'province': province, 'city': city, 'district': district, 'street': street, 'xiaoqu': xiaoqu};
        console.log(dataString);

        $.ajax({
            type: "POST",
            url: API_URL + "gongchang/jichuxinxi/dizhiku/delete_address",
            data: dataString,
            success: function (data) {
                if (data.status == "success") {
                    location.reload();
                }
                else {
                    if(data.message)
                    {
                        show_warning_msg(data.message);
                    }
                }
            },
            error: function (data) {
                console.log(data);

            }
        })

    }


    $('button[data-action = "set_flag"]').click(function () {

        var tr = $(this).closest('tr');
        var button = $(this);

        var use = button.attr('data-enable');

        var province = tr.find(".street_td").data('province');
        var city = tr.find(".street_td").data('city');
        var district = tr.find(".street_td").data('district');
        var street = tr.find(".street_td").data('content');
        var xiaoqu = tr.find(".xiaoqu_td").data('content');

        var dataString = {
            'province': province,
            'city': city,
            'district': district,
            'street': street,
            'xiaoqu': xiaoqu,
            'use': use
        };
        console.log(dataString);

        $.ajax({
            type: "POST",
            url: API_URL + "gongchang/jichuxinxi/dizhiku/setflag",
            data: dataString,
            success: function (data) {
                console.log(data);
                if (data.status == "success") {
                    if (data.action == "disabled") {
                        button.text("使用");
                        button.removeClass('btn-success');
                        button.attr("data-enable", 0);
                    } else {
                        button.text("停用");
                        button.addClass('btn-success');
                        button.attr("data-enable", 1);
                    }
                } else {
                    if(data.message)
                    {
                        show_warning_msg(data.message);
                    }
                }

            },
            error: function (data) {
                console.log(data);
                alert(data.statusText);
            }
        })
    });

    $('button[data-action = "export_csv"]').click(function () {
        data_export('address_tb', gnUserTypeFactory, '地址库管理', 0, 1);
    });
});