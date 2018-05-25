<?php

use backend\models\Setting;
use yii\helpers\Html;
use dkhlystov\widgets\TreeGrid;
//use leandrogehlen\treegrid\TreeGrid;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\SettingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('setting', 'Settings');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="backend-setting-index grid-view box box-primary">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php Pjax::begin(); ?>
    <div class="box-header with-border">
        <div class="box-header pull-left">
            <i class="fa fa-fw fa-sun-o"></i>
            <h3 class="box-title"><?= Yii::t('common', 'message_manage') ?></h3>
        </div>
        <div class="btn-group pull-right">
            <?= Html::a('<i class="fa fa-plus"></i> ' . Yii::t('common', 'create'), ['create'], ['data-pjax' => 0, 'class' => 'btn btn-success', 'title' => Yii::t('common', 'create')]) . ' ' .
            Html::a('<i class="fa fa-repeat"></i> ' . Yii::t('common', 'reset'), ['index'], ['data-pjax' => 0, 'class' => 'btn btn-default', 'title' => Yii::t('common', 'reset')]) ?>
        </div>
    </div>
    <?= TreeGrid::widget([
        'tableOptions' => ['class' => 'table table-bordered  table-hover table-striped'],
        'rowOptions' => ['class' => 'expanded'],
        'emptyTextOptions' => ['class' => 'empty p-10'],
        'dataProvider' => $dataProvider,
        'parentIdAttribute' => 'pid',
        'showRoots' => true,
        'lazyLoad' => false,
//        'moveAction' => ['move'],
        'columns' => [
            'name',
            'id',
//            'pid',
            'alias',
            [
                'attribute' => 'type',
                'value' => function ($model, $key, $index, $column) {
                    return Setting::getTypeOptions($model->type);
                }
            ],
            'sort',
            [
                'attribute' => 'status',
                'value' => function ($model, $key, $index, $column) {
                    return Setting::getStatusOptions($model->status);
                }
            ],
            // 'value:ntext',
            // 'extra',
            // 'hint',
            // 'created_by',
            // 'created_at',
            // 'updated_by',
            // 'updated_at',

            [
                'class' => '\yii\grid\ActionColumn',
                'header' => Yii::t('common', 'Actions'),
                'headerOptions'=>['style' => 'width:200px'],
                'template' => '{create} {update} {delete}',
                'buttons' => [
                    'create' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('common', 'create_sub'),
                            'aria-label' => Yii::t('common', 'create_sub'),
                            'data-pjax' => '0',
                            'class' => 'btn btn-xs btn-success'
                        ];
                        return Html::a('<i class="fa fa-fw fa-plus"></i> '.Yii::t('common', 'create_sub'), ['create', 'pid' => $model->id], $options);
                    },
                    'update' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('common', 'update'),
                            'aria-label' => Yii::t('common', 'update'),
                            'data-pjax' => '0',
                            'class' => 'btn btn-xs btn-warning'
                        ];
                        return Html::a('<i class="fa fa-fw fa-pencil"></i> '.Yii::t('common', 'update'), ['update', 'id' => $model->id], $options);
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
                        return Html::a('<i class="fa fa-fw fa-trash"></i> '.Yii::t('common', 'delete'), ['delete', 'id' => $model->id], $options);
                    }
                ]
            ],
        ]
    ]); ?>
    <?php Pjax::end(); ?></div>
