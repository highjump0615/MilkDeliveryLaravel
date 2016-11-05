$(document).on('click', 'button[data-action="edit_template"]', function(){
    
    $('#add_price_bt').hide();
    $('#update_price_bt').show();

    var tab = $(this).closest('.tab-pane');

    var province_name = $(tab).find('span.province_sp').html().toString();
    var city_name = $(tab).find('span.city_sp').html().toString();
    var districts = $(tab).find('span.district_sp').html().toString();
        districts = districts.trim();

    selected_city_for_update = city_name;
    selected_district_array = districts.split(',');

    //set province, city, district
    var province_list = $('.province_list');
    var city_list = $('.city_list');
    var district_list = $('.district_list');

    $(province_list).find('option[value="'+province_name+'"]').prop('selected', true);
    $(province_list).trigger('change');

    $(city_list).find('option[value="'+city_name+'"]').prop('selected', true);

    var temp_name = $(tab).find('input.name_sp').val();

    var retail = $(tab).find('span.retail_sp').html().toString();
    var month = parseFloat($(tab).find('span.month_sp').html());
    var season = parseFloat($(tab).find('span.season_sp').html());
    var half_year = parseFloat($(tab).find('span.half_year_sp').html());
    var settle = parseFloat($(tab).find('span.settle_sp').html());

    $('#template_name').val(temp_name);
    
    $('#retail_price').val(retail);
    $('#month_price').val(month);
    $('#season_price').val(season);
    $('#half_year_price').val(half_year);
    $('#settle_price').val(settle);

    var price_tp_id = $(tab).find('input.price_tp_id').val();
    //set price id for the button
    $('#update_price_bt').attr('data-pricetp-id', price_tp_id);
});

function cancel_update_product(){
   //refresh this page
   window.location = SITE_URL+'gongchang/jichuxinxi/shangpin';
}

