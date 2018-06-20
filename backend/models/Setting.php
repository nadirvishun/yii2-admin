<?php

namespace backend\models;

use common\components\Tree;
use kartik\datetime\DateTimePicker;
use kartik\file\FileInput;
use kartik\widgets\Select2;
use kartik\widgets\SwitchInput;
use kucha\ueditor\UEditor;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\caching\DbDependency;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

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
    const RADIO_SWITCH = 7;//开关类型
    const RICKTEXT = 8;//富文本
    const DATE = 9;//日期选择
    const FILE = 10;//文件上传类型

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
        $arr = [
            self::TEXT => Yii::t('setting', 'text'),
            self::PASSWORD => Yii::t('setting', 'password'),
            self::SELECT => Yii::t('setting', 'select'),
            self::RADIO => Yii::t('setting', 'radio'),
            self::RADIO_SWITCH => Yii::t('setting', 'switch'),
            self::CHECKBOX => Yii::t('setting', 'checkbox'),
            self::TEXTAREA => Yii::t('setting', 'textarea'),
            self::RICKTEXT => Yii::t('setting', 'richtext'),
            self::DATE => Yii::t('setting', 'date'),
            self::FILE => Yii::t('setting', 'file'),
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
        $list = $query->orderBy(['sort' => SORT_DESC, 'id' => SORT_ASC])->asArray()->all();
        return $list;
    }

    /**
     * 获取分类下拉菜单选项
     */
    public static function getSettingTreeOptions()
    {
        $cache = Yii::$app->cache;
        //增加缓存获取
        $data = $cache->get('setting_tree_options');
        if ($data == false) {
            $list = static::find()->select('id,pid,name')
                ->where(['status' => self::STATUS_VISIBLE])//不显示隐藏的
                ->orderBy(['sort' => SORT_DESC, 'id' => SORT_ASC])
                ->asArray()
                ->all();
            //创建树实例
            $tree = new Tree(['icon' => '']);
            $rootOption = ['0' => Yii::t('setting', 'Root Tree')];//顶级显示
            $data = ArrayHelper::merge($rootOption, $tree->getTreeOptions($list));
            //写入缓存
            $dependency = new DbDependency(['sql' => 'SELECT max(updated_at) FROM ' . static::tableName()]);
            $cache->set('setting_tree_options', $data, 0, $dependency);
        }
        return $data;
    }

    /**
     * 根据Alias获取对应的value值
     * @param $alias
     * @return string
     */
    public static function getValueByAlias($alias)
    {
        //取出所有的配置参数，加入缓存,方便后续无需通过数据库即可调用
        //优先从缓存中取数据
        $cache = Yii::$app->cache;
        $setting = $cache->get('setting');
        if ($setting[$alias] == false) {
            $setting = static::find()
                ->select('value')
                ->where(['status' => self::STATUS_VISIBLE])
                ->indexBy('alias')
                ->asArray()
                ->column();
            //添加缓存依赖，当最新的更新时间变更，则说明有数据更新
            $dependency = new DbDependency(['sql' => 'SELECT max(updated_at) FROM ' . static::tableName()]);
            //写入缓存
            $cache->set('setting', $setting, 0, $dependency);
        }
        return $setting[$alias];
    }

    /**
     * 根据不同的类型创建不同的input表单
     * 只能封装成通用的样式
     * @param integer $type
     * @param string $alias
     * @param string $value
     * @param string $extra
     * @param array $options
     * @return string
     * @throws \Exception
     */
    public static function createInputTag($type, $alias, $value, $extra = '', $options = [])
    {
        $name = "Setting[$alias]";
        if (empty($options)) {
            $options = ['class' => 'form-control', 'id' => $name];
        }
        switch ($type) {
            case self::TEXT ://普通输入框
                $tag = Html::textInput($name, $value, $options);
                break;
            case self::PASSWORD ://密码输入框
                $tag = Html::passwordInput($name, $value, $options);
                break;
            case self::SELECT ://下拉菜单
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
            case  self::RADIO ://单选按钮
                $selection = $value;
                $items = static::parseExtra($extra);
                $tag = Html::radioList($name, $selection, $items, [
                    'separator' => '<br>',//换行
                    'item' => function ($index, $label, $name, $checked, $value) {
                        return Html::radio($name, $checked, [
                            'value' => $value,
                            'label' => Html::encode($label),
                            'labelOptions' => ['style' => 'margin-left:5px;font-weight:normal']
                        ]);
                    }
                ]);
                break;
            case self::CHECKBOX ://多选按钮
                $selection = json_decode($value, true);
                $items = static::parseExtra($extra);
                $tag = Html::checkboxList($name, $selection, $items, [
                    'separator' => '<br>',//换行
                    'item' => function ($index, $label, $name, $checked, $value) {
                        return Html::checkbox($name, $checked, [
                            'value' => $value,
                            'label' => Html::encode($label),
                            'labelOptions' => ['style' => 'margin-left:5px;font-weight:normal']
                        ]);
                    }
                ]);
                break;
            case  self::RADIO_SWITCH ://开关
                $tag = SwitchInput::widget([
                    'name' => $name,
                    'value' => $value,
                    'options' => ['uncheck' => 0, 'value' => 1],
                    'pluginOptions' => ['size' => 'small']
                ]);
                break;
            case self::TEXTAREA ://多行文本
                $options['rows'] = 6;
                $tag = Html::textarea($name, $value, $options);
                break;
            case self::RICKTEXT ://富文本
                $tag = UEditor::widget([
                    'id' => $name,
                    'name' => $name,
                    'value' => $value,
                    'clientOptions' => [
                        //上传地址，需修改为上方action一致，默认是upload，但和文件上传同一名字，所以修改为此
                        'serverUrl' => Url::to(['ueditorUpload']),
                        //编辑区域大小
                        'initialFrameHeight' => '200',
                        //定制菜单
                        'toolbars' => [
                            [
                                'fullscreen', 'source', 'undo', 'redo', '|',
                                'fontsize',
                                'bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'removeformat',
                                'formatmatch', 'autotypeset', 'blockquote', 'pasteplain', '|',
                                'forecolor', 'backcolor', '|',
                                'lineheight', '|',
                                'indent', '|'
                            ],
                            ['preview', 'simpleupload', 'insertimage', 'link', 'emotion', 'map', 'insertvideo', 'insertcode',]
                        ]
                    ]
                ]);
                break;
            case self::DATE ://日期时间
                $format = 'php:Y-m-d';
                $minView = 'month';
                //如果参数是datetime，则精确到分钟
                if ($extra == 'datetime') {
                    $format = 'php:Y-m-d H:i:s';
                    $minView = 'hour';
                }
                $tag = DateTimePicker::widget([
                    'name' => $name,
                    'value' => $value,
//                    'options' => ['placeholder' => Yii::t('common', 'Please Select...')],
                    'convertFormat' => true,
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => $format,
                        'todayHighlight' => true,
                        'minView' => $minView
                    ]
                ]);
                break;
            case self::FILE :
                //如果参数是multiple，则可以上传多图
                $multiple = false;
                if ($extra == 'multiple') {
                    $multiple = true;
                }
                //设置初始参数
                $valueArr = explode(',', $value);
                $initialPreviewConfig = [];
                foreach ($valueArr as $item) {
                    $initialPreviewConfig[] = [
                        'caption' => basename($item),
                        'url' => Url::to(['upload', 'action' => 'delete']),//ajax删除路径
                        'key' => $item
                    ];
                }
                $tag = Html::hiddenInput($name, $value);//隐藏字段，用于ajax提交后将保存图片路径传递过来提交
                $tag .= FileInput::widget([
                    'name' => $name,
                    'options' => ['multiple' => $multiple],
                    'pluginOptions' => [
                        'uploadUrl' => Url::to(['upload', 'action' => 'upload']),//ajax上传路径
                        'uploadExtraData' => [
                            'name' => $name,//表单名称,也可以在独立action中指定
                        ],
                        'showPreview' => true,
                        'showClose' => false,
                        'initialPreview' => empty($value) ? [] : $valueArr,
                        'initialPreviewConfig' => $initialPreviewConfig,
                        'initialPreviewAsData' => true,
                        'overwriteInitial' => $multiple ? false : true,//多文件不覆盖原有的，单文件覆盖
                    ],
                    'pluginEvents' => [
                        //批量上传按钮
                        'filebatchuploadcomplete' => "function (event, files, extra){
                        var arr=[];
                        $('.field-setting-".$alias." .kv-file-remove').each(function(){
                            var key=$(this).data('key');
                            if(key && arr.indexOf(key)=='-1'){
                                arr.push(key)
                            }
                        })
                        $('input[type=\'hidden\'][name=\'" . $name . "\']').val(arr.join(','));
                       }",
                        //单个点击上传完毕后给隐藏表单赋值
                        'fileuploaded' => "function (event,data){
                        var arr=[];
                        $('.field-setting-".$alias." .kv-file-remove').each(function(){
                            var key=$(this).data('key');
                            if(key && arr.indexOf(key)=='-1'){
                                arr.push(key)
                            }
                        })
                        $('input[type=\'hidden\'][name=\'" . $name . "\']').val(arr.join(','));
                       }",
                        //单个点击删除时清空隐藏表单(由于触发时，kv-file-remove还存在，所以需要去除本身)
                        'filedeleted' => "function (event,key,jqXHR,data){
                        var arr=[];
                        var self=key;
                        $('.field-setting-".$alias." .kv-file-remove').each(function(){
                            var key=$(this).data('key');
                            if(key && key!=self && arr.indexOf(key)=='-1'){
                                arr.push(key)
                            }
                        })
                        $('input[type=\'hidden\'][name=\'" . $name . "\']').val(arr.join(','));
                       }",
                    ]
                ]);
                break;
            default:
                $tag = '';
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

    /**
     * 获取所有提示信息
     * @param bool $type
     * @return array|mixed|string
     */
    public static function getPlaceholderOptions($type = false)
    {
        $arr = [
            self::SELECT => Yii::t('setting', "Example: \n\t value1:showName1 \n\t value2:showName2 \n\t ..."),
            self::RADIO => Yii::t('setting', "Example: \n\t value1:label1 \n\t value2:label2 \n\t ..."),
            self::CHECKBOX => Yii::t('setting', "Example: \n\t value1:label1 \n\t value2:label2 \n\t ..."),
            self::DATE => Yii::t('setting', "If you want show minutes ,please input:datetime"),
            self::FILE => Yii::t('setting', 'If you want upload multiple file ,please input:multiple'),
            self::RADIO_SWITCH => Yii::t('setting', 'No necessary input!'),
            self::TEXT => Yii::t('setting', 'No necessary input!'),
            self::PASSWORD => Yii::t('setting', 'No necessary input!'),
            self::TEXTAREA => Yii::t('setting', 'No necessary input!'),
            self::RICKTEXT => Yii::t('setting', 'No necessary input!'),
        ];
        if ($type === false) {
            return $arr;
        }
        return isset($arr[$type]) ? $arr[$type] : Yii::t('setting', 'No necessary input!');
    }
}
