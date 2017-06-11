
$(function(){

    // 订单导入
    $('#btn-order').click(function(){
        $('#input-type').val(0);
        $('#input-upload').click();
    });

    // 客户导入
    $('#btn-customer').click(function(){
        $('#input-type').val(1);
        $('#input-upload').click();
    });

    // 地址库导入
    $('#btn-address').click(function(){
        $('#input-type').val(2);
        $('#input-upload').click();
    });

    // 打开文件处理
    $('#input-upload').change(function(){
        console.log('uploading');
        $('#upload-form').submit();
    });
});
