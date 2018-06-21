<?php

use kartik\widgets\Select2;
use kartik\widgets\SwitchInput;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\BackendMenu */
/* @var $form yii\widgets\ActiveForm */
/* @var $treeOptions backend\controllers\BackendMenuController*/
?>

<div class="backend-menu-form">

    <?php $form = ActiveForm::begin([
        'id' => 'backend-menu-form',
        'options' => ['class' => 'box-body']
    ]); ?>

    <?= $form->field($model, 'pid', ['options' => ['class' => 'form-group c-md-5']])->widget(Select2::classname(), [
        'data' => $treeOptions,
        'options' => [
            'prompt' => Yii::t('common', 'Please Select...'),
            'encode' => false,
        ],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]) ?>
    <?= $form->field($model, 'name', ['options' => ['class' => 'form-group c-md-5']])->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'url', ['options' => ['class' => 'form-group c-md-5']])
        ->textInput(['maxlength' => true])
        ->hint(Yii::t('backend_menu','like "index/index"'))
    ?>
    <?= $form->field($model, 'url_param', ['options' => ['class' => 'form-group c-md-5']])
        ->textInput(['maxlength' => true])
        ->hint(Yii::t('backend_menu','like "id=1&pid=2"'))
    ?>
    <?= $form->field($model, 'icon', ['options' => ['class' => 'form-group c-md-5']])
        ->textInput(['maxlength' => true])
        ->hint(Yii::t('backend_menu','support font awesome icon'));
    ?>
    <?= $form->field($model, 'sort', ['options' => ['class' => 'form-group c-md-5']])->textInput()->hint(Yii::t('common','The bigger number is ranked previous')) ?>
    <?= $form->field($model, 'status', ['options' => ['class' => 'form-group c-md-5']])->widget(SwitchInput::classname(), ['pluginOptions' => ['size' => 'small']]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('common', 'create') : Yii::t('common', 'update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-warning']) ?>

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

