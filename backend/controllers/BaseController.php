<?php

namespace backend\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;

/**
 * Backend中的后台基类
 * 所有后台contoller都要继承此类
 */
class BaseController extends Controller
{
    /**
     * 左侧菜单搜索，给layout传值，方便view中调用
     * 需注意后续controller中不可出现同名成员变量
     * @var
     */
    public $backMenuSearch;

    /**
     * 左侧菜单中检索
     * 需注意后续参数中不可出现同名POST参数
     */
    public function init()
    {
        parent::init();
        //左侧菜单检索赋值，方便显示检索的内容
        $this->backMenuSearch = Yii::$app->request->get('backend-menu-search', '');
    }

    /**
     * 登录和权限判定
     * @param $action
     * @return bool|\yii\web\Response
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action)
    {
        parent::beforeAction($action);

        //判定是否登录
        $permission = $action->getUniqueId();
        if (!in_array($permission, $this->noLoginActions())) {
            if (Yii::$app->user->isGuest) {
                $this->redirect(Yii::$app->user->loginUrl);
                return false;
            }
        }

        //判定是否有权限
        //如果是超级管理员，则拥有全部权限
        if (Yii::$app->user->id == Yii::$app->params['super_admin_id']) {
            return true;
        }
        //如果是其它管理员，则需要判定
        if (!in_array($permission, $this->noAuthActions())) {
            if (!Yii::$app->user->can($permission)) {
                $url = isset(Yii::$app->request->referrer) ? Yii::$app->request->referrer : Yii::$app->homeUrl;
                $this->redirectError($url, Yii::t('common', 'No permissions to operate this!'));
                return false;
            }
        }
        return true;
    }

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
            ]
        ];
    }

    /**
     * 不需要登陆判定的action，后续可在此种增加，或者继承此方法修改，但最好还是写在此处一目了然
     */
    protected function noLoginActions()
    {
        $actions = [
            'site/login',//登陆
            'site/captcha',//验证码
            'site/request-password-reset',//密码重置请求
            'site/reset-password',//密码重置
        ];
        return $actions;
    }

    /**
     * 不需要权限判定的action，后续可在此种增加，或者继承此方法修改，但最好还是写在此处一目了然
     */
    protected function noAuthActions()
    {
        //无需登录判定的一般也不需要相关的权限
        $noLoginActions = $this->noLoginActions();
        $actions = [
            'site/index',//首页
            'site/logout',//退出登录
            'site/search',//左侧菜单检索
            'admin/modify',//管理员修改自身信息
        ];
        return array_merge($noLoginActions, $actions);
    }

    /**
     * 成功跳转
     * 写入flash消息，并跳转
     * @param string|array $url
     * @param null $msg
     * @return \yii\web\Response
     */
    public function redirectSuccess($url, $msg = null)
    {
        if ($msg === null) {
            $msg = Yii::t('common', 'Create Success');
        }
        $session = Yii::$app->session;
        $session->setFlash('success', $msg);
        return $this->redirect($url);
    }

    /**
     * 只写入正确的消息而不跳转
     * @param null $msg
     */
    public function setSuccessFlash($msg = null)
    {
        if ($msg === null) {
            $msg = Yii::t('common', 'Create Success');
        }
        $session = Yii::$app->session;
        $session->setFlash('success', $msg);
    }

    /**
     * 失败跳转
     * 写入flash消息，并跳转
     * @param string|array $url
     * @param null $msg
     * @return \yii\web\Response
     */
    public function redirectError($url, $msg = null)
    {
        if ($msg === null) {
            $msg = Yii::t('common', 'Invalid Parameter');
        }
        $session = Yii::$app->session;
        $session->setFlash('error', $msg);
        return $this->redirect($url);
    }

    /**
     * 只写入失败的消息而不跳转
     * @param null $msg
     */
    public function setErrorFlash($msg = null)
    {
        if ($msg === null) {
            $msg = Yii::t('common', 'Invalid Parameter');
        }
        $session = Yii::$app->session;
        $session->setFlash('error', $msg);
    }

    /**
     * 纪录上一个链接ulr
     * @param $name
     */
    public function rememberReferrerUrl($name)
    {
        $session = Yii::$app->session;
        $session->setFlash($name, Yii::$app->request->referrer);
    }

    /**
     * 获取存储的上一个链接
     * @param $name
     * @param array $defaultUrl 如果没有获取到则取此默认值
     * @param bool $delete 默认读取完毕删除
     * @return mixed
     */
    public function getReferrerUrl($name, $defaultUrl = ['index'], $delete = true)
    {
        $session = Yii::$app->session;
        $url = $session->getFlash($name, $defaultUrl, $delete);
        return $url;
    }
}
