<?php


use Framework_Library_Contracts_Container_ContextualBindingBuilder as ContextualBindingBuilderContract;

class Framework_Library_Concrete_Container_ContextualBindingBuilder implements ContextualBindingBuilderContract
{
    /**
     *
     */
    protected $container;

    /**
     * The concrete instance.
     *
     * @var string
     */
    protected $concrete;

    /**
     * The abstract target.
     *
     * @var string
     */
    protected $needs;

    /**
     * 创建上下文绑定构造器
     *
     * @param  string  $concrete
     * @return void
     */
    public function __construct(Container $container, $concrete)
    {
        $this->concrete = $concrete;
        $this->container = $container;
    }

    /**
     * 确定契约类需要的上下文对象
     *
     * @param  string  $abstract
     * @return $this
     */
    public function needs($abstract)
    {
        $this->needs = $abstract;

        return $this;
    }

    /**
     * Define the implementation for the contextual binding.
     *
     * @param  \Closure|string  $implementation
     * @return void
     */
    public function give($implementation)
    {
        $this->container->addContextualBinding(
            $this->concrete, $this->needs, $implementation
        );
    }
}

