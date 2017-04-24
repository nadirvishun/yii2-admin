<?php

use backend\models\BackendSetting;
use yii\bootstrap\Tabs;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\BackendSettingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $items backend\controllers\BackendSettingController */

$this->title = Yii::t('backend_setting', 'Backend Settings');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="backend-setting-index grid-view box box-primary">

    <div class="box-header with-border">
        <div class="box-header pull-left">
            <i class="fa fa-fw fa-sun-o"></i>
            <h3 class="box-title"><?= Yii::t('common', 'message_manage') ?></h3>
        </div>
        <div class="btn-group pull-right">
            <?= Html::a('<i class="fa fa-plus"></i>', ['create'], ['data-pjax' => 0, 'class' => 'btn btn-success', 'title' => Yii::t('common', 'create')]) . ' ' .
            Html::a('<i class="fa fa-repeat"></i>', ['index'], ['data-pjax' => 0, 'class' => 'btn btn-default', 'title' => Yii::t('common', 'reset')]) ?>
        </div>
    </div>
    <div>
        <?php $form = ActiveForm::begin([
            'id' => 'backend-setting-form',
            'options' => ['class' => 'box-body']
        ]); ?>
        <?= Tabs::widget([
            'items' => $items
        ]);
        ?>
        <div class="form-group">
            <label class="col-lg-2 control-label" for="">&nbsp;</label>
            <?= Html::submitButton(Yii::t('common', 'save'), ['class' => 'btn btn-primary']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>

</div>
