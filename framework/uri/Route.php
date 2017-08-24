<?php
/**
 * Created by PhpStorm.
 * User: FOREACH
 * Date: 17/8/5
 * Time: 下午11:59
 */
abstract class Framework_Uri_Route
{
    function __construct() {
        $this->request = Framework_Uri_Request::getInstance();

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
       /* $action_class = new Action_Index();
        $action_class->index();
        //action必须继承自BaseAction
        if (!$action_class instanceof BaseAction) {
            trigger_error("{$action_class_name} is not instanceof BaseAction", E_USER_ERROR);
            die();
        }*/
    }
}
