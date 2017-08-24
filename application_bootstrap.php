<?php
/**
 * Created by PhpStorm.
 * Author by a super man
 * Email <17888835130@163.com>
 * Date: 17/8/24
 * Time: 16:16
 */

date_default_timezone_set('Asia/Shanghai');

require_once(APPLICATION_ROOT . '/framework/autoload/Loader.php');
require(APPLICATION_ROOT . '/conf/Env.php');
// autoload class
Lazy_Loader::initialize(APPLICATION_ROOT);
// init application
$application_obj = Framework_Application::getInstance(APPLICATION_ROOT);
$application_obj->dispatch();
