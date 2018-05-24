<?php

use yii\db\Migration;

/**
 * Handles the creation of table `backend_auth_item_child`.
 */
class m180522_063033_create_backend_auth_item_child_table extends Migration
{
    const TBL_NAME = '{{%backend_auth_item_child}}';

    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB COMMENT="角色和权限层次表"';
        }

        $this->createTable(self::TBL_NAME, [
            'parent' => $this->string(64)->notNull()->comment('父级'),
            'child' => $this->string(64)->notNull()->comment('子级'),
        ], $tableOptions);
        //联合主键
        $this->addPrimaryKey('parent_child', self::TBL_NAME, ['parent', 'child']);
        //设置外键，如果要用内置的rbac方法，则必须设置，相关修改是自动改的
        $this->addForeignKey('foreign_parent', self::TBL_NAME, 'parent', '{{backend_auth_item}}', 'name', 'CASCADE', 'CASCADE');
        $this->addForeignKey('foreign_child', self::TBL_NAME, 'child', '{{backend_auth_item}}', 'name', 'CASCADE', 'CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable(self::TBL_NAME);
    }
}
