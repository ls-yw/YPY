<?php
namespace Controllers;

use Basic\BasicController;
use library\ErrorCode;
use library\Log;
use library\YpyException;
use logic\UserLogic;
use Exception;

class UserController extends BasicController
{
    /**
     * 获取财务大人列表
     *
     * @author yls
     * @return \Phalcon\Http\Response
     */
    public function getFinancerAction()
    {
        try {
            $financer = (new UserLogic())->getFinancer((int) $this->uid);
            $pairs = [];
            if (!empty($financer)) {
                $users = (new UserLogic())->getPairs();
                foreach ($financer as $val) {
                    $pairs[] = ['value' => $val['to_uid'], 'name' => $users[$val['to_uid']] ?? ''];
                }
            }
            return $this->ajaxReturn(ErrorCode::SUCCESS, "ok", $pairs);
        } catch (YpyException $e) {
            return $this->ajaxReturn(ErrorCode::FAIL, $e->getMessage());
        } catch (Exception $e) {
            Log::write($this->controllerName.'|'.$this->actionName, $e->getMessage() . $e->getFile() . $e->getLine(), 'error');
            return $this->ajaxReturn(ErrorCode::FAIL, "系统错误");
        }
    }
}