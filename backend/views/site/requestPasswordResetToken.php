<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\PasswordResetRequestForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = Yii::t('site','Request password reset');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="login-box login-box-body">
    <h2><?= Html::encode($this->title) ?></h2>

    <p><?=Yii::t('site','Please fill out your email. A link to reset password will be sent there.')?></p>

    <div class="row ">
        <div class="col-lg-11">
            <?php $form = ActiveForm::begin(['id' => 'request-password-reset-form']); ?>

                <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>

                <div class="form-group">
                    <?= Html::submitButton(Yii::t('site','Send'), ['class' => 'btn btn-primary']) ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
