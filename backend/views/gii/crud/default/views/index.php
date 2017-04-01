<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$urlParams = $generator->generateUrlParams();
$nameAttribute = $generator->getNameAttribute();

echo "<?php\n";
?>

use yii\helpers\Html;
use <?= $generator->indexWidgetType === 'grid' ? "kartik\\grid\\GridView" : "yii\\widgets\\ListView" ?>;
<?= $generator->enablePjax ? 'use yii\widgets\Pjax;' : '' ?>

/* @var $this yii\web\View */
<?= !empty($generator->searchModelClass) ? "/* @var \$searchModel " . ltrim($generator->searchModelClass, '\\') . " */\n" : '' ?>
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-index">

<?php if(!empty($generator->searchModelClass)): ?>
<?= "    <?php " . ($generator->indexWidgetType === 'grid' ? "// " : "") ?>echo $this->render('_search', ['model' => $searchModel]); ?>
<?php endif; ?>


<?= $generator->enablePjax ? '<?php Pjax::begin(); ?>' : '' ?>
<?php if ($generator->indexWidgetType === 'grid'): ?>
    <?= "<?= " ?>GridView::widget([
        'options' => ['class' => 'grid-view box box-primary'],
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
                'vAlign' => 'middle',
                'template' => '{view} {update} {delete}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a('<i class="fa fa-fw fa-eye"></i>', ['view', 'id' => $model->id], ['title' => Yii::t('common', 'view'), 'class' => 'btn btn-xs btn-info']);
                    },
                    'update' => function ($url, $model) {
                        return Html::a('<i class="fa fa-fw fa-edit"></i>', ['update', 'id' => $model->id], ['title' => Yii::t('common', 'update'), 'class' => 'btn btn-xs btn-warning']);
                    },
                    'delete' => function ($url, $model) {
                        return Html::a('<i class="fa fa-fw fa-trash"></i>', ['delete', 'id' => $model->id], ['title' => Yii::t('common', 'delete'), 'class' => 'btn btn-xs btn-danger']);
                    }
                ],
            ]
        ],
        'panel' => [
            'heading' => false,
            'before' => '<div class="box-header pull-left">
                    <i class="fa fa-fw fa-gear"></i><h3 class="box-title">' . Yii::t('common', 'manage') . '</h3>
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
                    Html::a('<i class="glyphicon glyphicon-plus"></i>', ['create'], ['data-pjax' => 0, 'class' => 'btn btn-success', 'title' => Yii::t('common', 'create')]) . ' ' .
                    Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['index'], ['data-pjax' => 0, 'class' => 'btn btn-default', 'title' => Yii::t('common', 'reset')])
            ],
            '{toggleData}',
            '{export}'
        ],

    ]); ?>
<?php else: ?>
    <?= "<?= " ?>ListView::widget([
        'dataProvider' => $dataProvider,
        'itemOptions' => ['class' => 'item'],
        'itemView' => function ($model, $key, $index, $widget) {
            return Html::a(Html::encode($model-><?= $nameAttribute ?>), ['view', <?= $urlParams ?>]);
        },
    ]) ?>
<?php endif; ?>
<?= $generator->enablePjax ? '<?php Pjax::end(); ?>' : '' ?>
</div>
