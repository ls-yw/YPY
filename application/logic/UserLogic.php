<?php

namespace logic;

use Basic\BasicLogic;
use library\Helper;
use library\Redis;
use library\YpyException;
use models\User;
use models\UserBalance;
use models\UserBalanceFlow;
use models\UserBalanceRecharge;
use models\UserRelation;

class UserLogic extends BasicLogic
{

    /**
     * 登录
     *
     * @author yls
     * @param string $mobile
     * @param string $password
     * @return bool
     * @throws YpyException
     */
    public function login(string $mobile, string $password)
    {
        $user = (new User())->getOne(['mobile' => $mobile]);
        if (!$user) {
            throw new YpyException('用户不存在');
        }

        if (md5($password) !== $user['password']) {
            throw new YpyException('密码错误');
        }

        $token = sha1($user['id']);
        Redis::getInstance()->setex($token, 86400 * 30, Helper::jsonEncode($user));

        return $token;
    }

    /**
     * 获取财务大人列表
     *
     * @author yls
     * @param int $uid
     * @return array
     * @throws \Exception
     */
    public function getFinancer(int $uid)
    {
        $res = (new UserRelation())->getList(['uid' => $uid, 'relation_status' => 1]);
        return $res;
    }

    /**
     * 获取用户pairs
     *
     * @author yls
     * @return array
     * @throws \Exception
     */
    public function getPairs()
    {
        $res = (new User())->getList([]);
        $arr = [];
        if (!empty($res)) {
            foreach ($res as $val) {
                $arr[$val['id']] = $val['realname'];
            }
        }
        return $arr;
    }

    /**
     * 获取余额
     *
     * @author yls
     * @param int $uid
     * @return string
     */
    public function getBalance(int $uid)
    {
        $balance = (new UserBalance())->getSum(['uid' => $uid], ['balance']);
        return Helper::moneyToShow((int) $balance['balance_sum']);
    }

    /**
     * 充值申请
     *
     * @author yls
     * @param int $uid
     * @param int $amount
     * @return bool|int
     * @throws YpyException
     */
    public function recharge(int $uid, int $amount)
    {
        // 获取充值对象
        $users = (new UserRelation())->getAll(['to_uid' => $uid]);
        if (count($users) != 1) {
            throw new YpyException('暂不支持多个对象的充值');
        }
        $toUid = current($users)['uid'];
        $data  = [
            'uid'             => $uid,
            'to_uid'          => $toUid,
            'amount'          => $amount,
            'recharge_status' => 1,
        ];
        return (new UserBalanceRecharge())->insertData($data);
    }

    /**
     * 获取充值列表
     *
     * @author yls
     * @param int $uid
     * @param int $page
     * @return array|bool
     * @throws \Exception
     */
    public function getRechargeList(int $uid, int $page)
    {
        $limit  = 10;
        $offset = ($page - 1) * $limit;
        $list   = (new UserBalanceRecharge())->getList(['uid' => ['or', 'uid' => $uid, 'to_uid' => $uid]], 'id desc', $offset, $limit);
        if (!empty($list)) {
            $users = $this->getPairs();
            foreach ($list as &$val) {
                $val['uid_name']    = $users[$val['uid']] ?? '';
                $val['to_uid_name'] = $users[$val['to_uid']] ?? '';
                $val['amount']      = Helper::moneyToShow($val['amount']);
                $val['hand']        = (1 === (int) $val['recharge_status'] && (int) $val['to_uid'] === $uid) ? 1 : 0;
            }
        }
        $count = (new UserBalanceRecharge())->getCount(['uid' => ['or', 'uid' => $uid, 'to_uid' => $uid]]);
        return ['list' => $list, 'count' => $count, 'totalPage' => ceil($count / $limit)];
    }

    /**
     * 变更充值记录状态
     *
     * @author yls
     * @param int $uid
     * @param int $id
     * @param int $status
     * @return bool|int
     * @throws YpyException
     */
    public function rechargeStatus(int $uid, int $id, int $status)
    {
        $info = (new UserBalanceRecharge())->getOne(['id' => $id, 'to_uid' => $uid]);
        if (empty($info)) {
            throw new YpyException('找不到数据');
        }
        $row = (new UserBalanceRecharge())->updateData(['recharge_status' => $status], ['id' => $id]);

        if (2 === $status && $row) {
            $amount      = $info['amount'];
            $userBalance = (new UserBalance())->getOne(['uid' => $info['uid'], 'to_uid' => $info['to_uid']]);
            if (empty($userBalance)) {
                $userBalance = ['uid' => $info['uid'], 'to_uid' => $info['to_uid'], 'balance' => $amount];
                (new UserBalance())->insertData($userBalance);
            } else {
                $userBalance['balance'] += $amount;
                (new UserBalance())->updateData(['balance' => $userBalance['balance']], ['uid' => $info['uid'], 'to_uid' => $info['to_uid']]);
            }

            (new UserBalanceFlow())->insertData(['uid' => $info['uid'], 'to_uid' => $info['to_uid'], 'type' => 1, 'amount' => $amount, 'balance' => $userBalance['balance']]);
        }

        return $row;
    }

    /**
     * 流水
     *
     * @author yls
     * @param int $uid
     * @param int $page
     * @param int $row
     * @return array
     * @throws \Exception
     */
    public function getRechargeFlow(int $uid, int $page, int $row)
    {
        $offset = ($page - 1) * $row;
        $list   = (new UserBalanceFlow())->getList(['uid' => $uid], 'id desc', $offset, $row);
        if (!empty($list)) {
            $users = $this->getPairs();
            foreach ($list as &$val) {
                $val['uid_name']    = $users[$val['uid']] ?? '';
                $val['to_uid_name'] = $users[$val['to_uid']] ?? '';
                $val['amount']      = Helper::moneyToShow($val['amount']);
                $val['balance']     = Helper::moneyToShow($val['balance']);
            }
        }
        $count = (new UserBalanceFlow())->getCount(['uid' => $uid]);
        return ['list' => $list, 'count' => $count, 'totalPage' => ceil($count / $row)];
    }

}