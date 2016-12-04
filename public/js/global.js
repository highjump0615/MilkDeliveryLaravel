/**
 * Created by SuperMan on 9/8/2016.
 */

/*var BASE_URL = location.origin + (location.port && ":" + location.port) + "/";*/
var BASE_URL = location.origin + "/";
var API_URL = BASE_URL + "milk/public/api/";
var SITE_URL = BASE_URL + 'milk/public/';

// 验证手机号码形式
function verifyPhone() {
    var objInput = $('input[name="phone"]');
    objInput.prop('pattern', '^1[345678][0-9]{9}$');

    objInput.on('input', function (e) {
        e.target.setCustomValidity('');
    });
    objInput.on('invalid', function (e) {
        e.target.setCustomValidity('手机号码格式不正确');
    });
}

// 验证身份证号码形式
function verifyIdNum() {
    var objInput = $('input[name="idnumber"]');
    objInput.prop('pattern', '^(\d){15}|(\d{17}(\d|x|X))$');

    objInput.on('input', function (e) {
        e.target.setCustomValidity('');
    });
    objInput.on('invalid', function (e) {
        e.target.setCustomValidity('身份证号不符合');
    });
}
