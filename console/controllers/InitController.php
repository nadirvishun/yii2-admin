<?php

namespace console\controllers;

use backend\models\Admin;

/**
 * 添加初始的后台用户
 */
class InitController extends \yii\console\Controller
{
    /**
     * Create init admin
     * 在控制台中运行命令 yii init/admin
     */
    public function actionAdmin()
    {
        $adminInfo = Admin::findOne(['status' => Admin::STATUS_ACTIVE]);
        if (!empty($adminInfo)) {//只能创建1次
            echo 'Already has admin account!';
            return 1;
        }
        echo "Create init admin ...\n";                  // 提示当前操作
        $model = new Admin();
        $username = $this->prompt('Admin Name:', ['default' => 'admin']);        // 接收用户名
//        $email = $this->prompt('Email:');               // 接收Email
        $password = $this->prompt('Password:');         // 接收密码
        // 创建一个新用户
        $model->username = $username;                   // 完成赋值
//        $model->email = $email;
        $model->password_hash = $password;              //通过beforeSave来加密
        if (!$model->save())                            // 保存新的用户
        {
            foreach ($model->getErrors() as $error)     // 如果保存失败，说明有错误，那就输出错误信息。
            {
                foreach ($error as $e) {
                    echo "$e\n";
                }
            }
            return 1;                                   // 命令行返回1表示有异常
        }
        echo 'Create successfully!';
        return 0;                                       // 返回0表示一切OK
    }
}