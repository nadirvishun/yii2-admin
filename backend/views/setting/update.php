<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Setting */
/* @var $placeholderOptions backend\controllers\SettingController */

$this->title = Yii::t('setting', 'Update Setting');
$this->params['breadcrumbs'][] = ['label' => Yii::t('setting', 'Settings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="backend-setting-update box box-warning">
    <div class="box-header with-border">
        <i class="fa fa-fw fa-pencil"></i>
        <h3 class="box-title"><?= Yii::t('common', 'message_update') ?></h3>
    </div>
    <?= $this->render('_form', [
        'model' => $model,
        'placeholderOptions' => $placeholderOptions
    ]) ?>
</div>
