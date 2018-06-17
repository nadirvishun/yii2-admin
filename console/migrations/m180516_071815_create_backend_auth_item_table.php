<?php

use yii\db\Migration;

/**
 * Handles the creation of table `backend_auth_item`.
 */
class m180516_071815_create_backend_auth_item_table extends Migration
{
    const TBL_NAME = '{{%backend_auth_item}}';


    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB COMMENT="后台角色和权限表"';
        }

        $this->createTable(self::TBL_NAME, [
            'name' => $this->string(64)->notNull()->defaultValue('')->comment('角色或权限名称'),
            'type' => $this->tinyInteger()->notNull()->defaultValue(1)->comment('类别：1角色，2权限'),
            'description' => $this->text()->comment('描述'),
            'rule_name' => $this->string(64)->defaultValue('')->comment('规则名称'),
            'data' => $this->text()->comment('数据'),
            'created_at' => $this->bigInteger()->unsigned()->notNull()->defaultValue(0)->comment('创建时间'),
            'updated_at' => $this->bigInteger()->unsigned()->notNull()->defaultValue(0)->comment('更新时间')
        ], $tableOptions);
        //添加主键及索引
        $this->addPrimaryKey('name', self::TBL_NAME, 'name');
        $this->createIndex('type', self::TBL_NAME, 'type');
        //设置外键，如果要用内置的rbac方法，则必须设置，相关修改是自动改的
        $this->addForeignKey('foreign_rule_name', self::TBL_NAME, 'rule_name', '{{backend_auth_rule}}', 'name', 'SET NULL', 'CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable(self::TBL_NAME);
    }
}
