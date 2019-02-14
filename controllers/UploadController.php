<?php
namespace app\controllers;

use app\basic\BasicController;
use woodlsy\upload\Upload;

class UploadController extends BasicController
{
    public $enableCsrfValidation = false;
    
    public function actionImg() {
        try {
            if(!isset($this->sytemConfig['imgHost']))return $this->ajaxReturn(2, '图片地址未配置');
            $serverUrl = $this->sytemConfig['imgHost'].'/upload/img?project='.APP_NAME;
            
            $data = (new Upload())->setMaxSize('1M')->setServerUrl($serverUrl)->upload();
            return $this->ajaxReturn(0, '上传成功', $this->sytemConfig['imgHost'].'/'.$data['url']);
        } catch (\Exception $e) {
            // Fail!
            $errors = $e->getMessage();
            \Yii::warning($errors);
            return $this->ajaxReturn(101, '图片上传失败');
        }
    }
}