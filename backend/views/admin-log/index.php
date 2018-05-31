<?php

use backend\models\Admin;
use backend\models\AdminLog;
use kartik\select2\Select2;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\AdminLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('admin_log', 'Admin Logs');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="admin-log-index grid-view box box-primary">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'pjax' => true,
        'hover' => true,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'class' => '\kartik\grid\CheckboxColumn',
                'rowSelectedClass' => GridView::TYPE_INFO
            ],

//            'id',
            'title',
            [
                'class' => '\kartik\grid\DataColumn',
                'attribute' => 'admin_id',
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => Admin::getUsernameOptions(),
                    'options' => [
                        'prompt' => Yii::t('common', 'Please Select...'),
                    ],
                    'hideSearch' => true,
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
                'value' => function ($model, $key, $index, $column) {
                    return Admin::getUsernameOptions($model->admin_id);
                }
            ],
            [
                'class' => '\kartik\grid\DataColumn',
                'attribute' => 'type',
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => AdminLog::getTypeOptions(),
                    'options' => [
                        'prompt' => Yii::t('common', 'Please Select...'),
                    ],
                    'hideSearch' => true,
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
                'value' => function ($model, $key, $index, $column) {
                    return AdminLog::getTypeOptions($model->type);
                }
            ],
//            'model',
            'controller',
            'action',
            'url_param',
            [
                'class' => '\kartik\grid\DataColumn',
                'attribute' => 'created_at',
                'filterType' => GridView::FILTER_DATE_RANGE,
                'filterWidgetOptions' => [
                    'attribute'=>'created_at',
                    'convertFormat'=>true,
                    'startAttribute'=>'datetime_min',
                    'endAttribute'=>'datetime_max',
                    'pluginOptions'=>[
                        'opens'=>'left',
                    ],
                ],
                'value' => function ($model, $key, $index, $column) {
                    return Yii::$app->formatter->asDate($model->created_at, 'php:Y-m-d H:i:s');
                }
            ],
            // 'description:ntext',
            // 'ip',

            [
                'class' => '\kartik\grid\ActionColumn',
                'header' => Yii::t('common', 'Actions'),
                'vAlign' => GridView::ALIGN_MIDDLE,
                'template' => '{view}',
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('common', 'view'),
                            'aria-label' => Yii::t('common', 'view'),
                            'data-pjax' => '0',
                            'class' => 'btn btn-xs btn-info'
                        ];
                        return Html::a('<i class="fa fa-fw fa-eye"></i> ' . Yii::t('common', 'view'), ['view', 'id' => $model->id], $options);
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
                Html::beginTag('div',['style'=>'float:left']).
                    Select2::widget([
                        'name' => 'delete_type',
                        'id'=>'delete_type',
                        'hideSearch'=>true,
                        'value' => '',
                        'data' => AdminLog::getDeleteOptions(),
                    ]).
                Html::endTag('div').
                Html::a('<i class="fa fa-fw fa-trash"></i> ' . Yii::t('common', 'delete'), ['delete', 'type' => 1], [
                    'title' => Yii::t('common', 'delete'),
                    'aria-label' => Yii::t('common', 'delete'),
                    'data-pjax' => '0',
                    'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                    'data-method' => 'post',
                    'class' => 'btn btn-danger',
                    'id'=>'delete_button'
                ]). ' ' .
                    Html::a('<i class="fa fa-repeat"></i> ' . Yii::t('common', 'reset'), ['index'], ['data-pjax' => 0, 'class' => 'btn btn-default', 'title' => Yii::t('common', 'reset')])
            ],
            '{toggleData}',
            '{export}'
        ],

    ]); ?>
</div>
<?php
    //选择不同时间段，修改提交的数据
    //pjax提交时，select2会无法初始化，所以重新初始化
    $js=<<<EOF
$('#delete_type').on('change',function(){
    var selectId=$(this).val();
    $('#delete_button').attr('href','/admin-log/delete?type='+selectId);
})
$(document).on('pjax:complete', function() {
    var el=$('#delete_type');
    id = el.attr('id');
    var settings = el.attr('data-krajee-select2');
    settings = window[settings];
    $.when(el.select2(settings)).done(initS2Loading(id));
})
EOF;
    $this->registerJs($js);
?>
