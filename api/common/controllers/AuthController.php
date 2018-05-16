<?php

namespace api\common\controllers;

use yii\filters\auth\CompositeAuth;
use yii\filters\auth\QueryParamAuth;

/**
 * API中的需要登陆权限的后台基类
 * 如果需要验证登陆，请继承此类
 */
class AuthController extends BaseController
{
    /**
     * 开启相关验证
     * @return array|mixed
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => CompositeAuth::className(),
            'authMethods' => [
//                HttpBasicAuth::className(),
//                HttpBearerAuth::className(),
                QueryParamAuth::className(),
            ],
        ];
        return $behaviors;
    }
}
