<?php

namespace backend\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\caching\TagDependency;
use yii\helpers\ArrayHelper;

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
    const STATUS_HIDE = 0;//隐藏
    const STATUS_VISIBLE = 1;//显示

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
            [['pid', 'name', 'url'], 'required'],
            ['status', 'default', 'value' => self::STATUS_VISIBLE],
            ['status', 'in', 'range' => [self::STATUS_VISIBLE, self::STATUS_HIDE]],
            ['sort', 'default', 'value' => 0],
            [['pid', 'sort', 'status'], 'integer'],
            [['name', 'url', 'icon'], 'string', 'max' => 64],
            [['url'], 'unique'],//url不能相同，因为和权限挂钩了
            //父ID有效性,当为0时不验证
            ['pid', 'exist', 'targetAttribute' => 'id', 'isEmpty' => function ($value) {
                return empty($value);
            }],
            //当更新时，父ID不能为自身或其下级节点
            ['pid', 'validatePid', 'on' => 'update'],
            [['url_param'], 'string', 'max' => 255],
            [['created_by', 'created_at', 'updated_by', 'updated_at'], 'safe']
        ];
    }

    /**
     * 更新时验证选择的pid不能为本身及其下级节点
     */
    public function validatePid()
    {
        $childIds = static::getChildIds($this->id);
        if (in_array($this->pid, $childIds)) {
            $this->addError('pid', Yii::t('backend_menu', 'Parent ID can not be itself or its subordinate node'));
        }
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
     * 另一种递归获取下级所有的节点
     * @param $ids
     * @param bool $self
     * @return array
     */
    public static function getChildIds2($ids, $self = true)
    {
        if (!is_array($ids)) {
            $ids = explode(',', $ids);
        }
        if ($self == true) {
            $selfId = $ids;
        }
        $childIds = static::find()
            ->select('id')
            ->where(['in', 'pid', $ids])
            ->asArray()
            ->column();
        if (!empty($childIds)) {
            $tmpIds = static::getChildIds2($childIds, false);//递归时设置$self为false，不再重复计算
            $childIds = isset($selfId) ? array_merge($selfId, $childIds, $tmpIds) : array_merge($childIds, $tmpIds);
        } else {
            return isset($selfId) ? $selfId : [];
        }
        return $childIds;
    }

    /**
     * 左侧菜单显示
     * 按照dmstr\widgets\Menu所要求格式组装
     * 由于需要实时检索，所以无法加入缓存
     * @param string $search
     * @return array
     */
    public static function getMenus($search = '')
    {
        //获取当前角色的权限
        $userId = Yii::$app->user->id;
        //开启缓存来提升加载速度
        $cache = Yii::$app->cache;
        //获取缓存的名称
        $auth = Yii::$app->authManager;
        if ($userId != Yii::$app->params['super_admin_id']) {
            //获取此管理员对应角色
            $rolesArr = $auth->getRolesByUser($userId);
            $roles = array_keys($rolesArr);
        } else {
            //超级管理员缓存tag
            $roles = ['super_admin'];
        }
        //按照用户具有的角色来命名
        $tree = $cache->get('menus_role_' . implode('_', $roles));
        //如果没有缓存或者是在搜索
        if ($tree == false || !empty($search)) {
            $list = static::find()
                ->select('id,pid,name,url,url_param,icon,status')
                ->where(['like', 'name', $search])
                ->indexBy('id')
                ->orderBy(['sort' => SORT_ASC, 'id' => SORT_ASC])
                ->asArray()
                ->all();
            $tree = [];
            if (!empty($list)) {
                //获取相关的权限
                $permissionsArr = $auth->getPermissionsByUser($userId);
                $permissions = array_keys($permissionsArr);
                //先重新组装label，url等数据
                foreach ($list as $k => $info) {
                    //赋值为label,并注销掉name
                    $list[$k]['label'] = $info['name'];
                    unset($list[$k]['name']);
                    //组装url
                    $list[$k]['url'] = static::mergeUrl($info['url'], $info['url_param']);
                    unset($list[$k]['url_param']);//url参数注销掉
                    //如果数据库中字段为隐藏，则增加visible参数，且设置为false，再有就是无权限的隐藏掉
                    if (!$info['status'] || ($userId != Yii::$app->params['super_admin_id'] && !in_array($info['url'], $permissions))) {
                        $list[$k]['visible'] = false;
                    }
                    unset($list[$k]['status']);//注销掉状态
                }
                //组装成要求的树结构
                foreach ($list as $value) {
                    if (isset($list[$value['pid']])) {
                        $list[$value['pid']]['items'][] = &$list[$value['id']];
                    } else {
                        $tree[] = &$list[$value['id']];
                    }
                }
            }
            //如果是搜索，则不加入缓存
            if (empty($search)) {
                //按照角色名标签作为依赖，这样以来，以下情况的变动需要调用TagDependency::invalidate清除缓存:
                //1.角色修改、删除、授权（BackendRoleController中，设置目的是为了清除旧的缓存，以免后期新建的角色是曾经用过后又修改的，但缓存还在导致出错[也可以在新建角色时调用清除一下]，后一个授权变更会引起菜单的显隐）
                //2.菜单修改、删除、新建（BackendMenuController中，菜单变化，直接清除全部的菜单缓存）
                //3.用户赋予角色（AdminController中，这个不做设置也可以，设置目的是为了清除旧的缓存，以免积累，但也可能导致正常缓存的重新缓存，有利有弊，这里就不设置了）
                $dependency = new TagDependency(['tags' => $roles]);
                //写入缓存
                $cache->set('menus_role_' . implode('_', $roles), $tree, 0, $dependency);
            }
        }
        return $tree;
    }

    /**
     * 根据url和参数组装成Yii对应的url格式
     * @param string $url 格式：’index/index'
     * @param string $urlParam 格式：‘id=3&pid=4...’
     * @return array
     */
    public static function mergeUrl($url, $urlParam = null)
    {
        $newUrl = [$url];
        if (!empty($urlParam)) {
            $paramArr = explode('&', $urlParam);
            $param = [];
            foreach ($paramArr as $arr) {
                $subArr = explode('=', $arr);
                if (count($subArr) < 2) {
                    continue;
                }
                $param[$subArr[0]] = $subArr[1];
            }
            $newUrl = ArrayHelper::merge($newUrl, $param);
        }
        return $newUrl;
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

}
