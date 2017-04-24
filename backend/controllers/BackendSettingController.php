<?php

namespace backend\controllers;

use common\components\Tree;
use Yii;
use backend\models\BackendSetting;
use backend\models\search\BackendSettingSearch;
use backend\controllers\BaseController;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * BackendSettingController implements the CRUD actions for BackendSetting model.
 */
class BackendSettingController extends BaseController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * 系统菜单设置
     */
    public function actionSystem()
    {
        //组装成TabWidget所需求的形式，这里是显示全部的，而不是一个链接一个链接的保存
        //获取顶级
        $rootList = BackendSetting::getSettingList();
        $items = [];
        if (!empty($rootList)) {
            foreach ($rootList as $k=> $list) {
                $str = '';
                $items[$k]['label'] = $list['name'];
                $children = BackendSetting::getSettingList($list['id']);
                if (!empty($children)) {
                    foreach ($children as $key => $child) {
                    $str .= '<div class="form-group field-blogcatalog-parent_id">'
                        .'<label class="col-lg-2 control-label" for="blogcatalog-parent_id">' . $child['name'] . '</label>'
                        .'<div class="col-lg-3">';
                        if ($child['type'] == BackendSetting::TEXT) {//如果是普通文本
                            $str .= Html::textInput("Setting[{$child['alias']}]", $child['value'], ["class" => "form-control"]);
                        } elseif ($child['type'] == BackendSetting::PASSWORD) {//如果米密码域
                            $str .= Html::passwordInput("Setting[{$child['alias']}]", $child['value'], ["class" => "form-control"]);
                        } elseif ($child['type'] == BackendSetting::SELECT) {//如果是下拉菜单
                            $options = [];
                            $arrayOptions = explode(',', $child->store_range);
                            foreach ($arrayOptions as $option)
                                $options[$option] = Module::t('setting', $option);
                            $str .= Html::dropDownList("Setting[{$child['alias']}]", $child['value'], $options, ["class" => "form-control"]);
                        } elseif ($child['type'] == BackendSetting::RADIO) {//如果是单选

                        } elseif ($children['type'] == BackendSetting::TEXTAREA) {//如果是多行文本

                        }
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
        $searchModel = new BackendSettingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
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
     */
    public function actionCreate($pid = null)
    {
        $model = new BackendSetting();
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
            $list = BackendSetting::find()
                ->asArray()
                ->all();
            //创建树实例
            $tree = new Tree();
            $rootOption = ['0' => Yii::t('backend_setting', 'Root Tree')];
            $data['treeOptions'] = ArrayHelper::merge($rootOption, $tree->getTreeOptions($list));

            return $this->render('create', $data);
        }
    }

    /**
     * Updates an existing BackendSetting model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = 'update';
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirectSuccess(['index'], Yii::t('common', 'Update Success'));
        } else {
            //显示树下拉菜单
            $list = BackendSetting::find()
                ->asArray()
                ->all();
            //创建树实例
            $tree = new Tree();
            $rootOption = ['0' => Yii::t('backend_setting', 'Root Tree')];
            $treeOptions = ArrayHelper::merge($rootOption, $tree->getTreeOptions($list));
            return $this->render('update', [
                'model' => $model,
                'treeOptions' => $treeOptions
            ]);
        }
    }

    /**
     * Deletes an existing BackendSetting model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirectSuccess(['index'], Yii::t('common', 'Delete Success'));
    }

    /**
     * Finds the BackendSetting model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return BackendSetting the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BackendSetting::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('common', 'The requested page does not exist.'));
        }
    }
}
