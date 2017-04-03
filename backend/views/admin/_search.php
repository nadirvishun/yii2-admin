<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\AdminSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="admin-search box box-primary">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => ['class' => 'box-body']
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'username') ?>

    <?= $form->field($model, 'auth_key') ?>

    <?= $form->field($model, 'password_hash') ?>

    <?= $form->field($model, 'password_reset_token') ?>

    <?php // echo $form->field($model, 'email') ?>

    <?php // echo $form->field($model, 'mobile') ?>

    <?php // echo $form->field($model, 'avatar') ?>

    <?php // echo $form->field($model, 'sex') ?>

    <?php // echo $form->field($model, 'last_login_ip') ?>

    <?php // echo $form->field($model, 'last_login_time') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('common', 'search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('common', 'reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
