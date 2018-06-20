<?php

use yii\db\Migration;

/**
 * Handles the creation of table `backend_auth_assignment`.
 */
class m180522_063551_create_backend_auth_assignment_table extends Migration
{
    const TBL_NAME = '{{%backend_auth_assignment}}';

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
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB COMMENT="角色权限指派表"';
        }

        $this->createTable(self::TBL_NAME, [
            'item_name' => $this->string(64)->notNull()->defaultValue('')->comment('权限角色名称'),
            'user_id' => $this->string(64)->notNull()->defaultValue('')->comment('用户ID'),
            'created_at' => $this->bigInteger()->notNull()->defaultValue(0)->comment('创建时间'),
        ], $tableOptions);
        //联合主键
        $this->addPrimaryKey('item_user', self::TBL_NAME, ['item_name', 'user_id']);
        //设置外键，如果要用内置的rbac方法，则必须设置，相关修改是自动改的
        $this->addForeignKey('foreign_item_name', self::TBL_NAME, 'item_name', '{{backend_auth_item}}', 'name', 'CASCADE', 'CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable(self::TBL_NAME);
    }
}
