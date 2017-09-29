<?php
/**
 * Created by PhpStorm.
 * Author by asuper  man
 * Email <17888835130@163.com>
 * Date: 17/9/25
 * Time: 19:15
 */
use Framework_Library_Concrete_Facade_Facade as Facade;
use Framework_Library_Contracts_Request_Request as Request;

class Framework_Library_Concrete_Facade_Request extends Facade
{
    /**
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Request::class;
    }
}