<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    //设置i18n语言，前后台通用
    'sourceLanguage'=>'en-US',
    'language'=>'zh-CN',

    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
    ],
];
