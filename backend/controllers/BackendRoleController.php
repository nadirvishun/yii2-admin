<?php

namespace backend\controllers;

use backend\models\BackendMenu;
use common\components\Tree;
use Yii;
use backend\models\BackendRole;
use backend\models\search\BackendRoleSearch;
use backend\controllers\BaseController;
use yii\helpers\Url;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * BackendRoleController implements the CRUD actions for BackendRole model.
 */
class BackendRoleController extends BaseController
{
    /**
     * Lists all BackendRole models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BackendRoleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 授权
     * @param string $name
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionAuth($name)
    {
        $auth = Yii::$app->authManager;
        $request = Yii::$app->request;
        if ($request->isPost) {
            //判定角色是否存在
            $role = $auth->getRole($name);
            if (empty($role)) {
                return $this->redirectError(Url::to(), Yii::t('backend_role', 'This role not exist!'));
            }
            //判定权限是否勾选
            $permissions = $request->post('permissions');
            if (empty($permissions)) {
                return $this->redirectError(Url::to(), Yii::t('backend_role', 'This permission must select!'));
            }
            //开始处理数据
            $transaction = Yii::$app->db->beginTransaction();
            try {
                //将原有此角色下属权限删除
                $auth->removeChildren($role);
                //组装权限
                foreach ($permissions as $permission) {
                    list($permissionName, $permissionDes) = explode('|', $permission);
                    //判定权限是否存在，如果不存在，则写入
                    $exist = $auth->getPermission($permissionName);
                    if (!$exist) {
                        $permissionClass = $auth->createPermission($permissionName);
                        $permissionClass->description = $permissionDes;
                        $auth->add($permissionClass);
                    } else {
                        $permissionClass = $auth->getPermission($permissionName);
                    }
                    //写入上下级关系
                    $auth->addChild($role, $permissionClass);
                }
                $transaction->commit();
                $url = $this->getReferrerUrl('backend-role-auth');
                return $this->redirectSuccess($url, Yii::t('backend_role', 'Auth Success!'));
            } catch (\Exception $e) {
                $transaction->rollBack();
            } catch (\Throwable $e) {
                $transaction->rollBack();
            }
            return $this->redirectError(Url::to(), Yii::t('backend_role', 'Auth Failed!'));
        } else {
            $this->rememberReferrerUrl('backend-role-auth');
            //获取backend_menu中的数据
            $list = BackendMenu::find()
                ->select('id,name,pid,url')
                ->asArray()
                ->all();
            $tree = new Tree();
            $menuList = $tree->getTree($list);
            //获取本角色下属的权限
            $permissionsList = $auth->getPermissionsByRole($name);
            $permissionsOptions = array_keys($permissionsList);//只取名称
            return $this->render('auth', [
                'menuList' => $menuList,
                'permissionsOptions' => $permissionsOptions,
                'roleName' => $name
            ]);
        }
    }

    /**
     * Creates a new BackendRole model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new BackendRole();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $auth = Yii::$app->authManager;
            //调用auth中相关方法写入，也可以用自己写的model方法写入,懒得写了
            $role = $auth->createRole($model->name);
            $role->description = $model->description;
            //原方法都是返回true，或者抛出异常，所以这里也不判断了
            $auth->add($role);
            //获取列表页url，方便跳转
            $url = $this->getReferrerUrl('backend-role-create');
            return $this->redirectSuccess($url, Yii::t('common', 'Create Success'));
        } else {
            //为了更新完成后返回列表检索页数原有状态，所以这里先纪录下来
            $this->rememberReferrerUrl('backend-role-create');

            $model->loadDefaultValues();
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing BackendRole model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $name
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionUpdate($name)
    {
        $model = $this->findModel($name);
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $auth = Yii::$app->authManager;
            $role = $auth->getRole($name);//相当于查询了两次数据库，好处是不用改前端了，所以用这种方法
            $role->name = $model->name;
            $role->description = $model->description;
            $auth->update($name, $role);
            //获取列表页url，方便跳转
            $url = $this->getReferrerUrl('backend-role-update');
            return $this->redirectSuccess($url, Yii::t('common', 'Update Success'));
        } else {
            //为了更新完成后返回列表检索页数原有状态，所以这里先纪录下来
            $this->rememberReferrerUrl('backend-role-update');

            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing BackendRole model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $name
     * @return mixed
     */
    public function actionDelete($name)
    {
        $model = $this->findModel($name);
        $auth = Yii::$app->authManager;
        $role = $auth->getRole($name);
        $auth->remove($role);
        $url = Yii::$app->request->referrer;
        //如果是从view中删除，则返回列表页
        if (strpos(urldecode($url), 'backend-role/view') !== false) {
            $url = ['index'];
        }
        return $this->redirectSuccess($url, Yii::t('common', 'Delete Success'));
    }

    /**
     * Finds the BackendRole model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $name
     * @return BackendRole the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($name)
    {
        if (($model = BackendRole::findOne($name)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('common', 'The requested page does not exist.'));
        }
    }
}
