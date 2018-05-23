<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * icheck相关样式引入
 */
class IcheckAsset extends AssetBundle
{
    public $sourcePath = '@vendor/almasaeed2010/adminlte/plugins';
    public $baseUrl = '@web';
    public $css = [
        'iCheck/all.css',
    ];
    public $js = [
        'iCheck/icheck.js',
    ];
    public $depends = [
        'backend\assets\AppAsset',
    ];
}
