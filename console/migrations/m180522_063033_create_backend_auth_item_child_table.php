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
            //获取mysql版本
            $version = $this->db->getServerVersion();
            //utf8mb4在小于5.5.3的mysql版本中不支持
            if (version_compare($version, '5.5.3', '<')) {
                throw new \yii\base\Exception('Character utf8mb4 is not supported in mysql < 5.5.3');
            }
            //如果mysql数据库版本小于5.7.7，则需要将varchar默认值修改为191，否则报错：Specified key was too long error
            if (version_compare($version, '5.7.7', '<')) {
                $queryBuilder = $this->db->getQueryBuilder();
                $queryBuilder->typeMap[\yii\db\mysql\Schema::TYPE_STRING] = 'varchar(191)';
            }
            //如果是用utf8字符集，则不需要上面的两个判定
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB COMMENT="角色和权限层次表"';
        }

        $this->createTable(self::TBL_NAME, [
            'parent' => $this->string(64)->notNull()->defaultValue('')->comment('父级'),
            'child' => $this->string(64)->notNull()->defaultValue('')->comment('子级'),
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
