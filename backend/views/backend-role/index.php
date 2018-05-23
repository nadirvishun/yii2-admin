<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\BackendRoleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend_role', 'Backend Roles');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="backend-role-index grid-view box box-primary">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'hover' => true,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'class' => '\kartik\grid\CheckboxColumn',
                'rowSelectedClass' => GridView::TYPE_INFO
            ],

            'name',
//            'type',
            'description:ntext',
//            'rule_name',
//            'data:ntext',
            // 'created_at',
            // 'updated_at',

            [
                'class' => '\kartik\grid\ActionColumn',
                'header' => Yii::t('common', 'Actions'),
                'vAlign' => 'middle',
                'template' => '{auth} {update} {delete}',
                'buttons' => [
                    'auth' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('backend_role', 'auth'),
                            'aria-label' => Yii::t('backend_role', 'auth'),
                            'data-pjax' => '0',
                            'class' => 'btn btn-xs btn-info'
                        ];
                        return Html::a('<i class="fa fa-fw fa-key"></i> '.Yii::t('backend_role', 'auth'), ['auth', 'name' => $model->name], $options);
                    },
                    'update' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('common', 'update'),
                            'aria-label' => Yii::t('common', 'update'),
                            'data-pjax' => '0',
                            'class' => 'btn btn-xs btn-warning'
                        ];
                        return Html::a('<i class="fa fa-fw fa-pencil"></i> '.Yii::t('common', 'update'), ['update', 'name' => $model->name], $options);
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
                        return Html::a('<i class="fa fa-fw fa-trash"></i> '.Yii::t('common', 'delete'), ['delete', 'name' => $model->name], $options);
                    }
                ],
            ]
        ],
        'panel' => [
            'heading' => false,
            'before' => '<div class="box-header pull-left">
                    <i class="fa fa-fw fa-sun-o"></i><h3 class="box-title">' . Yii::t('common', 'message_manage') . '</h3>
                </div>',
            'after' => '<div class="pull-left" style="margin-top: 8px">{summary}</div><div class="kv-panel-pager pull-right">{pager}</div><div class="clearfix"></div>',
            'footer' => false,
            //'footer' => '<div class="pull-left">'
            //    . Html::button('<i class="glyphicon glyphicon-remove-circle"></i>' . Yii::t('common', 'batch'), ['class' => 'btn btn-primary', 'id' => 'bulk_forbid'])
            //    . '</div>',
            //'footerOptions' => ['style' => 'padding:5px 15px']
        ],
        'panelFooterTemplate' => '{footer}<div class="clearfix"></div>',
        'toolbar' => [
            [
                'content' =>
                    Html::a('<i class="fa fa-plus"></i> '.Yii::t('common', 'create'), ['create'], ['data-pjax' => 0, 'class' => 'btn btn-success', 'title' => Yii::t('common', 'create')]) . ' ' .
                    Html::a('<i class="fa fa-repeat"></i> '.Yii::t('common', 'reset'), ['index'], ['data-pjax' => 0, 'class' => 'btn btn-default', 'title' => Yii::t('common', 'reset')])
            ],
            '{toggleData}',
            '{export}'
        ],

    ]); ?>
</div>
