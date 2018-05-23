<?php

use yii\db\Migration;

/**
 * Handles the creation of table `backend_menu`.
 */
class m170402_093152_create_backend_menu_table extends Migration
{
    const TBL_NAME = '{{%backend_menu}}';

    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB COMMENT="后台菜单表"';
        }
        $this->createTable(self::TBL_NAME, [
            'id' => $this->primaryKey()->unsigned()->comment('菜单ID'),
//            'pid' => $this->integer()->unsigned()->notNull()->comment('父ID'),//与tree-grid冲突，暂未查明原因
            'pid' => $this->integer()->notNull()->comment('父ID'),
            'name' => $this->string(64)->notNull()->comment('菜单名称'),
            'url' => $this->string(64)->notNull()->comment('菜单链接'),
            'url_param' => $this->string()->notNull()->comment('菜单链接参数'),
            'icon' => $this->string(64)->notNull()->comment('小图标'),
            'status' => $this->boolean()->unsigned()->notNull()->defaultValue(1)->comment('状态:0隐藏，1显示'),
            'sort' => $this->integer()->notNull()->defaultValue(0)->comment('排序'),
            'created_by' => $this->integer()->unsigned()->notNull()->comment('创建人'),
            'created_at' => $this->bigInteger()->unsigned()->notNull()->comment('创建时间'),
            'updated_by' => $this->integer()->unsigned()->notNull()->comment('更新人'),
            'updated_at' => $this->bigInteger()->unsigned()->notNull()->comment('更新时间')
        ], $tableOptions);
        //增加后台菜单的初始显示
        $time = time();
        $this->batchInsert(self::TBL_NAME, ['pid', 'name', 'url', 'icon', 'sort', 'created_by', 'created_at', 'updated_by', 'updated_at'], [
            [0, '控制面板', '/', 'dashboard', 0, 1, $time, 1, $time],
            [1, '系统设置', 'setting/system', 'cog', 0, 1, $time, 1, $time],
            [1, '配置管理', 'setting/index', 'cube', 2, 1, $time, 1, $time],
            [1, '后台菜单', 'backend-menu/index', 'tree', 5, 1, $time, 1, $time],
            [0, '用户管理', '/', 'users', 0, 1, $time, 1, $time],
            [5, '后台管理员', 'admin/index', 'user', 0, 1, $time, 1, $time],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable(self::TBL_NAME);
    }
}
