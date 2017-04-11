<?php
/**
 * parent_id类型树结构相关
 * 需要首先将所有数据取出，然后再用此方法重新规划数组
 * 其它的边取边查询数据库的方法不推荐
 */

namespace common\components;

use yii\helpers\ArrayHelper;

class Tree
{
    /**
     * 图标
     * @var string
     */
    public static $icon = '└';
    /**
     * 填充
     * @var string
     */
    public static $blank = '&nbsp;';


    /**
     * 普通列表转为select可用的树列表
     * @param array $list 原数组
     * @param int $pid 起始pid
     * @param int $level 起始层级
     * @param string $pkFieldName 主键字段名称
     * @param string $pidFieldName 父ID字段名称
     * @param string $cateFieldName 名称字段名称
     * @return array
     */
    public static function getTreeOptions($list, $pid = 0, $level = 0, $pkFieldName = 'id', $pidFieldName = 'pid', $cateFieldName = 'name')
    {
        if (empty($list)) {
            return [];
        }
        $blankStr = str_repeat(static::$blank, $level) . static::$icon;
        if ($level == 0) {//第一次无需有图标及空格
            $blankStr = '';
        }
        foreach ($list as $key => $value) {
            if ($value[$pidFieldName] == $pid) {
                $options[$value[$pkFieldName]] = $blankStr . $value[$cateFieldName];
                unset($list[$key]);//销毁已查询的，减轻下次递归时查询数量
                $optionsTmp = static::getTreeOptions($list, $value[$pkFieldName], $level + 1);//递归
                if (!empty($optionsTmp)) {
                    $options = ArrayHelper::merge($options, $optionsTmp);
                }
            }
        }
        if (!empty($options)) {
            return $options;
        } else {
            return [];
        }
    }

    /**
     * 另一种展示select可用树列表的方法
     * @param array $list
     * @param int $pid
     * @param int $level
     * @param string $pkFieldName
     * @param string $pidFieldName
     * @param string $cateFieldName
     * @param string $childName
     * @return array
     */
    public static function getTreeOptions2($list, $pid = 0, $level = 0, $pkFieldName = 'id', $pidFieldName = 'pid', $cateFieldName = 'name', $childName = 'son')
    {
        //先生成特殊规格的树
        $tree = static::getTree($list, $pid, $pkFieldName, $pidFieldName, $childName);
        //再组装成select需要的形式
        return static::formatTree($tree, $level, $pkFieldName, $cateFieldName, $childName);
    }

    /**
     * 组装成select可用的方法
     * @param array $tree 特殊规格的数组
     * @param int $level
     * @param string $pkFieldName
     * @param string $cateFieldName
     * @param string $childName
     * @return array
     */
    public static function formatTree($tree, $level = 0, $pkFieldName = 'id', $cateFieldName = 'name', $childName = 'son')
    {
        if (empty($tree)) {
            return [];
        }
        $blankStr = str_repeat(static::$blank, $level) . static::$icon;
        if ($level == 0) {//第一次无需有图标及空格
            $blankStr = '';
        }
        foreach ($tree as $key => $value) {
            $options[$value[$pkFieldName]] = $blankStr . $value[$cateFieldName];
            if (isset($value[$childName])) {
                $optionsTmp = static::formatTree($value[$childName], $level + 1);
                if (!empty($optionsTmp)) {
                    $options = ArrayHelper::merge($options, $optionsTmp);
                }
            }
        }
        if (!empty($options)) {
            return $options;
        } else {
            return [];
        }
    }

    /**
     * 生成类似下种格式的树结构
     * 利用了引用&来实现，参照：http://blog.csdn.net/gxdvip/article/details/24434801
     * [
     *  'id'=>1,
     *  'pid'=>0,
     *  'son'=>[
     *      'id'=>2,
     *      'pid'=>'1'
     *       。。。
     *  ]
     * ]
     * @param array $list
     * @param int $pid
     * @param string $pkFieldName
     * @param string $pidFieldName
     * @param string $childName
     * @return array
     */
    public static function getTree($list, $pid = 0, $pkFieldName = 'id', $pidFieldName = 'pid', $childName = 'son')
    {
        $tree = [];
        if (!empty($list)) {
            $newList = [];
            foreach ($list as $k => $v) {
                $newList[$v[$pkFieldName]] = $v;
            }
            foreach ($newList as $value) {
                if ($pid ==$value[$pidFieldName]) {
                    $tree[] = &$newList[$value[$pkFieldName]];
                } elseif (isset($newList[$value[$pidFieldName]])) {
                    $newList[$value[$pidFieldName]][$childName][] = &$newList[$value[$pkFieldName]];
                }
            }
        }
        return $tree;
    }
}