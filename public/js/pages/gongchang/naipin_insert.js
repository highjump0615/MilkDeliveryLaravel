var init_tab_count = 0;
var names = [];
var ue;//ueditor

$(document).ready(function () {
    currentimgs = 0;

    ue = UE.getEditor('editor');
});

function insert_product_price_template(pid) {
    var price_template_data = new Array();

    $('#product_area_set_result .tab-content .tab-pane').each(function () {
        var province = $(this).find('.province_sp').text();
        var city = $(this).find('.city_sp').text();
        var district = $(this).find('.district_sp').text();
        var template_name = $(this).find('.name_sp').val();
        var retail_price = $(this).find('.retail_sp').text();
        var month_price = $(this).find('.month_sp').text();
        var season_price = $(this).find('.season_sp').text();
        var half_year_price = $(this).find('.half_year_sp').text();
        var settle_price = $(this).find('.settle_sp').text();
        
        district = district.replace(/\s+/g, '');

        var price_tp = {
            'template_name': template_name, 'province': province, 'city': city,
            'district': district, 'retail': retail_price, 'month': month_price, 'season': season_price,
            'half': half_year_price, 'settle': settle_price
        };

        price_template_data.push(price_tp);
    });

    var product_data = {'product_id': pid, 'price_template_data': price_template_data};


    $.ajax({
        type: 'POST',
        url: API_URL + 'gongchang/naipinluru/insert_product_price_template',
        data: product_data,
        success: function (data) {
            console.log("result: " + data);
            if (data.status == "success") {
                // var product_id = data.price_added_product_id;
                show_success_msg("奶品录入成功");
                // var url = SITE_URL+'gongchang/jichuxinxi/shangpin/shangpinxiangqing/'+product_id;
                var url = SITE_URL+'gongchang/jichuxinxi/shangpin';
                window.location.replace(url);
            } else {
                show_info_msg(data.message);
            }
        },
        error: function (data) {
            console.log(data);
            show_err_msg("虽然节能产品，错误发生. 请稍后再试.");
        }
    });

}

function cancel_add_product() {
    $('#product_name').val('');

    $('#product_simple_name').val('');

    $('#product_category option:first-child').attr('selected', 'selected');
    $('#product_introduction').val('');
    $('#bottle_type option:first-child').attr('selected', 'selected');

    $('#guarantee_period option:first-child').attr('selected', 'selected');
    $('#guarantee_req').val('');

    $('#material').val('');
    $('#production_period option:first-child').attr('selected', 'selected');

    $('#product_basket_spec option:first-child').attr('selected', 'selected');
    $('#depot_need').val();

    product_image_init();

    init_all_price_template_data();

    ueditor_init();
}

function insert_product(){

    //check data integrtiy-optional
    var name = $('#product_name').val();
    if(name == '' || name == null || name == undefined)
    {
        show_info_msg("请输入产品名称.");
        return;
    }

    if((names != null) && (names != undefined) && (names.length > 0) && (Array.isArray(names)) && (names.indexOf(name) > -1 )) {
        show_info_msg("同名产品存在");
        return;
    }


    var simple_name = $('#product_simple_name').val();
    if(simple_name == '' || simple_name == null || simple_name == undefined)
    {
        show_info_msg("请输入商品简称.");
        return;
    }

    var product_data = new FormData();//form data to send

    var imgcount =0;
    var file1=null, file2=null, file3=null, file4=null;
    var target1 = $('.image-preview[data-attached = "1"] .image-upload')[0];
    var target2 = $('.image-preview[data-attached = "1"] .image-upload')[1];
    var target3 = $('.image-preview[data-attached = "1"] .image-upload')[2];
    var target4 = $('.image-preview[data-attached = "1"] .image-upload')[3];

    if(target1 && target1.files[0])
    {
        file1 = target1.files[0];
        product_data.append('file1', file1);
        imgcount++;
    }
    if(target2 && target2.files[0])
    {
        file2 = target2.files[0];
        product_data.append('file2', file2);
        imgcount++;
    }

    if(target3 && target3.files[0])
    {
        file3 = target3.files[0];
        product_data.append('file3', file3);
        imgcount++;
    }
    if(target4 && target4.files[0])
    {
        file4 = target4.files[0];
        product_data.append('file4', file4);
        imgcount++;
    }

    if(imgcount == 0)
    {
        show_info_msg("请输入产品中的至少一个图像。");
        return;
    }

    var property = $('#milk_type').val();
    var category = $('#product_category').val();
    var introduction = $('#product_introduction').val();
    var bottle_type = $('#bottle_type').val();
    if(bottle_type == "none")
    {
        show_info_msg('请选择商品规格');
        return;
    }

    var guarantee_period = $('#guarantee_period').val();
    var guarantee_req = $('#guarantee_req').val();

    var material = $('#material').val();
    var production_period = $('#production_period').val();

    var product_basket_spec = $('#product_basket_spec').val();
    var depot_need = $('#depot_need').prop('checked');

    //product price template
    var price_template_count = $('#product_area_set_result .tab-content .tab-pane').size();
    if(price_template_count == 0)
    {
        show_info_msg("请插入模板价格");
        return;
    }

    var uecontent = ue_getContent();

    product_data.append('name', name);
    product_data.append('simple_name', simple_name);
    product_data.append('category', category);
    product_data.append('introduction', introduction);
    product_data.append('bottle_type', bottle_type);
    product_data.append('guarantee_period', guarantee_period);
    product_data.append('guarantee_req', guarantee_req);
    product_data.append('material', material);
    product_data.append('production_period', production_period);
    product_data.append('product_basket_spec', product_basket_spec);
    product_data.append('depot_need', depot_need);
    product_data.append('uecontent', uecontent);
    product_data.append('property', property);

    //insert product
    $.ajax({
        type:'POST',
        url: API_URL+'gongchang/naipinluru/insert_product',
        data: product_data,
        processData:false,
        contentType:false,
        success: function(data){
            console.log("result: "+data);
            if(data.status == "success")
            {
                var product_id = data.saved_product_id;
                insert_product_price_template(product_id);
            } else {
                show_warning_msg(data.message);
            }
        },
        error:function(data){
            console.log(data);
            show_err_msg("虽然节能产品，错误发生. 请稍后再试.");
        }
    });
}