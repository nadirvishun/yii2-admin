<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\BackendMenu */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="backend-menu-form">

    <?php $form = ActiveForm::begin([
        'id' => 'backend-menu-form',
        'options' => ['class' => 'box-body']
    ]); ?>

    <?= $form->field($model, 'pid')->textInput(['maxlength' => true,'class'=>'form-control c-md-5']) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true,'class'=>'form-control c-md-5']) ?>

    <?= $form->field($model, 'url')->textInput(['maxlength' => true,'class'=>'form-control c-md-5']) ?>

    <?= $form->field($model, 'url_param')->textInput(['maxlength' => true,'class'=>'form-control c-md-5']) ?>

    <?= $form->field($model, 'icon')->textInput(['maxlength' => true,'class'=>'form-control c-md-5']) ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'sort')->textInput() ?>

    <?= $form->field($model, 'created_by')->textInput(['maxlength' => true,'class'=>'form-control c-md-5']) ?>

    <?= $form->field($model, 'created_at')->textInput(['maxlength' => true,'class'=>'form-control c-md-5']) ?>

    <?= $form->field($model, 'updated_by')->textInput(['maxlength' => true,'class'=>'form-control c-md-5']) ?>

    <?= $form->field($model, 'updated_at')->textInput(['maxlength' => true,'class'=>'form-control c-md-5']) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('common','create') : Yii::t('common','update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-warning']) ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>
