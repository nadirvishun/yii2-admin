<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\AdminLog */

$this->title = Yii::t('admin_log', 'View Admin Log');
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin_log', 'Admin Logs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="admin-log-view box box-primary">
    <div class="box-header with-border">
        <i class="fa fa-fw fa-eye"></i>
        <h3 class="box-title"><?= Yii::t('common', 'message_view') ?></h3>
    </div>
    <div class="box-body">
        <?= DetailView::widget([
            'model' => $model,
            'options' => ['class' => 'table table-striped table-bordered detail-view', 'style' => 'word-break:break-all; word-wrap:break-all'],
            'attributes' => [
                'id',
                'title',
                [
                    'label' => $model->getAttributeLabel('admin_id'),
                    'value' => \backend\models\Admin::getUsernameOptions($model->admin_id)
                ],
                [
                    'label' => $model->getAttributeLabel('type'),
                    'value' => \backend\models\AdminLog::getTypeOptions($model->type)
                ],
                'model',
                'controller',
                'action',
                'url_param',
                'description:text',
                'ip',
                'created_at:datetime',
            ],
        ]) ?>
        <p style="margin-top:10px">
            <?= Html::a(Yii::t('common', 'update'), ['update', 'id' => $model->id], ['class' => 'btn btn-warning']) ?>
            <?= Html::a(Yii::t('common', 'delete'), ['delete', 'id' => $model->id],
                ['class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                        'method' => 'post',
                    ],
                ]) ?>
        </p>
    </div>
</div>
