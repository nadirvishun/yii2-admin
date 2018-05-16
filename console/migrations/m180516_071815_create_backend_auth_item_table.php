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
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB COMMENT="后台角色和权限表"';
        }

        $this->createTable(self::TBL_NAME, [
            'id' => $this->primaryKey(),
        ],$tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable(self::TBL_NAME);
    }
}
