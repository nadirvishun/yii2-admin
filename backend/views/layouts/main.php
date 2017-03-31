<?php
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $content string */
backend\assets\AppAsset::register($this);
dmstr\web\AdminLteAsset::register($this);
//修改表格的样式，也可以直接自定义bootstrap样式，然后将全部的bootstrap样式替换掉，这里直接在这里添加了
$this->registerCss("
    .kv-merged-header{border-bottom:1px solid #e7ecf1 !important}
    .table-hover > tbody > tr:hover {background-color: #eef1f5;}
    .table > thead > tr > th,
    .table > tbody > tr > th,
    .table > tfoot > tr > th,
    .table > thead > tr > td,
    .table > tbody > tr > td,
    .table > tfoot > tr > td {
        border: 1px solid #e7ecf1;
    }
");
$directoryAsset = Yii::$app->assetManager->getPublishedUrl('@vendor/almasaeed2010/adminlte/dist');
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="hold-transition skin-blue sidebar-mini">
<!--下面也没什么必要了，改用js来控制样式-->
<!--<body class="hold-transition --><? //= \dmstr\helpers\AdminLteHelper::skinClass() ?><!-- sidebar-mini">-->
<?php $this->beginBody() ?>
<div class="wrapper">

    <?= $this->render(
        'header.php',
        ['directoryAsset' => $directoryAsset]
    ) ?>

    <?= $this->render(
        'left.php',
        ['directoryAsset' => $directoryAsset]
    )
    ?>

    <?= $this->render(
        'content.php',
        ['content' => $content, 'directoryAsset' => $directoryAsset]
    ) ?>

</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

