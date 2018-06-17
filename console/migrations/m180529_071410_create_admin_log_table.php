<?php

use yii\db\Migration;

/**
 * Handles the creation of table `admin_log`.
 */
class m180529_071410_create_admin_log_table extends Migration
{
    const TBL_NAME = '{{%admin_log}}';

    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB COMMENT="管理员操作日志表"';
        }

        $this->createTable(self::TBL_NAME, [
            'id' => $this->primaryKey(),
            'title' => $this->string()->notNull()->defaultValue('')->comment('标题'),
            'admin_id' => $this->integer()->unsigned()->notNull()->defaultValue(0)->comment('管理员ID'),
            'type' => $this->tinyInteger()->unsigned()->notNull()->defaultValue(0)->comment('操作类型：1添加，2修改，3删除'),
            'model' => $this->string()->notNull()->defaultValue('')->comment('模型'),
            'controller' => $this->string()->notNull()->defaultValue('')->comment('控制器'),
            'action' => $this->string()->notNull()->defaultValue('')->comment('方法'),
            'url_param' => $this->string()->notNull()->defaultValue('')->comment('url参数'),
            'description' => $this->text()->comment('操作详情'),
            'ip' => $this->string(20)->notNull()->defaultValue('')->comment('ip地址'),
            'created_at' => $this->bigInteger()->unsigned()->notNull()->defaultValue(0)->comment('创建时间'),
        ], $tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable(self::TBL_NAME);
    }
}
