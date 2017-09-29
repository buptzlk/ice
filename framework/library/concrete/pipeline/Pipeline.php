<?php

use Framework_Library_Contracts_Pipeline_Pipeline as PipelineContract;
use Framework_Library_Concrete_Container_Container as Container;

class Framework_Library_Concrete_Pipeline_Pipeline implements PipelineContract
{
    /**
     * 容器
     */
    protected $container;

    /**
     * 管道中传递的值
     *
     * @var mixed
     */
    protected $passable;

    /**
     * 管道门阀组
     *
     * @var array
     */
    protected $pipes = [];

    /**
     * 调用管道方法
     *
     * @var string
     */
    protected $method = 'handle';

    /**
     * @param
     * @return
     */
    public function __construct(Container $container = null)
    {
        $this->container = $container;
    }

    /**
     * 发送请求.
     *
     * @param  mixed  $passable
     * @return $this
     */
    public function send($passable)
    {
        $this->passable = $passable;

        return $this;
    }

    /**
     * 经过的门阀组
     *
     * @param  array|mixed  $pipes
     * @return $this
     */
    public function through($pipes)
    {
        $this->pipes = is_array($pipes) ? $pipes : func_get_args();

        return $this;
    }

    /**
     * 设置方法
     *
     * @param  string  $method
     * @return $this
     */
    public function via($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * 运行管道
     *
     * @param  \Closure  $destination
     * @return mixed
     */
    public function then(Closure $destination)
    {
        $pipeline = array_reduce(
            array_reverse($this->pipes), $this->carry(), $this->prepareDestination($destination)
        );

        return $pipeline($this->passable);
    }

    /**
     * 获得一个闭包
     *
     * @param  \Closure  $destination
     * @return \Closure
     */
    protected function prepareDestination(Closure $destination)
    {
        return function ($passable) use ($destination) {
            return $destination($passable);
        };
    }

    /**
     * 调用管道门阀
     *
     * @return \Closure
     */
    protected function carry()
    {
        return function ($stack, $pipe) {
            return function ($passable) use ($stack, $pipe) {
                if ($pipe instanceof Closure) {
                    // pipe为闭包，通过调用
                    return call_user_func($pipe, $passable, $stack);
                } elseif (! is_object($pipe)) {
                    list($name, $parameters) = $this->parsePipeString($pipe);
                    // 如果pipe是字符串，从容器中获取其对应的类
                    $pipe = $this->getContainer()->make($name);
                    $parameters = array_merge([$passable, $stack], $parameters);
                } else {
                    // 如果传入的pipe是对象，直接调用handle即可
                    $parameters = [$passable, $stack];
                }

                return $pipe->{$this->method}(...$parameters);
            };
        };
    }

   /**
    * 解析字符串获取类名和参数
    *
    * @param  string $pipe
    * @return array
    */
    protected function parsePipeString($pipe)
    {
        list($name, $parameters) = array_pad(explode(':', $pipe, 2), 2, []);

        if (is_string($parameters)) {
            $parameters = explode(',', $parameters);
        }
        return [$name, $parameters];
    }

    /**
     * 获取容器实例
     *
     * @return Container
     * @throws \RuntimeException
     */
    protected function getContainer() {

        $this->container = Container::getInstance();
        if (! $this->container) {
            throw new RuntimeException('container is not initial');
        }

        return $this->container;
    }
}

