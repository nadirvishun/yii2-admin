<?php

use yii\helpers\Html;
//use dkhlystov\widgets\TreeGrid;
use leandrogehlen\treegrid\TreeGrid;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\BackendMenuSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend_menu', 'Backend Menus');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="backend-menu-index grid-view box box-primary">
    <div class="box-header with-border">
        <i class="fa fa-fw fa-sun-o"></i>
        <h3 class="box-title"><?= Yii::t('common', 'message_manage') ?></h3>
    </div>

    <?= TreeGrid::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'options' => ['class' => 'table table-bordered  table-hover table-striped', 'id' => 'tree'],
        'keyColumnName' => 'id',
        'parentColumnName' => 'pid',
        'parentRootValue' => '0', //first parentId value
        'pluginOptions' => [
            'initialState' => 'collapsed',
        ],
        'columns' => [
            'name',
            'id',
            'pid',
            'url:url',
            'url_param:url',
            //'icon',
            //'status',
            //'sort',
            //'created_by',
            //'created_at',
            //'updated_by',
            //'updated_at',
            [
                'class' => '\yii\grid\ActionColumn',
                'header' => '操作',
                'options' => ['style' => 'text-align:center'],
                'template' => '{view} {update} {delete}',
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('common', 'view'),
                            'aria-label' => Yii::t('common', 'view'),
                            'data-pjax' => '0',
                            'class' => 'btn btn-xs btn-info'
                        ];
                        return Html::a('<i class="fa fa-fw fa-eye"></i>', ['view', 'id' => $model->id], $options);
                    },
                    'update' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('common', 'update'),
                            'aria-label' => Yii::t('common', 'update'),
                            'data-pjax' => '0',
                            'class' => 'btn btn-xs btn-warning'
                        ];
                        return Html::a('<i class="fa fa-fw fa-pencil"></i>', ['update', 'id' => $model->id], $options);
                    },
                    'delete' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('common', 'delete'),
                            'aria-label' => Yii::t('common', 'delete'),
                            'data-pjax' => '0',
                            'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                            'data-method' => 'post',
                            'class' => 'btn btn-xs btn-danger'
                        ];
                        return Html::a('<i class="fa fa-fw fa-trash"></i>', ['delete', 'id' => $model->id], $options);
                    }
                ],
            ]
        ]
    ]); ?>


    <?/*= TreeGrid::widget([
        'tableOptions' => ['class' => 'table table-bordered  table-hover table-striped'],
        'dataProvider' => $dataProvider,
        'parentIdAttribute' => 'pid',
        'showRoots' => true,
        'lazyLoad' => false,
//        'moveAction' => ['move'],
        'columns' => [
            'name',
            'id',
            'pid',
            'url:url',
            'url_param:url',
            //'icon',
            //'status',
            //'sort',
            //'created_by',
            //'created_at',
            //'updated_by',
            //'updated_at',

            [
                'class' => '\yii\grid\ActionColumn',
                'header' => '操作',
                'options' => ['style' => 'text-align:center'],
//                'vAlign' => 'middle',
                'template' => '{view} {update} {delete}',
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('common', 'view'),
                            'aria-label' => Yii::t('common', 'view'),
                            'data-pjax' => '0',
                            'class' => 'btn btn-xs btn-info'
                        ];
                        return Html::a('<i class="fa fa-fw fa-eye"></i>', ['view', 'id' => $model->id], $options);
                    },
                    'update' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('common', 'update'),
                            'aria-label' => Yii::t('common', 'update'),
                            'data-pjax' => '0',
                            'class' => 'btn btn-xs btn-warning'
                        ];
                        return Html::a('<i class="fa fa-fw fa-pencil"></i>', ['update', 'id' => $model->id], $options);
                    },
                    'delete' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('common', 'delete'),
                            'aria-label' => Yii::t('common', 'delete'),
                            'data-pjax' => '0',
                            'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                            'data-method' => 'post',
                            'class' => 'btn btn-xs btn-danger'
                        ];
                        return Html::a('<i class="fa fa-fw fa-trash"></i>', ['delete', 'id' => $model->id], $options);
                    }
                ],
            ],
        ],
    ]); */?>
</div>
