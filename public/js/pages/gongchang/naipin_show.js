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

    var butUpdate = $('#update_price_bt');
    butUpdate.attr('data-tab-index', $(this).data('tab-index'));
    butUpdate.attr('data-price-id', getPriceId(tab));
});

function cancel_update_product(){
   //refresh this page
   window.location = SITE_URL+'gongchang/jichuxinxi/shangpin';
}

/**
 * 点击修改价格按钮
 */
$('#update_price_bt').click(function(){
    add_price_template($(this).data('tab-index'), $(this).data('price-id'));
});



