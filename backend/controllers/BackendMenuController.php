<?php

namespace backend\controllers;

use backend\models\TestTree;
use Yii;
use backend\models\BackendMenu;
use backend\models\BackendMenuSearch;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * BackendMenuController implements the CRUD actions for BackendMenu model.
 */
class BackendMenuController extends Controller
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
    public function actionIndex()
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

        return $this->render('index', [
            'dataProvider' => $dataProvider,
//            'initial' => $initial,
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
    public function actionCreate()
    {
        $model = new BackendMenu();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
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

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
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

        return $this->redirect(['index']);
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
            throw new NotFoundHttpException('The requested page does not exist.');
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