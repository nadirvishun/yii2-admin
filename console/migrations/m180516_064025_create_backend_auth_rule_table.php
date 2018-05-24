<?php

use yii\db\Migration;

/**
 * Handles the creation of table `backend_auth_rule`.
 */
class m180516_064025_create_backend_auth_rule_table extends Migration
{
    const TBL_NAME = '{{%backend_auth_rule}}';

    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB COMMENT="规则表"';
        }

        $this->createTable(self::TBL_NAME, [
            'name' => $this->string(64)->notNull()->comment('规则名称'),
            'data' => $this->text()->comment('数据'),
            'created_at' => $this->bigInteger()->unsigned()->notNull()->comment('创建时间'),
            'updated_at' => $this->bigInteger()->unsigned()->notNull()->comment('更新时间')
        ], $tableOptions);

        //增加主键
        $this->addPrimaryKey('name', self::TBL_NAME, 'name');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable(self::TBL_NAME);
    }
}
