<?php

namespace api\versions\v1\models;

use Yii;
use yii\db\ActiveRecord;

class User extends ActiveRecord
{
    const STATUS_FORBID = 0;//账户禁止
    const STATUS_ACTIVE = 1;//账户正常
    const SEX_SECRET = 0;//性别保密
    const SEX_MAN = 1;//性别男
    const SEX_WOMAN = 2;//性别女
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
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
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('user', 'ID'),
            'username' => Yii::t('user', 'Username'),
            'auth_key' => Yii::t('user', 'Auth Key'),
            'password_hash' => Yii::t('user', 'Password Hash'),
            'passwordRepeat' => Yii::t('user', 'Password Repeat'),//增加确认密码
            'password_reset_token' => Yii::t('user', 'Password Reset Token'),
            'email' => Yii::t('user', 'Email'),
            'mobile' => Yii::t('user', 'Mobile'),
            'avatar' => Yii::t('user', 'Avatar'),
            'sex' => Yii::t('user', 'Sex'),
            'last_login_ip' => Yii::t('user', 'Last Login Ip'),
            'last_login_time' => Yii::t('user', 'Last Login Time'),
            'status' => Yii::t('user', 'Status'),
            'created_at' => Yii::t('user', 'Created At'),
            'updated_at' => Yii::t('user', 'Updated At'),
        ];
    }
}