<?php
return [
    'adminEmail' => 'admin@example.com',
    //上传文件路径，如果不设置，则默认default
    'defaultPath' => '/uploads/default/',
    //后台会员头像上传的路径,在backend/web目录下
    'avatarPath' => '/uploads/avatars/',
    //系统设置上传的文件路径，在backend/web目录下
    'settingPath' => '/uploads/setting/',
    //指定超级管理员所对应的id
    'superAdminId' => 1,
    //ueditor编辑器上传路径配置
    'ueditorConfig' => [
        //上传图片配置
        "imageUrlPrefix" => "/uploads",//访问路径前缀
        "imagePathFormat" => "/ueditor/image/{yyyy}{mm}{dd}/{time}{rand:6}", //上传保存路径
        "imageRoot" => Yii::getAlias("@backend") . '/web/uploads/',//根路径,无法直接用Yii::getAlias("@webroot")，因为引入前还没赋值
        //上传文件配置
        "fileUrlPrefix" => "/uploads",//访问路径前缀
        "filePathFormat" => "/ueditor/file/{yyyy}{mm}{dd}/{time}{rand:6}", //上传保存路径
        "fileRoot" => Yii::getAlias("@backend") . '/web/uploads/',//根路径
        //上传视频配置
        "videoUrlPrefix" => "/uploads",//访问路径前缀
        "videoPathFormat" => "/ueditor/video/{yyyy}{mm}{dd}/{time}{rand:6}", //上传保存路径
        "videoRoot" => Yii::getAlias("@backend") . '/web/uploads/',//根路径
        //上传涂鸦配置
        "scrawlUrlPrefix" => "/uploads",//访问路径前缀
        "scrawlPathFormat" => "/ueditor/scrawl/{yyyy}{mm}{dd}/{time}{rand:6}", //上传保存路径
        "scrawlRoot" => Yii::getAlias("@backend") . '/web/uploads/',//根路径
    ]
];
