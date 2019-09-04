<?php

namespace Basic;

use library\Helper;
use library\Redis;
use Phalcon\DI;

class BasicLogic
{

    protected $admin = null;

    public $config = null;

    /**
     * 初始化
     *
     * @author yls
     */
    public function __construct()
    {
        $this->config = Di::getDefault()->get('config')->toArray();
        if (!defined('RUN_TYPE') || 'cli' !== RUN_TYPE) {
            $token = $this->getHeader('token');

            if (!empty($token) && Redis::getInstance()->exists($token)) {
                $admin       = Redis::getInstance()->get($token);
                $this->admin = Helper::jsonDecode($admin);
            }
        }
    }

    /**
     * 获取头部信息
     *
     * @author yls
     * @param string|null     $name header key
     * @param null|string|int $defaultValue 默认值
     * @return mixed
     */
    final protected function getHeader(string $name = null, $defaultValue = null)
    {
        $value = Di::getDefault()->get('request')->getHeader($name);
        if (null === $value && null !== $defaultValue) {
            return $defaultValue;
        }
        return $value;
    }
}