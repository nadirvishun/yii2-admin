<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
        ],
        'user' => [
            'identityClass' => 'backend\models\Admin',//修改为对应de后台类
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        /*
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        */
        //缓存
        'cache' => [
            'class' => 'yii\caching\FileCache',//文件缓存
        ],
        //翻译
        'i18n' => [
            'translations' => [
                '*' => [
                    'class' => 'yii\i18n\PhpMessageSource'
                ],
            ],
        ],
        /**
         * 后台模板颜色（不需要了，直接用js来修改颜色）
         * "skin-blue","skin-black","skin-red","skin-yellow","skin-purple","skin-green","skin-blue-light",
         *"skin-black-light","skin-red-light","skin-yellow-light","skin-purple-light","skin-green-light"
         */
//        'assetManager' => [
//            'bundles' => [
//                'dmstr\web\AdminLteAsset' => [
//                    'skin' => 'skin-purple',
//                ],
//            ],
//            //谷歌字体库，如果想修改的话可以在这里重新定位新的css文件，目前没有必要
//            'assetMap' => [
//                'AdminLTE.min.css' => '@web/css/AdminLTE.min.css',
//            ],
//        ],

    ],
    'params' => $params,
];
