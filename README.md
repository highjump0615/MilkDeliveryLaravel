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

- jQuery twbsPagination 分页插件  
- jQuery打印插件
- Switchery开关按钮插件
- jQuery confirm插件 v2.3.1
- jQuery iCheck插件
- jQuery chosen选择框插件
- 自制上传图片预览jQuery插件
- HTML5 Webcam插件
- 定制Bootstrap日历输入按周送、按月送数量
- jQuery tags input插件
- UEditor编辑器
- jQuery multiselect插件
- jQuery star-rating插件
- jQuery notify插件
- jQuery metisMenu插件
- jQuery pace页面加载进度条插件


#### 2.2 后段开发 (Laravel框架 v5.2.45) 

- Laravel Excel插件
- 微信支付PHP插件
  
## Need to Improve  
- 提高加载地区列表速度
- 微信公众号、支付采用Laravel插件  
- model里删除多余的appends属性, 换成get函数，提高性能
- 屏幕小界面布局很难看  
... ...

------
Testing is in progress...