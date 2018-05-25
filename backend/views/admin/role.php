<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
backend\assets\IcheckAsset::register($this);
$this->title = Yii::t('admin', 'Role Admin');
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Admins'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="backend-role-view box box-primary">
    <div class="box-header with-border">
        <i class="fa fa-fw fa-key"></i>
        <h3 class="box-title"><?= Yii::t('admin', 'message_role') . '---' . $model->username ?></h3>
    </div>
    <div class="box-body">
        <?= Html::beginForm(['admin/role', 'id' => $model->id], 'post') ?>
        <?= Html::tag('p', Yii::t('admin','Suggest select only one role!'),['class'=>'hint-block']) ?>
        <?php foreach ($roles as $role): ?>
            <?php $Check = in_array($role, $assignments) ?>
            <?= Html::checkbox('roles[]', $Check, [
                'label' => $role,
                'value' => $role,
                'class' => 'flat',
                'labelOptions' => ['style' => 'margin-top:8px']
            ]) ?>
        <br>
        <?php endforeach; ?>
        <hr>
        <div class="form-group">
            <?= Html::submitButton(Yii::t('common', 'submit'), ['class' => 'btn btn-success']) ?>
            <?= Html::a(Yii::t('common', 'reset'), ['role', 'id' => $model->id], ['class' => 'btn btn-warning', 'style' => 'margin-left:10px']) ?>
        </div>
        <?= Html::endForm() ?>
    </div>
</div>
<?php
//icheck相关的js,控制显示颜色和全选与取消全选
$js = <<<EOF
$('input[type="checkbox"].flat, input[type="radio"].flat').iCheck({
      checkboxClass: 'icheckbox_flat-blue',
      radioClass   : 'iradio_flat-blue'
    });
EOF;
$this->registerJs($js);
?>
