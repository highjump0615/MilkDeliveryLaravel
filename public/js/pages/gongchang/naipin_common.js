var price_temp_changed; //share for insert and update

var update_page = false;
var selected_city_for_update = "";
var selected_district_array;

$(document).ready(function () {

    //for choosen select init
    show_chosen();

    var elem = document.querySelector('.js-switch');
    var switchery = new Switchery(elem, {color: '#1AB394'});

    //get exists products name for integrity
    get_existing_product_names();

    $.uploadPreview({
        cancel_bt: ".cancel",
        file_panel: ".file-panel",
        imageset: "#imageset",
        input_field: ".image-upload",   // Default: .image-upload
        preview_box: ".image-preview",  // Default: .image-preview
        label_field: ".image-label",    // Default: .image-label
        label_default: "选择文件",   // Default: Choose File
        label_selected: "更改文件",  // Default: Change File
        no_label: false                 // Default: false
    });

    $('.tabs-container .nav-tabs.closeable-tabs').css('border', 'none');

    $('.province_list').trigger('change');

});

function get_existing_product_names() {
    //get exists products name for integrity
    $.ajax({
        type: "GET",
        url: API_URL + "get_exist_product_names",
        success: function (data) {
            if (data.status == "success") {
                names = data.names;
                console.log("existing proudct names: " + names);

            } else {
                console.log("get product names fail: " + data);
            }
        },
        error: function (data) {
            console.log("get product names error: " + data);
        }
    });

}

//get city and distirct on selected province and city
$('.province_list').change(function () {

    var current_province = $('.province_list option:selected').val();

    var city_list = $(this).parent().parent().find('.city_list');

    $(city_list).empty();

    if (!current_province) {
        return;
    }

    var dataString = {'province': current_province};

    $.ajax({
        type: "GET",
        url: API_URL + "active_province_to_city",
        data: dataString,
        success: function (data) {
            if (data.status == "success") {
                cities = data.city;

                for (var i = 0; i < cities.length; i++) {
                    city_name = cities[i];

                    if (update_page) {
                        if (selected_city_for_update != "" && selected_city_for_update == city_name) {
                            citydata = '<option value="' + city_name + '" selected>' + city_name + '</option>';
                        } else {
                            citydata = '<option value="' + city_name + '">' + city_name + '</option>';
                        }

                    } else {
                        if (i == 0) {
                            citydata = '<option value="' + city_name + '" selected>' + city_name + '</option>';
                        } else {
                            citydata = '<option value="' + city_name + '">' + city_name + '</option>';
                        }
                    }

                    $(city_list).append(citydata);
                }
                $(city_list).trigger('change');
            }
        },
        error: function (data) {
            console.log(data);
        }
    })
});

$('.city_list').change(function () {

    var current_city = $(this).find('option:selected').val();

    var district_list = $(this).parent().parent().find('.district_list');

    $(district_list).empty();
    $(district_list).find('li.search-choice').remove();
    $(district_list).find('ul.chosen-results li').remove();

    if (!current_city) {
        return;
    }

    var province = $('.province_list').val();
    var city = $('.city_list').val();

    var dataString = {
        'province': province,
        'city': city,
    };

    $.ajax({
        type: "GET",
        url: API_URL + "active_city_to_district",
        data: dataString,
        success: function (data) {
            console.log(data);

            if (data.status == "success") {

                var districts = data.district;
                var districtdata;

                if (update_page && selected_district_array && selected_district_array.length > 0) {
                    for (var i = 0; i < districts.length; i++) {

                        var district_name = districts[i];

                        if (selected_district_array.indexOf(district_name) > -1) {
                            districtdata = '<option selected value="' + district_name + '">' + district_name + '</option>';
                        } else {
                            districtdata = '<option value="' + district_name + '">' + district_name + '</option>';
                        }

                        $(district_list).append(districtdata);

                        show_chosen();
                    }

                } else {
                    for (var i = 0; i < districts.length; i++) {
                        var district_name = districts[i];

                        districtdata = '<option value="' + district_name + '">' + district_name + '</option>';

                        $(district_list).append(districtdata);
                        show_chosen();
                    }
                }
            }
        },
        error: function (data) {
            console.log(data);
        }
    })
});

