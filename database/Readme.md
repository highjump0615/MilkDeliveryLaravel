# Database Structure of Milk Delivery System

> 采用关系型数据库，下面是主要数据表的结构

* 所有数据表包含id字段
* 带链接的字段是针对该表的外键

### 基础信息
<h4 id="factory"> 1. factory (奶厂)</h4>

字段名称 | 数据类型 | 长度 | 允许空 | 说明 | 备注 | 默认值 | 实例
--- | ------- | ---- | --- | ----- | ---- | --- | ---
name | varchar | 255 | YES | 名称 | | | 圣牧高科
number | varchar | 45 | YES | 编号 | | | FAC1
contact | varchar | 45 | YES | 联系人 | | |
phone | varchar | 45 | YES | 手机号 | | |
status | int | 11 | YES | 状态 | 1: ON<br>0: OFF | | 1
last_used_ip | varchar | 45 | YES | 上次登录IP地址 | | |
end_at | date | | YES | 到期日期 | | | 2018-07-12
logo_url | varchar | 1024 | YES | LOGO链接 | | |
~~public_name~~ | varchar | 45 | YES | | 过时 | | 
~~public_id~~ | varchar | 45 | YES | | 过时 | | 
~~wechat_id~~ | varchar | 45 | YES | | 过时 | | 
~~qrcode~~ | text | | YES | | 过时 | | 
~~wechat_type~~ | int | 11 | YES | | 过时 | | 
gap_day | int | 2 | YES | 新单开始日期 | | 3 | 
is_deleted | tinyint | 1 | NO | 是否已删除 | 0: 未删除<br>1: 已删除 | 0 |
factory_id | varchar | 45 | YES | 账户名称 | | | shengmu 
factory_password | varchar | 45 | YES | 密码 | | | 
app_url | varchar | 1024 | YES | 微信公众号url | | |
app_id | varchar | 45 | YES | 微信公众号信息 | | | 
app_secret | varchar | 45 | YES | 微信公众号信息 | | | 
app_token | varchar | 32 | YES | 微信公众号信息 | | | 
app_encoding_key | varchar | 50 | YES | 微信公众号信息 | | | 
app_mchid | varchar | 50 | YES | 微信公众号信息 | | | 
app_paysignkey | varchar | 50 | YES | 微信公众号信息 | | | 
service_phone | varchar | 45| YES | 客服电话 | | | 
return_phone | varchar | 45| YES | 退订电话 | | | 

#### 2. address (地址)

