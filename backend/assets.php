<?php
/**
 * Configuration file for the "yii asset" console command.
 * 需要先安装java环境，而且如果上线后修改了js或css文件，每次都需要重新运行一次
 * 命令：`yii asset backend/assets.php backend/config/assets-prod.php`
 */

// In the console environment, some path aliases may not exist. Please define these:
Yii::setAlias('@webroot', __DIR__ . '/web');
Yii::setAlias('@web', '/');

return [
    // Adjust command/callback for JavaScript files compressing:
    'jsCompressor' => 'java -jar common/tools/closure/closure-compiler-v20180506.jar --js {from} --js_output_file {to}',
    // Adjust command/callback for CSS files compressing:
    //2.4.8最新的版本在windows下会报文件找不到的错误，用低版本就可以，linux未测试，应该是最新版本也可以
    'cssCompressor' => 'java -jar common/tools/yui/yuicompressor-2.3.6.jar --type css {from} -o {to}',
    // Whether to delete asset source after compression:
    'deleteSource' => false,
    // The list of asset bundles to compress:
    'bundles' => [
        'dmstr\web\AdminLteAsset',
        'kartik\dialog\DialogBootstrapAsset',
        'kartik\dialog\DialogAsset',
        'kartik\dialog\DialogYiiAsset',
        'backend\assets\AppAsset',
    ],
    // Asset bundle for compression output:
    'targets' => [
        'all' => [
            'class' => 'yii\web\AssetBundle',
            'basePath' => '@webroot/assets',
            'baseUrl' => '@web/assets',
            'js' => 'all-{hash}.js',
            'css' => 'all-{hash}.css',
        ],
    ],
    // Asset manager configuration:
    'assetManager' => [
        'basePath' => '@webroot/assets',
        'baseUrl' => '@web/assets',
    ],
];