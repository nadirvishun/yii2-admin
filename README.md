Yii2高级模板搭建的一个小项目
=========
AdminLTE模板的后台,前后台用户分两个表，实现了一些基本功能，gii模板已修改，可用gii快速创建此后台风格的页面。

## 安装
1.  下载后运行：`composer update`，安装所需的组件
2.  初始化：`init`，然后配置自己的数据库参数
3.  数据库：`yii migrate`，来生成表
4.  创建后台账户：`yii init/admin`，按照提示创建后台管理员
5.  登陆后台，进行相关操作

## 截图
![截图](https://github.com/nadirvishun/abp/blob/master/backend/web/img/screenshot.jpg)

## TODO
1.  权限管理集成