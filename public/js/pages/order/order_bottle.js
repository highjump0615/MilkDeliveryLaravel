/**
 * Created by Administrator on 3/22/17.
 */

/**
 * 日期转换带格式的string
 * @param date
 * @param fmt
 * @returns {*}
 */
function formatDate(date, fmt) { //author: meizz 
    var o = {
        "M+": date.getMonth() + 1, //月份 
        "d+": date.getDate() //日 
    };

    if (/(y+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, (date.getFullYear() + "").substr(4 - RegExp.$1.length));
    for (var k in o) {
        if (new RegExp("(" + k + ")").test(fmt))
            fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
    }

    return fmt;
}

//bottle object
var bottle = function (date, num){
    this.date = date;
    this.num = num;

    // 格式化的数据值 如2017-03-23:4
    this.getFormattedValue = function () {
        return formatDate(this.date, 'yyyy-MM-dd') + ':' + this.num;
    };
};
