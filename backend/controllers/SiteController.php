<?php

namespace backend\controllers;

use backend\models\Admin;
use backend\models\PasswordResetRequestForm;
use backend\models\ResetPasswordForm;
use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\filters\VerbFilter;
use backend\models\LoginForm;

/**
 * Site controller
 */
class SiteController extends BaseController
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
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                'maxLength' => 5, //最大显示个数
                'minLength' => 5,//最少显示个数
                'height' => 35,//高度
                'foreColor' => 0x3C8DBC,//字体颜色
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        //后台首页展示的信息
        $system = [
            'yii_version' => Yii::getVersion(),//Yii2框架版本
            'operating_system' => php_uname('s') . ' ' . php_uname('r'),//操作系统
            'php_version' => PHP_VERSION,//php版本
            'web_environment' => $_SERVER["SERVER_SOFTWARE"],//运行环境
            'php_sapi' => PHP_SAPI,//运行方式
            'mysql_version' => Yii::$app->db->pdo->getAttribute(\PDO::ATTR_SERVER_VERSION),//mysql版本
            'upload_max_filesize' => ini_get('upload_max_filesize'),//上传限制
            'max_execution_time' => ini_get('max_execution_time'),//执行时间
        ];
        //转为语言文件
        $systemAttr = [];
        foreach (array_keys($system) as $key => $value) {
            $systemAttr[$key]['attribute'] = $value;
            $systemAttr[$key]['label'] = Yii::t('site', $value);
            $systemAttr[$key]['captionOptions'] = ['class' => 'c-md-3'];
        }
        //开发信息
        $developer = [
            'team' => '那时花开',
            'manager' => 'vishun',
            'qq' => '68618704',
        ];
        //转为语言文件
        $developerAttr = [];
        foreach (array_keys($developer) as $key => $value) {
            $developerAttr[$key]['attribute'] = $value;
            $developerAttr[$key]['label'] = Yii::t('site', $value);
            $developerAttr[$key]['captionOptions'] = ['class' => 'c-md-3'];
        }
        return $this->render('index', [
            'system' => $system,
            'systemAttr' => $systemAttr,
            'developer' => $developer,
            'developerAttr' => $developerAttr,
        ]);
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        $this->layout = '/main-login';
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            //登陆成功后修改登录时间和登录IP，也可以在在配置文件用on来处理，但太不美观了
            $admin = Admin::findOne(Yii::$app->user->id);
            $admin->last_login_time = time();
            $admin->last_login_ip = Yii::$app->request->userIP;
            $admin->save();
            //跳转上个页面
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $this->layout = '/main-login';
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                return $this->redirectSuccess(Yii::$app->getHomeUrl(), Yii::t('site', 'Check your email for further instructions.'));
            } else {
                $model->addError('email', Yii::t('site', 'Sorry, we are unable to reset password for the provided email address.'));
            }
        }
        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        $this->layout = '/main-login';
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            return $this->redirectSuccess(Yii::$app->getHomeUrl(), Yii::t('site', 'New password saved.'));
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    /**
     * 后台左侧菜单检索
     * 其实最好的方法是前台用js来实现检索，自己js不好，不费劲了。
     * @return \yii\web\Response
     */
    public function actionSearch()
    {
        $backMenuSearch = Yii::$app->request->post('backend-menu-search', '');
        $referrer = Yii::$app->request->referrer;//请求前的url
        $path = parse_url($referrer, PHP_URL_PATH);//路径，类似admin/index
        parse_str(parse_url($referrer, PHP_URL_QUERY), $queryArr);//将参数转为数组
        if (empty($backMenuSearch)) {
            //如果这次没有传值，则判定url中是否有检索，有则去掉
            if (isset($queryArr['backend-menu-search'])) {
                unset($queryArr['backend-menu-search']);
            }
        } else {
            //如果有传值，直接让后面的覆盖掉前面的
            $queryArr = array_merge($queryArr, ['backend-menu-search' => $backMenuSearch]);
        }
        //组装成redirect需要的参数
        $url = array_merge([$path], $queryArr);
        //跳转刷新
        return $this->redirect($url);
    }

}
