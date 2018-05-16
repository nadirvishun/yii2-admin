<?php

namespace backend\models;

use kartik\widgets\Select2;
use kartik\widgets\SwitchInput;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * This is the model class for table "{{%setting}}".
 *
 * @property integer $id
 * @property integer $pid
 * @property string $name
 * @property string $alias
 * @property integer $type
 * @property string $value
 * @property string $extra
 * @property string $hint
 * @property integer $sort
 * @property integer $status
 * @property string $created_by
 * @property string $created_at
 * @property string $updated_by
 * @property string $updated_at
 */
class Setting extends \yii\db\ActiveRecord
{
    //状态
    const STATUS_HIDE = 0;//隐藏
    const STATUS_VISIBLE = 1;//显示
    //类型
    const TEXT = 1;//文本类型
    const PASSWORD = 2;//密码类型
    const SELECT = 3;//下拉菜单类型
    const RADIO = 4;//单选类型
    const CHECKBOX = 5;//多选类型
    const TEXTAREA = 6;//文本域类型
    const FILE = 7;//文件上传类型

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%setting}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            BlameableBehavior::className()
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pid', 'name', 'alias'], 'required'],
            [['pid', 'type', 'sort', 'status'], 'integer'],
            [['value'], 'string'],
            ['sort', 'default', 'value' => 0],
            ['type', 'default', 'value' => 1],
            //父ID有效性,当为0时不验证
            ['pid', 'exist', 'targetAttribute' => 'id', 'isEmpty' => function ($value) {
                return empty($value);
            }],
            //当更新时，父ID不能为自身或其下级节点
            ['pid', 'validatePid', 'on' => 'update'],
            //只支持两级
            ['pid', 'twoLevel'],
            [['name', 'alias'], 'string', 'max' => 64],
            [['extra'], 'string', 'max' => 255],
            [['hint'], 'string', 'max' => 100],
            [['alias'], 'unique'],
        ];
    }

    /**
     * 更新时验证选择的pid不能为本身及其下级节点
     */
    public function validatePid()
    {
        $childIds = static::getChildIds($this->id);
        if (in_array($this->pid, $childIds)) {
            $this->addError('pid', Yii::t('setting', 'Parent ID can not be itself or its subordinate node'));
        }
    }

    /**
     * 只支持两级的层级关系
     */
    public function twoLevel()
    {
        $grandpaInfo = static::findOne($this->pid);
        if (!empty($grandpaInfo)) {
            if ($grandpaInfo->pid != 0) {
                $this->addError('pid', Yii::t('setting', 'Only support two levels'));
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('setting', 'ID'),
            'pid' => Yii::t('setting', 'Pid'),
            'name' => Yii::t('setting', 'Name'),
            'alias' => Yii::t('setting', 'Alias'),
            'type' => Yii::t('setting', 'Type'),
            'value' => Yii::t('setting', 'Value'),
            'extra' => Yii::t('setting', 'Extra'),
            'hint' => Yii::t('setting', 'Hint'),
            'sort' => Yii::t('setting', 'Sort'),
            'status' => Yii::t('setting', 'Status'),
            'created_by' => Yii::t('setting', 'Created By'),
            'created_at' => Yii::t('setting', 'Created At'),
            'updated_by' => Yii::t('setting', 'Updated By'),
            'updated_at' => Yii::t('setting', 'Updated At'),
        ];
    }

    /**
     * 用while获取下级所有节点
     * @param integer|array $ids
     * @param bool $self 是否需要包含自身，默认包含
     * @return array
     */
    public static function getChildIds($ids, $self = true)
    {
        if (!is_array($ids)) {
            $ids = explode(',', $ids);
        }
        if ($self) {
            $childIds = $ids;
        } else {
            $childIds = [];
        }
        while (!empty($ids)) {
            $ids = static::find()
                ->select('id')
                ->where(['in', 'pid', $ids])
                ->asArray()
                ->column();
            $childIds = array_merge($childIds, $ids);
        }
        return $childIds;
    }

    /**
     *  获取下拉菜单列表或者某一名称
     * @param bool $key
     * @return array|mixed
     */
    public static function getStatusOptions($key = false)
    {
        $arr = [
            self::STATUS_HIDE => Yii::t('common', 'Hide'),
            self::STATUS_VISIBLE => Yii::t('common', 'Visible')
        ];
        return $key === false ? $arr : ArrayHelper::getValue($arr, $key, Yii::t('common', 'Unknown'));
    }

    /**
     *  获取类型
     * @param bool $key
     * @return array|mixed
     */
    public static function getTypeOptions($key = false)
    {
        //todo,暂时只支持这几种，后续的需要完善
        $arr = [
            self::TEXT => Yii::t('setting', 'text'),
            self::PASSWORD => Yii::t('setting', 'password'),
            self::SELECT => Yii::t('setting', 'select'),
            //todo,github上的switchIput widget 对此支持不好，暂时注释掉已提交issue： https://github.com/kartik-v/yii2-widget-switchinput/issues/29
//            self::RADIO => Yii::t('setting', 'radio'),
//            self::CHECKBOX => Yii::t('setting', 'checkbox'),
            self::TEXTAREA => Yii::t('setting', 'textarea'),
//            self::FILE => Yii::t('setting', 'file'),
        ];
        return $key === false ? $arr : ArrayHelper::getValue($arr, $key, Yii::t('common', 'Unknown'));
    }

    /**
     * 根据pid获取所归属的一级配置选项
     * @param $pid
     * @param bool|string|array $exceptAlias 不传递则不排除任何，如果传递则排除，可以string用逗号分隔，也可以数组
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getSettingList($pid = 0, $exceptAlias = false)
    {
        $query = static::find()
            ->where(['status' => self::STATUS_VISIBLE, 'pid' => $pid]);
        if ($exceptAlias !== false) {
            if (is_string($exceptAlias)) {
                $exceptAlias = explode(',', $exceptAlias);
            }
            $query->andWhere(['not in', 'alias', $exceptAlias]);
        }
        $list = $query->asArray()->all();
        return $list;
    }

    /**
     * 根据Alias获取对应的value值
     * @param $alias
     * @return
     */
    public static function getValueByAlias($alias)
    {
        //取出所有的配置参数，加入缓存,方便后续无需通过数据库即可调用
        //优先从缓存中取数据
        $cache = Yii::$app->cache;
        $setting = $cache->get('setting');
        if ($setting[$alias] == false) {
            $setting = static::find()
                ->select('value,alias')
                ->where(['status' => self::STATUS_VISIBLE])
                ->indexBy('alias')
                ->asArray()
                ->column();
            //添加缓存依赖，当最新的更新时间变更，则说明有数据更新
            $dependency = new \yii\caching\DbDependency(['sql' => 'SELECT max(updated_at) FROM ' . ' {{%setting}}']);
            //写入缓存
            $cache->set('setting', $setting, 0, $dependency);
        }
        return $setting[$alias];
    }

    /**
     * 根据不同的类型创建不同的input表单
     * 只能封装成通用的样式
     * @param integer $type
     * @param string $name 如果是多维的，需提前组装好
     * @param string $value
     * @param string $extra
     * @param array $options
     * @return string
     */
    public static function createInputTag($type, $name, $value, $extra = '', $options = [])
    {

        if (empty($options)) {
            $options = ['class' => 'form-control', 'id' => $name];
        }
        $tag = '';
        switch ($type) {
            case self::TEXT :
                $tag = Html::textInput($name, $value, $options);
                break;
            case self::PASSWORD :
                $tag = Html::passwordInput($name, $value, $options);
                break;
            case self::SELECT :
                $tag = Select2::widget([
                    'name' => $name,
                    'value' => $value,
                    'data' => static::parseExtra($extra),
//                    'options' => [
//                        'prompt' => Yii::t('common', 'Please Select...'),
//                        'encode' => false,
//                    ],
//                    'pluginOptions' => [
//                        'allowClear' => true
//                    ],
                ]);
                break;
            case  self::RADIO :
                $noValue=$value==1?0:1;
                $tag = SwitchInput::widget([
                    'name' => $name,
                    'value' => $value,
//                    'options' => ['uncheck' => $noValue],
                    'pluginOptions' => ['size' => 'small']
                ]);
//                $tag=Html::checkbox($name,$value,['uncheck' => 0]);

                break;
            case self::CHECKBOX :
                break;
            case self::TEXTAREA :
                $options['rows'] = 6;
                $tag = Html::textarea($name, $value, $options);
                break;
            case self::FILE :
                break;
            default:
                break;
        }
        return $tag;
    }

    /**
     * 解析为下拉菜单数组
     * 支持,;换行符，如果是关联数组则需要:分隔
     */
    public static function parseExtra($extra)
    {
        $array = preg_split('/[,;\r\n]+/', trim($extra, ",;\r\n"));
        if (strpos($extra, ':')) {
            $value = [];
            foreach ($array as $val) {
                list($k, $v) = explode(':', $val);
                $value[$k] = $v;
            }
        } else {
            $value = $array;
        }
        return $value;
    }
}
