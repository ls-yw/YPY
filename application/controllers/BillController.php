<?php

namespace Controllers;

use Basic\BasicController;
use library\ErrorCode;
use library\Helper;
use library\Log;
use library\YpyException;
use logic\BillLogic;
use logic\UserLogic;

class BillController extends BasicController
{
    /**
     * 保存报销
     *
     * @author yls
     * @return \Phalcon\Http\Response
     */
    public function setExpenseAction()
    {
        try {
            $id      = $this->post('id');
            $toUid   = $this->post('to_uid');
            $cateId  = $this->post('cate_id');
            $title   = $this->post('title');
            $content = $this->post('content');
            $atDate  = $this->post('at_date');
            $price   = $this->post('price');
            $imgs    = $this->post('imgs');
            $type    = $this->post('type', 'string', 'expense');
            if ('income' !== $type) {
                $type = 'expense';
            }

            if (empty($toUid)) {
                throw new YpyException('请选择财务大人');
            }

            if (empty($cateId)) {
                throw new YpyException('请选择类别');
            }

            if (empty($title)) {
                throw new YpyException('请填写标题');
            }

            if (empty($atDate)) {
                throw new YpyException('请选择费用时间');
            }

            if (empty($price)) {
                throw new YpyException('请填写报销金额');
            }

            $data = [
                'id'      => $id,
                'uid'     => $this->uid,
                'to_uid'  => $toUid,
                'cate_id' => $cateId,
                'title'   => $title,
                'content' => $content,
                'price'   => $price,
                'at_type' => $type,
                'at_date' => $atDate,
                'imgs'    => $imgs,
            ];

            (new BillLogic())->saveExpense($data);

            return self::ajaxReturn(ErrorCode::SUCCESS, "保存成功");
        } catch (YpyException $e) {
            return self::ajaxReturn(ErrorCode::FAIL, $e->getMessage());
        } catch (\Exception $e) {
            Log::write($this->controllerName . '|' . $this->actionName, $e->getMessage() . $e->getFile() . $e->getLine(), 'error');
            return self::ajaxReturn(ErrorCode::FAIL, "系统错误");
        }
    }

    /**
     * 报销列表
     *
     * @author yls
     * @return \Phalcon\Http\Response
     */
    public function expenseAction()
    {
        try {
            $page = (int) $this->get('page', 'int', 1);
            $size = (int) $this->get('size', 'int', 5);
            $list = (new BillLogic())->expense($this->uid, $page, $size);

            return self::ajaxReturn(ErrorCode::SUCCESS, "ok", $list);
        } catch (YpyException $e) {
            return self::ajaxReturn(ErrorCode::FAIL, $e->getMessage());
        } catch (\Exception $e) {
            Log::write($this->controllerName . '|' . $this->actionName, $e->getMessage() . $e->getFile() . $e->getLine(), 'error');
            return self::ajaxReturn(ErrorCode::FAIL, "系统错误");
        }
    }

    /**
     * 获取详情
     *
     * @author yls
     * @return \Phalcon\Http\Response
     */
    public function infoAction()
    {
        try {
            $id = (int) $this->get('id');

            if (empty($id)) {
                throw new YpyException('参数错误');
            }

            $info = (new BillLogic())->info($id);

            return self::ajaxReturn(ErrorCode::SUCCESS, "ok", $info);
        } catch (YpyException $e) {
            return self::ajaxReturn(ErrorCode::FAIL, $e->getMessage());
        } catch (\Exception $e) {
            Log::write($this->controllerName . '|' . $this->actionName, $e->getMessage() . $e->getFile() . $e->getLine(), 'error');
            return self::ajaxReturn(ErrorCode::FAIL, "系统错误");
        }
    }

    public function changeAction()
    {
        try {
            $id     = (int) $this->post('id');
            $status = (int) $this->post('status');

            if (empty($id) || empty($status)) {
                throw new YpyException('参数错误');
            }

            $info = (new BillLogic())->changeExpenseStatus($this->uid, $id, $status);

            return self::ajaxReturn(ErrorCode::SUCCESS, "更改成功", $info);
        } catch (YpyException $e) {
            return self::ajaxReturn(ErrorCode::FAIL, $e->getMessage());
        } catch (\Exception $e) {
            Log::write($this->controllerName . '|' . $this->actionName, $e->getMessage() . $e->getFile() . $e->getLine(), 'error');
            return self::ajaxReturn(ErrorCode::FAIL, "系统错误");
        }
    }
}