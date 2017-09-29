<?php
/**
 * Created by PhpStorm.
 * User: FOREACH
 * Date: 17/8/5
 * Time: 下午11:59
 */

use Framework_Library_Concrete_Container_Container as Container;

class Framework_Library_Concrete_Request_Route implements Framework_Library_Contracts_Request_Route
{
    function __construct() {
        $this->request = Container::getInstance()
            ->make(Framework_Library_Contracts_Request_Request::class);

    }


    /**
     * 执行当前请求
     * @param string $request_uri
     */
    public function dispatch()
    {
        $path = APPLICATION_ROOT.DIRECTORY_SEPARATOR.$this->request->path();

        $action = $this->request->action();

        $class = new $action();
        if($class instanceof Framework_ActionBase) {
            $class->index();
        }
    }
}
