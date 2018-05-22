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
 * @property integer $user_id
 * @property string $access_token
 * @property integer $access_expires
 * @property integer $client_type
 * @property string $refresh_token
 * @property integer $refresh_expires
 * @property integer $created_at
 * @property integer $updated_at
 */
class UserToken extends ActiveRecord implements IdentityInterface
{
    /**
     * access_token过期时间 10天，待定10 * 24 * 3600
     */
    CONST ACCESS_EXPIRES = 864000;
    /**
     * refresh_token过期时间 180天,待定180 * 24 * 3600
     */
    CONST REFRESH_EXPIRES = 15552000;

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
        $userTokenInfo = static::findOne(['access_token' => $token]);
        //先查找user_token表中是否存在,且是否超时
        $userId = $userTokenInfo->user_id;
        if (!$userId && ((time() - $userTokenInfo->updated_at) > $userTokenInfo->access_expires)) {
            return false;
        }
        //如果存在，则继续查找用户是否存在
        if (!User::findIdentity($userId)) {
            return false;
        }
        return $userTokenInfo;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
//        throw new NotSupportedException('"getId" is not implemented.');
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

    /**
     * 生成access_token
     * @throws \yii\base\Exception
     */
    public function generateAccessToken()
    {
        $this->access_token = Yii::$app->security->generateRandomString();
    }

    /**
     * 生成refresh_token
     * @throws \yii\base\Exception
     */
    public function generateRefreshToken()
    {
        $this->refresh_token = Yii::$app->security->generateRandomString();
    }
}
