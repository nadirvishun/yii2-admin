<?php

use backend\models\Setting;
use yii\bootstrap\Tabs;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\SettingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $items backend\controllers\SettingController */

$this->title = Yii::t('setting', 'System Setting');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="backend-setting-system box box-primary">

    <div class="box-header with-border">
        <i class="fa fa-fw fa-cog"></i>
        <h3 class="box-title"><?= Yii::t('setting', 'System Setting') ?></h3>
    </div>

    <?php $form = ActiveForm::begin([
        'id' => 'backend-setting-form',
        'options' => ['class' => 'box-body','enctype'=>'multipart/form-data']
    ]); ?>
    <?= Tabs::widget([
        'items' => $items,
        'itemOptions' => ['class' => 'p-10']
    ]);
    ?>
    <div class="form-group p-10">
        <?= Html::submitButton(Yii::t('common', 'save'), ['class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>

</div>
