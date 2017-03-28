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
            'user_id' => $this->integer()->notNull()->comment('用户ID'),
            'expires' => $this->bigInteger()->notNull()->comment('过期时间'),
            'client_type' => $this->integer(1)->notNull()->comment('客户端类型，1为安卓，2为ios'),
            'device_id' => $this->string()->notNull()->comment('客户端设备号'),
            'login_ip' => $this->string(20)->notNull()->comment('最近一次登录的IP'),
            'login_time' => $this->integer()->notNull()->comment('最近一次登录的时间'),
            'login_attempts' => $this->integer()->notNull()->comment('尝试登录次数'),
        ], $tableOptions);
        //添加主键
        $this->addPrimaryKey('access_token', self::TBL_NAME, 'access_token');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable(self::TBL_NAME);
    }
}
