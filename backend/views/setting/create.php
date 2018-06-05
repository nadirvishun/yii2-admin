<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Setting */
/* @var $treeOptions backend\controllers\SettingController */
/* @var $placeholder backend\controllers\SettingController */
$this->title = Yii::t('setting', 'Create Setting');
$this->params['breadcrumbs'][] = ['label' => Yii::t('setting', 'Settings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="backend-setting-create box box-success">
    <div class="box-header with-border">
        <i class="fa fa-fw fa-plus"></i>
        <h3 class="box-title"><?= Yii::t('common', 'message_create') ?></h3>
    </div>
    <?= $this->render('_form', [
        'model' => $model,
        'treeOptions' => $treeOptions,
        'placeholderOptions' => $placeholderOptions
    ]) ?>
</div>
