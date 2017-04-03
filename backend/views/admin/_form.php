<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Admin */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="admin-form">

    <?php $form = ActiveForm::begin([
        'id' => 'admin-form',
        'options' => ['class' => 'box-body']
    ]); ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true,'class'=>'form-control c-md-5']) ?>

    <?= $form->field($model, 'auth_key')->textInput(['maxlength' => true,'class'=>'form-control c-md-5']) ?>

    <?= $form->field($model, 'password_hash')->textInput(['maxlength' => true,'class'=>'form-control c-md-5']) ?>

    <?= $form->field($model, 'password_reset_token')->textInput(['maxlength' => true,'class'=>'form-control c-md-5']) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true,'class'=>'form-control c-md-5']) ?>

    <?= $form->field($model, 'mobile')->textInput(['maxlength' => true,'class'=>'form-control c-md-5']) ?>

    <?= $form->field($model, 'sex')->textInput(['class'=>'form-control c-md-5']) ?>

    <?= $form->field($model, 'last_login_ip')->textInput(['maxlength' => true,'class'=>'form-control c-md-5']) ?>

    <?= $form->field($model, 'last_login_time')->textInput(['maxlength' => true,'class'=>'form-control c-md-5']) ?>

    <?= $form->field($model, 'status')->textInput(['class'=>'form-control c-md-5']) ?>

    <?= $form->field($model, 'created_at')->textInput(['maxlength' => true,'class'=>'form-control c-md-5']) ?>

    <?= $form->field($model, 'updated_at')->textInput(['maxlength' => true,'class'=>'form-control c-md-5']) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('common','create') : Yii::t('common','update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-warning']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
