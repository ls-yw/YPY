<?php
namespace app\controllers;

use Upload\Storage\FileSystem;
use Upload\File;
use app\library\Dir;
use app\basic\BasicController;

class UploadController extends BasicController
{
    public $enableCsrfValidation = false;
    
    public function actionImg() {
        try {
            $path = \Yii::$app->basePath.'/web/upload/'.date('Ymd');
            Dir::directory($path);
            $storage = new FileSystem($path);
            $file = new File('file', $storage);
            $new_filename = uniqid();
            $file->setName($new_filename);
            
            $file->addValidations(array(
                // Ensure file is of type "image/png"
                new \Upload\Validation\Mimetype(array('image/png', 'image/gif', 'image/jpeg')),
            
                // Ensure file is no larger than 5M (use "B", "K", M", or "G")
                new \Upload\Validation\Size('1M')
            ));
            
//             $data = array(
//                 'name'       => $file->getNameWithExtension(),
//                 'extension'  => $file->getExtension(),
//                 'mime'       => $file->getMimetype(),    
//                 'size'       => $file->getSize(),
//                 'md5'        => $file->getMd5(),
//                 'dimensions' => $file->getDimensions()
//             );
            $file->upload();
            return $this->ajaxReturn(0, '上传成功', '/upload/'.date('Ymd').'/'.$file->getNameWithExtension());
        } catch (\Exception $e) {
            // Fail!
            $errors = $file->getErrors();
            return $this->ajaxReturn(1, '图片上传失败');
        }
    }
}