function update_product(){

    //check data integrtiy-optional
    var name = $('#product_name').val();
    if(name == '' || name == null || name == undefined)
    {
        show_warning_msg("请输入产品名称.");
        return;
    }

    if((names != null) && (names != undefined) && (names.length > 0) && (name!=current_product_name) && (Array.isArray(names)) && (names.indexOf(name) > -1 )) {
        show_warning_msg("同名产品存在");
        return;
    }
    

    var simple_name = $('#product_simple_name').val();
    if(simple_name == '' || simple_name == null || simple_name == undefined)
    {
        show_warning_msg("请输入商品简称.");
        return;
    }

    var product_data = new FormData();//form data to send

    var imgcount = $('.image-preview[data-attached = "1"]').length;

    if(imgcount == 0)
    {
        show_warning_msg("请输入产品中的至少一个图像。");
        return;
    }

    var file1=null, file2=null, file3=null, file4=null;
    var target1 = $('.image-preview[data-attached = "1"] .image-upload')[0];
    var target2 = $('.image-preview[data-attached = "1"] .image-upload')[1];
    var target3 = $('.image-preview[data-attached = "1"] .image-upload')[2];
    var target4 = $('.image-preview[data-attached = "1"] .image-upload')[3];

    if(target1 && target1.files[0])
    {
        file1 = target1.files[0];
        product_data.append('file1', file1);
    }
    if(target2 && target2.files[0])
    {
        file2 = target2.files[0];
        product_data.append('file2', file2);
    }

    if(target3 && target3.files[0])
    {
        file3 = target3.files[0];
        product_data.append('file3', file3);
    }
    if(target4 && target4.files[0])
    {
        file4 = target4.files[0];
        product_data.append('file4', file4);
    }


    // var preview1 =$('.image-preview').eq(0);
    // var preview2 =$('.image-preview').eq(1);
    // var preview3 =$('.image-preview').eq(2);
    // var preview4 =$('.image-preview').eq(3);
    //
    // var status1 = "NOCHANGED", status2 = "NOCHANGED", status3 = "NOCHANGED", status4 = "NOCHANGED";
    // var file1, file2, file3, file4;
    //
    // //Removed, added, changed
    // if((preview1.attr("data-changed") == "1") && (preview1.attr("data-attached") == "1"))
    // {
    //     file1 = preview1.find('.image-upload')[0].files[0];
    //     product_data.append('file1', file1);
    //
    //     if(preview1.attr("origin-data-attached") == "1")
    //         status1 = "Changed";
    //     else
    //         status1 = "Added";
    //
    // }
    // else if ((preview1.attr("data-changed") == "1") && (preview1.attr("data-attached") == "0"))
    // {
    //     status1 = "Removed";
    //
    // } else if ((preview1.attr("data-changed") == "0")){
    //
    //     status1 = "NOCHANGED";
    // }
    // product_data.append('status1', status1);
    //
    //
    // if((preview2.attr("data-changed") == "1") && (preview2.attr("data-attached") == "1"))
    // {
    //     file2 = preview2.find('.image-upload')[0].files[0];
    //     product_data.append('file2', file2);
    //
    //     if(preview2.attr("origin-data-attached") == "1")
    //         status2 = "Added";
    //     else
    //         status2 = "Changed";
    // }
    // else if ((preview2.attr("data-changed") == "1") && (preview2.attr("data-attached") == "0"))
    // {
    //     status2 = "Removed";
    //
    // } else if ((preview2.attr("data-changed") == "0")){
    //
    //     status2 = "NOCHANGED";
    // }
    // product_data.append('status2', status2);
    //
    // if((preview3.attr("data-changed") == "1") && (preview3.attr("data-attached") == "1"))
    // {
    //     file3 = preview3.find('.image-upload')[0].files[0];
    //     product_data.append('file3', file3);
    //     if(preview3.attr("origin-data-attached") == "1")
    //         status3 = "Added";
    //     else
    //         status3 = "Changed";
    //
    // }
    // else if ((preview3.attr("data-changed") == "1") && (preview3.attr("data-attached") == "0"))
    // {
    //     status3 = "Removed";
    //
    // } else if ((preview3.attr("data-changed") == "0")){
    //
    //     status3 = "NOCHANGED";
    // }
    // product_data.append('status3', status3);
    //
    //
    //  if((preview4.attr("data-changed") == "1") && (preview4.attr("data-attached") == "1"))
    // {
    //     file4 = preview4.find('.image-upload')[0].files[0];
    //     product_data.append('file4', file4);
    //     if(preview4.attr("origin-data-attached") == "1")
    //         status4 = "Added";
    //     else
    //         status4 = "Changed";
    //
    // }
    // else if ((preview4.attr("data-changed") == "1") && (preview4.attr("data-attached") == "0"))
    // {
    //     status4 = "Removed";
    //
    // } else if ((preview4.attr("data-changed") == "0")){
    //
    //     status4 = "NOCHANGED";
    // }
    // product_data.append('status4', status4);

    var category = $('#product_category').val();
    var introduction = $('#product_introduction').val();
    var bottle_type = $('#bottle_type').val();
    if(bottle_type == "none")
    {
        show_warning_msg('请选择商品规格');
        return;
    }
    
    var guarantee_period = $('#guarantee_period').val();
    var guarantee_req = $('#guarantee_req').val();
    var property = $('#milk_type').val();

    var material = $('#material').val();
    var production_period = $('#production_period').val();

    var product_basket_spec = $('#product_basket_spec').val();
    var depot_need = $('#depot_need').prop('checked');

    var uecontent = ue_getContent();

    //product price template
    var price_template_count = $('#product_area_set_result .tab-content .tab-pane').size();
    if(price_template_count == 0)
    {
        show_warning_msg("请插入模板价格");
        return;
    }

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
    product_data.append('cpid', current_product_id);
    product_data.append('property', property);

    //save product
    $.ajax({
        type:'POST',
        url: API_URL+'gongchang/jichuxinxi/shangpin/shangpinxiangqing/update_product',
        data: product_data,
        processData:false,
        contentType:false,
        success: function(data){
            console.log("result: "+data);
            if(data.status == "success")
            {
                if(price_temp_changed)
                {
                    update_product_price_template(current_product_id);
                } else
                {
                    show_success_msg("更新成功");
                    window.location = SITE_URL+'gongchang/jichuxinxi/shangpin';
                }

            } else {
                if(data.message)
                    show_warning_msg(data.message);
            }
        },
        error:function(data){
            console.log(data);
            show_err_msg("虽然节能产品，错误发生. 请稍后再试.");
        }
    });        
}

