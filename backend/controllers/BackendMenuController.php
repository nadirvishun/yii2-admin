<?php

namespace backend\controllers;

use Yii;
use backend\models\BackendMenu;
use yii\caching\TagDependency;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\web\Response;


/**
 * BackendMenuController implements the CRUD actions for BackendMenu model.
 */
class BackendMenuController extends BaseController
{
    /**
     * Lists all BackendMenu models.
     * @param null $id
     * @return mixed
     */
    public function actionIndex($id = null)
    {
        //另一个插件，也不错，但是没有ajax加载，和拖动移动（虽然没用到）
//        $searchModel = new BackendMenuSearch();
//        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
//
//        return $this->render('index_1', [
//            'searchModel' => $searchModel,
//            'dataProvider' => $dataProvider,
//        ]);

        if (Yii::$app->request->post('hasEditable')) {
            $id = Yii::$app->request->post('editableKey');//获取ID
            $model = BackendMenu::findOne($id);
            $attribute = Yii::$app->request->post('editableAttribute');//获取名称
            $output = '';
            $message = '';
            if ($model->load(Yii::$app->request->post(), '') && $model->save()) {
                $output = $model->$attribute;
                //将左侧菜单缓存设置为失效，取所有角色，因为没有通过权限获取角色的方法，自己也懒得写了
                $auth = Yii::$app->authManager;
                $rolesArr = $auth->getRoles();
                $roles = array_merge(['super_admin'], array_keys($rolesArr));
                TagDependency::invalidate(Yii::$app->cache, $roles);
            } else {
                //由于本插件不会自动捕捉model的error，所以需要放在$message中展示出来
                $message = $model->getFirstError($attribute);
            }
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['output' => $output, 'message' => $message];
        } else {
            //todo,目前此widget不支持sort，后续可能改进，如果后期菜单太多，可改为懒加载
            $dataProvider = new ActiveDataProvider([
                'query' => BackendMenu::find()->orderBy(['sort' => SORT_DESC, 'id' => SORT_ASC]),
//            'sort' => ['defaultOrder' => ['sort' => SORT_ASC, 'id' => SORT_ASC]]
            ]);
//        $initial = BackendMenu::findOne($id);
            return $this->render('index', [
                'dataProvider' => $dataProvider,
//            'initial' => $initial,
            ]);
        }
    }

    /**
     * Displays a single BackendMenu model.
     * @param string $id
     * @return mixed
     */
    /*public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }*/

    /**
     * Creates a new BackendMenu model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param null | integer $pid
     * @return mixed
     */
    public function actionCreate($pid = null)
    {
        $model = new BackendMenu();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {//如果是post传递保存
            //将左侧菜单缓存设置为失效，取所有角色，因为没有通过权限获取角色的方法，自己也懒得写了
            $auth = Yii::$app->authManager;
            $rolesArr = $auth->getRoles();
            $roles = array_merge(['super_admin'], array_keys($rolesArr));
            TagDependency::invalidate(Yii::$app->cache, $roles);

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
            //获取树形下拉选项
            $data['treeOptions'] = BackendMenu::getMenuTreeOptions();

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
        //如果是提交数据
        if (Yii::$app->request->isPost) {
            $model->scenario = 'update';
            //载入数据
            $model->load(Yii::$app->request->post());
            //判定是否已写入权限表(url为权限名称，name为权限描述)
            $hasChange = false;//标记是否需要变动
            $auth = Yii::$app->authManager;
            $oldUrl = $model->getOldAttribute('url');//旧的名称
            $permission = $auth->getPermission($oldUrl);
            if ($permission) {
                //已写入，则判定判定url和name是否有变动，如果有变动，需要更新权限表里的名称
                if ($model->isAttributeChanged('name') || $model->isAttributeChanged('url')) {
                    $hasChange = true;
                }
            }
            if ($model->save()) {
                if ($hasChange) {
                    $permission->name = $model->url;
                    $permission->description = $model->name;
                    $auth->update($oldUrl, $permission);
                }
                //将左侧菜单缓存设置为失效，取所有角色，因为没有通过权限获取角色的方法，自己也懒得写了
                $rolesArr = $auth->getRoles();
                $roles = array_merge(['super_admin'], array_keys($rolesArr));
                TagDependency::invalidate(Yii::$app->cache, $roles);

                return $this->redirectSuccess(['index'], Yii::t('common', 'Update Success'));
            }
        }
        //显示树下拉菜单
        $treeOptions = BackendMenu::getMenuTreeOptions();
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
        $model = $this->findModel($id);
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $model->delete();
            //判定是否已作为权限
            $auth = Yii::$app->authManager;
            $permission = $auth->getPermission($model->url);
            if ($permission) {
                //移除此权限
                $auth->remove($permission);
            }

            //将左侧菜单缓存设置为失效，取所有角色，因为没有通过权限获取角色的方法，自己也懒得写了
            $rolesArr = $auth->getRoles();
            $roles = array_merge(['super_admin'], array_keys($rolesArr));
            TagDependency::invalidate(Yii::$app->cache, $roles);

            $transaction->commit();
            return $this->redirectSuccess(['index'], Yii::t('common', 'Delete Success'));
        } catch (\Exception $e) {
            $transaction->rollBack();
        } catch (\Throwable $e) {
            $transaction->rollBack();
        }
        return $this->redirectError(['index'], Yii::t('common', 'Delete Failed'));
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
    /*  function actionMove($id, $target, $position)
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
      }*/
}
