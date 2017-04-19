<?php

namespace backend\controllers;

use Yii;
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
        $this->backMenuSearch = Yii::$app->request->post('backend-menu-search', '');
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

}
