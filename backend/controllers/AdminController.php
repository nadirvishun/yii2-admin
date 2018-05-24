<?php

namespace backend\controllers;

use backend\models\BackendRole;
use Yii;
use backend\models\Admin;
use backend\models\search\AdminSearch;
use yii\helpers\Url;
use yii\rbac\Item;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\UploadedFile;

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
     * 设置角色
     * @param $id
     */
    public function actionRole($id)
    {
        $model = $this->findModel($id);
        //如果是超级管理员则不用设置角色
        if ($id == Yii::$app->params['super_admin_id']) {
            return $this->redirectError(Url::to('index'), Yii::t('admin', 'Super admin do not need set!'));
        }
        $auth = Yii::$app->authManager;
        if (Yii::$app->request->isPost) {
            //判定角色是否勾选
            $roles = Yii::$app->request->post('roles');
            if (empty($roles)) {
                return $this->redirectError(Url::to(), Yii::t('admin', 'This role must select!'));
            }
            //删除此用户原有的角色
            $auth->revokeAll($id);
            //增加角色赋值
            foreach ($roles as $role) {
                $roleClass = $auth->getRole($role);
                $auth->assign($roleClass, $id);
            }

            $url = $this->getReferrerUrl('admin-role');
            return $this->redirectSuccess($url, Yii::t('admin', 'Set Role Success'));
        } else {
            //获取所有角色
            $rolesArr = $auth->getRoles();
            $roles = array_keys($rolesArr);
            //获取当前用户的角色
            $assignmentsArr = $auth->getAssignments($id);
            $assignments = array_keys($assignmentsArr);
            $this->rememberReferrerUrl('admin-role');
            return $this->render('role', [
                'model' => $model,
                'roles' => $roles,
                'assignments' => $assignments
            ]);
        }
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
            //获取列表页url，方便跳转
            $url = $this->getReferrerUrl('admin-create');
            return $this->redirectSuccess($url, Yii::t('common', 'Create Success'));
        } else {
            //为了更新完成后返回列表检索页数原有状态，所以这里先纪录下来
            $this->rememberReferrerUrl('admin-create');

            $model->loadDefaultValues();
            $act = 'create';
            return $this->render('create', [
                'model' => $model,
                'act' => $act,
                'avatarUrl' => null
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
            //获取列表页url，方便跳转
            $url = $this->getReferrerUrl('admin-update');
            return $this->redirectSuccess($url, Yii::t('common', 'Update Success'));
        } else {
            //为了更新完成后返回列表检索页数原有状态，所以这里先纪录下来
            $this->rememberReferrerUrl('admin-update');
            //将密码字段清空
            $model->password_hash = '';
            $act = 'update';
            return $this->render('update', [
                'model' => $model,
                'act' => $act,
                'avatarUrl' => null
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
            unset($model->avatar);
            //如果有上传头像
            $avatar = UploadedFile::getInstance($model, 'avatar');
            if ($avatar) {//如果上传文件
                //文件重命名
                $newName = time() . rand(1000, 9999);
                if (!$avatar->saveAs(Yii::getAlias('@webroot') . Yii::$app->params['avatarPath'] . $newName . '.' . $avatar->extension)) {
                    $model->addError('avatar', Yii::t('admin', 'Upload avatar failed'));
                }
                $model->avatar = Yii::$app->params['avatarPath'] . $newName . '.' . $avatar->extension;
            }

            //如果传递过来的密码为空,则不更新密码
            if (empty($model->password_hash)) {
                unset($model->password_hash);
            } else {//否则重新加密后的密码写入
                $model->setPassword($model->password_hash);
            }
            //必须在上面先validate，然后save必须为false，否则由于密码被加密后导致确认密码不一致
            if (!$model->hasErrors() && $model->save(false)) {
                return $this->redirectSuccess(['modify'], Yii::t('common', 'Update Success'));
            }
        }
        //将密码字段清空
        $model->password_hash = '';
        if ($model->avatar) {
            $avatarUrl = Yii::$app->request->hostInfo . $model->avatar;
        } else {
            $avatarUrl = null;
        }
        $act = 'modify';
        return $this->render('update', [
            'model' => $model,
            'act' => $act,
            'avatarUrl' => $avatarUrl
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
        $url = Yii::$app->request->referrer;
        //超级管理员不能删除
        if (Yii::$app->params['super_admin_id'] == $id) {
            return $this->redirectError($url, Yii::t('admin', 'Super admin can not delete!'));
        }
        //删除时需要判定不能删除自身
        if ($id == Yii::$app->user->id) {
            return $this->redirectError($url, Yii::t('admin', 'Can not delete self'));
        }
        //如果是从view中删除，则返回列表页
        if (strpos(urldecode($url), 'admin/view') !== false) {
            $url = ['index'];
        }
        $model = $this->findModel($id);
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $model->delete();
            //删除用户时，判定是否有角色，如果有则一起删除
            $auth = Yii::$app->authManager;
            $roles = $auth->getAssignments($id);
            if (!empty($roles)) {
                $auth->revokeAll($id);
            }
            $transaction->commit();
            return $this->redirectSuccess($url, Yii::t('common', 'Delete Success'));
        } catch (\Exception $e) {
            $transaction->rollBack();
        } catch (\Throwable $e) {
            $transaction->rollBack();
        }
        return $this->redirectError($url, Yii::t('common', 'Delete Failed'));
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
