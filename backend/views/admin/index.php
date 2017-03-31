<?php

use backend\models\Admin;
use kartik\dialog\Dialog;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\AdminSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('admin', 'Admins');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="admin-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('admin', 'Create Admin'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'options'=>['class'=>'grid-view box box-primary'],
        'hover'=>true,
        'krajeeDialogSettings'=>[
            'libName' => 'krajeeDialog',
            'options' => [
                'size' => Dialog::SIZE_NORMAL,
            ]
        ],
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'class' => '\kartik\grid\CheckboxColumn',
                'rowSelectedClass' => GridView::TYPE_INFO
            ],

            'id',
            'username',
            // 'auth_key',
            // 'password_hash',
            // 'password_reset_token',
            'email:email',
            // 'status',
            // 'created_at',
            // 'updated_at',
            [
                'attribute' => 'status',
                'filter'=>Html::activeDropDownList($searchModel,'status',Admin::getZhStatus(),['class' => 'form-control ']),
                'value'=>function($data){
                    return Admin::getZhStatus($data->status);
                },
            ],
            [
                'class' => 'kartik\grid\ActionColumn',
                'header'=>'操作',
                'headerOptions'=>['style'=>'color:#3c8dbc'],
            ],
        ],
        'panel' => [
            'heading'=>false,
            'before'=>'<div style="margin-top:8px">{summary}</div>',
            'after'=>false,
            'footer'=>'<div class="pull-right">'.Html::button('<i class="glyphicon glyphicon-remove-circle"></i> 批量禁止', [ 'class' => 'btn btn-primary','id'=>'bulk_forbid']).'</div>',
            'footerOptions'=>[
                'style'=>'padding:5px 15px'
            ]
        ],
        'panelFooterTemplate'=>'<div class="kv-panel-pager pull-left">{pager}</div>{footer}<div class="clearfix"></div>',
        'toolbar'=>[
            ['content'=>
                Html::a('<i class="glyphicon glyphicon-plus"></i>', ['create'],['data-pjax'=>0, 'class'=>'btn btn-success','title'=>'创建']).' '.
                Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['index'],['data-pjax'=>0, 'class'=>'btn btn-default','title'=>'重置'])
            ],
            '{toggleData}',
            '{export}',
        ]
    ]); ?>
</div>
