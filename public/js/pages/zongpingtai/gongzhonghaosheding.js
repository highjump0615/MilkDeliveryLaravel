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

    // 添加url标志
    $('#input_img_banner_url_' + id).val('url');
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

    // 添加url标志
    $('#input_img_promo_url_' + id).val('url');
});

/**
 * 删除广告图片
 */
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
            delete_banner(id, 1);
        },
        cancel: function () {
        }
    });
});

/**
 * 删除促销图片
 */
$(".delete-promo").on('click', function(e){
    var id = $(this).data('id');

    e.preventDefault();
    e.stopPropagation();

    $.confirm({
        icon: 'fa fa-warning',
        title: '删除',
        text: '你确定要删除此图片吗？',
        confirmButton: "确定",
        cancelButton: "取消",
        confirmButtonClass: "btn-success",
        confirm: function () {
            delete_banner(id, 2);
        },
        cancel: function () {
        }
    });
});

function delete_banner(banner_id, type) {

    // 默认是广告图
    var img_id = '#img_ad_banner_' + banner_id;
    var input_url_id = '#input_img_banner_url_' + banner_id;

    // 促销图
    if (type == 2) {
        img_id = '#img_ad_promo_' + banner_id;
        input_url_id = '#input_img_promo_url_' + banner_id;
    }

    $(img_id).attr('src', '');

    // 清除url标志
    $(input_url_id).val('');
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