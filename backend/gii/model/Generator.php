<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace backend\gii\model;

use Yii;
use yii\gii\CodeFile;


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

            //生成i18n的语言文件
            if ($this->enableI18N) {//如果勾选
                //需要labels参数
                $params = [
                    'labels' => $this->generateLabels($tableSchema),
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
}
