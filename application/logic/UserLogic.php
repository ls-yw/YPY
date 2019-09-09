<?php

namespace logic;

use Basic\BasicLogic;
use library\Helper;
use library\Redis;
use library\YpyException;
use models\User;
use models\UserBalance;
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
        Redis::getInstance()->setex($token, 86400, Helper::jsonEncode($user));

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
        $offset = ($page - 1) * 10;
        $list   = (new UserBalanceRecharge())->getList(['uid' => ['or', 'uid' => $uid, 'to_uid' => $uid]], 'id desc', $offset, 10);
        if (!empty($list)) {
            $users = $this->getPairs();
            foreach ($list as &$val) {
                $val['uid_name']    = $users[$val['uid']] ?? '';
                $val['to_uid_name'] = $users[$val['to_uid']] ?? '';
                $val['amount']      = Helper::moneyToShow($val['amount']);
            }
        }
        return $list;
    }

}