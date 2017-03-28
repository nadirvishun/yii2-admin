<?php

use yii\db\Migration;

/**
 * Handles the creation of table `access_token`.
 */
class m170328_083427_create_access_token_table extends Migration
{
    const TBL_NAME = '{{%access_token}}';

    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB COMMENT="移动端登录token表"';
        }

        $this->createTable(self::TBL_NAME, [
            'access_token' => $this->string(40)->notNull()->comment('token,主要用于移动端'),
            'user_id' => $this->integer()->unsigned()->notNull()->comment('用户ID'),
            'expires' => $this->bigInteger()->notNull()->comment('过期时间'),
            'client_type' => $this->smallInteger(1)->notNull()->comment('客户端类型，1为安卓，2为ios，3为wap'),
            'device_id' => $this->string()->notNull()->unique()->comment('客户端设备号')
        ], $tableOptions);
        //添加主键及索引
        $this->addPrimaryKey('access_token', self::TBL_NAME, 'access_token');
        $this->createIndex('user_id', self::TBL_NAME, 'user_id');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable(self::TBL_NAME);
    }
}
