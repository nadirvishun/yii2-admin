<?php

namespace api\versions\v1\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\Url;
use yii\web\Linkable;
use yii\web\Link;

class User extends \api\common\models\User implements Linkable
{
    const CREATE_ACCESS_TOKEN = 'create_access_token';

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $this->on(self::CREATE_ACCESS_TOKEN, ['\api\versions\v1\UserToken', 'createAccessToken']);
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
            'email',
            'mobile',
        ];
    }

    /**
     * 要展示的链接
     * @return array
     */
    public function getLinks()
    {
        return [
            Link::REL_SELF => Url::to(['user/view', 'id' => $this->id], true),
            'edit' => Url::to(['user/view', 'id' => $this->id], true),
            'profile' => Url::to(['user/profile/view', 'id' => $this->id], true),
            'index' => Url::to(['users'], true),
        ];
    }

    /**
     * 验证规则
     * @return array
     */
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_FORBID]],
            [['username'], 'match', 'pattern' => '/^[a-zA-Z]\w*$/i'],
            [['username'], 'unique'],
            //由于下三个都是唯一的所以必须填写，但是由于console中的初始化后台用户时越简单越好，所以设定场景
            [['username', 'email', 'mobile'], 'required', 'on' => ['create', 'update', 'modify']],
            //创建时必须填写确认密码
            [['password_hash', 'passwordRepeat'], 'required', 'on' => 'create'],
            //在创建和修改自身时需要填写确认密码
            ['passwordRepeat', 'compare', 'compareAttribute' => 'password_hash', 'on' => ['create', 'modify']],
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
            $this->setPassword($this->password_hash);
        }
        return parent::beforeSave($insert);
    }
}