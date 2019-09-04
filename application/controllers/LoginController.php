<?php
namespace Controllers;

use Basic\BasicController;
use library\ErrorCode;
use library\Helper;
use library\Log;
use library\YpyException;
use logic\UserLogic;

class LoginController extends BasicController
{
    public function loginAction()
    {
        try {
            $mobile   = $this->post('mobile', 'string', '');
            $password  = $this->post('password');

            if (true !== Helper::checkMobile($mobile)) {
                return $this->ajaxReturn(ErrorCode::FAIL, '手机号码错误');
            }

            if (6 > strlen($password)) {
                return $this->ajaxReturn(ErrorCode::FAIL, '密码错误');
            }

            $res = (new UserLogic())->login($mobile, $password);

            return self::ajaxReturn(ErrorCode::SUCCESS, "登录成功", ['token' => $res]);
        } catch (YpyException $e) {
            return self::ajaxReturn(ErrorCode::FAIL, $e->getMessage());
        } catch (\Exception $e) {
            Log::write($this->controllerName.'|'.$this->actionName, $e->getMessage() . $e->getFile() . $e->getLine(), 'error');
            return self::ajaxReturn(ErrorCode::FAIL, "系统错误");
        }
    }
}