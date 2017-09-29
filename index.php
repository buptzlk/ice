<?php
/**
 * Created by PhpStorm.
 * Author by a super man
 * Email <17888835130@163.com>
 * Date: 17/8/24
 * Time: 16:16
 */
define('APPLICATION_ROOT',	dirname(__FILE__));

/**
 *
 * ice是一个简单优雅的php框架, 它利用了autoload思想，实现了类的自动加载
 * 通过A_B_C的形式进入类，替代了命名空间，让代码看起来更加简洁。同时，框架引入了IoC
 * 思想，通过容器管理类和类与类之间的依赖，让项目开发更简单更优雅。
 * 从这里开启你的ice之旅吧。。。
 *
 */
define('APPLICATION_NAME', 'ice');

require(APPLICATION_ROOT . '/application_bootstrap.php');
