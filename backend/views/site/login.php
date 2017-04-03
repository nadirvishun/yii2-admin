<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = Yii::t('admin','Sign in');

$fieldOptions1 = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='fa fa-fw fa-user form-control-feedback'></span>"
];

$fieldOptions2 = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='fa fa-fw fa-lock form-control-feedback'></span>"
];
?>

<div class="login-box">
    <div class="login-logo">
        <a href="#"><b>Admin</b>LTE</a>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">
        <h3 class="login-box-msg"><?= Yii::t('admin', 'Admin Login') ?></h3>

        <?php $form = ActiveForm::begin(['id' => 'login-form', 'enableClientValidation' => false]); ?>

        <?= $form
            ->field($model, 'username', $fieldOptions1)
            ->label(false)
            ->textInput(['placeholder' => $model->getAttributeLabel('username')]) ?>

        <?= $form
            ->field($model, 'password', $fieldOptions2)
            ->label(false)
            ->passwordInput(['placeholder' => $model->getAttributeLabel('password')]) ?>

        <?= $form
            ->field($model, 'verifyCode')
            ->label(false)
            ->widget(yii\captcha\Captcha::className(), [
                'template' => '<div class="row"><div class="col-lg-7">{input}</div><div class="col-lg-5">{image}</div></div>',
                'imageOptions'=>['alt'=>Yii::t('common', 'click change verifyCode'),'title'=>Yii::t('common', 'click change verifyCode'), 'style'=>'cursor:pointer'],
                'options' => [
                    'class' => 'form-control',
                    'placeholder' => $model->getAttributeLabel('verifyCode')
                ]
            ])

        ?>

        <div class="row">
            <div class="col-xs-8">
                <?= $form->field($model, 'rememberMe')->checkbox() ?>
            </div>
            <!-- /.col -->
            <div class="col-xs-4">
                <?= Html::submitButton(Yii::t('admin', 'Sign in'), ['class' => 'btn btn-primary btn-block btn-flat', 'name' => 'login-button']) ?>
            </div>
            <!-- /.col -->
        </div>


        <?php ActiveForm::end(); ?>

        <!--<div class="social-auth-links text-center">
            <p>- OR -</p>
            <a href="#" class="btn btn-block btn-social btn-facebook btn-flat"><i class="fa fa-facebook"></i> Sign in
                using Facebook</a>
            <a href="#" class="btn btn-block btn-social btn-google-plus btn-flat"><i class="fa fa-google-plus"></i> Sign
                in using Google+</a>
        </div>-->
        <!-- /.social-auth-links -->

        <a href="#"><?= Yii::t('admin', 'forgot password') ?></a><br>
        <!--        <a href="register.html" class="text-center">-->
        <? //=Yii::t('admin','Register a new membership')?><!--</a>-->

    </div>
    <!-- /.login-box-body -->
</div><!-- /.login-box -->
