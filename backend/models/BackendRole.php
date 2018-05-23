<?php

namespace backend\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\rbac\Item;

/**
 * This is the model class for table "{{%backend_auth_item}}".
 *
 * @property string $name
 * @property integer $type
 * @property string $description
 * @property string $rule_name
 * @property string $data
 * @property string $created_at
 * @property string $updated_at
 */
class BackendRole extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%backend_auth_item}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
//            TimestampBehavior::className(),
//            BlameableBehavior::className()
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            ['type', 'default', 'value' => Item::TYPE_ROLE],
            [['type', 'created_at', 'updated_at'], 'integer'],
            [['description', 'data'], 'string'],
            [['name', 'rule_name'], 'string', 'max' => 64],
            [['name'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('backend_role', 'Name'),
            'type' => Yii::t('backend_role', 'Type'),
            'description' => Yii::t('backend_role', 'Description'),
            'rule_name' => Yii::t('backend_role', 'Rule Name'),
            'data' => Yii::t('backend_role', 'Data'),
            'created_at' => Yii::t('backend_role', 'Created At'),
            'updated_at' => Yii::t('backend_role', 'Updated At'),
        ];
    }
}
