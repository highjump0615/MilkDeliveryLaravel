/**
 * Created by Administrator on 16/12/1.
 */

$(".upload-banner-link").on('click', function (e) {
    e.preventDefault();

    var id = $(this).data('id');
    console.log(id);
    var upload_id = "#upload-banner-" + id ;

    console.log(upload_id);
    $(upload_id + ":hidden").trigger('click');
});

$(".upload-banner").on('change', function(){

    var id = $(this).data('id');
    console.log(this);
    var img_id = "#img_ad_banner_" + id;
    readURL(this, img_id);
});


$(".upload-promo-link").on('click', function (e) {
    e.preventDefault();

    var id = $(this).data('id');
    console.log(id);
    var upload_id = "#upload-promo-" + id ;

    console.log(upload_id);
    $(upload_id + ":hidden").trigger('click');
});

$(".upload-promo").on('change', function(){

    var id = $(this).data('id');
    var img_id = "#img_ad_promo_" + id;
    readURL(this, img_id);
});

$(".delete-banner").on('click', function(e){
    var id = $(this).data('id');

    e.preventDefault();
    e.stopPropagation();

    $.confirm({
        icon: 'fa fa-warning',
        title: '删除',
        text: '你会真的删除吗？',
        confirmButton: "是",
        cancelButton: "不",
        confirmButtonClass: "btn-success",
        confirm: function () {
            delete_banner(id);
        },
        cancel: function () {
        }
    });
});

function delete_banner(banner_id) {

//			var product_id = $(button).data('target');

    var senddata = {'banner_id': banner_id, 'factory_id': factory_id};
    $.ajax({
        type: 'POST',
        url: API_URL + 'zongpingtai/yonghu/gongzhonghaosheding/delete_banner',
        data: senddata,
        success: function (data) {
            console.log(data);
            if (data.status == "success") {

                var img_id = "#img_ad_banner_" + banner_id;
                $(img_id).attr('src', '');

                show_success_msg("删除成功");
            } else {
                show_warning_msg(data.message);
            }
        },
        error: function (data) {
            console.log(data);
        }

    });
}


function readURL(input, img_id) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $(img_id).attr('src', e.target.result);
        };
        reader.readAsDataURL(input.files[0]);
    }
}
