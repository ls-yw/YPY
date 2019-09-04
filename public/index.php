<?php

use library\ErrorCode;
use library\Helper;
use library\YpyException;
use Phalcon\Mvc\Application;
use Phalcon\Di\FactoryDefault;
use library\Log;

error_reporting(E_ALL);

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/application');
define('APP_NAME', 'ypy');
date_default_timezone_set('Asia/Shanghai');

define('APP_DEBUG', true);

$di = new FactoryDefault();

require BASE_PATH . '/vendor/autoload.php';

/**
 * Read services
 */
include APP_PATH . '/config/services.php';

/**
 * Include Autoloader
 */
include APP_PATH . '/config/loader.php';

Log::setTriggerError();

$debug = new \Phalcon\Debug();
$debug->listen();

try {
    // 创建应用
    $application = new Application($di);

    // 处理请求
    $response = $application->handle();

    $response->send();
} catch (YpyException $e) {
    header('Content-type: application/json');
    echo Helper::jsonEncode(['code' => (0 === (int) $e->getCode() ? ErrorCode::FAIL : $e->getCode()), 'msg' => $e->getMessage()]);
} catch (Exception $e) {
    if (true === APP_DEBUG) {
        header('Content-type: application/json');
        echo Helper::jsonEncode(['code' => ErrorCode::FAIL, 'msg' => $e->getMessage() . '|' . $e->getFile() . '|' . $e->getLine()]);
    } else {
        header('Content-type: application/json');
        Log::write('system', $e->getMessage() . '|' . $e->getFile() . '|' . $e->getLine(), 'error');
        echo Helper::jsonEncode(['code' => ErrorCode::FAIL, 'msg' => '系统错误，请联系管理员']);
    }
}