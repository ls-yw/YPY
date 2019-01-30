<?php
namespace app\controllers;

use Upload\File;
use app\library\Dir;
use app\basic\BasicController;
use woodlsy\upload\Upload;

class UploadController extends BasicController
{
    public $enableCsrfValidation = false;
    
    public function actionImg() {
        try {
            $path = \Yii::$app->basePath.'/web/upload/'.date('Ymd');
            Dir::directory($path);
            
            $data = (new Upload())->setFieldName('file')->setMaxSize('1M')->setUploadPath($path)->upload();
            return $this->ajaxReturn(0, '上传成功', '/upload/'.date('Ymd').'/'.$data['name']);
        } catch (\Exception $e) {
            // Fail!
            $errors = $e->getMessage();
            return $this->ajaxReturn(1, '图片上传失败');
        }
    }
}