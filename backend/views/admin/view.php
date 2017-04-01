<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Admin */

$this->title = Yii::t('admin', 'View Admin');
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Admins'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="admin-view">


    <p>
        <?= Html::a(Yii::t('common', 'update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('common', 'delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('common', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'username',
            'auth_key',
            'password_hash',
            'password_reset_token',
            'email:email',
            'mobile',
            'avatar',
            'sex',
            'last_login_ip',
            'last_login_time',
            'status',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
