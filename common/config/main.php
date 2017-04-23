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
        //相关格式默认值,否则默认英文类的格式，调用Yii::$app->formatter->asDateTime()等函数时还需要设置第二个参数
        'formatter' => [
            'dateFormat' => 'yyyy-MM-dd',
            'datetimeFormat' => 'yyyy-MM-dd HH:mm:ss',
            'decimalSeparator' => ',',
            'thousandSeparator' => '',
            'currencyCode' => 'CNY',
        ]
    ],
];
