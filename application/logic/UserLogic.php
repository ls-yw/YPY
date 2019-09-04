<?php
namespace logic;

use Basic\BasicLogic;
use library\Helper;
use library\Redis;
use library\YpyException;
use models\User;
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

}