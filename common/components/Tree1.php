<?php
/**
 * parent_id类型树结构相关
 * 没必要非要写成静态的方法，静态方法参数太多，所以用实例在构造函数中修改参数更合适
 * 需要首先将所有数据取出，然后再用此方法重新规划数组，其它的边取边查询数据库的方法不推荐
 * 经测试第一种方法要快很多，建议使用
 * @author   vishun <nadirvishun@gmail.com>
 */

namespace common\components;


class Tree1
{
    /**
     * 图标
     */
    public $icon = '└';
    /**
     * 填充
     */
    public $blank = '&nbsp;&nbsp;&nbsp;';
    /**
     * 默认ID字段名称
     */
    public $idName = 'id';
    /**
     * 默认PID字段名称
     */
    public $pidName = 'pid';
    /**
     * 默认名称字段名称
     */
    public $titleName = 'name';
    /**
     * 默认子元素字段名称
     */
    public $childrenName = 'items';

    /**
     * 构造函数，可覆盖默认字段值
     * @param array $config
     */
    function __construct($config = [])
    {
        if (!empty($config)) {
            foreach ($config as $name => $value) {
                $this->$name = $value;
            }
        }
    }

    /**
     * 生成下拉菜单可用树列表的方法
     * 经测试4000条地区数据耗时0.08左右，比另外两种方法快超级多
     * 流程是先通过引用方法来生成一种特殊树结构，再通过递归来解析这种特殊的结构
     * @param array $list
     * @param int $pid
     * @param int $level
     * @return array
     */
    public function getTreeOptions($list, $pid = 0, $level = 0)
    {
        //先生成特殊规格的树
        $tree = $this->getTree($list, $pid);
        //再组装成select需要的形式
        return $this->formatTree($tree, $level);
    }

    /**
     * 通过递归来解析特殊的树结构来组装成下拉菜单所需要的样式
     * @param array $tree 特殊规格的数组
     * @param int $level
     * @return array
     */
    protected function formatTree($tree, $level = 0)
    {
        $options = [];
        if (!empty($tree)) {
            $blankStr = str_repeat($this->blank, $level) . $this->icon;
            if ($level == 0) {//第一次无需有图标及空格
                $blankStr = '';
            }
            foreach ($tree as $key => $value) {
                $options[$value[$this->idName]] = $blankStr . $value[$this->titleName];
                if (isset($value[$this->childrenName])) {//查询是否有子节点
                    $optionsTmp = $this->formatTree($value[$this->childrenName], $level + 1);
                    if (!empty($optionsTmp)) {
                        $options = array_merge($options, $optionsTmp);
                    }
                }
            }
        }
        return $options;
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
     * @return array
     */
    protected function getTree($list, $pid = 0)
    {
        $tree = [];
        if (!empty($list)) {
            //先修改为以id为下标的列表
            $newList = [];
            foreach ($list as $k => $v) {
                $newList[$v[$this->idName]] = $v;
            }
            //然后开始组装成特殊格式
            foreach ($newList as $value) {
                if ($pid == $value[$this->pidName]) {
                    $tree[] = &$newList[$value[$this->idName]];
                } elseif (isset($newList[$value[$this->pidName]])) {
                    $newList[$value[$this->pidName]][$this->childrenName][] = &$newList[$value[$this->idName]];
                }
            }
        }
        return $tree;
    }

    /**
     * 第二种方法，利用出入栈递归来实现
     * 经测试4000条地区数据耗时6.5s左右，比较慢
     * @param $list
     * @param int $pid
     * @param int $level
     * @return array
     */
    public function getTreeOptions2($list, $pid = 0, $level = 0)
    {
        $tree = [];
        if (!empty($list)) {

            //先将数组反转，因为后期出栈时会有限出最上面的
            $list = array_reverse($list);
            //先取出顶级的来压入数组$stack中，并将在$list中的删除掉
            $stack = [];
            foreach ($list as $key => $value) {
                if ($value[$this->pidName] == $pid) {
                    array_push($stack, ['data' => $value, 'level' => $level]);//将层级记录下来，方便填充空格
                    unset($list[$key]);
                }
            }
            while (count($stack)) {
                //先从栈中取出第一项
                $info = array_pop($stack);
                //查询剩余的$list中pid与其id相等的，也就是查找其子节点
                foreach ($list as $key => $child) {
                    if ($child[$this->pidName] == $info['data'][$this->idName]) {
                        //如果有子节点则入栈，while循环中会继续查找子节点的下级
                        array_push($stack, ['data' => $child, 'level' => $info['level'] + 1]);
                        unset($list[$key]);
                    }
                }
                //组装成下拉菜单格式
                $blankStr = str_repeat($this->blank, $info['level']) . $this->icon;
                if ($info['level'] == 0) {//第一次无需有图标及空格
                    $blankStr = '';
                }
                $tree[$info['data'][$this->idName]] = $blankStr . $info['data'][$this->titleName];
            }
        }
        return $tree;
    }

    /**
     * 第三种普通列表转为下拉菜单可用的树列表
     * 经测试4000条地区数据耗时8.7s左右，最慢
     * @param array $list 原数组
     * @param int $pid 起始pid
     * @param int $level 起始层级
     * @return array
     */
    public function getTreeOptions3($list, $pid = 0, $level = 0)
    {
        $options = [];
        if (!empty($list)) {
            $blankStr = str_repeat($this->blank, $level) . $this->icon;
            if ($level == 0) {//第一次无需有图标及空格
                $blankStr = '';
            }
            foreach ($list as $key => $value) {
                if ($value[$this->pidName] == $pid) {
                    $options[$value[$this->idName]] = $blankStr . $value[$this->titleName];
                    unset($list[$key]);//销毁已查询的，减轻下次递归时查询数量
                    $optionsTmp = $this->getTreeOptions3($list, $value[$this->idName], $level + 1);//递归
                    if (!empty($optionsTmp)) {
                        $options = array_merge($options, $optionsTmp);
                    }
                }
            }
        }
        return $options;
    }
}