$(document).on('click', '.close-tab', function (event) {

    var close_bt = $(this);

    $.confirm({
        icon: 'fa fa-warning',
        title: '删除价格模板',
        text: '你会真的删除价格模板吗？',
        confirmButton: "是",
        cancelButton: "不",
        confirmButtonClass: "btn-success",
        confirm: function () {
            delete_price_template(close_bt);
        },
        cancel: function () {
            return;
        }
    });
});

function delete_price_template(close_bt){

    var tabscontainer = $(close_bt).closest('.tabs-container');
    var tabcontent = tabscontainer.find('.tab-content');

    var li = $(close_bt).closest('li');
    var target = li.find('[data-toggle="tab"]').attr("href");
    $(target).remove();
    li.remove();

    //if there is not active tab, then give fisrt tab class:acitve
    var first = tabcontent.find('.tab-pane').first();
    if (first) {
        var first_li = tabscontainer.find('.nav-tabs li').first();
        $(first_li).addClass('active');
        first.addClass('active');
    }

    if ($('.tab-pane').length == 0)
        $('.tabs-container .nav-tabs.closeable-tabs').css('border', 'none');

    price_temp_changed = true;
};

function ue_getContent() {
    var arr = [];
    arr.push(UE.getEditor('editor').getContent());
    return (arr.join("\n"));
}

function ueditor_init() {
    //UE.getEditor('editor').setContent('');
    ue.setContent('');
}


function ue_getPlainTxt() {
    var arr = [];
    arr.push(UE.getEditor('editor').getPlainTxt());
    console.log(arr.join('\n'))
}

function product_image_init() {
    var imdata = '<div class="image-preview col-md-2" data-attached="0">\
                        <label for="image-upload" class="image-label">选择文件</label>\
                        <input type="file" name="logoimage[]" class="image-upload"/>\
                        <div class="file-panel" style="height: 0px;">\
                            <span class="cancel">删除</span>\
                        </div>\
                 </div>';
    $('#imageset').html(imdata);
}

function show_chosen() {
    var config = {
        '.district_list': {placeholder_text_multiple: '选择一些选项...'},
        '.chosen-select-deselect': {allow_single_deselect: true},
        '.chosen-select-no-single': {disable_search_threshold: 10},
        '.chosen-select-no-results': {no_results_text: 'Oops, nothing found!'},
        '.chosen-select-width': {width: "95%"}
    }

    for (var selector in config) {
        $(selector).trigger("chosen:updated");
        $(selector).chosen(config[selector]);
    }
}

function init_price_template() {
    //Empty the price fields
    $('#retail_price').val('');
    $('#month_price').val('');
    $('#season_price').val('');
    $('#half_year_price').val('');
    $('#settle_price').val('');
    $('#template_name').val('');

    $('.district_list').val('').trigger('chosen:updated');

    if (update_page) {
        //hide the update price template button
        $('#add_price_bt').show();
        $('#update_price_bt').hide();

        $('#update_price_bt').attr('data-pricetp-id', 0);
    }
}

