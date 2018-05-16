<?php

use yii\db\Migration;

/**
 * Handles the creation of table `access_token`.
 */
class m170328_083427_create_user_token_table extends Migration
{
    const TBL_NAME = '{{%user_token}}';

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
            'access_token' => $this->string(40)->notNull()->comment('接口token'),
            'user_id' => $this->integer()->unsigned()->notNull()->comment('用户ID'),
            'access_expires' => $this->bigInteger()->unsigned()->notNull()->comment('access_token过期时间'),
            'client_type' => $this->boolean()->unsigned()->notNull()->comment('客户端类型，1为安卓，2为ios，3为wap'),
            'refresh_token'=>$this->string(40)->notNull()->comment('刷新token'),
            'refresh_expires'=>$this->bigInteger()->unsigned()->notNull()->comment('refresh_token过期时间'),
            'created_at' => $this->bigInteger()->unsigned()->notNull()->comment('创建时间'),
            'updated_at' => $this->bigInteger()->unsigned()->notNull()->comment('更新时间')
        ], $tableOptions);
        //添加主键及索引
        $this->addPrimaryKey('access_token', self::TBL_NAME, 'access_token');
        $this->createIndex('user_id', self::TBL_NAME, 'user_id');
        $this->createIndex('refresh_token', self::TBL_NAME, 'refresh_token');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable(self::TBL_NAME);
    }
}
