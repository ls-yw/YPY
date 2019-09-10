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
            $pairs    = [];
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
            Log::write($this->controllerName . '|' . $this->actionName, $e->getMessage() . $e->getFile() . $e->getLine(), 'error');
            return $this->ajaxReturn(ErrorCode::FAIL, "系统错误");
        }
    }

    /**
     * 获取账户余额
     *
     * @author yls
     * @return \Phalcon\Http\Response
     */
    public function infoAction()
    {
        try {
            $balance         = (new UserLogic())->getBalance((int) $this->uid);
            $user['balance'] = $balance;

            return $this->ajaxReturn(ErrorCode::SUCCESS, "ok", $user);
        } catch (YpyException $e) {
            return $this->ajaxReturn(ErrorCode::FAIL, $e->getMessage());
        } catch (Exception $e) {
            Log::write($this->controllerName . '|' . $this->actionName, $e->getMessage() . $e->getFile() . $e->getLine(), 'error');
            return $this->ajaxReturn(ErrorCode::FAIL, "系统错误");
        }
    }

    /**
     * 充值申请
     *
     * @author yls
     * @return \Phalcon\Http\Response
     */
    public function rechargeAction()
    {
        try {

            $amount = (float) $this->post('amount');
            $amount = $amount * 100;
            if (empty($amount)) {
                throw new YpyException('充值金额不能为0');
            }
            $res = (new UserLogic())->recharge($this->uid, $amount);
            if (!$res) {
                throw new YpyException('充值失败');
            }
            return $this->ajaxReturn(ErrorCode::SUCCESS, "ok");
        } catch (YpyException $e) {
            return $this->ajaxReturn(ErrorCode::FAIL, $e->getMessage());
        } catch (Exception $e) {
            Log::write($this->controllerName . '|' . $this->actionName, $e->getMessage() . $e->getFile() . $e->getLine(), 'error');
            return $this->ajaxReturn(ErrorCode::FAIL, "系统错误");
        }
    }

    /**
     * 充值列表
     *
     * @author yls
     * @return \Phalcon\Http\Response
     */
    public function rechargeListAction()
    {
        try {
            $page = (int) $this->get('page');
            $list = (new UserLogic())->getRechargeList($this->uid, $page);
            return $this->ajaxReturn(ErrorCode::SUCCESS, "ok", $list);
        } catch (YpyException $e) {
            return $this->ajaxReturn(ErrorCode::FAIL, $e->getMessage());
        } catch (Exception $e) {
            Log::write($this->controllerName . '|' . $this->actionName, $e->getMessage() . $e->getFile() . $e->getLine(), 'error');
            return $this->ajaxReturn(ErrorCode::FAIL, "系统错误");
        }
    }

    /**
     * 更改充值记录状态
     *
     * @author yls
     * @return \Phalcon\Http\Response
     */
    public function rechargeStatusAction()
    {
        try {
            $id     = (int) $this->post('id');
            $status = (int) $this->post('status');

            $row   = (new UserLogic())->rechargeStatus($this->uid, $id, $status);
            if (!$row) {
                throw new YpyException('变更失败');
            }
            return $this->ajaxReturn(ErrorCode::SUCCESS, "状态更改成功");
        } catch (YpyException $e) {
            return $this->ajaxReturn(ErrorCode::FAIL, $e->getMessage());
        } catch (Exception $e) {
            Log::write($this->controllerName . '|' . $this->actionName, $e->getMessage() . $e->getFile() . $e->getLine(), 'error');
            return $this->ajaxReturn(ErrorCode::FAIL, "系统错误");
        }
    }
}