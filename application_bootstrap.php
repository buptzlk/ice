<?php

/**
 * Created by PhpStorm.
 * Author by a buptzlk 
 * Email <17888835130@163.com>
 * Date: 17/8/24
 * Time: 16:16
 */

date_default_timezone_set('Asia/Shanghai');


require_once(APPLICATION_ROOT . '/framework/autoload/Loader.php');

require_once(APPLICATION_ROOT . '/conf/Facade.php');

Lazy_Loader::initialize(APPLICATION_ROOT);

/**
 * 启动IoC容器, 在容器中注入cgi请求需要的类
 * 如果你的类不想让IoC容器管理，ice框架也支持new操作符
 */
$app = new Framework_Library_Application();

$application = $app->make(Framework_Library_Concrete_Request_Route::class);


$application->dispatch();


