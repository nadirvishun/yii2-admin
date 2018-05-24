<?php

use yii\helpers\Html;

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
                <?= Html::checkbox('permissions[]', $firstCheck, [
                    'label' => $menu['name'],
                    'value' => $menu['url'] . '|' . $menu['name'],
                    'class' => 'flat top-checkbox'
                ]) ?>
                <?php if (isset($menu['items'])): ?>
                    <?php foreach ($menu['items'] as $secondMenu): ?>
                        <div class="second-menu">
                            <?php $secondCheck = in_array($secondMenu['url'], $permissionsOptions) ?>
                            <?= Html::checkbox('permissions[]', $secondCheck, [
                                'label' => $secondMenu['name'],
                                'value' => $secondMenu['url'] . '|' . $secondMenu['name'],
                                'class' => 'flat second-checkbox',
                                'labelOptions' => ['style' => 'margin:6px 0 6px 15px;font-weight:normal']
                            ]) ?>
                            <?php if (isset($secondMenu['items'])): ?>
                                <br>
                                <?php foreach ($secondMenu['items'] as $thirdMenu): ?>
                                    <?php $thirdCheck = in_array($thirdMenu['url'], $permissionsOptions) ?>
                                    <?= Html::checkbox('permissions[]', $thirdCheck, [
                                        'label' => $thirdMenu['name'],
                                        'value' => $thirdMenu['url'] . '|' . $thirdMenu['name'],
                                        'class' => 'flat third-checkbox',
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
            <?= Html::a(Yii::t('common', 'reset'), ['auth', 'name' => $roleName], ['class' => 'btn btn-warning', 'style' => 'margin-left:10px']) ?>
        </div>
        <?= Html::endForm() ?>
    </div>
</div>
<?php
//icheck相关的js、控制显示颜色和全选与取消全选、设置如果二三级有选择，则上级自动勾选
$js = <<<EOF
$('input[type="checkbox"].flat, input[type="radio"].flat').iCheck({
      checkboxClass: 'icheckbox_flat-blue',
      radioClass   : 'iradio_flat-blue'
    });
    var openAllSelect=true;
    $('.top-checkbox').on('ifChanged',function(){
        if(openAllSelect){
            var first_menu=$(this).parent().parent().parent();
            if($(this).prop('checked')){
                first_menu.find('input[type="checkbox"]').iCheck('check');
            }else{
                first_menu.find('input[type="checkbox"]').iCheck('uncheck');
            }
        }
    });
    $('.second-checkbox').on('ifChecked',function(){
        var top=$(this).parent().parent().parent().parent().find('.top-checkbox');
        if(!top.prop('checked')){
            openAllSelect=false;
            top.iCheck('check');
            openAllSelect=true;
        }
    });
    $('.third-checkbox').on('ifChecked',function(){
        var second_menu=$(this).parent().parent().parent();
        var second=second_menu.find('.second-checkbox');
        if(!second.prop('checked')){
            second.iCheck('check');
        }
        var top=second_menu.parent().find('.top-checkbox');
        if(!top.prop('checked')){
            openAllSelect=false;
            top.iCheck('check');
            openAllSelect=true;
        }
    });
EOF;
$this->registerJs($js);
?>
