<?php
/*
 * Modified: prepend directory path of current file, because of this file own different ENV under between Apache and command line.
 * NOTE: please remove this comment.
 */

use Phalcon\Config;

return new Config([
    'application' => [
        'database'  => require_once APP_PATH.'/config/database.php',
        'redis'     => require_once APP_PATH.'/config/redis.php',
        'logsPath'  => '/data/logs/'.APP_NAME.'/',
        'viewsDir'  => APP_PATH.'/views',
    ]
]);
