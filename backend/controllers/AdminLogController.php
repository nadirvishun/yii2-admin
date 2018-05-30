<?php

namespace backend\controllers;

use Yii;
use backend\models\AdminLog;
use backend\models\search\AdminLogSearch;
use yii\helpers\Inflector;
use yii\web\NotFoundHttpException;

/**
 * AdminLogController implements the CRUD actions for AdminLog model.
 */
class AdminLogController extends BaseController
{
    /**
     * Lists all AdminLog models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AdminLogSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AdminLog model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new AdminLog model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    /*public function actionCreate()
    {
        $model = new AdminLog();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            //获取列表页url，方便跳转
            $url = $this->getReferrerUrl('admin-log-create');
            return $this->redirectSuccess($url, Yii::t('common', 'Create Success'));
        } else {
            //为了更新完成后返回列表检索页数原有状态，所以这里先纪录下来
            $this->rememberReferrerUrl('admin-log-create');

            $model->loadDefaultValues();
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }*/

    /**
     * Updates an existing AdminLog model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    /*public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            //获取列表页url，方便跳转
            $url = $this->getReferrerUrl('admin-log-update');
            return $this->redirectSuccess($url, Yii::t('common', 'Update Success'));
        } else {
            //为了更新完成后返回列表检索页数原有状态，所以这里先纪录下来
            $this->rememberReferrerUrl('admin-log-update');

            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }*/

    /**
     * 按不同类型删除日志
     * @param $type
     * @return \yii\web\Response
     */
    public function actionDelete($type)
    {
        $url = Yii::$app->request->referrer;
        //如果是从view中删除，则返回列表页
        if (strpos(urldecode($url), 'admin-log/view') !== false) {
            $url = ['index'];
        }
        switch ($type) {
            case AdminLog::DELETE_MONTH:
                //删除一个月之前的日志
                $month = strtotime('-1 month');
                $result = AdminLog::deleteAll(['<', 'created_at', $month]);
                $title = Yii::t('admin_log', 'Delete month ago');
                break;
            case AdminLog::DELETE_WEEK:
                //删除一星期之前的日志
                $week = strtotime('-1 week');
                $result = AdminLog::deleteAll(['<', 'created_at', $week]);
                $title = Yii::t('admin_log', 'Delete week ago');
                break;
            case AdminLog::DELETE_ALL;
                //删除全部日志
                $result = AdminLog::deleteAll();
                $title = Yii::t('admin_log', 'Delete all');
                break;
            default:
                return $this->redirectError($url, Yii::t('common', 'Invalid Parameter'));
                break;
        }
        //由于是批量删除不触发afterDelete，所以手动写入日志
        if ($result) {
            AdminLog::saveAdminLog(AdminLog::className(), AdminLog::TYPE_DELETE, $title, $title);
        }
        return $this->redirectSuccess($url, Yii::t('common', 'Delete Success'));
    }

    /**
     * Finds the AdminLog model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AdminLog the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AdminLog::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('common', 'The requested page does not exist.'));
        }
    }
}
