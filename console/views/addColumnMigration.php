<?php
/**
 * This view is used by console/controllers/MigrateController.php
 * The following variables are available in this view:
 */
/* @var $className string the new migration class name without namespace */
/* @var $namespace string the new migration class namespace */
/* @var $table string the name table */
/* @var $fields array the fields */

preg_match('/^add_(.+)_columns?_to_(.+)_table$/', $name, $matches);
$columns = $matches[1];

echo "<?php\n";
if (!empty($namespace)) {
    echo "\nnamespace {$namespace};\n";
}
?>

use yii\db\Migration;

/**
 * Handles adding <?= $columns ?> to table `<?= $table ?>`.
<?= $this->render('_foreignTables', [
     'foreignKeys' => $foreignKeys,
 ]) ?>
 */
class <?= $className ?> extends Migration
{
    const TBL_NAME = '{{%<?=$table?>}}';

    /**
     * @inheritdoc
     */
    public function up()
    {
        if ($this->db->driverName === 'mysql') {
            //获取mysql版本
            $version = $this->db->getServerVersion();
            //获取字符集
            $charset = $this->db->charset;
            if($charset === 'utf8mb4'){
                //如果mysql数据库版本小于5.7.7，则需要将varchar默认值修改为191，否则报错：Specified key was too long error
                if (version_compare($version, '5.7.7', '<')) {
                    $queryBuilder = $this->db->getQueryBuilder();
                    $queryBuilder->typeMap[\yii\db\mysql\Schema::TYPE_STRING] = 'varchar(191)';
                }
            }
        }
<?= $this->render('_addColumns', [
    'table' => $table,
    'fields' => $fields,
    'foreignKeys' => $foreignKeys,
])
?>
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
<?= $this->render('_dropColumns', [
    'table' => $table,
    'fields' => $fields,
    'foreignKeys' => $foreignKeys,
])
?>
    }
}
