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
