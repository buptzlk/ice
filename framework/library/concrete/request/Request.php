<?php

/**
 * Class Framework_Uri_Request
 * @autor by zhulukun
 * @email 17888835130@163.com
 * @breif 请求类
 *
 */
use Framework_Library_Contracts_Request_Request as Request;
class Framework_Library_Concrete_Request_Request implements Request {

    /**
     * @brief 获取get请求参数
     * @param array()
     * @return
     */
    public function get() {

        return $_GET;

    }

    /**
     * @brief 获取post请求参数
     * @param array()
     * @return
     */
    public function post() {

        return $_POST;

    }

    /**
     * @brief 获取request请求参数
     * @param array()
     * @return
     */
    public function request() {

        return $_REQUEST;

    }

    /**
     * @brief 获取uri
     * @param
     * @return
     */
    public function uri() {

        return $_SERVER['REQUEST_URI'];

    }

    /**
     * @brief 获取路由信息
     * @param
     * @return string
     */
    public function action() {

        $action = ucfirst(Conf_Env::ACTION).'_';

        $position = strpos($_SERVER['REQUEST_URI'],'?');

        if($position !== false) {
            $originUri = substr($_SERVER['REQUEST_URI'], 0 , $position);
        } else {
            $originUri = $_SERVER['REQUEST_URI'];
        }

        $uri = trim($originUri, DIRECTORY_SEPARATOR);

        if(empty($uri)) {
            return $action.Conf_Env::DEFAULTREQUEST;
        }

        return $action.str_replace(' ','_',ucwords(str_replace(DIRECTORY_SEPARATOR,' ', $uri)));
    }

    /**
     * @brief 文件路径
     * @param
     * @return
     */
    public function path() {

        $action = Conf_Env::ACTION.DIRECTORY_SEPARATOR;

        $originUri = substr($_SERVER['REQUEST_URI'], 0 ,  strpos($_SERVER['REQUEST_URI'],'?'));

        $uri = trim($originUri, DIRECTORY_SEPARATOR);

        if(empty($uri)) {
            return $action.Conf_Env::DEFAULTREQUEST.'.php';
        }

        $uriArray = explode(DIRECTORY_SEPARATOR, $uri);

        $uriArray[count($uriArray) - 1] = ucfirst($uriArray[count($uriArray) - 1]);

        return implode(DIRECTORY_SEPARATOR, $uriArray).'.php';

    }
    /**
     * @brief 获取请求方式
     * @param array()
     * @return
     */
    public function method() {

        return $_SERVER['REQUEST_METHOD'];

    }

}
