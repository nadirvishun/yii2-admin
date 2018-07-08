<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    //设置i18n语言，前后台通用
    'sourceLanguage' => 'en-US',
    'language' => 'zh-CN',
    'timeZone' => 'Asia/Shanghai',
    //配置kartik-v/yii2-grade
    'modules' => [
        'gridview' => [
            'class' => '\kartik\grid\Module'
        ]
    ],
    'components' => [
        //翻译
        'i18n' => [
            'translations' => [
                '*' => [
                    'class' => 'yii\i18n\PhpMessageSource'
                ],
            ],
        ],
        //缓存，暂用文件缓存
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        //方便清理其它模块的缓存,也方便在命令行中yii cache/flush-all 可以清除全部的缓存
        'cacheConsole' => [
            'class' => 'yii\caching\FileCache',
            'cachePath' => '@console/runtime/cache'
        ],
        'cacheFrontend' => [
            'class' => 'yii\caching\FileCache',
            'cachePath' => '@frontend/runtime/cache'
        ],
        'cacheBackend' => [
            'class' => 'yii\caching\FileCache',
            'cachePath' => '@backend/runtime/cache'
        ],
        'cacheApi' => [
            'class' => 'yii\caching\FileCache',
            'cachePath' => '@api/runtime/cache'
        ],
        //相关格式默认值,否则默认英文类的格式，调用Yii::$app->formatter->asDateTime()等函数时还需要设置第二个参数
        'formatter' => [
            'dateFormat' => 'yyyy-MM-dd',
            'datetimeFormat' => 'yyyy-MM-dd HH:mm:ss',
            'decimalSeparator' => '.',
            'thousandSeparator' => '',
            'currencyCode' => 'CNY',
        ]
    ],
];
