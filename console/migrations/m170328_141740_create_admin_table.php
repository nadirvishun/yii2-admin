<?php

use yii\db\Migration;

/**
 * Handles the creation of table `admin`.
 */
class m170328_141740_create_admin_table extends Migration
{
    const TBL_NAME = '{{%admin}}';

    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB COMMENT="后台管理员表"';
        }

        $this->createTable(self::TBL_NAME, [
            'id' => $this->primaryKey()->unsigned()->comment('用户ID'),
            'username' => $this->string(50)->notNull()->unique()->defaultValue('')->comment('用户名'),
            'auth_key' => $this->string(32)->notNull()->defaultValue('')->comment('认证key，与cookie登录有关'),
            'password_hash' => $this->string()->notNull()->defaultValue('')->comment('密码'),
            'password_reset_token' => $this->string()->notNull()->unique()->defaultValue('')->comment('重置密码时所用key'),
            'email' => $this->string()->notNull()->defaultValue('')->comment('邮箱'),
            'mobile' => $this->string(20)->notNull()->defaultValue('')->comment('手机号'),
            'avatar' => $this->string()->notNull()->defaultValue('')->comment('用户头像'),
            'sex' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('用户性别：0未知，1男，2女'),
            'last_login_ip' => $this->string(20)->notNull()->defaultValue('')->comment('最近一次登录的IP'),
            'last_login_time' => $this->bigInteger()->unsigned()->notNull()->defaultValue(0)->comment('最近一次登录的时间'),
            'status' => $this->boolean()->unsigned()->notNull()->defaultValue(1)->comment('账号状态'),
            'created_at' => $this->bigInteger()->unsigned()->notNull()->defaultValue(0)->comment('创建时间'),
            'updated_at' => $this->bigInteger()->unsigned()->notNull()->defaultValue(0)->comment('更新时间')
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