function add_price_template() {

    //get tab count
    var tabcount = $('.tab-content .tab-pane').size();

    var template_name = $('#template_name').val();
    if (template_name == "" || !template_name) {
        show_info_msg("请输入模板名称");
        return;
    }

    var price1 = $('#retail_price').val();
    var price2 = $('#month_price').val();
    var price3 = $('#season_price').val();
    var price4 = $('#half_year_price').val();
    var price5 = $('#settle_price').val();

    if ((price1 === '') || (price2 === '') || (price3 === '') || (price4 === '') || (price5 === '')) {
        show_info_msg('请填写价格模板');
        return;
    }

    var district_area = "";

    //currently one
    var district_list = $('.district_list');

    var province = $('.province_list').val();
    var city = $('.city_list').val();

    if (province == "" || !province) {
        show_info_msg("请选择省");
        return;
    }

    if (city == "" || !city) {
        show_info_msg("请选择城市");
        return;
    }

    $(district_list).find('option:selected').each(function () {
        if (district_area == "")
            district_area += $(this).text();
        else
            district_area += "," + $(this).text();
    });
    district_area = district_area.trim();

    if (district_area == "" || !district_area) {
        show_info_msg("请选择区");
        return;
    }

    var tabindex = init_tab_count + 1;

    var activeclass = "active";
    var expanded = "true";

    $('.nav-tabs li').each(function () {
        $(this).removeClass('active');
        $(this).find('a [data-toggle="tab"]').attr('aria-expanded', false);
    });
    $('.tab-content .tab-pane').each(function () {
        $(this).removeClass('active');
    });


    var li_data = '<li class="' + activeclass + '">\
                    <a data-toggle="tab" aria-expanded="' + expanded + '" href="#tab-' + tabindex + '">' + template_name + '</a>\
                    <a class="close-tab" ><i class="fa fa-times"></i></a>\
                </li>';

    if (update_page) {
        //for update page
        var tab_data = '<div id="tab-' + tabindex + '" class="tab-pane ' + activeclass + '">\
                <div class="panel-body">\
                    <div class="col-md-3">\
                        <label><span class="province_sp">' + province + '</span>&nbsp;&nbsp;<span class="city_sp">' + city + '</span></label>\
                    </div>\
                    <div class="col-sm-7">\
                        <label>包含分区:&nbsp; <span class="district_sp">' + district_area + '</span></label><br>\
                    </div>\
                    <div class="col-sm-2 text-right">\
                        <button type="button" class="btn btn-success btn-outline" data-action="edit_template"><i class="fa fa-pencil"></i>修改</button>\
                    </div>\
                    <br>\
                    <div class="col-sm-offset-3 col-sm-9">\
                        <input type="hidden" name="template_name" class="name_sp" value="' + template_name + '"/><br>\
                        <label>零售价:&nbsp; <span class="retail_sp">' + price1 + '</span>元</label><br>\
                        <label>月单:&nbsp; <span class="month_sp">' + price2 + '</span>元</label><br>\
                        <label>季单:&nbsp; <span class="season_sp">' + price3 + '</span>元</label><br>\
                        <label>半年单:&nbsp; <span class="half_year_sp">' + price4 + '</span>元</label><br>\
                        <label>结算价:&nbsp; <span class="settle_sp">' + price5 + '</span>元</label>\
                    </div>\
                </div>\
            </div>';
    } else {
        //for insert page
        var tab_data = '<div id="tab-' + tabindex + '" class="tab-pane ' + activeclass + '">\
                <div class="panel-body">\
                    <div class="col-md-3">\
                        <label><span class="province_sp">' + province + '</span>&nbsp;&nbsp;<span class="city_sp">' + city + '</span></label>\
                    </div>\
                    <div class="col-sm-7">\
                        <label>包含分区:&nbsp; <span class="district_sp">' + district_area + '</span></label><br>\
                    </div>\
                    <div class="col-sm-2">\
                    </div>\
                    <br>\
                    <div class="col-sm-offset-3 col-sm-9">\
                        <input type="hidden" name="template_name" class="name_sp" value="' + template_name + '"/>\
                        <label>零售价:&nbsp; <span class="retail_sp">' + price1 + '</span>元</label><br>\
                        <label>月单:&nbsp; <span class="month_sp">' + price2 + '</span>元</label><br>\
                        <label>季单:&nbsp; <span class="season_sp">' + price3 + '</span>元</label><br>\
                        <label>半年单:&nbsp; <span class="half_year_sp">' + price4 + '</span>元</label><br>\
                        <label>结算价:&nbsp; <span class="settle_sp">' + price5 + '</span>元</label>\
                    </div>\
                </div>\
            </div>';
    }

    $('ul.nav-tabs.closeable-tabs').append(li_data);
    $('.tab-content').append(tab_data);
    init_tab_count += 1;

    if ($('.tab-pane').length == 0)
        $('.tabs-container .nav-tabs.closeable-tabs').css('border', 'none');

    init_price_template();

    price_temp_changed = true;
}

function init_all_price_template_data() {
    init_tab_count = 0;
    init_price_template();
    var init_temp_data = '<div class="col-md-12  form-group">\
                            <div class="tabs-container col-md-10">\
                                <ul class="nav nav-tabs closeable-tabs">\
                                </ul>\
                                <div class="tab-content">\
                                </div>\
                            </div>\
                        </div>';

    $('#product_area_set_result').html(init_temp_data);
    $('.tabs-container .nav-tabs.closeable-tabs').css('border', 'none');
}