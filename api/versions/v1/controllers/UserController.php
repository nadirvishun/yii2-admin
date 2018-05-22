<?php
/**
 * 仅用于测试
 */

namespace api\versions\v1\controllers;

use api\common\models\UserToken;
use Yii;
use api\common\controllers\AuthController;
use api\versions\v1\models\User;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\web\ServerErrorHttpException;

class UserController extends AuthController
{
    public $modelClass = 'api\versions\v1\models\User';
    //设置返回格式
    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];

    /**
     *  虽然需要验证登陆，但某个action不需要，所以需要重写此方法
     * @return array|mixed
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        //增加要排除的action
        $behaviors['authenticator']['except'] = [
//            'index',
            'create'
        ];
        return $behaviors;
    }

    /**
     * 重写action
     * @return array
     */
    public function actions()
    {
        $actions = parent::actions();
        //删除不需要的独立action
        unset($actions['index'], $actions['create'], $actions['delete']);
        return $actions;
    }

    /**
     * 重写自己的index方法
     */
    public function actionIndex()
    {
        $userModel = $this->modelClass;
        $query = $userModel::find();
        $provider = new ActiveDataProvider([
            'query' => $query,
//            'pagination' => false
        ]);
        return $provider;
    }

    /**
     * 自定义action，且自己组装数据
     */
    public function actionIndex1()
    {
        //自己组装增加相关的code等
        $userModel = $this->modelClass;
        $query = $userModel::find();
        $provider = new ActiveDataProvider([
            'query' => $query,
        ]);
        //获取数据
        $model = $provider->getModels();
        //获取分页信息
        $pagination = $provider->getPagination();
        $meta = [
            'totalCount' => $pagination->totalCount,
            'pageCount' => $pagination->getPageCount(),
            'currentPage' => $pagination->getPage() + 1,
            'perPage' => $pagination->getPageSize(),
        ];
        //获取上下链接
        $link = $pagination->getLinks(true);
        //自定义的参数和items,_link等参数平级
        $code = 400;
        $msg = '提示';
        //组装展示
        return [
            'code' => $code,
            'msg' => $msg,
            'items' => $model,
            '_link' => $link,
            '_meta' => $meta
        ];
    }

    /**
     * 用户注册
     */
    public function actionCreate()
    {
        $model = new $this->modelClass;
        $model->scenario = 'create';
        if ($model->load(Yii::$app->request->post(), '') && $model->save()) {
            //当创建完成后，创建accessToken相关数据
            $response = Yii::$app->getResponse();
            $response->setStatusCode(201);
            //todo,是否需要返回access_token，还是再登陆一次
            //取最近的一次
            $userTokenInfo = UserToken::find()
                ->select('access_token,refresh_token')
                ->where(['user_id' => $model->id])
                ->orderBy(['created_at' => SORT_DESC])
                ->one();
            $returnData = [
                'username' => $model->username,
                'access_token' => $userTokenInfo->access_token,
                'refresh_token' => $userTokenInfo->refresh_token
            ];
            return $returnData;
//            $id = implode(',', array_values($model->getPrimaryKey(true)));
//            $response->getHeaders()->set('Location', Url::toRoute(['view', 'id' => $id], true));
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }
        return $model;
    }
}