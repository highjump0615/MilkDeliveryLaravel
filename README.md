Milk Delivery Admin & Mobile Web App
======

> Milk delivery admin web application, made using Laravel PHP framework.

## Overview

### 1. 主要功能
#### 1.1 后台  
- 奶厂端  
用户管理、奶站管理、基础信息管理、订单管理、财务管理、生产管理、奶卡管理、瓶框管理、客户管理、评价管理、统计分析  
- 奶站端  
用户管理、订单管理、基础信息管理、生产配送管理、客户管理、瓶框管理、财务管理、统计分析  
- 总平台端  
系统日志、用户管理、财务管理、客户管理、统计分析  

#### 1.2 微信端  
- 订单管理  
下单、订单查看、订单修改、评价

### 2. 技术内容
#### 2.1 前段开发 (Bootstrap框架 v3.3.5) 
基于[INSPINIA模板](http://www.snschina.com/archives/2484)做后台页面设计的  

- [jQuery twbsPagination 分页插件](https://github.com/esimakin/twbs-pagination)  
- [jQuery打印插件](https://github.com/DoersGuild/jQuery.print)
- [Switchery开关按钮插件](https://github.com/abpetkov/switchery)
- [jQuery confirm插件 v2.3.1](https://github.com/craftpip/jquery-confirm)
- [jQuery iCheck插件](https://github.com/fronteed/iCheck)
- <strike>[jQuery Select2插件](https://github.com/select2/select2)</strike>
- [jQuery chosen选择框插件](https://github.com/harvesthq/chosen)
- 自制上传图片预览jQuery插件  
public/js/plugins/simpleimgupload  
public/js/plugins/imgupload
- [HTML5 Webcam插件](https://github.com/jhuckaby/webcamjs)
- 定制Bootstrap日历输入按周送、按月送数量
- [jQuery tags input插件](https://github.com/bootstrap-tagsinput/bootstrap-tagsinput)
- [UEditor编辑器](https://github.com/fex-team/ueditor)
- [jQuery multiselect插件](https://github.com/crlcu/multiselect)
- [jQuery star-rating插件](https://github.com/kartik-v/bootstrap-star-rating)
- [jQuery notify插件](https://github.com/jpillora/notifyjs)
- [jQuery metisMenu插件](https://github.com/onokumus/metisMenu)
- [jQuery pace页面加载进度条插件](https://github.com/HubSpot/pace)


#### 2.2 后段开发 (Laravel框架 v5.2.45) 

- 采用关系型数据库，数据表结构在[database目录](database)
- [Laravel Excel插件](https://github.com/Maatwebsite/Laravel-Excel)
- 微信支付PHP插件
- 通过[亿美软通](http://www.emay.cn/)短信服务实现短信验证
- 大规模数据的csv导入（订单列表）  
```
select * from TABLE  
into outfile FILE
character set gbk 
fields terminated by ','
escaped by '\"'
enclosed by '\"'
lines terminated by '\n';
```
  
## Need to Improve  
- 提高加载地区列表速度
- 微信公众号、支付采用Laravel插件  
- model里删除多余的appends属性, 换成get函数，提高性能
- ~~屏幕小界面布局很难看~~
- 微信端界面  
- ~~数据显示需要分页查询~~  
- ~~整理所有垃圾代码~~ ```->get()->first()```, ```->get()->count()```  
... ...

