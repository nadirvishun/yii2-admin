<?php
/**
 * fileInput上传独立控制器
 * 同步上传
 * 前端设置：'uploadAsync' => false
 * 如果是多图，必须必须设置maxFiles规则不为1，且上传字段最后加[],例如Post['img'][]
 * 如果是单图，则不需要设置maxFiles，且上传字段不要加[]
 * 目前项目未采用此种上传，如果要用，还需要更改各种回调事件中的逻辑
 */

namespace common\components;

use Yii;
use yii\base\Action;
use yii\base\DynamicModel;
use yii\helpers\FileHelper;
use yii\helpers\Url;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * ajax上传文件action
 */
class UploadSyncAction extends Action
{
    /**
     * 上传字段名称
     * 可以不设置，在上传时传递name参数也可以，优先使用上传时的name参数
     * @var string
     */
    public $name;
    /**
     * 保存路径
     * @var string
     */
    public $path;
    /**
     * 验证规则
     * ['extensions'=>'png,jpg,gif','maxFiles' => 2,...]
     * @var array
     */
    public $rule = [];

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        //关闭csrf
        Yii::$app->request->enableCsrfValidation = false;
        //默认名称
        if (empty($this->name)) {
            $this->name = 'file_data';
        }
        //默认路径
        if (empty($this->path)) {
            $this->path = Yii::$app->params['defaultPath'];
        }
    }

    /**
     * 运行
     * @throws \yii\base\Exception
     */
    public function run()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $action = Yii::$app->request->get('action');
            if ($action == 'upload') {
                //如果是上传
                return $this->upload();
            } elseif ($action == 'delete') {
                //如果是删除
                return $this->delete();
            } else {
                //参数错误
                return ['error' => Yii::t('common', 'Invalid Parameter')];
            }
        }
    }

    /**
     * 上传文件
     * 由于用的是kartik的yii2-widget-fileinput组件，所以需要返回组件需要的格式
     * @throws \yii\base\Exception
     */
    public function upload()
    {
        //文件字段名称
        $name = Yii::$app->request->post('name');
        if (empty($name)) {
            $name = $this->name;
        }
        //上传的文件,判断是单图上传还是多图上传
        if (isset($this->rule['maxFiles']) && ($this->rule['maxFiles'] != 1 || $this->rule['maxFiles'] > 1)) {
            $fileInstances = UploadedFile::getInstancesByName($name);
            $uploadFile = $fileInstances;
        } else {
            $fileInstance = UploadedFile::getInstanceByName($name);
            $uploadFile = $fileInstance;
            $fileInstances[0] = $fileInstance;
        }
        //验证文件上传
        $model = new DynamicModel([$name => $uploadFile]);
        $model->addRule($name, 'file', $this->rule)->validate();
        if ($model->hasErrors()) {
            $error = $model->getFirstError($name);
            return ['error' => $error];
        }
        //如果没有目录，则创建目录
        FileHelper::createDirectory(Yii::getAlias('@webroot') . $this->path);
        $saveFiles = [];
        $configs = [];
        foreach ($fileInstances as $key => $fileInstance) {
            //保存文件
            $newName = time() . rand(1000, 9999) . $key;//文件重命名
            if (!$fileInstance->saveAs(Yii::getAlias('@webroot') . $this->path . $newName . '.' . $fileInstance->extension)) {
                $uploadError = Yii::t('common', 'Upload failed!');
                //如果一个出错，将所有上传的都删除掉
                foreach ($saveFiles as $item) {
                    @unlink(Yii::getAlias('@webroot') . $item);
                }
                return ['error' => $uploadError];
            }
            //返回正确信息
            $saveFile = $this->path . $newName . '.' . $fileInstance->extension;
            $saveFiles[] = $saveFile;
            $configs[] = [
                'caption' => $newName . '.' . $fileInstance->extension,
                'url' => Url::to(['upload', 'action' => 'delete']),
                //todo,后续如果用数据库存储，则需要返回对应的id，方便删除
                'key' => $saveFile,
            ];
        }
        return [
            'initialPreview' => $saveFiles, //必须返回数据才能调用ajax删除
            'initialPreviewConfig' => $configs,
            'keys' => $saveFiles//单独自定义，不用上面的值了
        ];
    }

    /**
     * 删除文件
     */
    public function delete()
    {
        $key = Yii::$app->request->post('key');
        @unlink(Yii::getAlias('@webroot') . $key);
        //todo,后续如果用数据库存储，需删除数据，可能返回错误什么的['error'=>'error message']
        return [
            'key' => $key
        ];
    }
}