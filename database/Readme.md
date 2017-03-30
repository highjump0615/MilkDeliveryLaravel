## Database Structure of Milk Delivery System

> 采用关系型数据库，下面是主要数据表的结构

* 所有数据表包含id字段

### 基础信息
<h4 id="1"> 1. factory </h4>

字段名称 | 数据类型 | 长度 | 允许空 | 说明 | 备注 | 默认值 | 实例
--- | ------- | ---- | --- | ----- | ---- | --- | ---
name | varchar | 255 | YES | 名称 | | | 圣牧高科
number | varchar | 45 | YES | 编号 | | | FAC1
contact | varchar | 45 | YES | 联系人 | | |
phone | varchar | 45 | YES | 手机号 | | |
status | int | 11 | YES | 状态 | 1: ON<br>0: OFF | | 1
last_used_ip | varchar | 45 | YES | 上次登录IP地址 | 预留 | |
end_at | date | | YES | 到期日期 | | | 2018-07-12
logo_url | varchar | 1024 | YES | LOGO链接 | | | /uploads/images/logo/***.jpg
~~public_name~~ | varchar | 45 | YES | | 过时 | | 
~~public_id~~ | varchar | 45 | YES | | 过时 | | 
~~wechat_id~~ | varchar | 45 | YES | | 过时 | | 
~~qrcode~~ | text | | YES | | 过时 | | 
~~wechat_type~~ | int | 11 | YES | | 过时 | | 
gap_day | int | 2 | YES | 新单开始日期 | | 3 | 3 
is_deleted | tinyint | 1 | NO | 是否已删除 | 0: 未删除<br>1: 已删除 | | 0 
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

#### 2. address

字段名称 | 数据类型 | 长度 | 允许空 | 说明 | 备注 | 默认值 | 实例
--- | ------- | ---- | --- | ----- | ---- | --- | ---
name | varchar | 45 | NO | 名称 | | | 内蒙古
level | int | 11 | NO | 级别 | 1: 省<br>2: 市<br>3: 区<br>4: 街道<br>5: 小区 | | 1
parent_id | int | 11 | NO | 上级地址id | 0: 一级地址 | | 0
is_active | tinyint | 1 | NO | 状态 | 1: 使用<br>0: 停用 | | 1
[factory_id](#1) | int | 11 | NO | 奶厂id | | | 1
is_deleted | tinyint | 1 | NO | 是否已删除 | 0: 未删除<br>1: 已删除 | | 0