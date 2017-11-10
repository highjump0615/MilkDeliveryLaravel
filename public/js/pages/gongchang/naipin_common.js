var price_temp_changed = false; //share for insert and update
var current_product_id = 0;

var selected_city_for_update = "";
var selected_district_array = new Array();

/**
 * 获取价格模板id
 * @param tabPane
 */
function getPriceId(tabPane) {
    var priceId = 0;
    if ($(tabPane).find('.price_tp_id').length) {
        priceId = parseInt($(tabPane).find('.price_tp_id').val());
    }

    return priceId;
}

$(document).ready(function () {

    //for choosen select init
    show_chosen();

    var elem = document.querySelector('.js-switch');
    var switchery = new Switchery(elem, {color: '#1AB394'});

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

                    if (selected_city_for_update != "" && selected_city_for_update == city_name) {
                        citydata = '<option value="' + city_name + '" selected>' + city_name + '</option>';
                    } else {
                        citydata = '<option value="' + city_name + '">' + city_name + '</option>';
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
    if (tabcontent.find('.active').length === 0) {
        var first = tabcontent.find('.tab-pane').first();
        if (first) {
            var first_li = tabscontainer.find('.nav-tabs li').first();
            $(first_li).addClass('active');
            first.addClass('active');
        }
    }

    if ($('.tab-pane').length == 0)
        $('.tabs-container .nav-tabs.closeable-tabs').css('border', 'none');

    price_temp_changed = true;

    // 初始化下面的内容
    init_price_template();
}

function ueditor_init() {
    //UE.getEditor('editor').setContent('');
    ue.setContent('');
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

    //hide the update price template button
    $('#add_price_bt').show();
    $('#update_price_bt').hide();
    $('#update_price_bt').attr('data-tab-index', 0);
}

/**
 * 创建/更新价格模板
 */
function add_price_template(tabIndex, priceId) {

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

    var nCount = $('.nav-tabs li').length;

    var activeclass = "active";
    var expanded = "true";

    if (tabIndex === 0) {
        tabIndex = nCount + 1;

        var li_data = '<li class="' + activeclass + '">\
                <a data-toggle="tab" aria-expanded="' + expanded + '" href="#tab-' + tabIndex + '">' + template_name + '</a>\
                <a class="close-tab" ><i class="fa fa-times"></i></a>\
            </li>';

        var tab_data = '<div id="tab-' + tabIndex + '" class="tab-pane ' + activeclass + '"></div>';

        $('ul.nav-tabs.closeable-tabs').append(li_data);
        $('.tab-content').append(tab_data);
    }

    //
    // 隐藏所有的Tab
    //
    $('.nav-tabs li').each(function () {
        $(this).removeClass(activeclass);
        $(this).find('a [data-toggle="tab"]').attr('aria-expanded', false);
    });
    $('.tab-content .tab-pane').each(function () {
        $(this).removeClass(activeclass);
    });

    //
    // 显示该Tab
    //
    var strSelector = '.nav-tabs li:nth-child(' + tabIndex + ')';
    $(strSelector).addClass(activeclass);
    $(strSelector).find('a [data-toggle="tab"]').attr('aria-expanded', true);

    strSelector = '.tab-content .tab-pane:nth-child(' + tabIndex + ')';
    $(strSelector).addClass(activeclass);

    // 填充内容
    var strContent = '<div class="panel-body">\
            <div class="col-md-3">\
                <label><span class="province_sp">' + province + '</span>&nbsp;&nbsp;<span class="city_sp">' + city + '</span></label>\
            </div>\
            <div class="col-sm-7">\
                <label>包含分区:&nbsp; <span class="district_sp">' + district_area + '</span></label><br>\
            </div>\
            <div class="col-sm-2 text-right">\
                <button type="button" class="btn btn-success btn-outline" data-action="edit_template" data-tab-index="' + tabIndex + '">\
                    <i class="fa fa-pencil"></i>修改\
                </button>\
            </div>\
            <br>\
            <input type="hidden" class="price_tp_id" value="' + priceId + '"/>\
            <div class="col-sm-offset-3 col-sm-9">\
                <input type="hidden" name="template_name" class="name_sp" value="' + template_name + '"/>\
                <label>零售价:&nbsp; <span class="retail_sp">' + price1 + '</span>元</label><br>\
                <label>月单:&nbsp; <span class="month_sp">' + price2 + '</span>元</label><br>\
                <label>季单:&nbsp; <span class="season_sp">' + price3 + '</span>元</label><br>\
                <label>半年单:&nbsp; <span class="half_year_sp">' + price4 + '</span>元</label><br>\
                <label>结算价:&nbsp; <span class="settle_sp">' + price5 + '</span>元</label>\
            </div>\
        </div>';

    $(strSelector).html(strContent);

    if ($('.tab-pane').length == 0)
        $('.tabs-container .nav-tabs.closeable-tabs').css('border', 'none');

    init_price_template();

    price_temp_changed = true;
}

function init_all_price_template_data() {

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