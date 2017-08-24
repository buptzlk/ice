<?php
/**
 * 业务逻辑处理基类 
 */
class Framework_ActionBase {
    public $request = null;
    function __construct() {
        $this->request = Framework_Uri_Request::getInstance();
    }

}
