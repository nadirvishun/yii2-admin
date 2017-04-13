<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\BackendMenu */

$this->title = Yii::t('backend_menu', 'Update Backend Menu');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend_menu', 'Backend Menus'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="backend-menu-update box box-warning">
    <div class="box-header with-border">
        <i class="fa fa-fw fa-pencil"></i>
        <h3 class="box-title"><?= Yii::t('common', 'message_update') ?></h3>
    </div>
    <?= $this->render('_form', [
        'model' => $model,
        'treeOptions' => $treeOptions
    ]) ?>
</div>
