<?php

namespace Controllers;

use Basic\BasicController;
use Exception;
use library\ErrorCode;
use library\Log;
use library\YpyException;
use logic\ConfigLogic;
use woodlsy\upload\Upload;

class UploadController extends BasicController
{

    /**
     * 上传图片
     *
     * @author yls
     * @return \Phalcon\Http\Response
     */
    public function imgAction()
    {
        try {
            $host = (new ConfigLogic())->getConfig('system', 'imgHost')['config_value'];
            $serverUrl = $host . '/upload/img?project=' . APP_NAME;
            $data      = (new Upload())->setMaxSize('1M')->setServerUrl($serverUrl)->upload();
            return $this->ajaxReturn(ErrorCode::SUCCESS, "ok", $host.'/'.$data['url']);
        } catch (YpyException $e) {
            return $this->ajaxReturn(ErrorCode::FAIL, $e->getMessage());
        } catch (Exception $e) {
            Log::write($this->controllerName . '|' . $this->actionName, $e->getMessage() . $e->getFile() . $e->getLine(), 'error');
            return $this->ajaxReturn(ErrorCode::FAIL, "系统错误");
        }
    }

}