$('#update_price_bt').click(function(){
    var price_tp_id = $(this).data('pricetp-id');
    update_product_price_one_template(price_tp_id);
});

function update_product_price_one_template(price_tp_id)
{
    var province = $('.province_list').val();
    var city = $('.city_list').val();

    var template_name = $('#template_name').val();
    var retail_price = $('#retail_price').val();
    var month_price = $('#month_price').val();
    var season_price = $('#season_price').val();
    var half_year_price = $('#half_year_price').val();
    var settle_price = $('#settle_price').val();

    if ((retail_price === '') || (month_price === '') || (season_price === '') || (half_year_price === '') || (settle_price === '')) {
        show_info_msg('请填写价格模板');
        return;
    }

    if (province == "" || !province) {
        show_info_msg("请选择省");
        return;
    }

    if (city == "" || !city) {
        show_info_msg("请选择城市");
        return;
    }

    var district_area="";

    var district_list = $('.district_list');

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

    var price_tp = {'product_id': current_product_id, 'template_id':price_tp_id, 'template_name':template_name, 'province':province, 'city':city, 'district':district_area, 'retail':retail_price, 'month':month_price, 'season':season_price, 'half': half_year_price, 'settle':settle_price};

    console.log(price_tp);

    $.ajax({
        type:'POST',
        url: API_URL+'gongchang/jichuxinxi/shangpin/shangpinxiangqing/update_product_price_template_one',
        data: price_tp,
        success: function(data){
            console.log("result: "+data);
            if(data.status == "success")
            {
                show_success_msg("更新成功");
                init_price_template();
                location.reload();
            } else {
                if(data.message)
                    show_warning_msg(data.message);
            }
        },
        error:function(data){
            console.log(data);
            show_err_msg("虽然节能产品，错误发生. 请稍后再试.");
        }
    });

}

function update_product_price_template(pid){
    var price_template_data;

    price_template_data = new Array();

    $('#product_area_set_result .tab-content .tab-pane').each(function(){
        var template_name = $(this).find('.name_sp').val();

        var province = $(this).find('.province_sp').text();
        var city = $(this).find('.city_sp').text();
        var district = $(this).find('.district_sp').text();
        var retail_price = $(this).find('.retail_sp').text();
        var month_price = $(this).find('.month_sp').text();
        var season_price = $(this).find('.season_sp').text();
        var half_year_price = $(this).find('.half_year_sp').text();
        var settle_price = $(this).find('.settle_sp').text();

        district = district.replace(/\s+/g, '');

        var price_tp = {'template_name':template_name, 'province':province, 'city':city, 'district':district, 'retail':retail_price, 'month':month_price, 'season':season_price, 'half': half_year_price, 'settle':settle_price};
        price_template_data.push(price_tp);
    });

    var product_data = {'product_id': pid, 'price_template_data':price_template_data};

    $.ajax({
        type:'POST',
        url: API_URL+'gongchang/jichuxinxi/shangpin/shangpinxiangqing/update_product_price',
        data: product_data,
        success: function(data){
            console.log("result: "+data);
            if(data.status == "success")
            {
                show_success_msg("更新成功");
                window.location = SITE_URL+'gongchang/jichuxinxi/shangpin';
                
            } else {
                if(data.message)
                    show_warning_msg(data.message);
            }
        },
        error:function(data){
            console.log(data);
            show_err_msg("虽然节能产品，错误发生. 请稍后再试.");
        }
    });      
}





