<?php

namespace backend\controllers;

use backend\models\AdminLog;
use Yii;
use backend\models\Setting;
use backend\models\search\SettingSearch;
use yii\helpers\Html;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * BackendSettingController implements the CRUD actions for BackendSetting model.
 */
class SettingController extends BaseController
{
    /**
     * ueditor上传
     * @return array
     */
    public function actions()
    {
        return [
            //ueditor上传
            'ueditorUpload' => [
                'class' => 'kucha\ueditor\UEditorAction',
                'config' => Yii::$app->params['ueditorConfig']
            ],
            //fileInput上传
            'upload' => [
                'class' => 'common\components\UploadAction',
                'path' => Yii::$app->params['settingPath'],//上传路径
                'rule' => ['skipOnEmpty' => false]
            ]
        ];
    }

    /**
     * 系统菜单设置
     */
    public function actionSystem()
    {
        if (Yii::$app->request->post()) {
            $settings = Yii::$app->request->post('Setting');
            foreach ($settings as $key => $value) {
                //如果是checkboxlist类似的数组形式，则用json存储
                if (is_array($value)) {
                    $value = json_encode($value);
                }
                Setting::updateAll(['value' => $value], ['alias' => $key]);
            }
            //写入操作日志
            $title = Yii::t('setting', 'change setting');
            AdminLog::saveAdminLog(Setting::className(), AdminLog::TYPE_UPDATE, $title, $title);

            return $this->redirectSuccess(['system'], Yii::t('common', 'Update Success'));
        }
        //组装成TabWidget所需求的形式，这里是显示全部的，而不是一个链接一个链接的保存
        //获取顶级
        $rootList = Setting::getSettingList();
        $items = [];
        if (!empty($rootList)) {
            foreach ($rootList as $k => $list) {
                $str = '';
                $items[$k]['label'] = $list['name'];
                $children = Setting::getSettingList($list['id']);
                if (!empty($children)) {
                    foreach ($children as $key => $child) {
                        $str .= Html::beginTag('div', ['class' => 'form-group  c-md-5']);
                        $str .= Html::label($child['name'], "Setting[{$child['alias']}]");
                        $str .= Setting::createInputTag($child['type'], "Setting[{$child['alias']}]", $child['value'], $child['extra']);
                        //增加提示
                        if (!empty($child['hint'])) {
                            $str .= Html::tag('div', $child['hint'], ['class' => 'hint-block']);
                        }
                        $str .= Html::endTag('div');
                    }
                }
                $items[$k]['content'] = $str;
            }
        }
        return $this->render('system', [
            'items' => $items
        ]);
    }

    /**
     * Lists all BackendSetting models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SettingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        if (Yii::$app->request->post('hasEditable')) {
            $id = Yii::$app->request->post('editableKey');//获取ID
            $model = Setting::findOne($id);
            $attribute = Yii::$app->request->post('editableAttribute');//获取名称
            $output = '';
            $message = '';
            if ($model->load(Yii::$app->request->post(), '') && $model->save()) {
                $output = $model->$attribute;
            } else {
                //由于本插件不会自动捕捉model的error，所以需要放在$message中展示出来
                $message = $model->getFirstError($attribute);
            }
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['output' => $output, 'message' => $message];
        } else {
            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }
    }

    /**
     * Displays a single BackendSetting model.
     * @param integer $id
     * @return mixed
     */
//    public function actionView($id)
//    {
//        return $this->render('view', [
//            'model' => $this->findModel($id),
//        ]);
//    }

    /**
     * Creates a new BackendSetting model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionCreate($pid = null)
    {
        $model = new Setting();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {//如果是post传递保存
            return $this->redirectSuccess(['index'], Yii::t('common', 'Create Success'));
        } else {//如果是展示页面
            //获取默认状态
            $model->loadDefaultValues();
            //如果仅仅是建下级，需要传递父级的id
            if ($pid !== null) {
                //判断pid是否存在
                $this->findModel($pid);
                $model->pid = $pid;
            }
            $data['model'] = $model;
            //显示树下拉菜单
            $data['treeOptions'] = Setting::getSettingTreeOptions();
            //用于给js赋值，切换不同的提示信息
            $data['placeholderOptions'] = json_encode(Setting::getPlaceholderOptions());
            return $this->render('create', $data);
        }
    }

    /**
     * Updates an existing BackendSetting model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = 'update';
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirectSuccess(['index'], Yii::t('common', 'Update Success'));
        } else {
            //显示树下拉菜单
            $treeOptions = Setting::getSettingTreeOptions();
            //用于给js赋值，切换不同的提示信息
            $placeholderOptions = json_encode(Setting::getPlaceholderOptions());
            return $this->render('update', [
                'model' => $model,
                'treeOptions' => $treeOptions,
                'placeholderOptions' => $placeholderOptions
            ]);
        }
    }

    /**
     * Deletes an existing BackendSetting model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        $model=$this->findModel($id);
        //判定是否有下级，如果有则提示先删除下级
        $children = Setting::getChildIds([$id], false);
        if (!empty($children)) {
            return $this->redirectError(['index'],
                Yii::t('setting', 'This node has children ,please delete children first'));
        }

        $model->delete();

        return $this->redirectSuccess(['index'], Yii::t('common', 'Delete Success'));
    }

    /**
     * Finds the BackendSetting model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Setting the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Setting::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('common', 'The requested page does not exist.'));
        }
    }

    /**
     * ueditor上传图片的权限与显示system的权限是一样的
     * fileInput上传图片的权限与显示system的权限是一样的
     * @param $permission
     * @return mixed
     */
    public function getSamePermission($permission)
    {
        $arr = [
            'setting/ueditorUpload' => 'setting/system',
            'setting/upload' => 'setting/system',
        ];
        return isset($arr[$permission]) ? $arr[$permission] : $permission;
    }
}
