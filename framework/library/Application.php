<?php

use Framework_Library_Concrete_Container_Container as Container;

class Framework_Library_Application extends Container
{
    const VERSION = '1.0';

    /**
     * @var string
     */
    protected $basePath;


    public function __construct($basePath = null)
    {
        $this->registerBaseBindings();

        $this->registerMainClass();
    }

    /**
     * 返回版本号.
     *
     * @return string
     */
    public function version()
    {
        return static::VERSION;
    }

    /**
     * Register the basic bindings into the container.
     * 将基本的类绑定到容器
     * @return void
     */
    protected function registerBaseBindings()
    {
        static::setInstance($this);

        $this->instance('app', $this);

        $this->instance(Container::class, $this);
    }

   /**
    * 将重要的类加载进容器
    * @param null
    * @return void
    */
   protected function registerMainClass() {
       $this->singleton(Framework_Library_Contracts_Request_Request::class, Framework_Library_Concrete_Request_Request::class);
       $this->singleton(Framework_Library_Concrete_Request_Route::class, Framework_Application::class);
       $this->singleton(Framework_Library_Contracts_Pipeline_Pipeline::class, Framework_Library_Concrete_Pipeline_Pipeline::class);
   }

   /**
    * 加载过滤器
    *
    * @param
    * @return
    */
   protected function registerPipe() {

       $this->instance(Http_Pipe_Login::class, Http_Pipe_Login::class);
       $this->instance(Http_Pipe_AgeAuth::class, Http_Pipe_AgeAuth::class);

   }

}

