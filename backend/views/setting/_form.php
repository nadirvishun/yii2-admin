<?php

use backend\models\Setting;
use kartik\widgets\Select2;
use kartik\widgets\SwitchInput;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Setting */
/* @var $form yii\widgets\ActiveForm */
/* @var $placeholderOptions backend\controllers\SettingController */
?>

<div class="backend-setting-form">

    <?php $form = ActiveForm::begin([
        'id' => 'backend-setting-form',
        'options' => ['class' => 'box-body']
    ]); ?>

    <?=
    $form->field($model, 'pid', ['options' => ['class' => 'form-group c-md-5']])->widget(Select2::classname(), [
        'data' => Setting::getSettingTreeOptions(),
        'options' => [
            'prompt' => Yii::t('common', 'Please Select...'),
            'encode' => false,
        ],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]) ?>

    <?= $form->field($model, 'name', ['options' => ['class' => 'form-group c-md-5']])->textInput(['maxlength' => true]) ?>

    <?php $input = $form->field($model, 'alias', ['options' => ['class' => 'form-group c-md-5']])->textInput(['maxlength' => true]);
    if (!$model->isNewRecord) {
        echo $input->hint(Yii::t('setting', 'Unless you know it not use in code,otherwise do not change it'));
    } else {
        echo $input;
    } ?>

    <?=
    $form->field($model, 'type', ['options' => ['class' => 'form-group c-md-5']])->widget(Select2::classname(), [
        'data' => Setting::getTypeOptions(),
        'options' => [
            'prompt' => Yii::t('common', 'Please Select...'),
            'encode' => false,
        ],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]) ?>

    <?= $form->field($model, 'extra', ['options' => ['class' => 'form-group c-md-6']])->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'hint', ['options' => ['class' => 'form-group c-md-5']])->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sort', ['options' => ['class' => 'form-group c-md-5']])->textInput()->hint(Yii::t('common','The bigger number is ranked previous')) ?>

    <?= $form->field($model, 'status', ['options' => ['class' => 'form-group c-md-5']])->widget(SwitchInput::classname(), ['pluginOptions' => ['size' => 'small']]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('common', 'create') : Yii::t('common', 'update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-warning']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
    //选择不同按钮时不同的提示信息
    $js=<<<EOF
var phpJson='$placeholderOptions';
//将单斜线转为双斜线，否则js解析不了
var jsJson=phpJson.replace(/\\n/g,"\\\\n");
jsJson=jsJson.replace(/\\t/g,"\\\\t")
var placeholderOptions= JSON.parse(jsJson);
//初始显示
var typeValue=$('#setting-type').val();
$('#setting-extra').attr('placeholder',placeholderOptions[typeValue])
//当更改时
$('#setting-type').on('change',function(){
    var typeValue=$(this).val();
    $('#setting-extra').attr('placeholder',placeholderOptions[typeValue])
})
EOF;

    $this->registerJs($js)
?>
