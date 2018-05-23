<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\BackendRole */

$this->title = Yii::t('backend_role', 'Create Backend Role');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend_role', 'Backend Roles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="backend-role-create box box-success">
    <div class="box-header with-border">
        <i class="fa fa-fw fa-plus"></i>
        <h3 class="box-title"><?= Yii::t('common', 'message_create') ?></h3>
    </div>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
