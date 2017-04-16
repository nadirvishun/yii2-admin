<?php
/**
 * Adjacency list类型树结构相关
 * 需要首先将所有数据取出，然后再用此方法重新规划数组,其它的边取边查询数据库的方法不推荐
 * 经测试第一种方法要快很多，建议使用
 * 由于参数太多，此类废弃，修改为用实例的方法实现
 */

namespace common\components;

use yii\helpers\ArrayHelper;

class Tree2
{
    /**
     * 图标
     */
    const ICON = '└';
    /**
     * 填充
     */
    const BLANK = '&nbsp;&nbsp;&nbsp;';
    /**
     * 默认ID字段名称
     */
    const ID_NAME = 'id';
    /**
     * 默认PID字段名称
     */
    const PID_NAME = 'pid';
    /**
     * 默认名称字段名称
     */
    const TITLE_NAME = 'name';
    /**
     * 默认子元素字段名称
     */
    const CHILDREN_NAME = 'items';

    /**
     * 生成下拉菜单可用树列表的方法
     * 经测试3400条地区数据耗时0.28602，比另一种方法快超级多
     * 流程是先通过引用方法来生成一种特殊树结构，再通过递归来解析这种特殊的结构
     * @param array $list
     * @param int $pid
     * @param int $level
     * @param array $params
     * @return array
     * @internal param string $idName
     * @internal param string $pidName
     * @internal param string $titleName
     * @internal param string $childName
     */
    public static function getTreeOptions($list, $pid = 0, $level = 0, $params = [])
    {
        if (empty($list)) {
            return [];
        }
        $idName = self::ID_NAME;
        $pidName = self::PID_NAME;
        $titleName = self::TITLE_NAME;
        $childName = self::CHILDREN_NAME;
        $blank = self::BLANK;
        $icon = self::ICON;
        extract($params);
        //先生成特殊规格的树
        $tree = static::getTree($list, $pid, $idName, $pidName, $childName);
        //再组装成select需要的形式
        return static::formatTree($tree, $level, $idName, $titleName, $childName, $blank, $icon);
    }

    /**
     * 通过递归来解析特殊的树结构来组装成下拉菜单所需要的样式
     * @param array $tree 特殊规格的数组
     * @param int $level
     * @param string $idName
     * @param string $titleName
     * @param string $childName
     * @param string $blank
     * @param string $icon
     * @return array
     */
    protected static function formatTree($tree, $level = 0, $idName = 'id', $titleName = 'name', $childName = 'items', $blank = '&nbsp;', $icon = '└')
    {
        if (empty($tree)) {
            return [];
        }
        $blankStr = str_repeat($blank, $level) . $icon;
        if ($level == 0) {//第一次无需有图标及空格
            $blankStr = '';
        }
        foreach ($tree as $key => $value) {
            $options[$value[$idName]] = $blankStr . $value[$titleName];
            if (isset($value[$childName])) {
                $optionsTmp = static::formatTree($value[$childName], $level + 1, $idName, $titleName, $childName, $blank, $icon);
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
     *  'items'=>[
     *      'id'=>2,
     *      'pid'=>'1'
     *       。。。
     *  ]
     * ]
     * @param array $list
     * @param int $pid
     * @param string $idName
     * @param string $pidName
     * @param string $childName
     * @return array
     */
    protected static function getTree($list, $pid = 0, $idName = 'id', $pidName = 'pid', $childName = 'items')
    {
        $tree = [];
        if (!empty($list)) {
            $newList = [];
            foreach ($list as $k => $v) {
                $newList[$v[$idName]] = $v;
            }
            foreach ($newList as $value) {
                if ($pid == $value[$pidName]) {
                    $tree[] = &$newList[$value[$idName]];
                } elseif (isset($newList[$value[$pidName]])) {
                    $newList[$value[$pidName]][$childName][] = &$newList[$value[$idName]];
                }
            }
        }
        return $tree;
    }

    /**
     * 另一种普通列表转为下拉菜单可用的树列表
     * 经测试3400条地区数据耗时4.64227，比另一种方法慢超级多，数据量多时不建议使用
     * @param array $list 原数组
     * @param int $pid 起始pid
     * @param int $level 起始层级
     * @param array $params 参数
     * @return array
     * @internal param string $idName 主键字段名称
     * @internal param string $pidName 父ID字段名称
     * @internal param string $titleName 名称字段名称
     */
    public static function getTreeOptions2($list, $pid = 0, $level = 0, $params = [])
    {
        if (empty($list)) {
            return [];
        }
        $idName = self::ID_NAME;
        $pidName = self::PID_NAME;
        $titleName = self::TITLE_NAME;
        $blank = self::BLANK;
        $icon = self::ICON;
        extract($params);
        return static::formatTree2($list, $pid, $level, $idName, $pidName, $titleName, $blank, $icon);
    }

    /**
     * 通过递归来解析特殊的树结构来组装成下拉菜单所需要的样式
     * 通过递归遍历所有数组的方式
     * @param array $list 原数组
     * @param int $pid 起始pid
     * @param int $level 起始层级
     * @param string $idName 主键字段名称
     * @param string $pidName 父ID字段名称
     * @param string $titleName 名称字段名称
     * @param string $blank 空格填充
     * @param string $icon 图标
     * @return array
     */
    protected static function formatTree2($list, $pid = 0, $level = 0, $idName = 'id', $pidName = 'pid', $titleName = 'name', $blank = '&nbsp;', $icon = '└')
    {
        if (empty($list)) {
            return [];
        }
        $blankStr = str_repeat($blank, $level) . $icon;
        if ($level == 0) {//第一次无需有图标及空格
            $blankStr = '';
        }
        foreach ($list as $key => $value) {
            if ($value[$pidName] == $pid) {
                $options[$value[$idName]] = $blankStr . $value[$titleName];
                unset($list[$key]);//销毁已查询的，减轻下次递归时查询数量
                $optionsTmp = static::formatTree2($list, $value[$idName], $level + 1, $idName, $pidName, $titleName, $blank, $icon);//递归
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
}