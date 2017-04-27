<?php
/* @var $this yii\web\View */
/* @var $system backend\Controllers\SiteController */
/* @var $systemAttr backend\Controllers\SiteController */
/* @var $developer backend\Controllers\SiteController */
/* @var $developerAttr backend\Controllers\SiteController */

use yii\widgets\DetailView;

$this->title = Yii::t('site', 'index');
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="index-index box box-primary">

    <div class="box-header with-border">
        <i class="fa fa-fw fa-flag"></i>
        <h3 class="box-title"><?= Yii::t('site', 'system') ?></h3>
    </div>
    <div class="box-body">
        <?= DetailView::widget([
            'model' => $system,
            'attributes' => $systemAttr
        ]) ?>
    </div>
    <div class="box-header with-border">
        <i class="fa fa-fw fa-fire"></i>
        <h3 class="box-title"><?= Yii::t('site', 'developer') ?></h3>
    </div>
    <div class="box-body">
        <?= DetailView::widget([
            'model' => $developer,
            'attributes' => $developerAttr,
        ]) ?>
    </div>

</div>