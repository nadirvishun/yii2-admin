<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'console\controllers',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'controllerMap' => [
        //vishun,2017.03.28,增加自定义migration模板，但外键相关的视图没有改变，如用到外键需要注意
        'migrate' => [
            'class' => 'yii\console\controllers\MigrateController',
            'templateFile' => '@yii/views/migration.php',
            'generatorTemplateFiles' => [
                'create_table' => '@console/views/createTableMigration.php',
                'drop_table' => '@console/views/dropTableMigration.php',
                'add_column' => '@console/views/addColumnMigration.php',
                'drop_column' => '@console/views/dropColumnMigration.php',
                'create_junction' => '@console/views/createJunctionMigration.php'
            ],
        ],
        'fixture' => [
            'class' => 'yii\console\controllers\FixtureController',
            'namespace' => 'common\fixtures',
        ],
    ],
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
    ],
    'params' => $params,
];
