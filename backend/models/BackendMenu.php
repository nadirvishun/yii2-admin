<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%backend_menu}}".
 *
 * @property string $id
 * @property string $pid
 * @property string $name
 * @property string $url
 * @property string $url_param
 * @property string $icon
 * @property integer $status
 * @property integer $sort
 * @property string $created_by
 * @property string $created_at
 * @property string $updated_by
 * @property string $updated_at
 */
class BackendMenu extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%backend_menu}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pid', 'name', 'url', 'url_param', 'icon', 'sort', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'required'],
            [['pid', 'status', 'sort', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
            [['name', 'url', 'icon'], 'string', 'max' => 64],
            [['url_param'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('backend_menu', 'ID'),
            'pid' => Yii::t('backend_menu', 'Pid'),
            'name' => Yii::t('backend_menu', 'Name'),
            'url' => Yii::t('backend_menu', 'Url'),
            'url_param' => Yii::t('backend_menu', 'Url Param'),
            'icon' => Yii::t('backend_menu', 'Icon'),
            'status' => Yii::t('backend_menu', 'Status'),
            'sort' => Yii::t('backend_menu', 'Sort'),
            'created_by' => Yii::t('backend_menu', 'Created By'),
            'created_at' => Yii::t('backend_menu', 'Created At'),
            'updated_by' => Yii::t('backend_menu', 'Updated By'),
            'updated_at' => Yii::t('backend_menu', 'Updated At'),
        ];
    }
}
