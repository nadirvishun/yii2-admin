<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Admin */

$this->title = Yii::t('admin', 'View Admin');
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Admins'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="admin-view box box-primary">
    <div class="box-header with-border">
        <i class="fa fa-fw fa-eye"></i>
        <h3 class="box-title"><?= Yii::t('common', 'message_view') ?></h3>
    </div>
    <div class="box-body">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                'username',
//                'auth_key',
//                'password_hash',
//                'password_reset_token',
                'email:email',
                'mobile',
//                'avatar',
                [
                    'label' => $model->getAttributeLabel('sex'),
                    'value' => \backend\models\Admin::getSexOptions($model->sex)
                ],
                'last_login_ip',
                [
                    'label' => $model->getAttributeLabel('last_login_time'),
                    'value' => $model->last_login_time ? Yii::$app->formatter->asDatetime($model->last_login_time) : Yii::t('common', 'Unknown')
                ],
                [
                    'label' => $model->getAttributeLabel('status'),
                    'value' => \backend\models\Admin::getStatusOptions($model->status)
                ],
                'created_at:datetime',
                'updated_at:datetime',
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
