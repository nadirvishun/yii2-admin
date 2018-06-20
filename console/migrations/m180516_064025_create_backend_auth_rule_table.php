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
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB COMMENT="规则表"';
        }

        $this->createTable(self::TBL_NAME, [
            'name' => $this->string(64)->notNull()->defaultValue('')->comment('规则名称'),
            'data' => $this->text()->comment('数据'),
            'created_at' => $this->bigInteger()->notNull()->defaultValue(0)->comment('创建时间'),
            'updated_at' => $this->bigInteger()->notNull()->defaultValue(0)->comment('更新时间')
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
