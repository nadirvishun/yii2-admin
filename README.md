Yii2高级模板搭建的后台
=========
AdminLTE模板的后台样式，可作为基础的后台管理

## 做了哪些
1.  前后台用户分为两个表，实现了前后台分离
2.  后台基础的config配置和menu配置，弹窗小部件等
3.  RBAC简单的权限控制，用户只能选择角色，角色再包含权限，角色和权限不能嵌套，否则想不出好的展示方式
5.  restful api相关模块基础搭建
6.  gii模板修改
    - 修改了各模板样式以适配后台样式
    - 增加了treeGrid模板，可方便生成树型结构
    - 可自动从表注释中读取注释生成i18n语言文件

## 安装
1.  下载后运行：`composer install`，安装所需的组件
2.  初始化：`init`或者`./init`，然后配置自己的数据库参数
3.  数据库：`yii migrate`，来生成表
4.  创建后台账户：`yii init/admin`，按照提示创建后台管理员
5.  登陆后台，进行相关操作

## 截图
![截图](https://github.com/nadirvishun/abp/blob/master/backend/web/img/screenshot.jpg)
