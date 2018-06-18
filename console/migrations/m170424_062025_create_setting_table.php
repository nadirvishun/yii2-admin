<?php

use yii\db\Migration;

/**
 * Handles the creation of table `setting`.
 */
class m170424_062025_create_setting_table extends Migration
{
    const TBL_NAME = '{{%setting}}';

    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB COMMENT="配置表"';
        }

        $this->createTable(self::TBL_NAME, [
            'id' => $this->primaryKey()->unsigned()->comment('配置ID'),
            'pid' => $this->integer()->notNull()->unsigned()->defaultValue(0)->comment('父ID'),
            'name' => $this->string(64)->notNull()->defaultValue(0)->comment('配置名称'),
            'alias' => $this->string(64)->notNull()->unique()->defaultValue('')->comment('配置别名'),
            'type' => $this->tinyInteger()->notNull()->defaultValue(1)->comment('类别，例如1代表text，2代表radio等'),
            'value' => $this->text()->notNull()->comment('值'),
            'extra' => $this->string()->notNull()->defaultValue('')->comment('配置参数'),
            'hint' => $this->string(100)->notNull()->defaultValue('')->comment('提示说明'),
            'sort' => $this->integer()->notNull()->defaultValue(0)->comment('排序'),
            'status' => $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(1)->comment('状态:0隐藏，1显示'),
            'created_by' => $this->integer()->unsigned()->notNull()->defaultValue(0)->comment('创建人'),
            'created_at' => $this->bigInteger()->unsigned()->notNull()->defaultValue(0)->comment('创建时间'),
            'updated_by' => $this->integer()->unsigned()->notNull()->defaultValue(0)->comment('更新人'),
            'updated_at' => $this->bigInteger()->unsigned()->notNull()->defaultValue(0)->comment('更新时间')
        ], $tableOptions);

        //增加配置基础显示
        $time = time();
        $this->batchInsert(self::TBL_NAME, ['pid', 'name', 'alias', 'type', 'value', 'extra', 'hint','sort', 'status','created_by', 'created_at', 'updated_by', 'updated_at'], [
            [0, '网站设置','website',1,'','','',0,1,1,$time,1,$time],
            [1, '网站名称','site_name',1,'','','',0,1,1,$time,1,$time],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable(self::TBL_NAME);
    }
}
