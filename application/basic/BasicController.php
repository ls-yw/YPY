<?php

namespace Basic;

use library\ErrorCode;
use library\Helper;
use library\Redis;
use library\YpyException;
use Phalcon\Di;
use Phalcon\Http\Response;
use Phalcon\Mvc\Controller;
use Phalcon\Http\Request;
use Phalcon\Security;

/**
 * Class BasicController
 *
 * @author yls
 * @package Basic
 * @property Request  request
 * @property Response response
 * @property Security security
 */
class BasicController extends Controller
{

    protected $moduleName     = null;
    protected $controllerName = null;
    protected $actionName     = null;

    protected $token;
    protected $user = null;
    protected $uid = null;
    protected $systemConfig = [];

    /**
     * 初始化
     *
     * @author yls
     * @throws YpyException
     */
    public function initialize()
    {
        $this->controllerName = $this->router->getControllerName();
        $this->actionName     = $this->router->getActionName();

        $this->token = $this->getHeader('token');

        $this->setUser();

        $this->checkLogin();
    }

    /**
     * 设置登录用户
     *
     * @author yls
     */
    protected function setUser()
    {
        if (Redis::getInstance()->exists($this->token)) {
            $user = Helper::jsonDecode(Redis::getInstance()->get($this->token));
            $this->uid = (int)$user['id'];
            $this->user = $user;
        }
    }

    /**
     * 检测是否登录
     *
     * @author yls
     * @throws YpyException
     */
    protected function checkLogin()
    {
        if ('login' === $this->controllerName) {
            return;
        }
        if (!$this->isLogin()) {
            throw new YpyException('未登录', ErrorCode::NO_LOGIN);
        }
    }

    /**
     * 是否登录
     *
     * @author yls
     * @return bool
     */
    public function isLogin()
    {
        if (null === $this->user) {
            return false;
        }
        return true;
    }

    /**
     * Ajax方式返回数据到客户端
     *
     * @author yls
     * @param int        $code 状态码
     * @param string     $msg 提示语
     * @param null       $data 要返回的数据
     * @param array|null $other 额外数据，和code同一级
     * @param string     $type AJAX返回数据格式
     * @return Response
     */
    protected function ajaxReturn(int $code, string $msg, $data = null, array $other = null, $type = 'json') : Response
    {
        $returnMsg = ['code' => $code, 'msg' => $msg];
        if (null !== $data) $returnMsg['data'] = $data;

        if ($other && is_array($other)) {
            foreach ($other as $key => $val) {
                $returnMsg[$key] = $val;
            }
        }

        switch (strtoupper($type)) {
            case 'JSON' :
                return $this->response->setJsonContent($returnMsg);
        }
    }

    /**
     * 获取get参数
     *
     * @author yls
     * @param string|null     $name get key
     * @param string|null     $filters 过滤方法
     * @param string|int|null $defaultValue 默认值
     * @return mixed
     */
    protected function get(string $name = null, string $filters = null, $defaultValue = null)
    {
        $filters = $filters ? : null;
        return Di::getDefault()->get('request')->getQuery($name, $filters, $defaultValue);
    }

    /**
     * 获取post参数
     *
     * @author yls
     * @param string|null     $name post key
     * @param string|null     $filters 过滤方法
     * @param string|int|null $defaultValue 默认值
     * @return mixed
     */
    protected function post(string $name = null, string $filters = null, $defaultValue = null)
    {
        return Di::getDefault()->get('request')->getPost($name, $filters, $defaultValue);
    }

    /**
     * 获取头部信息
     *
     * @author yls
     * @param string|null     $name header key
     * @param null|string|int $defaultValue 默认值
     * @return mixed
     */
    protected function getHeader(string $name = null, $defaultValue = null)
    {
        $value = $this->request->getHeader($name);
        if (null === $value && null !== $defaultValue) {
            return $defaultValue;
        }
        return $value;
    }

}