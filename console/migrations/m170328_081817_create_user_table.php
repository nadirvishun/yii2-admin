<?php

use yii\db\Migration;

/**
 * Handles the creation of table `user`.
 */
class m170328_081817_create_user_table extends Migration
{
    const TBL_NAME = '{{%user}}';

    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB COMMENT="前台会员表"';
        }

        $this->createTable(self::TBL_NAME, [
            'id' => $this->primaryKey()->comment('用户ID'),
            'username' => $this->string(50)->notNull()->unique()->comment('用户名'),
            'auth_key' => $this->string(32)->notNull()->comment('认证key，与cookie登录有关'),
            'password_hash' => $this->string()->notNull()->comment('密码'),
            'password_reset_token' => $this->string()->notNull()->unique()->comment('充值密码时所用key'),
            'email' => $this->string()->notNull()->unique()->comment('邮箱'),
            'mobile' => $this->string(20)->notNull()->unique()->comment('手机号'),
            'avatar' => $this->string()->notNull()->comment('用户头像'),
            'sex' => $this->integer(1)->notNull()->comment('用户性别：0未知，1男，2女'),
            'register_time' => $this->bigInteger()->notNull()->comment('注册时间'),
            'register_client' => $this->integer(1)->notNull()->comment('注册来源：1为安卓，2为苹果,3为wap，4为pc'),
            'status' => $this->smallInteger()->notNull()->defaultValue(10)->comment('账号状态'),
            'created_at' => $this->bigInteger()->notNull()->comment('创建时间'),
            'updated_at' => $this->bigInteger()->notNull()->comment('更新时间'),
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
