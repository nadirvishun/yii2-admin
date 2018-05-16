<?php

namespace api\common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 */
class UserToken extends ActiveRecord implements IdentityInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_token}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => Yii::t('user_token', 'User ID'),
            'access_token' => Yii::t('user_token', 'Access Token'),
            'access_expires' => Yii::t('user_token', 'Access Expires'),
            'client_type' => Yii::t('user_token', 'Client Type'),
            'refresh_token' => Yii::t('user_token', 'Refresh Token'),
            'refresh_expires' => Yii::t('user_token', 'Refresh Expires'),
            'created_at' => Yii::t('user_token', 'Created At'),
            'updated_at' => Yii::t('user_token', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
//        return static::findOne(['id' => $id]);
        throw new NotSupportedException('"findIdentity" is not implemented.');
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        //先查找user_token表中是否存在
        $userId = static::find()->select('user_id')->where(['access_token' => $token])->scalar();
        if (!$userId) {
            return false;
        }
        //如果存在，则继续查找用户是否存在
        return User::findIdentity($userId);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
//        return $this->getPrimaryKey();
        throw new NotSupportedException('"getId" is not implemented.');
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
//        return $this->auth_key;
        throw new NotSupportedException('"getAuthKey" is not implemented.');
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
//        return $this->getAuthKey() === $authKey;
        throw new NotSupportedException('"validateAuthKey" is not implemented.');
    }

}
