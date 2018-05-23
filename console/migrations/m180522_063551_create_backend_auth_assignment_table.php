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
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB COMMENT="角色权限指派表"';
        }

        $this->createTable(self::TBL_NAME, [
            'item_name' => $this->string(64)->notNull()->comment('权限角色名称'),
            'user_id' => $this->integer()->unsigned()->comment('用户ID'),
            'created_at' => $this->bigInteger()->unsigned()->notNull()->comment('创建时间'),
        ], $tableOptions);
        //联合主键
        $this->addPrimaryKey('item_user', self::TBL_NAME, ['item_name', 'user_id']);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable(self::TBL_NAME);
    }
}
