<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\BackendSetting */
/* @var $treeOptions backend\controllers\BackendMenuController*/

$this->title = Yii::t('backend_setting', 'Create Backend Setting');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend_setting', 'Backend Settings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="backend-setting-create box box-success">
    <div class="box-header with-border">
        <i class="fa fa-fw fa-plus"></i>
        <h3 class="box-title"><?= Yii::t('common', 'message_create') ?></h3>
    </div>
    <?= $this->render('_form', [
        'model' => $model,
        'treeOptions'=>$treeOptions
    ]) ?>
</div>
