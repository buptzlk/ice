<?php
/**
 * 业务逻辑处理基类 
 */
use Framework_Library_Concrete_Container_Container as Container;

class Framework_ActionBase {
    public $request = null;
    function __construct() {

        $this->request = Container::getInstance()
            ->make(Framework_Library_Contracts_Request_Request::class);
    }

}
