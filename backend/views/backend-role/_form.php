<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\BackendRole */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="backend-role-form">

    <?php $form = ActiveForm::begin([
        'id' => 'backend-role-form',
        'options' => ['class' => 'box-body']
    ]); ?>

    <?= $form->field($model, 'name', ['options' => ['class' => 'form-group c-md-5']])->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description', ['options' => ['class' => 'form-group c-md-6']])->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('common','create') : Yii::t('common','update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-warning']) ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
//增加必填字段红星提示
$js = <<<eof
    $('.required').each(function(){
        var label=$(this).children(':first');
        label.html(label.html()+'<i style="color:red">*</i>');
    });
eof;
$this->registerJs($js);
?>