字段名称 | 数据类型 | 长度 | 允许空 | 说明 | 备注 | 默认值 | 实例
--- | ------- | ---- | --- | ----- | ---- | --- | ---
name | varchar | 45 | NO | 名称 | | | 内蒙古
level | int | 11 | NO | 级别 | 1: 省<br>2: 城市<br>3: 区<br>4: 街道<br>5: 小区 | | 1
parent_id | int | 11 | NO | 上级地址id | 0: 一级地址 | | 0
is_active | tinyint | 1 | NO | 状态 | 1: 使用<br>0: 停用 | 1 |
[factory_id](#factory) | int | 11 | NO | 奶厂id | | 1 |
is_deleted | tinyint | 1 | NO | 是否已删除 | 0: 未删除<br>1: 已删除 | 0 |

#### 3. province (省)

字段名称 | 数据类型 | 长度 | 允许空 | 说明 | 备注 | 默认值 | 实例
--- | ------- | ---- | --- | ----- | ---- | --- | ---
code | varchar | 6 | NO | 区号 | | | 150000
name | varchar | 20 | NO | 名称 | | | 内蒙古

#### 4. city (城市)

字段名称 | 数据类型 | 长度 | 允许空 | 说明 | 备注 | 默认值 | 实例
--- | ------- | ---- | --- | ----- | ---- | --- | ---
code | varchar | 6 | NO | 区号 | | | 150100
name | varchar | 20 | NO | 名称 | | | 呼和浩特市
provincecode | varchar | 6 | NO | 所属省区号 | | | 150000

#### 5. district (区)

字段名称 | 数据类型 | 长度 | 允许空 | 说明 | 备注 | 默认值 | 实例
--- | ------- | ---- | --- | ----- | ---- | --- | ---
code | varchar | 6 | NO | 区号 | | | 150102
name | varchar | 20 | NO | 名称 | | | 新城区
citycode | varchar | 6 | NO | 所属城市区号 | | | 150100

<h4 id="customer">6. customer (客户)</h4>

字段名称 | 数据类型 | 长度 | 允许空 | 说明 | 备注 | 默认值 | 实例
--- | ------- | ---- | --- | ----- | ---- | --- | ---
name | varchar | 45 | YES | 名称 | | | 张展
phone | varchar | 45 | YES | 电话 | | | 13704710001
address | varchar | 1024 | YES | 全地址 | | | 内蒙古 呼和浩特市 玉泉区 展东路 紫华园 A-1-102
[station_id](#station) | int | 11 | YES | 奶站id | | | 3
status | int | 11 | YES | 状态 | | | 
[milkman_id](#milkman) | int | 11 | YES | 配送员id | | | 1 
[factory_id](#factory) | int | 11 | NO | 奶厂id | | | 1
remain_amount | float | | YES| 账户余额 | | 0 | 20.0
is_deleted | tinyint | 1 | NO | 是否已删除 | 0: 未删除<br>1: 已删除 | 0 |
created_at | datetime | | YES | 创建时间 | | | 2017-03-28 05:17:48
updated_at | datetime | | YES | 更新时间 | | | 2017-03-28 05:17:48

<h4 id="station">7. deliverystations (奶站)</h4>

字段名称 | 数据类型 | 长度 | 允许空 | 说明 | 备注 | 默认值 | 实例
--- | ------- | ---- | --- | ----- | ---- | --- | ---
name | varchar | 255 | YES | 名称 | | | 新希望奶站
address | varchar | 1024 | YES | 地址 | | | 内蒙古 呼和浩特市 新城区 经济开发区01
boss | varchar | 45 | YES | 负责人 | | | 王蒙
phone | varchar | 45 | YES | 电话 | | | 13704710001
number | varchar | 45 | YES | 编号 | | | F1_NZ1
image_url | varchar | 1024 | YES | 图片url | | | F1_NZ1
[factory_id](#factory) | int | 11 | NO | 所属奶厂id | | | 1
[station_type](#dstype) | int | 11 | NO | 奶站类型 | | | 1
[payment_calc_type](#dspctype) | int | 11 | NO | 费用结算方式 | | | 1
billing_account_name | varchar | 45 | YES | 结算账户 | | |
billing_account_card_no | varchar | 45 | YES | 结算账户卡号 | | |
freepay_account_name | varchar | 45 | YES | 自由账户 | | |
billing_account_card_no | varchar | 45 | YES | 自由账户卡号 | | |
guarantee_receipt_number | varchar | 45 | YES | 收据凭证 | | |
guarantee_receipt_path | varchar | 1024 | YES | 收据凭证url | | |
last_used_ip | varchar | 45 | YES | 上次登录IP地址 | | |
init_guarantee_amount | double | | YES | 保证金金额 | | | 20000
init_delivery_credit_amount | double | | YES | 配送信用额度 | | | 8000
delivery_credit_balance | double | | YES | 配送信用额数 | | 0 | -3016
calculation_balance | double | | YES | 结算账户余额 | | 0 | 314
init_business_credit_amount | double | | YES | 自营信用额度 | | | 5000
business_credit_balance | double | | YES | 自营账户余额 | | 0 | -2969.9
~~userkind~~ | int | 3 | YES | 用户类型 | 过时 | |
created_at | datetime | | YES | 创建时间 | | | 2017-03-28 05:17:48
updated_at | datetime | | YES | 更新时间 | | | 2017-03-28 05:17:48
is_deleted | tinyint | 1 | NO | 是否已删除 | 0: 未删除<br>1: 已删除 | 0 |
status | int | 11 | YES | 状态 | 1: ON<br>0: OFF | 1 |

<h4 id="dstype">8. dstype (奶站类型)</h4>

字段名称 | 数据类型 | 长度 | 允许空 | 说明 | 备注 | 默认值 | 实例
--- | ------- | ---- | --- | ----- | ---- | --- | ---
name | varchar | 45 | YES | 名称 | | | 奶站
is_active | tinyint | 1 | NO | 状态 | 1: 使用<br>0: 停用 | 1 |

<h4 id="dspctype">9. dspaymentcalctypes (费用结算方式)</h4>

字段名称 | 数据类型 | 长度 | 允许空 | 说明 | 备注 | 默认值 | 实例
--- | ------- | ---- | --- | ----- | ---- | --- | ---
name | varchar | 45 | YES | 名称 | | | 配送费结算
is_active | tinyint | 1 | NO | 状态 | 1: 使用<br>0: 停用 | 1 |

<h4 id="milkman">10. milkman (配送员)</h4>

字段名称 | 数据类型 | 长度 | 允许空 | 说明 | 备注 | 默认值 | 实例
--- | ------- | ---- | --- | ----- | ---- | --- | ---
name | varchar | 255 | YES | 姓名 | | | 
phone | varchar | 45 | YES | 手机号 | | | 
[station_id](#station) | int | 11 | YES | 所属奶站id | | | 3
number | varchar | 45 | YES | 身份证号 | | |
is_active | tinyint | 1 | NO | 状态 | 1: 使用<br>0: 停用 | 1 |

<h4 id="dtype">11. deliverytype (配送规则类型)</h4>

字段名称 | 数据类型 | 长度 | 允许空 | 说明 | 备注 | 默认值 | 实例
--- | ------- | ---- | --- | ----- | ---- | --- | ---
name | varchar | 45 | YES | 姓名 | | | 天天送 

<h4 id="darea">12. dsdeliveryarea (奶站配送范围)</h4>

字段名称 | 数据类型 | 长度 | 允许空 | 说明 | 备注 | 默认值 | 实例
--- | ------- | ---- | --- | ----- | ---- | --- | ---
address | varchar | 1024 | YES | 地址 | | | 内蒙古 呼和浩特市 新城区 丰州南路 巨海城
[station_id](#station) | int | 11 | YES | 奶站id | | | 3

<h4 id="dsfem">13. dsflatentermodes (单元门进入方式)</h4>

字段名称 | 数据类型 | 长度 | 允许空 | 说明 | 备注 | 默认值 | 实例
--- | ------- | ---- | --- | ----- | ---- | --- | ---
name | varchar | 45 | YES | 名称 | | | password 

<h4 id="bottletype">14. mfbottletype (奶瓶类型)</h4>

字段名称 | 数据类型 | 长度 | 允许空 | 说明 | 备注 | 默认值 | 实例
--- | ------- | ---- | --- | ----- | ---- | --- | ---
[factory_id](#factory) | int | 11 | NO | 奶厂id | | | 1
name | varchar | 45 | YES | 名称 | | | 200ml 
number | varchar | 128 | YES | 编号 | | | F1_BOTTLE0 
is_deleted | tinyint | 1 | NO | 是否已删除 | 0: 未删除<br>1: 已删除 | | 0

<h4 id="boxtype">15. mfboxtype (奶框类型)</h4>

字段名称 | 数据类型 | 长度 | 允许空 | 说明 | 备注 | 默认值 | 实例
--- | ------- | ---- | --- | ----- | ---- | --- | ---
[factory_id](#factory) | int | 11 | NO | 奶厂id | | | 1
name | varchar | 45 | YES | 名称 | | | 32瓶装 
number | varchar | 128 | YES | 编号 | | | F1_BOX0
is_deleted | tinyint | 1 | NO | 是否已删除 | 0: 未删除<br>1: 已删除 | | 0

#### 16. mfdeliverytime (配送时间)

字段名称 | 数据类型 | 长度 | 允许空 | 说明 | 备注 | 默认值 | 实例
--- | ------- | ---- | --- | ----- | ---- | --- | ---
[factory_id](#factory) | int | 11 | NO | 奶厂id | | | 1
morning_start_at | varchar | 20 | YES | 上午开始时间 | | | 6:00 
morning_end_at | varchar | 20 | YES | 上午结束时间 | | | 12:00
afternoon_start_at | varchar | 20 | YES | 下午开始时间 | | | 14:00
afternoon_end_at | varchar | 20 | YES | 下午结束时间 | | | 20:00

#### 17. mfdeliverytype (奶厂配送规则类型)

字段名称 | 数据类型 | 长度 | 允许空 | 说明 | 备注 | 默认值 | 实例
--- | ------- | ---- | --- | ----- | ---- | --- | ---
[delivery_type](#dtype) | int | 11 | NO | 配送规则类型id | | | 1
[factory_id](#factory) | int | 11 | NO | 奶厂id | | | 1
is_active | tinyint | 1 | NO | 状态 | 1: 使用<br>0: 停用 | 1 |

#### ~~18. notificationcategory (消息类型， 过时)~~

#### 19. mfnotifications (奶厂消息)

字段名称 | 数据类型 | 长度 | 允许空 | 说明 | 备注 | 默认值 | 实例
--- | ------- | ---- | --- | ----- | ---- | --- | ---
[factory_id](#factory) | int | 11 | NO | 奶厂id | | | 1
category | int | 11 | NO | 消息类型id | | | 200
title | varchar | 1024 | YES | 标题 | | | 
content | text | | YES | 内容 | | | 
~~status~~ | int | 11 | YES | 状态 | 过时 | | 
read | tinyint | 1 | YES | 已读 | 0: 未读<br>1: 已读 | | 0 
created_at | datetime | | YES | 创建时间 | | | 2017-03-28 05:17:48
updated_at | datetime | | YES | 更新时间 | | | 2017-03-28 05:17:48

#### 20. dsnotifications (奶站消息)

字段名称 | 数据类型 | 长度 | 允许空 | 说明 | 备注 | 默认值 | 实例
--- | ------- | ---- | --- | ----- | ---- | --- | ---
[station_id](#station) | int | 11 | NO | 奶站id | | | 3
category | int | 11 | NO | 消息类型id | | | 200
title | varchar | 1024 | YES | 标题 | | | 
content | text | | YES | 内容 | | | 
~~status~~ | int | 11 | YES | 状态 | 过时 | | 
read | tinyint | 1 | YES | 已读 | 0: 未读<br>1: 已读 | | 0 
created_at | datetime | | YES | 创建时间 | | | 2017-03-28 05:17:48
updated_at | datetime | | YES | 更新时间 | | | 2017-03-28 05:17:48

<h4 id="otype">21. ordertype (订单类型)</h4>

字段名称 | 数据类型 | 长度 | 允许空 | 说明 | 备注 | 默认值 | 实例
--- | ------- | ---- | --- | ----- | ---- | --- | ---
name | varchar | 45 | YES | 名称 | | | 月单
days | int | 11 | YES | 天数 | | | 30

#### 22. mfordertype (订单类型)

字段名称 | 数据类型 | 长度 | 允许空 | 说明 | 备注 | 默认值 | 实例
--- | ------- | ---- | --- | ----- | ---- | --- | ---
[factory_id](#factory) | int | 11 | YES | 奶厂id | | | 1
[order_type](#otype) | int | 11 | YES | 订单类型id | | | 1
is_active | tinyint | 1 | NO | 状态 | 1: 使用<br>0: 停用 | 1 |
~~name~~ | varchar | 45 | YES | 名称 | 过时 | |

#### 23. milkmandeliveryarea (配送员配送范围)

字段名称 | 数据类型 | 长度 | 允许空 | 说明 | 备注 | 默认值 | 实例
--- | ------- | ---- | --- | ----- | ---- | --- | ---
[milkman_id](#milkman) | int | 11 | NO | 配送员id | | | 1 
address | varchar | 45 | NO | 地址 | | | 内蒙古 呼和浩特市 新城区 新建西街 36中 
order | int | 11 | NO | 排列顺序 | | | 1 

<h4 id="ochecker">24. ordercheckers (征订员)</h4>

字段名称 | 数据类型 | 长度 | 允许空 | 说明 | 备注 | 默认值 | 实例
--- | ------- | ---- | --- | ----- | ---- | --- | ---
name | varchar | 255 | YES | 名称 | | | 
number | varchar | 45 | YES | 编号 | | | ZDY0001 
phone | varchar | 45 | YES | 手机号 | | |  
[station_id](#station) | int | 11 | YES | 奶站id | | | 3
[or_factory_id](#factory) | int | 11 | YES | 奶厂id | | | 1
is_active | tinyint | 1 | YES | 状态 | 1: 使用<br>0: 停用 | | 1

<h4 id="oproperty">25. orderproperty (订单性质)</h4>

字段名称 | 数据类型 | 长度 | 允许空 | 说明 | 备注 | 默认值 | 实例
--- | ------- | ---- | --- | ----- | ---- | --- | ---
name | varchar | 455 | YES | 名称 | | | 新单  

<h4 id="page">26. pages (后台菜单)</h4>

字段名称 | 数据类型 | 长度 | 允许空 | 说明 | 备注 | 默认值 | 实例
--- | ------- | ---- | --- | ----- | ---- | --- | ---
backend_type | int | 11 | YES | 后台类型 | 1: 总平台<br>2: 奶厂<br>3: 奶站 | | 2 
parent_page | int | 11 | YES | 上级菜单id | 0: 一级菜单 | | 0 
name | varchar | 255 | YES | 名称 | | | 系统管理 
order_no | int | 11 | YES | 排列顺序 | | | 1 
page_ident | varchar | 45 | YES | 页面id | | | 
page_url | varchar | 255 | YES | 页面地址 | | | 
icon_name | varchar | 455 | YES | 图标名称 | | | 

<h4 id="ptype">27. paymenttype (支付类型)</h4>

字段名称 | 数据类型 | 长度 | 允许空 | 说明 | 备注 | 默认值 | 实例
--- | ------- | ---- | --- | ----- | ---- | --- | ---
name | varchar | 45 | YES | 名称 | | | 现金 

<h4 id="pcategory">28. productcategory (奶品分类)</h4>

字段名称 | 数据类型 | 长度 | 允许空 | 说明 | 备注 | 默认值 | 实例
--- | ------- | ---- | --- | ----- | ---- | --- | ---
name | varchar | 512 | YES | 名称 | | | 新品促销
[factory_id](#factory) | int | 11 | NO | 奶厂id | | | 1
is_deleted | tinyint | 1 | NO | 是否已删除 | 0: 未删除<br>1: 已删除 | 0 |
 
#### 29. ~~productspec (奶品规格，过时)~~

<h4 id="pprice">30. productprice (奶品价格模板)</h4>

字段名称 | 数据类型 | 长度 | 允许空 | 说明 | 备注 | 默认值 | 实例
--- | ------- | ---- | --- | ----- | ---- | --- | ---
template_name | varchar | 512 | YES | 名称 | | | 
[product_id](#product) | bigint | 20 | NO | 奶品id | | | 
sales_area | text | | YES | 销售区域 | 逗号分隔 | | 内蒙古 呼和浩特市 新城区, ... 
retail_price | float | | YES | 零售价 | | | 5.5  
month_price | float | | YES | 月单价各 | | | 5.2
season_price | float | | YES | 季单价各 | | | 5
half_year_price | float | | YES | 半年单价各 | | | 4.8  
settle_price | float | | YES | 结算价 | | | 3.5  

<h4 id="product">31. products (奶品)</h4>

字段名称 | 数据类型 | 长度 | 允许空 | 说明 | 备注 | 默认值 | 实例
--- | ------- | ---- | --- | ----- | ---- | --- | ---
name | varchar | 255 | YES | 名称 | | | 
simple_name | varchar | 255 | YES | 简称 | | | 
[factory_id](#factory) | int | 11 | YES | 奶厂id | | | 1
[category](#pcategory) | int | 11 | YES | 分类id | | | 1
introduction | text | | YES | 简介 | | |
property | int | 11 | NO | 属性 | 1: 鲜奶<br>2: 酸奶<br>3: 口味奶 | |
[bottle_type](#bottletype) | int | 11 | YES | 奶瓶规格 | | | 1
guarantee_period | varchar | 45 | YES | 保质期 | | | 5
guarantee_req | text | | YES | 储存条件 | | | 5度保存
material | varchar | 45 | YES | 配料 | | | 
production_period | int | 5 | YES | 生产周期 | 小时 | | 24 
[basket_spec](#boxtype) | int | 11 | YES | 奶筐规格 | | | 24 
bottle_back_factory | tinyint | 1 | YES | 是否需要返厂 | 1: 返厂<br>0: 不返厂 | | 
photo_url1 | varchar | 255 | YES | 图片1 | | | 
photo_url2 | varchar | 255 | YES | 图片2 | | | 
photo_url3 | varchar | 255 | YES | 图片3 | | | 
photo_url4 | varchar | 255 | YES | 图片4 | | | 
status | tinyint | 1 | YES | 状态 | 1: 在售<br>0: 下架 | 1 |
uecontent | text | 1 | YES | 产品详情 | | |
series_no | varchar | 45 | YES | 编号 | | | 10101
is_deleted | tinyint | 1 | NO | 是否已删除 | 0: 未删除<br>1: 已删除 | 0 |
created_at | datetime | | YES | 创建时间 | | | 2017-03-28 05:17:48
updated_at | datetime | | YES | 更新时间 | | | 2017-03-28 05:17:48

<h4 id="user">32. users (用户)</h4>

字段名称 | 数据类型 | 长度 | 允许空 | 说明 | 备注 | 默认值 | 实例
--- | ------- | ---- | --- | ----- | ---- | --- | ---
name | varchar | 255 | NO | 用户名 | | | 
password | varchar | 255 | YES | 密码 | | | 
status | int | 11 | YES | 状态 | 1: ON<br>0: OFF | 1 |
[factory_id](#factory) | int | 11 | NO | 奶厂id | | | 1
[station_id](#station) | int | 11 | YES | 奶站id | | | 3
[user_role_id](#userrole) | int | 11 | NO | 角色id | | | 3
nick_name | varchar | 255 | YES | 昵称 | | | 
backend_type | int | 11 | NO | 后台类型 | 1: 总平台<br>2: 奶厂<br>3: 奶站 | |
description | varchar | 1000 | YES | 描述 | | |
remember_token | varchar | 225 | YES | | | |
last_used_ip | varchar | 25 | YES | 上次登录IP地址 | | |
last_session | varchar | 255 | YES | | | |
created_at | datetime | | YES | 创建时间 | | | 2017-03-28 05:17:48
updated_at | datetime | | YES | 更新时间 | | | 2017-03-28 05:17:48

<h4 id="userrole">33. userroles (用户角色)</h4>

字段名称 | 数据类型 | 长度 | 允许空 | 说明 | 备注 | 默认值 | 实例
--- | ------- | ---- | --- | ----- | ---- | --- | ---
name | varchar | 45 | YES | 名称 | | | 
backend_type | int | 11 | NO | 后台类型 | 1: 总平台<br>2: 奶厂<br>3: 奶站 | |
[factory_id](#factory) | int | 11 | YES | 奶厂id | | |
[station_id](#station) | int | 11 | NO | 奶站id | | |

#### 34. userpageaccess (用户权限)

字段名称 | 数据类型 | 长度 | 允许空 | 说明 | 备注 | 默认值 | 实例
--- | ------- | ---- | --- | ----- | ---- | --- | ---
[page_id](#page) | int | 11 | NO | 菜单id | | | 
[user_role_id](#userrole) | int | 11 | NO | 角色id | | |

#### 35. systemlog (系统日志)

字段名称 | 数据类型 | 长度 | 允许空 | 说明 | 备注 | 默认值 | 实例
--- | ------- | ---- | --- | ----- | ---- | --- | ---
[user_id](#user) | int | 11 | NO | 用户id | | | 
ipaddress | varchar | 25 | YES | ip地址 | | | 
page | varchar | 20 | YES | 页面 | | | 
operation | int | 11 | YES | 操作 | 1: 添加<br>2: 删除<br>3: 修改<br>4: 导入<br>5: 导出<br>6: 打印 | |
created_at | datetime | | NO | 创建时间 | | | 2017-03-28 05:17:48
updated_at | datetime | | NO | 更新时间 | | | 2017-03-28 05:17:48

#### 36. yimeisms (亿美短信参数)

字段名称 | 数据类型 | 长度 | 允许空 | 说明 | 备注 | 默认值 | 实例
--- | ------- | ---- | --- | ----- | ---- | --- | ---
name | varchar | 255 | NO | 名称 | | | 
value | varchar | 255 | NO | 内容 | | | 

### 订单管理

<h4 id="order">37. orders (订单)</h4>

字段名称 | 数据类型 | 长度 | 允许空 | 说明 | 备注 | 默认值 | 实例
--- | ------- | ---- | --- | ----- | ---- | --- | ---
[customer_id](#customer) | int | 11 | NO | 客户id | | | 194 
phone | varchar | 45 | YES | 电话 | | | 13300101001
address | varchar | 255 | YES | 地址 | | | 北京 北京市 通州区 十里堡 十里堡新村 A-1-101
[order_property_id](#oproperty) | int | 11 | YES | 订单性质 | | | 1 
[station_id](#station) | int | 11 | NO | 奶站id | | | 5
receipt_number | varchar | 45 | YES | 票据号 | | | 8601010001 
receipt_path | varchar | 45 | YES | 票据照片 | | |
[order_checker_id](#ochecker) | int | 11 | NO | 征订员id | | | 5
milk_box_install | tinyint | 1 | YES | 是否安装奶箱 | 1: 是<br>0: 否 | | 1
total_amount | double | | YES | 订单金额 | | | 168
remaining_amount | double | | YES | 剩余金额 | | | 140
order_by_milk_card | tinyint | 1 | YES | 是否奶卡支付 | 1: 是<br>0: 否 | | 0
~~milk_card_id~~ | varchar | 45 | YES | 奶卡号 | 过时 | |
~~milk_card_code~~ | varchar | 45 | YES | 奶卡验证码 | 过时 | |
[payment_type](#page) | int | 11 | YES | 支付类型 | | | 1
status | int | 11 | YES | 状态 | 1: 新订单待审核<br>~~2: 未起奶~~<br>3: 在配送<br>~~4: 暂停~~<br>5: 新订单未通过<br>6: 退订<br>7: 已完成<br>8: 订单待审核<br>9: 订单未通过 | | 3 
ordered_at | date | | YES | 下单日期 | | | 2017-03-18 
stop_at | date | | YES | 暂停日期 | | |  
restart_at | date | | YES | 暂停恢复日期 | | |  
start_at | date | | YES | 起送日期 | | | 2017-03-20  
status_changed_at | date | | YES | 状态更新日期 | | |
comment | text | | YES | 备注 | | |
delivery_time | tinyint | 1 | YES | 配送时间 | 1: 上午<br>2: 下午 | | 1
[flat_enter_mode_id](#dsfem) | int | 11 | NO | 单元门进入方式 | | 2 |
previous_order_id | int | 11 | YES | 上回订单号 | 续单用 | |
[delivery_station_id](#dtype) | int | 11 | YES | 配送奶站id | | | 5
trans_check | tinyint | 1 | YES | 是否生成账单 | 1: 是<br>0: 否 | 0 |
[transaction_id](#trans) | int | 11 | YES | 账单id | | |
number | varchar | 45 | YES | 编号 | | 0 | F1S5C189O325
[factory_id](#factory) | int | 11 | YES | 奶厂id | | |
is_deleted | tinyint | 1 | YES | 是否已删除 | 0: 未删除<br>1: 已删除 | 0 |
created_at | datetime | | YES | 创建时间 | | | 2017-03-28 05:17:48
updated_at | datetime | | YES | 更新时间 | | | 2017-03-28 05:17:48

<h4 id="oproduct">38. orderproducts (订单奶品)</h4>

字段名称 | 数据类型 | 长度 | 允许空 | 说明 | 备注 | 默认值 | 实例
--- | ------- | ---- | --- | ----- | ---- | --- | ---
[order_id](#order) | bigint | 20 | NO | 订单id | | | 325 
[product_id](#product) | bigint | 20 | NO | 奶品id | | | 1
count_per_day | int | 11 | YES | 每次数量 | | | 1
[order_type](#otype) | int | 11 | YES | 订单类型id | | | 1
[delivery_type](#dtype) | int | 11 | YES | 配送规则id | | | 1
custom_order_dates | varchar | 2048 | YES | 配送规则<br>(按周送、随心送) | 日期:数量, 逗号分隔 | | 1:3, ...<br>2017-04-02:1, ...
total_count | int | 11 | YES | 总数量 | | | 30
total_amount | double | | YES | 金额 | | | 168
product_price | double | | YES | 单价 | | | 5.6
avg | double | | YES | 单数 | | | 1
start_at | date | | YES | 起送日期 | | | 2017-03-20
deleted_at | datetime | | YES | 删除时间 | | | 
created_at | datetime | | YES | 创建时间 | | | 2017-03-28 05:17:48
updated_at | datetime | | YES | 更新时间 | | | 2017-03-28 05:17:48

<h4 id="mdp">39. milkmandeliveryplan (配送明细)</h4>

字段名称 | 数据类型 | 长度 | 允许空 | 说明 | 备注 | 默认值 | 实例
--- | ------- | ---- | --- | ----- | ---- | --- | ---
[milkman_id](#milkman) | int | 11 | YES | 配送员id | | | 1
[station_id](#station) | int | 11 | YES | 奶站id | | | 5
[order_id](#order) | bigint | 20 | NO | 订单id | | | 325
[order_product_id](#oproduct) | bigint | 20 | NO | 订单奶品id | | | 467
~~price~~ | double | | YES | | 过时 | | 
product_price | double | | YES | 单价 | | | 3.7 
produce_at | date | | YES | 生产日期 | | | 2017-03-19 
deliver_at | date | | YES | 配送日期 | | | 2017-03-21 
status | int | 11 | YES | 状态 | 1: 待审核<br>2: 在配送<br>3: 已发货<br>4: 已配送<br>0: 取消 | | 4
plan_count | int | 11 | NO | 计划数量 | | | 1
changed_plan_count | int | 11 | NO | 变化后数量 | | | 1
delivery_count | int | 11 | NO | 配送单数量 | | | 1
delivered_count | int | 11 | NO | 返录数量 | | | 1
type | int | 11 | YES | 配送类型 | 1: 订单<br>2: 团购<br>3: 渠道<br>4: 试饮<br>6: 零售<br>~~5: 奶箱安装~~ | | 1
flag | int | 11 | YES | 标注 | 1: 奶箱安装 | 0 | 
report | varchar | 255 | NO | 返录情况 | | | 
comment | varchar | 1024 | YES | 备注 | | | 
cancel_reason | int | 11 | YES | 取消理由 | 1: 生产取消<br>2: 暂停<br>3: 订单修改 | | 
deleted_at | datetime | | YES | 删除时间 | | | 
created_at | datetime | | YES | 创建时间 | | | 2017-03-28 05:17:48
updated_at | datetime | | YES | 更新时间 | | | 2017-03-28 05:17:48

#### 40. milkcards (奶卡)

字段名称 | 数据类型 | 长度 | 允许空 | 说明 | 备注 | 默认值 | 实例
--- | ------- | ---- | --- | ----- | ---- | --- | ---
batch_number | varchar | 50 | NO | | | | 
number | varchar | 45 | NO | 编号 | | | 
product | varchar | 45 | NO | 奶品 | | | 
balance | int | 11 | NO | 金额 | | | 
password | varchar | 45 | NO | 验证码 | | | 
sale_status | int | 11 | NO | 领用状态 | 0: 未领用<br>1: 领用 | | 0 
pay_status | int | 11 | NO | 使用状态 | 0: 未使用<br>1: 使用 | | 0
recipient | varchar | 45 | YES | 领用方 | | |
sale_date | date | | YES | 领用日期 | | | 2017-03-18
payment_method | int | 11 | YES | 收款方式 | 1: 现金 | | 1
[factory_id](#factory) | int | 11 | NO | 奶厂id | | | 1
[order_id](#order) | bigint | 20 | YES | 订单id | | | 325

#### 41. orderchanges (订单修改，预留) 

字段名称 | 数据类型 | 长度 | 允许空 | 说明 | 备注 | 默认值 | 实例
--- | ------- | ---- | --- | ----- | ---- | --- | ---
[customer_id](#customer) | int | 11 | NO | 客户id | | | 
[order_id](#order) | bigint | 20 | NO | 订单id | | | 
[order_product_id](#oproduct) | bigint | 20 | NO | 订单奶品id | | | 
type | int | 11 | NO | | | |
original_value | varchar | 1024 | YES | | | |
changed_value | varchar | 1024 | YES | | | |
created_at | datetime | | YES | 创建时间 | | | 
updated_at | datetime | | YES | 更新时间 | | | 

#### 42. selforder (自营订单)

字段名称 | 数据类型 | 长度 | 允许空 | 说明 | 备注 | 默认值 | 实例
--- | ------- | ---- | --- | ----- | ---- | --- | ---
[station_id](#station) | int | 11 | NO | 奶站id | | | 5
customer_name | varchar | 45 | NO | 收件人 | | |
deliver_at | date | | YES | 配送日期 | | | 2017-03-21
phone | varchar | 45 | YES | 电话 | | | 
address | varchar | 255 | YES | 地址 | | | 
delivery_time | tinyint | 1 | YES | 配送时间 | 1: 上午<br>2: 下午 | | 1

#### 43. selforderproduct (自营订单奶品)

字段名称 | 数据类型 | 长度 | 允许空 | 说明 | 备注 | 默认值 | 实例
--- | ------- | ---- | --- | ----- | ---- | --- | ---
[order_id](#order) | bigint | 20 | NO | 订单id | | |  
[product_id](#product) | bigint | 20 | NO | 奶品id | | |
count | int | 11 | YES | 数量 | | | 
price | int | 11 | YES | 单价 | | |
 
#### 44. reviews (评价)

字段名称 | 数据类型 | 长度 | 允许空 | 说明 | 备注 | 默认值 | 实例
--- | ------- | ---- | --- | ----- | ---- | --- | ---
[customer_id](#customer) | int | 11 | NO | 客户id | | | 
[order_id](#order) | bigint | 20 | NO | 订单id | | |
mark | double | | YES | 得分 | | |
title | varchar | 512 | YES | 标题 | | |
content | text | | YES | 内容 | | |
status | int | 11 | YES | 状态 | 1: 待审核<br>2: 屏蔽<br>3: 通过 | |
created_at | datetime | | YES | 创建时间 | | |

### 生产管理

<h4 id="dspp">45. dsproductionplan (奶站计划)</h4>

字段名称 | 数据类型 | 长度 | 允许空 | 说明 | 备注 | 默认值 | 实例
--- | ------- | ---- | --- | ----- | ---- | --- | ---
[station_id](#station) | int | 11 | NO | 奶站id | | | 5
[product_id](#product) | bigint | 20 | NO | 奶品id | | | 1
order_count | int | 11 | YES | 配送计划数量 | | | 1
retail | int | 11 | YES | 零售数量 | | | 10
test_drink | int | 11 | YES | 试饮赠品数量 | | | 0
group_sale | int | 11 | YES | 团购业务数量 | | | 0
channel_sale | int | 11 | YES | 渠道销售数量 | | | 0
actual_count | int | 11 | YES | 奶厂发货数量 | | | 11
confirm_count | int | 11 | YES | 奶站签收数量 | | | 11
status | int | 11 | NO | 状态 | 1: 提交<br>2: 待审核<br>3: 生产取消<br>4: 待生产<br>5: 已生产<br>6: 已发货<br>7: 已签收 | | 7
settle_product_price | double | | NO | 结算价 | | | 3.5
subtotal_count | int | 11 | YES | 总数量 | | | 11
subtotal_money | double | | YES | 总数量 | | | 40.2
comment | varchar | 1024 | YES | 备注 | | | 
produce_start_at | date | | YES | 生产开始日期 | | | 2017-03-19 
produce_end_at | date | | YES | 生产结束日期 | | | 2017-03-19
box_count | int | 11 | YES | 奶框数量 | | | 
sender_name | varchar | 45 | YES | 发货人 | | | 
car_number | varchar | 45 | YES | 车牌号 | | | 

#### 46. mfproductionplan (奶厂生产计划)

字段名称 | 数据类型 | 长度 | 允许空 | 说明 | 备注 | 默认值 | 实例
--- | ------- | ---- | --- | ----- | ---- | --- | ---
[factory_id](#factory) | int | 11 | NO | 奶厂id | | | 1
[product_id](#product) | bigint | 20 | NO | 奶品id | | | 3
count | int | 11 | YES | 生产数量 | | | 60
real_count | int | 11 | YES | 实际生产数量 | | | 60
start_at | date | | YES | 生产开始日期 | | | 2017-03-19 
end_at | date | | YES | 生产结束日期 | | | 2017-03-19
status | int | 5 | YES | 状态 | 1: 确认<br>~~2: 开始~~<br>~~3: 已生产~~<br>~~4: 取消~~ | | 1

### 配送管理

#### 47. dsdeliveryplan (奶站配送)

字段名称 | 数据类型 | 长度 | 允许空 | 说明 | 备注 | 默认值 | 实例
--- | ------- | ---- | --- | ----- | ---- | --- | ---
[station_id](#station) | int | 11 | NO | 奶站id | | | 5
deliver_at | date | | YES | 配送日期 | | | 2017-03-21
[product_id](#product) | bigint | 20 | NO | 奶品id | | | 3
retail | int | 11 | YES | 零售数量 | | | 0
test_drink | int | 11 | YES | 试饮赠品数量 | | | 0
group_sale | int | 11 | YES | 团购业务数量 | | | 0
channel_sale | int | 11 | YES | 渠道销售数量 | | | 0
remain | int | 11 | YES | 库存数量 | | | 10
generated | int | 11 | YES | 是否生成今日配送单 | | | 1

#### 48. dsboxrefunds (奶框返厂)

字段名称 | 数据类型 | 长度 | 允许空 | 说明 | 备注 | 默认值 | 实例
--- | ------- | ---- | --- | ----- | ---- | --- | ---
[station_id](#station) | int | 11 | NO | 奶站id | | |
[box_type](#boxtype) | int | 11 | NO | 奶框类型id | | |
time | date | | YES | 返厂日期 | | | 
init_store | int | 11 | YES | 期初库存数量 | | | 
return_to_factory | int | 11 | YES | 返厂数量 | | | 
received | int | 11 | YES | 收货数量 | | | 
station_damaged | int | 11 | YES | 站内破损数量 | | | 
end_store | int | 11 | YES | 期末库存数量 | | | 

#### 49. dsbottlerefunds (奶瓶返厂)

字段名称 | 数据类型 | 长度 | 允许空 | 说明 | 备注 | 默认值 | 实例
--- | ------- | ---- | --- | ----- | ---- | --- | ---
[station_id](#station) | int | 11 | NO | 奶站id | | |
[bottle_type](#bottletype) | int | 11 | NO | 奶瓶类型id | | |
time | date | | YES | 返厂日期 | | | 
init_store | int | 11 | YES | 期初库存数量 | | | 
milkman_return | int | 11 | YES | 配送员返还数量 | | | 
return_to_factory | int | 11 | YES | 返还数量 | | | 
received | int | 11 | YES | 收货数量 | | | 
station_damaged | int | 11 | YES | 站内破损数量 | | | 
end_store | int | 11 | YES | 期末库存数量 | | | 

### 财务管理

#### 50. dsbusinesscreditbalancehistory (自营账户财务记录)

字段名称 | 数据类型 | 长度 | 允许空 | 说明 | 备注 | 默认值 | 实例
--- | ------- | ---- | --- | ----- | ---- | --- | ---
[station_id](#station) | int | 11 | NO | 奶站id | | | 5
type | int | 11 | NO | 项目 | 1: 零售业务<br>2: 团购业务<br>3: 渠道业务<br>4: 试饮赠品<br>5: 自营账户调整 | | 1
io_type | int | 11 | NO | 类型 | 1: 收款<br>2: 扣款 | | 2
amount | double | | YES | 金额 | | | 37.2
receipt_number | varchar | 45 | YES | 流水号 | | |
~~return_amount~~ | double | | YES | 返还金额 | 过时 | |
comment | varchar | 45 | YES | 备注 | | |
created_at | datetime | | YES | 创建时间 | | | 2017-03-28 05:17:48
updated_at | datetime | | YES | 更新时间 | | | 2017-03-28 05:17:48

#### 51. dscalcbalancehistory (结算账户财务记录)

字段名称 | 数据类型 | 长度 | 允许空 | 说明 | 备注 | 默认值 | 实例
--- | ------- | ---- | --- | ----- | ---- | --- | ---
[station_id](#station) | int | 11 | NO | 奶站id | | |
type | int | 11 | NO | 项目 | 1:划转奶厂奶款<br>2:结算配送费用<br>3:结算返利或提成费<br>4:其他用途划转<br>5:奶卡订单抵顶划转公司奶款<br>6:本站实收现金订单款<br>7:收到代理商订单款<br>8:收到转入奶卡订单款<br>9:收到其他奶站转入订单款<br>10:转出由其他奶站配送订单款 | |
amount | double | | YES | 金额 | | |
~~time~~ | date | | NO | 日期 | 过时 | |
receipt_number | varchar | 45 | YES | 流水号 | | |
io_type | int | 11 | NO | 类型 | 1: 收款<br>2: 扣款 | |
comment | varchar | 45 | YES | 备注 | | |
created_at | datetime | | YES | 创建时间 | | | 
updated_at | datetime | | YES | 更新时间 | | |

#### ~~52. dsdeliverycreditbalancehistory (奶站订单金额记录, 过时)~~

字段名称 | 数据类型 | 长度 | 允许空 | 说明 | 备注 | 默认值 | 实例
--- | ------- | ---- | --- | ----- | ---- | --- | ---
[station_id](#station) | int | 11 | NO | 奶站id | | |
type | int | 11 | NO | 项目 | 1:本站实收现金订单款<br>2:收到代理商订单款<br>3:收到转入奶卡订单款<br>4:收到其他奶站转入订单款<br>5:转出由其他奶站配送订单款 | |
amount | double | | YES | 金额 | | |
time | date | | NO | 日期 | 过时 | |
receipt_number | varchar | 45 | YES | 流水号 | | |
io_type | int | 11 | NO | 类型 | 1: 收款<br>2: 扣款 | |
comment | varchar | 45 | YES | 备注 | | |

<h4 id="dstrpay">53. dstransactionpay (账单转账)</h4>

字段名称 | 数据类型 | 长度 | 允许空 | 说明 | 备注 | 默认值 | 实例
--- | ------- | ---- | --- | ----- | ---- | --- | ---
receipt_number | varchar | 45 | NO | 流水号 | | |
amount | double | | YES | 金额 | | |
paid_at | datetime | | YES | 时间 | | |
comment | datetime | | YES | 备注 | | |
[payment_type](#ptype) | int | 11 | YES | 支付类型 | | |

<h4 id="trans">54. dstransactions (账单)</h4>

字段名称 | 数据类型 | 长度 | 允许空 | 说明 | 备注 | 默认值 | 实例
--- | ------- | ---- | --- | ----- | ---- | --- | ---
[station_id](#station) | int | 11 | NO | 奶站id | | |
[delivery_station_id](#station) | int | 11 | NO | 配送奶站id | | |
[payment_type](#ptype) | int | 11 | YES | 支付类型 | | |
total_amount | double | | YES | 金额 | | |
order_from | date | | YES | 订单日期范围 | | |
order_to | date | | YES | 订单日期范围 | | |
order_count | int | 11 | YES | 订单数量 | | |
[transaction_pay_id](#dstrpay) | int | 11 | YES | 账单转账id | | |
created_at | datetime | | YES | 创建时间 | | | 
updated_at | datetime | | YES | 更新时间 | | |

#### 55. ~~mfaccounttransferhistory (奶厂转账记录， 过时)~~

#### 56. ~~ordertransaction (订单账单， 过时)~~

#### 57. stationsmoneytransfer (其他奶站转账记录)

字段名称 | 数据类型 | 长度 | 允许空 | 说明 | 备注 | 默认值 | 实例
--- | ------- | ---- | --- | ----- | ---- | --- | ---
[station1_id](#station) | int | 11 | YES | 奶站1id | | |
[station2_id](#station) | int | 11 | YES | 奶站2id | | |
[transaction_pay_id](#dstrpay) | int | 11 | YES | 账单转账id | | |
amount | double | | YES | 金额 | | |
remaining | double | | YES | 差额 | | |
[payment_type](#ptype) | int | 11 | YES | 支付类型 | | |
created_at | datetime | | YES | 创建时间 | | | 
updated_at | datetime | | YES | 更新时间 | | |

### 微信版

#### 58. wechatads (广告图)

字段名称 | 数据类型 | 长度 | 允许空 | 说明 | 备注 | 默认值 | 实例
--- | ------- | ---- | --- | ----- | ---- | --- | ---
[factory_id](#factory) | int | 11 | NO | 奶厂id | | |
image_url | varchar | 1024 | YES | 图片路径 | | |
[product_id](#product) | bigint | 20 | NO | 奶品id | | |
type | int | 11 | NO | 类型 | 1: 上面<br>2: 下面 | |
image_no | int | 11 | NO | 图片号 | | |

#### ~~59. wechatmenu (微信公众号菜单，过时)~~

#### 60. wxmenu (微信公众号菜单)

字段名称 | 数据类型 | 长度 | 允许空 | 说明 | 备注 | 默认值 | 实例
--- | ------- | ---- | --- | ----- | ---- | --- | ---
[factory_id](#factory) | int | 11 | NO | 奶厂id | | |
mainindex | int | 11 | NO | | | |
displayorder | int | 11 | NO | | | |
type | varchar | 50 | NO | | | |
label | varchar | 50 | NO | | | |
name | varchar | 50 | NO | 名称 | | |
url | varchar | 255 | YES | | | |
keyword | varchar | 255 | YES | | | |
app | varchar | 50 | YES | | | |

#### 61. wxaddress (地址)

字段名称 | 数据类型 | 长度 | 允许空 | 说明 | 备注 | 默认值 | 实例
--- | ------- | ---- | --- | ----- | ---- | --- | ---
[wxuser_id](#wxuser) | int | 11 | NO | 微信用户id | | |
name | varchar | 45 | YES | 收件人 | | |
phone | varchar | 45 | YES | 电话 | | |
address | varchar | 512 | YES | 地址 | | |
sub_address | varchar | 45 | YES | 门牌号 | | |
primary | tinyint | 1 | YES | 是否默认 | 1: 是<br>0: 否 | |

<h4 id="wxuser">62. wxusers (微信用户)</h4>

字段名称 | 数据类型 | 长度 | 允许空 | 说明 | 备注 | 默认值 | 实例
--- | ------- | ---- | --- | ----- | ---- | --- | ---
openid | varchar | 50 | YES | | | |
device_token | varchar | 255 | YES | | | |
[customer_id](#customer) | int | 11 | YES | 客户id | | | 
last_session | int | 11 | YES | | | | 
last_used_ip | varchar | 45 | YES | | | |
image_url | varchar | 1024 | YES | 头像url | | |
name | varchar | 45 | YES | 昵称 | | |
phone_verify_code | varchar | 45 | YES | 验证码 | | |
[factory_id](#factory) | int | 11 | NO | 奶厂id | | |
area | varchar | 255 | YES | 地区 | | |

#### 63. wxcarts (购物车)

字段名称 | 数据类型 | 长度 | 允许空 | 说明 | 备注 | 默认值 | 实例
--- | ------- | ---- | --- | ----- | ---- | --- | ---
[wxuser_id](#wxuser) | int | 11 | NO | 微信用户id | | |
[wxorder_product_id](#wxop) | int | 11 | NO | 微信奶品id | | |
created_at | datetime | | YES | 创建时间 | | | 
updated_at | datetime | | YES | 更新时间 | | |

<h4 id="wxop">64. wxorderproducts (微信奶品)</h4>

字段名称 | 数据类型 | 长度 | 允许空 | 说明 | 备注 | 默认值 | 实例
--- | ------- | ---- | --- | ----- | ---- | --- | ---
[wxuser_id](#wxuser) | int | 11 | NO | 微信用户id | | |
[factory_id](#factory) | int | 11 | NO | 奶厂id | | |
[product_id](#product) | bigint | 20 | NO | 奶品id | | |
[order_type](#otype) | int | 11 | NO | 订单类型id | | |
[delivery_type](#dtype) | int | 11 | YES | 配送规则类型id | | |
total_count | int | 11 | NO | 总数量 | | | 30
product_price | double | | NO | 单价 | | | 5.6
count_per_day | int | 11 | YES | 每次数量 | | | 1
custom_order_dates | varchar | 2048 | YES | 配送规则<br>(按周送、随心送) | 日期:数量, 逗号分隔 | | 1:3, ...<br>2017-04-02:1, ...
start_at | date | | YES | 起送日期 | | | 2017-03-20
total_amount | double | | NO | 金额 | | | 168
is_deleted | tinyint | 1 | NO | 是否已删除 | 0: 未删除<br>1: 已删除 | 0 |
deleted_at | datetime | | YES | 删除时间 | | | 
created_at | datetime | | YES | 创建时间 | | | 2017-03-28 05:17:48
updated_at | datetime | | YES | 更新时间 | | | 2017-03-28 05:17:48

#### 65. wxreview (消息) 

字段名称 | 数据类型 | 长度 | 允许空 | 说明 | 备注 | 默认值 | 实例
--- | ------- | ---- | --- | ----- | ---- | --- | ---
[customer_id](#customer) | int | 50 | NO | 客户id | | |
content | varchar | 1000 | NO | 内容 | | |
status | int | 2 | NO | 状态 | | |
read | int | 11 | NO | 是否已读 | 0: 未读<br>1: 已读 | | 
created_at | datetime | | YES | 创建时间 | | | 
updated_at | datetime | | YES | 更新时间 | | | 
