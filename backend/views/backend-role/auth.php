<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
backend\assets\IcheckAsset::register($this);
$this->title = Yii::t('backend_role', 'Auth Backend Role');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend_role', 'Backend Roles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="backend-role-view box box-primary">
    <div class="box-header with-border">
        <i class="fa fa-fw fa-key"></i>
        <h3 class="box-title"><?= Yii::t('backend_role', 'message_auth') . '---' . $roleName ?></h3>
    </div>
    <div class="box-body">
        <?= Html::beginForm(['backend-role/auth', 'name' => $roleName], 'post') ?>
        <?php foreach ($menuList as $menu): ?>
            <div class="first-menu">
                <?php $firstCheck = in_array($menu['url'], $permissionsOptions) ?>
                <?= Html::checkbox('permissions[]', $firstCheck, ['label' => $menu['name'], 'value' => $menu['url'], 'class' => 'flat top-checkbox']) ?>
                <?php if (isset($menu['items'])): ?>
                    <?php foreach ($menu['items'] as $secondMenu): ?>
                        <div class="second-menu">
                            <?php $secondCheck = in_array($secondMenu['url'], $permissionsOptions) ?>
                            <?= Html::checkbox('permissions[]', $secondCheck, [
                                'label' => $secondMenu['name'],
                                'value' => $secondMenu['url'],
                                'class' => 'flat',
                                'labelOptions' => ['style' => 'margin:6px 0 6px 15px;font-weight:normal']
                            ]) ?>
                            <?php if (isset($secondMenu['items'])): ?>
                                <br>
                                <?php foreach ($secondMenu['items'] as $thirdMenu): ?>
                                        <?php $thirdCheck = in_array($thirdMenu['url'], $permissionsOptions) ?>
                                        <?= Html::checkbox('permissions[]', $thirdCheck, [
                                            'label' => $thirdMenu['name'],
                                            'value' => $thirdMenu['url'],
                                            'class' => 'flat',
                                            'labelOptions' => ['style' => 'margin:6px 0 6px 30px;font-weight:normal']
                                        ]) ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                <hr>
            </div>
        <?php endforeach; ?>
        <div class="form-group">
            <?= Html::submitButton(Yii::t('backend_role', 'auth'), ['class' => 'btn btn-success']) ?>
        </div>
        <?= Html::endForm() ?>
    </div>
</div>
<?php
//icheck相关的js
$this->registerJs("
    $('input[type=\"checkbox\"].flat, input[type=\"radio\"].flat').iCheck({
      checkboxClass: 'icheckbox_flat-blue',
      radioClass   : 'iradio_flat-blue'
    });
    $('.top-checkbox').on('ifChanged',function(){
        var first_menu=$(this).parent().parent().parent();
        console.log(first_menu);
        if($(this).prop('checked')){
            first_menu.find('input[type=\"checkbox\"]').prop('checked',1);
        }else{
            first_menu.find('input[type=\"checkbox\"]').prop('checked',0);
        }
    })
");
?>
