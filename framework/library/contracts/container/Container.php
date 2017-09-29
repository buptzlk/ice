<?php

interface Framework_Library_Contracts_Container_Container
{
    /**
     * 判断类是否已经被注册过
     * @param  string  $abstract
     * @return bo
     */
    public function bound($abstract);

    /**
     * 在容器中注册别名
     *
     * @param  string  $abstract
     * @param  string  $alias
     * @return void
     */
    public function alias($abstract, $alias);

    /**
     * 给已经注册过的类打标记
     *
     * @param  array|string  $abstracts
     * @param  array|mixed   ...$tags
     * @return void
     */
    public function tag($abstracts, $tags);

    /**
     * 实例化所有打这个标记的类
     *
     * @param  array  $tag
     * @return array
     */
    public function tagged($tag);

    /**
     * 在容器中绑定类，如果之前绑定过，会重新绑定
     *
     * @param  string|array  $abstract
     * @param  \Closure|string|null  $concrete
     * @param  bool  $shared
     * @return void
     */
    public function bind($abstract, $concrete = null, $shared = false);

    /**
     * 在容器中绑定类，如果之前没有绑定过，绑定；如果绑定过，不再绑定
     *
     * @param  string  $abstract
     * @param  \Closure|string|null  $concrete
     * @param  bool  $shared
     * @return void
     */
    public function bindIf($abstract, $concrete = null, $shared = false);

    /**
     * 在容器中注册单例
     *
     * @param  string|array  $abstract
     * @param  \Closure|string|null  $concrete
     * @return void
     */
    public function singleton($abstract, $concrete = null);

    /**
     * 扩展已注册过的类
     *
     * @param  string    $abstract
     * @param  \Closure  $closure
     * @return void
     *
     */
    public function extend($abstract, Closure $closure);

    /**
     * 将一个已经实例化过的对象绑定到容器
     *
     * @param  string  $abstract
     * @param  mixed   $instance
     * @return void
     */
    public function instance($abstract, $instance);

    /**
     * 根据具体情景实例化对象
     *
     * @param  string  $concrete
     */
    public function when($concrete);

    /**
     * Get a closure to resolve the given type from the container.
     *
     * @param  string  $abstract
     * @return \Closure
     */
    public function factory($abstract);

    /**
     * Resolve the given type from the container.
     *
     * @param  string  $abstract
     * @return mixed
     */
    public function make($abstract);

    /**
     * Call the given Closure / class@method and inject its dependencies.
     *
     * @param  callable|string  $callback
     * @param  array  $parameters
     * @param  string|null  $defaultMethod
     * @return mixed
     */
    public function call($callback, array $parameters = [], $defaultMethod = null);

    /**
     * Determine if the given abstract type has been resolved.
     *
     * @param  string $abstract
     * @return bool
     */
    public function resolved($abstract);

    /**
     * Register a new resolving callback.
     *
     * @param  string    $abstract
     * @param  \Closure|null  $callback
     * @return void
     */
    public function resolving($abstract, Closure $callback = null);

    /**
     * Register a new after resolving callback.
     *
     * @param  string    $abstract
     * @param  \Closure|null  $callback
     * @return void
     */
    public function afterResolving($abstract, Closure $callback = null);
}

