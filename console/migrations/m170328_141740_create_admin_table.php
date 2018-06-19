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
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB COMMENT="后台管理员表"';
        }

        $this->createTable(self::TBL_NAME, [
            'id' => $this->primaryKey()->unsigned()->comment('用户ID'),
            'username' => $this->string(50)->notNull()->unique()->defaultValue('')->comment('用户名'),
            'auth_key' => $this->string(32)->notNull()->defaultValue('')->comment('认证key，与cookie登录有关'),
            'password_hash' => $this->string()->notNull()->defaultValue('')->comment('密码'),
            'password_reset_token' => $this->string()->unique()->comment('重置密码时所用key'),//唯一但可以为null
            'email' => $this->string()->unique()->comment('邮箱'),//email唯一但可以为null
            'mobile' => $this->string(20)->unique()->comment('手机号'),//mobile唯一但可以为null
            'avatar' => $this->string()->notNull()->defaultValue('')->comment('用户头像'),
            'sex' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('用户性别：0未知，1男，2女'),
            'last_login_ip' => $this->string(20)->notNull()->defaultValue('')->comment('最近一次登录的IP'),
            'last_login_time' => $this->bigInteger()->unsigned()->notNull()->defaultValue(0)->comment('最近一次登录的时间'),
            'status' => $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(1)->comment('账号状态'),
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
