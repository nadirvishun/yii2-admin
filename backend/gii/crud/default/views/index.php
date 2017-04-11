<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */
//todo,增加treeview
$urlParams = $generator->generateUrlParams();
$nameAttribute = $generator->getNameAttribute();

echo "<?php\n";
?>

use yii\helpers\Html;
<?php //修改grid为kartik，增加treegrid ?>
<?php if ($generator->indexWidgetType === 'grid'): ?>
use kartik\grid\GridView;
<?php elseif ($generator->indexWidgetType === 'list'): ?>
use yii\widgets\ListView;
<?php elseif ($generator->indexWidgetType === 'tree'): ?>
use dkhlystov\widgets\TreeGrid;
//use leandrogehlen\treegrid\TreeGrid;
<?php endif; ?>
<?= $generator->enablePjax ? 'use yii\widgets\Pjax;' : '' ?>

/* @var $this yii\web\View */
<?= !empty($generator->searchModelClass) ? "/* @var \$searchModel " . ltrim($generator->searchModelClass, '\\') . " */\n" : '' ?>
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-index grid-view box box-primary">

<?php if(!empty($generator->searchModelClass)): ?>
<?= "    <?php " . ($generator->indexWidgetType === 'grid' || $generator->indexWidgetType === 'tree' ? "// " : "") ?>echo $this->render('_search', ['model' => $searchModel]); ?>
<?php endif; ?>

<?= $generator->enablePjax ? '<?php Pjax::begin(); ?>' : '' ?>
<?php if ($generator->indexWidgetType === 'grid'): ?>
    <?= "<?= " ?>GridView::widget([
        'dataProvider' => $dataProvider,
        'hover' => true,
        <?= !empty($generator->searchModelClass) ? "'filterModel' => \$searchModel,\n        'columns' => [\n" : "'columns' => [\n"; ?>
            [
                'class' => '\kartik\grid\CheckboxColumn',
                'rowSelectedClass' => GridView::TYPE_INFO
            ],

<?php
$count = 0;
if (($tableSchema = $generator->getTableSchema()) === false) {
    foreach ($generator->getColumnNames() as $name) {
        if (++$count < 6) {
            echo "            '" . $name . "',\n";
        } else {
            echo "            // '" . $name . "',\n";
        }
    }
} else {
    foreach ($tableSchema->columns as $column) {
        $format = $generator->generateColumnFormat($column);
        if (++$count < 6) {
            echo "            '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
        } else {
            echo "            // '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
        }
    }
}
?>

            [
                'class' => '\kartik\grid\ActionColumn',
                'header' => Yii::t('common', 'Actions'),
                'vAlign' => 'middle',
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
                    Html::a('<i class="fa fa-plus"></i>', ['create'], ['data-pjax' => 0, 'class' => 'btn btn-success', 'title' => Yii::t('common', 'create')]) . ' ' .
                    Html::a('<i class="fa fa-repeat"></i>', ['index'], ['data-pjax' => 0, 'class' => 'btn btn-default', 'title' => Yii::t('common', 'reset')])
            ],
            '{toggleData}',
            '{export}'
        ],

    ]); ?>
<?php elseif ($generator->indexWidgetType === 'list'): ?>
    <?= "<?= " ?>ListView::widget([
        'dataProvider' => $dataProvider,
        'itemOptions' => ['class' => 'item'],
        'itemView' => function ($model, $key, $index, $widget) {
            return Html::a(Html::encode($model-><?= $nameAttribute ?>), ['view', <?= $urlParams ?>]);
        },
    ]) ?>
<?php elseif ($generator->indexWidgetType === 'tree'): ?>
    <div class="box-header with-border">
        <div class="box-header pull-left">
            <i class="fa fa-fw fa-sun-o"></i>
            <h3 class="box-title"><?= "<?= " ?>Yii::t('common', 'message_manage') ?></h3>
        </div>
        <div class="btn-group pull-right">
            <?= "<?=" ?> Html::a('<i class="fa fa-plus"></i>', ['create'], ['data-pjax' => 0, 'class' => 'btn btn-success', 'title' => Yii::t('common', 'create')]) . ' ' .
            Html::a('<i class="fa fa-repeat"></i>', ['index'], ['data-pjax' => 0, 'class' => 'btn btn-default', 'title' => Yii::t('common', 'reset')])?>
        </div>
    </div>
    <?= "<?= " ?>TreeGrid::widget([
        'tableOptions' => ['class' => 'table table-bordered  table-hover table-striped'],
        'rowOptions'=>['class'=>'expanded'],
        'emptyTextOptions'=>['class'=>'empty p-10'],
        'dataProvider' => $dataProvider,
        'parentIdAttribute' => 'pid',
        'showRoots' => true,
        'lazyLoad' => false,
//        'moveAction' => ['move'],
        'columns' => [

<?php
$count = 0;
if (($tableSchema = $generator->getTableSchema()) === false) {
    foreach ($generator->getColumnNames() as $name) {
        if (++$count < 6) {
            echo "            '" . $name . "',\n";
        } else {
            echo "            // '" . $name . "',\n";
        }
    }
} else {
    foreach ($tableSchema->columns as $column) {
        $format = $generator->generateColumnFormat($column);
        if (++$count < 6) {
            echo "            '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
        } else {
            echo "            // '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
        }
    }
}
?>

            [
                'class' => '\yii\grid\ActionColumn',
                'header' => Yii::t('common', 'Actions'),
                'template' => '{create} {update} {delete}',
                'buttons' => [
                    'create' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('common', 'create_sub'),
                            'aria-label' => Yii::t('common', 'create_sub'),
                            'data-pjax' => '0',
                            'class' => 'btn btn-xs btn-success'
                        ];
                        return Html::a('<i class="fa fa-fw fa-plus"></i>', ['create', 'pid' => $model->id], $options);
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
                ]
            ],
        ]
    ]); ?>
<?php endif; ?>
<?= $generator->enablePjax ? '<?php Pjax::end(); ?>' : '' ?>
</div>
