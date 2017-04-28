<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;

/**
 * Backend中的后台基类
 * 所有后台contoller都要继承此类
 */
class BaseController extends Controller
{
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
