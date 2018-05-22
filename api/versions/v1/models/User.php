<?php

namespace api\versions\v1\models;

use api\common\models\UserToken;
use yii\helpers\Url;
use yii\web\Linkable;
use yii\web\Link;

class User extends \api\common\models\User implements Linkable
{
    public $password;

    /**
     * insert时开启事务，方便同时改动user_token表
     * @return array
     */
    public function transactions()
    {
        return ['create' => self::OP_INSERT];
    }

    /**
     * 要展示的字段
     * @return array
     */
    public function fields()
    {
        return [
            'id',
            'username',
//            'email',
//            'mobile',
        ];
    }

    /**
     * 要展示的链接
     * @return array
     */
    public function getLinks()
    {
        return [
            Link::REL_SELF => Url::to('', true),
            'index' => Url::to(['user/index'], true),
            'create' => Url::to(['user/index'], true),
            'edit' => Url::to(['user/view', 'id' => $this->id], true),
            'view' => Url::to(['user/view', 'id' => $this->id], true),
        ];
    }

    /**
     * 验证规则
     * @return array
     */
    public function rules()
    {
        return [
            ['reg_client_type', 'default', 'value' => self::CLIENT_WAP],
            ['reg_client_type', 'in', 'range' => [self::CLIENT_ANDROID, self::CLIENT_IOS, self::CLIENT_WAP]],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_FORBID]],
            [['username'], 'match', 'pattern' => '/^[a-zA-Z]\w*$/i'],
            [['username'], 'unique'],
            //mobile是否需要加入
            [['username', 'password'], 'required', 'on' => 'create'],
            ['email', 'email'],
            ['email', 'unique'],
            ['mobile', 'match', 'pattern' => '/^1(3|4|5|7|8)[0-9]\d{8}$/'],
            ['mobile', 'unique'],
            [['created_at', 'updated_at', 'last_login_time'], 'integer'],
            ['sex', 'in', 'range' => [self::SEX_SECRET, self::SEX_MAN, self::SEX_WOMAN]],
            [['auth_key', 'last_login_ip', 'password_hash'], 'safe'],
            [['avatar'], 'file', 'extensions' => 'png, jpg'],
        ];
    }

    /**
     * 存储前的动作
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        //如果是新增，则自动产生
        if ($this->isNewRecord) {
            $this->generateAuthKey();
            $this->generatePasswordResetToken();
            $this->setPassword($this->password);
        }
        return parent::beforeSave($insert);
    }

    /**
     * 存储后的动作
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        //同时增加user_token表中数据
        if ($insert) {
            $userToken = new UserToken();
            $userToken->generateAccessToken();
            $userToken->generateRefreshToken();
            $userToken->user_id = $this->id;
            $userToken->access_expires = UserToken::ACCESS_EXPIRES;
            $userToken->refresh_expires = UserToken::REFRESH_EXPIRES;
            $userToken->save();
        }
    }
}