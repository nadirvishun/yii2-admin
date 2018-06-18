<?php
namespace backend\models;

use Yii;
use yii\base\Model;

/**
 * Password reset request form
 */
class PasswordResetRequestForm extends Model
{
    public $email;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => '\backend\models\Admin',
                'filter' => ['status' => Admin::STATUS_ACTIVE],
                'message' => Yii::t('site', 'There is no user with this email address.')
            ],
        ];
    }

    /**
     * 属性标签
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'email' => Yii::t('site', 'email')
        ];
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return bool whether the email was send
     */
    public function sendEmail()
    {
        /* @var $admin Admin */
        $admin = Admin::findOne([
            'status' => Admin::STATUS_ACTIVE,
            'email' => $this->email,
        ]);

        if (!$admin) {
            return false;
        }

        if (!Admin::isPasswordResetTokenValid($admin->password_reset_token)) {
            $admin->generatePasswordResetToken();
            if (!$admin->save()) {
                return false;
            }
        }

        //由于如果配置出错，内部类会抛出异常，而不是返回false，所以这里捕捉记录日至
        $result = true;
        try {
            Yii::$app
                ->mailer
                ->compose(
                    ['html' => 'passwordResetToken-html', 'text' => 'passwordResetToken-text'],
                    ['user' => $admin]
                )
                ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
                ->setTo($this->email)
                ->setSubject(Yii::t('site', 'Password reset for ') . Yii::$app->name)
                ->send();
        } catch (\Exception $e) {
            $result = false;
            Yii::error($e->getMessage());
        }
        return $result;
    }
}
