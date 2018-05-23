<?php

namespace backend\controllers;

use common\components\Tree;
use Yii;
use backend\models\Setting;
use backend\models\search\SettingSearch;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * BackendSettingController implements the CRUD actions for BackendSetting model.
 */
class SettingController extends BaseController
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
        if (Yii::$app->request->post()) {
            $settings = Yii::$app->request->post('Setting');
            foreach ($settings as $key => $value) {
                Setting::updateAll(['value' => $value], ['alias' => $key]);
            }
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
            $list = Setting::find()->select('id,pid,name')
                ->where(['status' => Setting::STATUS_VISIBLE])//不显示隐藏的
                ->asArray()
                ->all();
            //创建树实例
            $tree = new Tree();
            $rootOption = ['0' => Yii::t('setting', 'Root Tree')];
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
            $list = Setting::find()->select('id,pid,name')
                ->where(['status' => Setting::STATUS_VISIBLE])//不显示隐藏的
                ->asArray()
                ->all();
            //创建树实例
            $tree = new Tree();
            $rootOption = ['0' => Yii::t('setting', 'Root Tree')];
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
}
