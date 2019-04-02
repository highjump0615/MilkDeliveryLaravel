Milk Delivery Admin & Mobile Web App
======

> Milk delivery admin web application, made using Laravel PHP framework.

## Overview
PC端后台为主，客户端为微信端Web App，通过公众号进入

### 主要功能
#### 1. 后台  
- 奶厂端  
用户管理、奶站管理、基础信息管理、订单管理、财务管理、生产管理、奶卡管理、瓶框管理、客户管理、评价管理、统计分析  
- 奶站端  
用户管理、订单管理、基础信息管理、生产配送管理、客户管理、瓶框管理、财务管理、统计分析  
- 总平台端  
系统日志、用户管理、财务管理、客户管理、统计分析  

#### 2. 微信端  
- 订单管理  
下单、订单查看、订单修改、评价

## Techniques
Laravel框架 v5.2.45

### 1. UI开发 
- 前段框架为[Bootstrap v3.3.5](https://getbootstrap.com/docs/3.3/) 
- 基于[INSPINIA模板](http://www.snschina.com/archives/2484)实现页面设计  

### 2. 功能开发
数据库为MySQL, 数据表结构参考[database目录](database)

#### 2.1 后台网站
#### 用户登录
通过Middleware与Auth实现过滤  

- ``RedirectIfNotGongchang``  
- ``RedirectIfNotNaizhan``  
- ``RedirectIfNotZongpingtai``  


#### 2.2 REST Api
网站内通过Ajax调用实现功能
  
- */api/** 开头路径


#### 2.3 微信支付
##### 支付下单流程
- 统一下单  
生成商户订单号，获取prepay_id
- JSAPI支付  
- 生成订单

##### JSAPI支付
[https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=7_7&index=6](https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=7_7&index=6)

- 确认订单时开启支付

```javascript
// 调用微信JS api 支付
function jsApiCall(param) {
    var objParam = JSON.parse(param);

    WeixinJSBridge.invoke(
        'getBrandWCPayRequest',
        objParam,
        function (res) {
            WeixinJSBridge.log(res.err_msg);
            // 支付成功
            if (res.err_msg == 'get_brand_wcpay_request:ok') {
                // 跳转到成功页面
                window.location = SITE_URL + "weixin/zhifuchenggong?tradeNo=" + gTradeNo;
            }
            // 用户取消
            else if (res.err_msg == 'get_brand_wcpay_request:cancel') {
            }
            // 支付失败
            else {
                window.location = SITE_URL + "weixin/zhifushibai";
            }
        }
    );
}
```

##### 微信支付API
- [统一下单](https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=9_1)  
```php
// 商户订单号
$strTradeNo = time() . uniqid();

// 预支付
$worder = new \WxPayUnifiedOrder();
$worder->SetBody($strBody);
$worder->SetOut_trade_no($strTradeNo);
$worder->SetTotal_fee(intval($dAmount * 100));
$worder->SetNotify_url('http://' . $request->server('HTTP_HOST') . '/' . env('SITE_PATH') . 'weixin/payresult');
$worder->SetTrade_type("JSAPI");
$worder->SetOpenid($user->openid);

$payOrder = \WxPayApi::unifiedOrder($worder);
```

### 3. 代码技巧
#### 大规模数据的csv导入（订单列表）  
```
select * from TABLE  
into outfile FILE
character set gbk 
fields terminated by ','
escaped by '\"'
enclosed by '\"'
lines terminated by '\n';
```


### 4. Third-Party Libraries
#### 4.1 jQuery plugins
- [jQuery twbsPagination 分页插件](https://github.com/esimakin/twbs-pagination)  
- [jQuery打印插件](https://github.com/DoersGuild/jQuery.print)
- [Switchery开关按钮插件](https://github.com/abpetkov/switchery)
- [jQuery confirm插件 v2.3.1](https://github.com/craftpip/jquery-confirm)
- [jQuery iCheck插件](https://github.com/fronteed/iCheck)
- <strike>[jQuery Select2插件](https://github.com/select2/select2)</strike>
- [jQuery chosen选择框插件](https://github.com/harvesthq/chosen)
- 自制上传图片预览jQuery插件  
*public/js/plugins/simpleimgupload*  
*public/js/plugins/imgupload*
- [HTML5 Webcam插件](https://github.com/jhuckaby/webcamjs)
- 定制Bootstrap日历输入按周送、按月送数量
- [jQuery tags input插件](https://github.com/bootstrap-tagsinput/bootstrap-tagsinput)
- [UEditor编辑器](https://github.com/fex-team/ueditor)
- [jQuery multiselect插件](https://github.com/crlcu/multiselect)
- [jQuery star-rating插件](https://github.com/kartik-v/bootstrap-star-rating)
- [jQuery notify插件](https://github.com/jpillora/notifyjs)
- [jQuery metisMenu插件](https://github.com/onokumus/metisMenu)
- [jQuery pace页面加载进度条插件](https://github.com/HubSpot/pace)

#### 4.2 [Laravel Excel插件](https://github.com/Maatwebsite/Laravel-Excel)
- 数据导出为xls或csv文件

#### 4.3 [微信支付PHP SDK](https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=11_1)
*app/Lib/Payweixin*

#### 4.3 [亿美软通](http://www.emay.cn/)
借用亿美软通短信服务实现短信验证  
```php
require_once app_path() . "/Lib/ChuanglanSmsHelper/ChuanglanSmsApi.php";

class YimeiSmsCtrl extends Controller {
	public function sendSMS($phone, $msg) {
		// 发送短信验证码
	}
}
```


## Updates

### 2019-04-01
- BUG: **少数情况下，微信支付成功，但订单没生成**

#### 原因  
把生成订单接入点设置为前段收到回复``get_brand_wcpay_request:ok``之后，并没处理支付回调接口

#### 修复
收到支付回调，结果成功，直接生成订单  
``WeChatCtrl::paymentResult()``

##### 订单生成时，需要附加信息，如备注  
微信订单参数里有attach,能保存附加信息，但长度有限制(127)，所以不能使用。  
因此，数据库里做个临时订单表，统一下单时保存相关数据，生成订单成功后删除。  数据表结构参考[wxorders](database#wxorder)  


  
## Need to Improve  
- 调整vendors里面的hard code，保持vendors为初始状态
- 提高加载地区列表速度
- 微信公众号、支付采用Laravel插件  
- model里删除多余的appends属性, 换成get函数，提高性能
- ~~屏幕小界面布局很难看~~
- 微信端界面  
- ~~数据显示需要分页查询~~  
- ~~整理所有垃圾代码~~ ```->get()->first()```, ```->get()->count()```  
... ...

