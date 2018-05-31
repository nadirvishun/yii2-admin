<?php

namespace backend\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/**
 * This is the model class for table "{{%admin_log}}".
 *
 * @property integer $id
 * @property string $title
 * @property string $admin_id
 * @property integer $type
 * @property string $model
 * @property string $controller
 * @property string $action
 * @property string $url_param
 * @property string $description
 * @property string $ip
 * @property string $created_at
 */
class AdminLog extends \yii\db\ActiveRecord
{
    /**
     * 操作类型
     */
    const TYPE_INSERT = 1;
    const TYPE_UPDATE = 2;
    const TYPE_DELETE = 3;
    /**
     * 删除类别
     */
    const DELETE_MONTH = 1;
    const DELETE_WEEK = 2;
    const DELETE_ALL = 3;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%admin_log}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'model', 'controller', 'action', 'ip', 'created_at'], 'required'],
            [['admin_id', 'type', 'created_at'], 'integer'],
            [['description'], 'string'],
            [['title', 'model', 'controller', 'action', 'url_param'], 'string', 'max' => 255],
            [['ip'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('admin_log', 'ID'),
            'title' => Yii::t('admin_log', 'Title'),
            'admin_id' => Yii::t('admin_log', 'Admin ID'),
            'type' => Yii::t('admin_log', 'Type'),
            'model' => Yii::t('admin_log', 'Model'),
            'controller' => Yii::t('admin_log', 'Controller'),
            'action' => Yii::t('admin_log', 'Action'),
            'url_param' => Yii::t('admin_log', 'Url Param'),
            'description' => Yii::t('admin_log', 'Description'),
            'ip' => Yii::t('admin_log', 'Ip'),
            'created_at' => Yii::t('admin_log', 'Created At'),
        ];
    }

    /**
     * 写入触发的日志事件
     * @param $event
     */
    public static function eventInsert($event)
    {
        //模型名称
        $modelName = get_class($event->sender);
        //如果是本身写入，则不做记录
        if ($modelName::tableName() != self::tableName()) {
            // 详细
            $arr = [];
            foreach ($event->sender as $key => $value) {
                $arr[$key] = $value;
            }
            $description = json_encode($arr);
            static::saveAdminLog($modelName, self::TYPE_INSERT, $description);
        }
    }

    /**
     * 更新触发的日志事件
     * @param $event
     */
    public static function eventUpdate($event)
    {
        //只有有变动才记录
        if (!empty($event->changedAttributes)) {
            // 详细
            $arr['changedAttributes'] = $event->changedAttributes;
            $arr['oldAttributes'] = [];
            foreach ($event->sender as $key => $value) {
                $arr['oldAttributes'][$key] = $value;
            }
            $description = json_encode($arr);
            //模型名称
            $modelName = get_class($event->sender);
            $modelBaseName = StringHelper::basename($modelName);//去除命名空间后
            //管理员登陆修改标题名称，而不是统一的更新xxx
            $controller = Yii::$app->controller->getUniqueId();
            $action = Yii::$app->controller->action->id;
            if ($controller == 'site' && $action == 'login') {
                $title = Yii::t('site', 'Admin login');
            } else {
                $title = Yii::t('common', 'update') . Yii::t(Inflector::camel2id($modelBaseName, '_'), Inflector::pluralize(Inflector::camel2words($modelBaseName)));
            }
            static::saveAdminLog($modelName, self::TYPE_UPDATE, $description, $title);
        }
    }

    /**
     * 删除触发的日志事件
     * @param $event
     */
    public static function eventDelete($event)
    {
        // 详细
        $arr = [];
        foreach ($event->sender as $key => $value) {
            $arr[$key] = $value;
        }
        $description = json_encode($arr);
        //模型名称
        $modelName = get_class($event->sender);
        static::saveAdminLog($modelName, self::TYPE_DELETE, $description);
    }

    /**
     * 保存日志
     * @param $modelName
     * @param $type
     * @param string $description
     * @param string $title
     */
    public static function saveAdminLog($modelName, $type, $description = '', $title = '')
    {
        //模型名称
        $modelBaseName = StringHelper::basename($modelName);//去除命名空间后
        //如果不传递title，也可以从语言文件中获取
        if (empty($title)) {
            // title需要保证gii自动生成相关语言文件不要改动，会自动寻找语言文件，转为中文
            $title =static::getTypeOptions($type). Yii::t(Inflector::camel2id($modelBaseName, '_'), Inflector::pluralize(Inflector::camel2words($modelBaseName)));
        }
        // 保存
        $data = [
            'title' => $title,
            'admin_id' => Yii::$app->user->id,
            'type' => $type,
            'model' => $modelName,
            'controller' => Yii::$app->controller->getUniqueId(),
            'action' => Yii::$app->controller->action->id,
            'url_param' => urldecode(Yii::$app->request->queryString),
            'description' => $description,
            'ip' => Yii::$app->request->userIP,
            'created_at' => time()
        ];
        $model = new static();
        $model->setAttributes($data);
        if (!$model->save()) {
            //如果失败，写入框架日志
            Yii::error('Controller:' . $data['controller'] . ',Action:' . $data['action'] . ',Query:' . $data['url_param'] . '.add admin log error');
        };
    }

    /**
     * 获取操作类别
     * @param bool $key
     * @return array|mixed
     */
    public static function getTypeOptions($key = false)
    {
        $arr = [
            self::TYPE_INSERT => Yii::t('common', 'create'),
            self::TYPE_UPDATE => Yii::t('common', 'update'),
            self::TYPE_DELETE => Yii::t('common', 'delete')
        ];
        return $key === false ? $arr : ArrayHelper::getValue($arr, $key, Yii::t('common', 'Unknown'));
    }

    /**
     * 删除操作分类
     * @param bool $key
     * @return array|mixed
     */
    public static function getDeleteOptions($key = false)
    {
        $arr = [
            self::DELETE_MONTH => Yii::t('admin_log', 'Delete month ago'),
            self::DELETE_WEEK => Yii::t('admin_log', 'Delete week ago'),
            self::DELETE_ALL => Yii::t('admin_log', 'Delete all')
        ];
        return $key === false ? $arr : ArrayHelper::getValue($arr, $key, Yii::t('common', 'Unknown'));
    }
}
