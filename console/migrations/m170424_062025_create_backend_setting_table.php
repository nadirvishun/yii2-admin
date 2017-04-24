<?php

use yii\db\Migration;

/**
 * Handles the creation of table `backend_setting`.
 */
class m170424_062025_create_backend_setting_table extends Migration
{
    const TBL_NAME = '{{%backend_setting}}';

    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB COMMENT="后台配置表"';
        }

        $this->createTable(self::TBL_NAME, [
            'id' => $this->primaryKey()->comment('配置ID'),
            'pid' => $this->integer()->notNull()->comment('父ID'),
            'name' => $this->string(64)->notNull()->comment('配置名称'),
            'alias' => $this->string(64)->notNull()->unique()->comment('配置别名'),
            'type' => $this->boolean()->notNull()->defaultValue(1)->comment('类别，例如1代表text，2代表radio'),
            'value' => $this->text()->notNull()->comment('值'),
            'extra' => $this->string()->notNull()->comment('配置参数'),
            'hint' => $this->string(100)->notNull()->comment('提示说明'),
            'sort' => $this->integer()->notNull()->defaultValue(0)->comment('排序'),
            'status' => $this->boolean()->unsigned()->notNull()->defaultValue(1)->comment('状态:0隐藏，1显示'),
            'created_by' => $this->integer()->unsigned()->notNull()->comment('创建人'),
            'created_at' => $this->bigInteger()->unsigned()->notNull()->comment('创建时间'),
            'updated_by' => $this->integer()->unsigned()->notNull()->comment('更新人'),
            'updated_at' => $this->bigInteger()->unsigned()->notNull()->comment('更新时间')
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
