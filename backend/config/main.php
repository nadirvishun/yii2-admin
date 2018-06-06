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
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'itemTable' => '{{%backend_auth_item}}',//数据库名称不同
            'itemChildTable' => '{{%backend_auth_item_child}}',
            'assignmentTable' => '{{%backend_auth_assignment}}',
            'ruleTable' => '{{%backend_auth_rule}}',
            // uncomment if you want to cache RBAC items hierarchy
            // 'cache' => 'cache',
        ],
        /**
         * 后台模板颜色（不需要了，直接用js来修改颜色）
         * "skin-blue","skin-black","skin-red","skin-yellow","skin-purple","skin-green","skin-blue-light",
         *"skin-black-light","skin-red-light","skin-yellow-light","skin-purple-light","skin-green-light"
         */
        'assetManager' => [
            'appendTimestamp' => true,//加时间戳，以方便css，js等缓存更新
            //压缩合并
            'bundles' => require __DIR__ . '/' . (YII_ENV_PROD ? 'assets-prod.php' : 'assets-dev.php'),

//            'bundles' => [
//                'dmstr\web\AdminLteAsset' => [
//                    'skin' => 'skin-purple',
//                ],
//            ],
            //原先引用的的谷歌字体地址https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic
            //先修改为本地的引用，以免谷歌访问很慢或或被墙
            //目前由于AdminLTE框架更新，已将此字体移除，所以不需要了
//            'assetMap' => [
//                'AdminLTE.min.css' => '@web/css/AdminLTE.min.css',
//            ],
        ],
    ],
    //在请求前绑定类事件来自动写入管理员操作日志，一些没有用ActiveRecord的操作还是需要自己写入，例如权限授权、批量写入等
    'on beforeRequest' => function ($event) {
        \yii\base\Event::on(\yii\db\BaseActiveRecord::className(), \yii\db\BaseActiveRecord::EVENT_AFTER_UPDATE, ['\backend\models\AdminLog', 'eventUpdate']);
        \yii\base\Event::on(\yii\db\BaseActiveRecord::className(), \yii\db\BaseActiveRecord::EVENT_AFTER_DELETE, ['\backend\models\AdminLog', 'eventDelete']);
        \yii\base\Event::on(\yii\db\BaseActiveRecord::className(), \yii\db\BaseActiveRecord::EVENT_AFTER_INSERT, ['\backend\models\AdminLog', 'eventInsert']);
    },
    'params' => $params,
];
