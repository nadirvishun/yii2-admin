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
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB COMMENT="后台菜单表"';
        }
        $this->createTable(self::TBL_NAME, [
            'id' => $this->primaryKey()->unsigned()->comment('菜单ID'),
            'pid' => $this->integer()->unsigned()->notNull()->defaultValue(0)->comment('父ID'),
            'name' => $this->string(64)->notNull()->defaultValue('')->comment('菜单名称'),
            'url' => $this->string(64)->notNull()->defaultValue('')->comment('菜单链接'),
            'url_param' => $this->string()->notNull()->defaultValue('')->comment('菜单链接参数'),
            'icon' => $this->string(64)->notNull()->defaultValue('')->comment('小图标'),
            'status' => $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(1)->comment('状态:0隐藏，1显示'),
            'sort' => $this->integer()->notNull()->defaultValue(0)->comment('排序'),
            'created_by' => $this->integer()->unsigned()->notNull()->defaultValue(0)->comment('创建人'),
            'created_at' => $this->bigInteger()->unsigned()->notNull()->defaultValue(0)->comment('创建时间'),
            'updated_by' => $this->integer()->unsigned()->notNull()->defaultValue(0)->comment('更新人'),
            'updated_at' => $this->bigInteger()->unsigned()->notNull()->defaultValue(0)->comment('更新时间')
        ], $tableOptions);
        //增加后台菜单的初始显示
        $time = time();
        $this->batchInsert(self::TBL_NAME, ['pid', 'name', 'url', 'icon', 'sort', 'created_by', 'created_at', 'updated_by', 'updated_at', 'status'], [
            [0, '系统设置', 'dashboard', 'dashboard', 0, 1, $time, 1, $time, 1],//1
            [1, '配置设置', 'system/setting/system', 'cog', 0, 1, $time, 1, $time, 1],//2
            [1, '配置管理', 'system/setting/index', 'cube', 0, 1, $time, 1, $time, 1],//3
            [3, '新增配置', 'system/setting/create', '', 1, 1, $time, 1, $time, 0],//4
            [3, '修改配置', 'system/setting/update', '', 2, 1, $time, 1, $time, 0],//5
            [3, '删除配置', 'system/setting/delete', '', 3, 1, $time, 1, $time, 0],//6
            [1, '后台菜单', 'system/backend-menu/index', 'tree', 0, 1, $time, 1, $time, 1],//7
            [7, '新增菜单', 'system/backend-menu/create', '', 1, 1, $time, 1, $time, 0],//8
            [7, '修改菜单', 'system/backend-menu/update', '', 2, 1, $time, 1, $time, 0],//9
            [7, '删除菜单', 'system/backend-menu/delete', '', 3, 1, $time, 1, $time, 0],//10
            [0, '用户管理', 'user', 'users', 0, 1, $time, 1, $time, 1],//11
            [11, '后台管理员', 'system/admin/index', 'user', 0, 1, $time, 1, $time, 1],//12
            [12, '新增管理员', 'system/admin/create', '', 3, 1, $time, 1, $time, 0],//13
            [12, '修改管理员', 'system/admin/update', '', 2, 1, $time, 1, $time, 0],//14
            [12, '删除管理员', 'system/admin/delete', '', 1, 1, $time, 1, $time, 0],//15
            [12, '角色授权', 'system/admin/role', '', 4, 1, $time, 1, $time, 0],//16
            [11, '后台角色', 'system/backend-role/index', 'key', 0, 1, $time, 1, $time, 1],//17
            [17, '新增角色', 'system/backend-role/create', '', 3, 1, $time, 1, $time, 0],//18
            [17, '修改角色', 'system/backend-role/update', '', 2, 1, $time, 1, $time, 0],//19
            [17, '删除角色', 'system/backend-role/delete', '', 1, 1, $time, 1, $time, 0],//20
            [17, '权限授权', 'system/backend-role/auth', '', 4, 1, $time, 1, $time, 0],//21
            [11, '后台日志', 'system/admin-log/index', 'umbrella', 0, 1, $time, 1, $time, 1],//22
            [22, '删除日志', 'system/admin-log/delete', '', 0, 1, $time, 1, $time, 0],//23
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
