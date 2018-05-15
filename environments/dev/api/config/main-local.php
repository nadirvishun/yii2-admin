<?php

$config = [];

if (!YII_ENV_TEST) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        //自定义模板和生成类
        'generators' => [
            'crud' => [ // crud生成器
                'class' => 'backend\gii\crud\Generator', // 自定义类
                //由于类改变了，所以如果不指定，则自动寻找类所在文件夹下的default文件，所以下方无需指定了
//                'templates' => [ //setting for out templates
//                    'vishun' => '@backend/gii/crud/default', // template name => path to template
//                ]
            ],
            'model'=>[//model生成器
                'class' => 'backend\gii\model\Generator', // 自定义类
            ]
        ],
    ];
}

return $config;
