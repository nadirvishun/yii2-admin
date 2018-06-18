Yii2高级模板搭建的后台
=========
AdminLTE模板的后台样式，可作为基础的后台管理

## 做了哪些
1.  前后台用户分为两个表，实现了前后台分离
2.  后台基础的setting配置(支持文本、下拉菜单、日期选择、文件等类型)和menu配置，弹窗小部件等
3.  RBAC简单的权限控制，用户选择角色，角色再包含权限，角色、权限不能自我嵌套，否则想不出好的展示方式
5.  restful api相关模块基础搭建
6.  gii模板修改
    - 修改了各模板样式以适配后台样式
    - 增加了treeGrid模板，可方便生成树型结构
    - 可自动从表中读取注释生成i18n语言文件
7.  增加后台管理员操作日志记录
8.  测试了js和css合并压缩，只是把后台各页面通用的合并了一下，生产环境下，如果第一次或者是修改了合并中引用的js或css，需要运行命令`yii asset backend/assets.php backend/config/assets-prod.php`,来重新生成合并的css和js文件，当然也可以不使用，在`backend/config/main.php`中将`assetManager`中的`bundles`注释掉即可

## 安装
1.  下载后运行：`composer install`，安装所需的组件
2.  初始化：`php init`，选择开发环境
3.  配置自己的数据库参数,然后运行`php yii migrate`，来生成表
4.  创建后台账户：`php yii init/admin`，按照提示创建后台管理员
5.  参照yii2高级模板的教程配置服务器环境及url美化，登陆后台，进行相关操作

## 截图
![菜单](https://github.com/nadirvishun/yii2-admin/blob/master/backend/web/img/menu.png)
![权限](https://github.com/nadirvishun/yii2-admin/blob/master/backend/web/img/role.png)
![日志](https://github.com/nadirvishun/yii2-admin/blob/master/backend/web/img/log.png)
![配置](https://github.com/nadirvishun/yii2-admin/blob/master/backend/web/img/setting.png)