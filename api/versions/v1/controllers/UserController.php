<?php
/**
 * 仅用于测试
 */

namespace api\versions\v1\controllers;

use api\common\controllers\AuthController;
use yii\data\ActiveDataProvider;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\QueryParamAuth;

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
            'index'
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
        unset($actions['index']);
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
}