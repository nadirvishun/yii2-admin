<?php

namespace backend\controllers;

use common\components\Tree;
use Yii;
use backend\models\BackendMenu;
use backend\models\search\BackendMenuSearch;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * BackendMenuController implements the CRUD actions for BackendMenu model.
 */
class BackendMenuController extends BaseController
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
     * Lists all BackendMenu models.
     * @return mixed
     */
    public function actionIndex($id = null)
    {
//        $searchModel = new BackendMenuSearch();
//        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
//
//        return $this->render('index_1', [
//            'searchModel' => $searchModel,
//            'dataProvider' => $dataProvider,
//        ]);
        $dataProvider = new ActiveDataProvider([
            'query' => BackendMenu::find(),
        ]);
        $initial = BackendMenu::findOne($id);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'initial' => $initial,
        ]);
    }

    /**
     * Displays a single BackendMenu model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new BackendMenu model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($pid = null)
    {
        $model = new BackendMenu();
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
            $list = BackendMenu::find()
                ->asArray()
                ->all();
            $rootOption = ['0' => Yii::t('backend_menu', 'Root Tree')];
            $data['treeOptions'] = ArrayHelper::merge($rootOption, Tree::getTreeOptions($list));

            return $this->render('create', $data);
        }
    }

    /**
     * Updates an existing BackendMenu model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            //单独验证下选择父ID不能为自身或其下级节点
            $childIds = BackendMenu::getChildIds($model->id);
            if (in_array($model->pid, $childIds)) {
                $model->addError('pid', Yii::t('backend_menu', 'Parent ID can not be itself or its subordinate node'));
            }
            if (!$model->hasErrors() && $model->save()) {//hasErrors必须放在前面，因为save()时调用validate方法会清空错误
                return $this->redirectSuccess(['index'], Yii::t('common', 'Update Success'));
            }
        }
        //显示树下拉菜单
        $list = BackendMenu::find()
            ->asArray()
            ->all();
        $rootOption = ['0' => Yii::t('backend_menu', 'Root Tree')];
        $treeOptions = ArrayHelper::merge($rootOption, Tree::getTreeOptions($list));
        return $this->render('update', [
            'model' => $model,
            'treeOptions' => $treeOptions
        ]);

    }

    /**
     * Deletes an existing BackendMenu model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirectSuccess(['index'], Yii::t('common', 'Delete Success'));
    }

    /**
     * Finds the BackendMenu model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return BackendMenu the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BackendMenu::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('common', 'The requested page does not exist.'));
        }
    }

    /**
     * 移动节点
     * @param $id
     * @param $target
     * @param $position
     */
    function actionMove($id, $target, $position)
    {
        $model = BackendMenu::findOne($id);

        $t = BackendMenu::findOne($target);

        switch ($position) {
            case 0:
                $model->insertBefore($t);
                break;

            case 1:
                $model->appendTo($t);
                break;

            case 2:
                $model->insertAfter($t);
                break;
        }
    }
}
