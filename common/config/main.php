<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    //设置i18n语言，前后台通用
    'sourceLanguage' => 'en-US',
    'language' => 'zh-CN',
    //配置kartik-v/yii2-grade
    'modules' => [
        'gridview' => [
            'class' => '\kartik\grid\Module'
        ]
    ],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
    ],
];
