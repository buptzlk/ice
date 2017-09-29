<?php
/**
 * 程序运行主要入口
 */
class Framework_Application extends Framework_Library_Concrete_Request_Route {
    function __construct() {
    
        parent::__construct();
    
    }

    public $application_root;

    private static $instance = null;

    public static function getInstance($application_root)
    {
        if (null === self::$instance) {

            self::$instance = new self();

            self::$instance->application_root = $application_root;

        }
        return self::$instance;
    }

}
