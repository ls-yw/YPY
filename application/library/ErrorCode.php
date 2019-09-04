<?php

namespace library;

/**
 * 错误码
 *
 * @author yls
 * @package library
 */
class ErrorCode
{
    const SUCCESS       = 0; // 成功
    const FAIL          = 1; // 失败
    const NO_LOGIN      = 201; // 未登录
    const UNBIND_MOBILE = 101; //未绑定手机号
    const FORBIDDEN     = 403; // 非法请求
}