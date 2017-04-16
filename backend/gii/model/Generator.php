<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace backend\gii\model;

use Yii;
use yii\gii\CodeFile;
use yii\helpers\Inflector;


/**
 * 继承原有的model生成类
 * 添加生成语言文件
 */
class Generator extends \yii\gii\generators\model\Generator
{
    //设定语言文件的路径，这里固定死了位置，如果想更灵活，则需要gii更多的改动
    public $messagePath = 'backend/messages/zh-CN';

    /**
     * @inheritdoc
     */
    public function generate()
    {
        $files = [];
        $relations = $this->generateRelations();
        $db = $this->getDbConnection();
        foreach ($this->getTableNames() as $tableName) {
            // model :
            $modelClassName = $this->generateClassName($tableName);
            $queryClassName = ($this->generateQuery) ? $this->generateQueryClassName($modelClassName) : false;
            $tableSchema = $db->getTableSchema($tableName);
            $params = [
                'tableName' => $tableName,
                'className' => $modelClassName,
                'queryClassName' => $queryClassName,
                'tableSchema' => $tableSchema,
                'labels' => $this->generateLabels($tableSchema),
                'rules' => $this->generateRules($tableSchema),
                'relations' => isset($relations[$tableName]) ? $relations[$tableName] : [],
            ];
            $files[] = new CodeFile(
                Yii::getAlias('@' . str_replace('\\', '/', $this->ns)) . '/' . $modelClassName . '.php',
                $this->render('model.php', $params)
            );

            // query :
            if ($queryClassName) {
                $params['className'] = $queryClassName;
                $params['modelClassName'] = $modelClassName;
                $files[] = new CodeFile(
                    Yii::getAlias('@' . str_replace('\\', '/', $this->queryNs)) . '/' . $queryClassName . '.php',
                    $this->render('query.php', $params)
                );
            }

            //增加生成i18n的语言文件
            if ($this->enableI18N) {//如果勾选
                //从注释中作为要翻译的语言
                $params = [
                    'messages' => $this->generateCommentMessage($tableSchema),
                ];
                //生成的文件路径及名称、跳转模板
                $files[] = new CodeFile(
                    Yii::getAlias('@' . $this->messagePath) . '/' . $this->messageCategory . '.php',
                    $this->render('message.php', $params)
                );
            }
        }

        return $files;
    }

    /**
     * 对于生成语言文件来说，都优先从注释中获取对应的翻译名称
     * @param $table
     * @return array
     */
    public function generateCommentMessage($table)
    {
        $message = [];
        foreach ($table->columns as $column) {
            //键与i18n自动生成的一致
            if (!strcasecmp($column->name, 'id')) {
                $key = 'ID';
            } else {
                $key = Inflector::camel2words($column->name);
                if (!empty($key) && substr_compare($key, ' id', -3, 3, true) === 0) {
                    $key = substr($key, 0, -3) . ' ID';
                }
            }
            //值从注释中获取
            if (!empty($column->comment)) {
                $message[$key] = $column->comment;
            } else {//如果没有注释，则为空
                $message[$key] = '';
            }
        }
        return $message;
    }
}
