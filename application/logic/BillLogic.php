<?php

namespace logic;

use Basic\BasicLogic;
use library\YpyException;
use models\Category;
use models\Expense;
use models\ExpenseImg;
use models\User;
use models\UserBalance;
use models\UserBalanceFlow;

class BillLogic extends BasicLogic
{
    /**
     * 保存报销
     *
     * @author yls
     * @param array $data
     * @return bool
     * @throws \Exception
     */
    public function saveExpense(array $data)
    {
        $imgs = $data['imgs'];
        $id   = $data['id'];
        unset($data['id'], $data['imgs']);
        if (empty($id)) {
            $id = (new Expense())->insertData($data);
            if (empty($id)) {
                return false;
            }
        } else {
            $row = (new Expense())->updateData($data, ['id' => $id]);
            if (empty($row)) {
                return false;
            }
            (new ExpenseImg())->delData(['id' => $id]);
        }
        if (!empty($imgs)) {
            foreach ($imgs as $img) {
                $imgData = [
                    'expense_id' => $id,
                    'uid'        => $data['uid'],
                    'img_url'    => $img,
                    'at_type'    => 'exp',
                ];
                (new ExpenseImg())->insertData($imgData);
            }
        }
        return true;
    }

    /**
     * 报销列表
     *
     * @author yls
     * @param int $uid
     * @param     $page
     * @param     $size
     * @return array
     * @throws \Exception
     */
    public function expense(int $uid, $page, $size)
    {
        $limit  = $size;
        $offset = ($page - 1) * $limit;
        $list   = (new Expense())->getList(['deleted' => 0], 'at_status asc,id desc', $offset, $limit);
        if (!empty($list)) {
            $users = (new UserLogic())->getPairs();
            foreach ($list as &$val) {
                $val['realname']    = $users[$val['uid']] ?? '';
                $val['to_realname'] = $users[$val['to_uid']] ?? '';
                $val['role']        = $val['uid'] == $uid ? 'expenser' : 'financer';
                $val['imgs']        = (new ExpenseImg())->getList(['deleted' => 0, 'expense_id' => $val['id']]);
            }
        }
        $count = (new Expense())->getCount(['deleted' => 0]);

        return ['list' => $list, 'count' => $count, 'totalPage' => ceil($count / $limit)];
    }

    /**
     * 详情
     *
     * @author yls
     * @param int $id
     * @return array|mixed
     * @throws YpyException
     * @throws \Exception
     */
    public function info(int $id)
    {
        $info = (new Expense())->getById($id);
        if (!$info) {
            throw new YpyException('数据不存在');
        }
        $users               = (new UserLogic())->getPairs();
        $info['to_realname'] = $users[$info['to_uid']] ?? '';
        $info['imgs']        = (new ExpenseImg())->getList(['deleted' => 0, 'expense_id' => $info['id']]);
        return $info;
    }

    /**
     * 更改成功
     *
     * @author yls
     * @param int $uid
     * @param int $id
     * @param int $status
     * @return bool
     * @throws YpyException
     * @throws \Exception
     */
    public function changeExpenseStatus(int $uid, int $id, int $status)
    {
        $expense = (new Expense())->getById($id);
        if ($uid != $expense['uid'] && $uid != $expense['to_uid']) {
            throw new YpyException('无权限更改');
        }

        //是否有权限更改该状态
        $r = $this->checkPowerChangeExpense($uid, $expense, $status);
        if (!$r) {
            throw new YpyException('无权限更改');
        }

        if (3 === $status && 'expense' === $expense['at_type']) { // 扣款
            $userBalance = (new UserBalance())->getOne(['uid' => $expense['to_uid'], 'to_uid' => $expense['uid']]);
            if (($expense['price'] * 100) > (int) $userBalance['balance']) {
                throw new YpyException('余额不足');
            }
            $balance = $userBalance['balance'] - ($expense['price'] * 100);
            (new UserBalance())->updateData(['balance' => $balance], ['id' => $userBalance['id']]);
            (new UserBalanceFlow())->insertData(['uid' => $expense['to_uid'], 'to_uid' => $expense['uid'], 'type' => 2, 'amount' => ($expense['price'] * 100), 'balance' => $balance]);
            $status = 4;
        }

        $update = ['at_status' => $status];
        if ((new Expense())->updateData($update, ['id' => $id])) {
            return true;
        }
        throw new YpyException('更改失败');
    }

    /**
     * 判断是否又权限更改
     *
     * @author yls
     * @param $uid
     * @param $expense
     * @param $status
     * @return bool
     */
    public function checkPowerChangeExpense($uid, $expense, $status)
    {
        if ($uid == $expense['uid']) {  //报销者

            if ($expense['at_type'] == 'income') {
                if ($expense['at_status'] == 2 && in_array($status, [3, 5])) return true;
                if ($expense['at_status'] == 3 && in_array($status, [5])) return true;
            } else {
                if ($expense['at_status'] == 1 && in_array($status, [5])) return true;
                if ($expense['at_status'] == 2 && in_array($status, [5])) return true;
                if ($expense['at_status'] == 3 && in_array($status, [4, 2])) return true;
            }
        } else {  //财务大人
            if ($expense['at_type'] == 'income') {
                if ($expense['at_status'] == 1 && in_array($status, [2, 6])) return true;
                if ($expense['at_status'] == 3 && in_array($status, [4, 2])) return true;
            } else {
                if ($expense['at_status'] == 1 && in_array($status, [2, 6])) return true;
                if ($expense['at_status'] == 2 && in_array($status, [3, 6])) return true;
            }
        }
        return false;
    }

    /**
     * 获取分类
     *
     * @author yls
     * @param int $type
     * @return array|bool
     */
    public function getCategory(int $type)
    {
        $category = (new Category())->getList(['type' => $type]);
        return $category;
    }
}