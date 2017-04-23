<?php

namespace backend\controllers;

use Yii;
use backend\models\Admin;
use backend\models\search\AdminSearch;
use backend\controllers\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * AdminController implements the CRUD actions for Admin model.
 */
class AdminController extends BaseController
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
                    //目前没有rbac，暂时设定只有admin账号才能修改或删除所有后台管理员的信息
                    [
                        'actions' => ['update', 'delete'],
                        'allow' => false,
                        'matchCallback' => function ($rule, $action) {
                            return Yii::$app->user->identity->username != 'admin';
                        }
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],

                ],
            ],
        ];
    }

    /**
     * Lists all Admin models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AdminSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Admin model.
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
     * 新建管理员
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Admin();
        $model->scenario = 'create';
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirectSuccess(['index'], Yii::t('common', 'Create Success'));
        } else {
            $model->loadDefaultValues();
            $act = 'create';
            return $this->render('create', [
                'model' => $model,
                'act' => $act
            ]);
        }
    }

    /**
     * Updates an existing Admin model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = 'update';
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirectSuccess(['index'], Yii::t('common', 'Update Success'));
        } else {
            //将密码字段清空
            $model->password_hash = '';
            $act = 'update';
            return $this->render('update', [
                'model' => $model,
                'act' => $act
            ]);
        }
    }

    /**
     * 修改自身
     * @return string|\yii\web\Response
     */
    public function actionModify()
    {
        //获取管理员自身的id
        $id = Yii::$app->user->id;
        $model = $this->findModel($id);
        $model->scenario = 'modify';
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            //如果传递过来的密码为空,则不更新密码
            if (empty($model->password_hash)) {
                unset($model->password_hash);
            } else {//否则重新加密后的密码写入
                $model->setPassword($model->password_hash);
            }
            //必须在上面先validate，然后save必须为false，否则由于密码被加密后导致确认密码不一致
            if ($model->save(false)) {
                return $this->redirectSuccess(['index'], Yii::t('common', 'Update Success'));
            }
        }
        //将密码字段清空
        $model->password_hash = '';
        $act = 'modify';
        return $this->render('update', [
            'model' => $model,
            'act' => $act
        ]);

    }

    /**
     * Deletes an existing Admin model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        //删除时需要判定不能删除自身
        if ($id == Yii::$app->user->id) {
            return $this->redirectError(['index'], Yii::t('admin', 'Can not delete self'));
        }
        $this->findModel($id)->delete();

        return $this->redirectSuccess(['index'], Yii::t('common', 'Delete Success'));
    }

    /**
     * Finds the Admin model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Admin the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Admin::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('common', 'The requested page does not exist.'));
        }
    }
}
