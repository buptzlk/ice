<?php

use Framework_Library_Contracts_Container_Container as ContainerContract;
use Framework_Library_Concrete_Container_BoundMethod as BoundMethod;

class Framework_Library_Concrete_Container_Container implements ArrayAccess, ContainerContract {
    
    protected static $instance;

    /**
     *
     * @var array
     */
    protected $resolved = [];

    /**
     *
     * @var array
     */
    protected $bindings = [];

    /**
     *
     * @var array
     */
    protected $methodBindings = [];

    /**
     *
     * @var array
     */
    protected $instances = [];

    /**
     *
     * @var array
     */
    protected $aliases = [];

    /**
     *
     * @var array
     */
    protected $abstractAliases = [];

    /**
     *
     * @var array
     */
    protected $extenders = [];

    /**
     *
     * @var array
     */
    protected $tags = [];

    /**
     *
     * @var array
     */
    protected $buildStack = [];

    /**
     *
     * @var array
     */
    protected $with = [];

    /**
     *
     * @var array
     */
    public $contextual = [];

    /**
     *
     * @var array
     */
    protected $reboundCallbacks = [];

    /**
     *
     * @var array
     */
    protected $globalResolvingCallbacks = [];

    /**
     *
     * @var array
     */
    protected $globalAfterResolvingCallbacks = [];

    /**
     *
     * @var array
     */
    protected $resolvingCallbacks = [];

    /**
     *
     * @var array
     */
    protected $afterResolvingCallbacks = [];

    public function when($concrete)
    {
        return new ContextualBindingBuilder($this, $this->getAlias($concrete));
    }

    /**
     * 判断给定的类是否被绑定
     *
     * @param  string  $abstract
     * @return bool
     */
    public function bound($abstract)
    {
        return isset($this->bindings[$abstract]) ||
               isset($this->instances[$abstract]) ||
               $this->isAlias($abstract);
    }

    /**
     * 判断给定的类是否已经被解析
     *
     * @param  string  $abstract
     * @return bool
     */
    public function resolved($abstract)
    {
        if ($this->isAlias($abstract)) {
            $abstract = $this->getAlias($abstract);
        }

        return isset($this->resolved[$abstract]) ||
               isset($this->instances[$abstract]);
    }

    /**
     * Determine if a given type is shared.
     *
     * @param  string  $abstract
     * @return bool
     */
    public function isShared($abstract)
    {
        return isset($this->instances[$abstract]) ||
              (isset($this->bindings[$abstract]['shared']) &&
               $this->bindings[$abstract]['shared'] === true);
    }

    /**
     * 判断给定的字符串是否有别名
     *
     * @param  string  $name
     * @return bool
     */
    public function isAlias($name)
    {
        return isset($this->aliases[$name]);
    }

    /**
     * 将实例绑定到IoC
     *
     * @param  string|array  $abstract
     * @param  \Closure|string|null  $concrete
     * @param  bool  $shared
     * @return void
     */
    public function bind($abstract, $concrete = null, $shared = false)
    {

        // 如果绑定的concrete为空，则设置$concrete = $abstract；
        // shared为true时，绑定的实体类为单例

        // 移除旧的契约类
        $this->dropStaleInstances($abstract);

        if (is_null($concrete)) {
            $concrete = $abstract;
        }

        // 绑定实例支持类名绑定和闭包绑定，如果实体类不是一个闭包，则表明是类名绑定，我们可以将类名绑定转化为闭包的方式
        // 这样的话我们扩展的时候就会更方便
        if (! $concrete instanceof Closure) {
            // 闭包转换
            $concrete = $this->getClosure($abstract, $concrete);
        }

        $this->bindings[$abstract] = compact('concrete', 'shared');

        // 如果对应的类名已经被解析过
        if ($this->resolved($abstract)) {
            $this->rebound($abstract);
        }
    }

    /**
     * 获取闭包
     *
     * @param  string  $abstract
     * @param  string  $concrete
     * @return \Closure
     */
    protected function getClosure($abstract, $concrete)
    {
        return function ($container, $parameters = []) use ($abstract, $concrete) {
            if ($abstract == $concrete) {
                return $container->build($concrete);
            }

            return $container->makeWith($concrete, $parameters);
        };
    }

    /**
     * 判断容器是否存在方法绑定
     * @param  string  $method
     * @return bool
     */
    public function hasMethodBinding($method)
    {
        return isset($this->methodBindings[$method]);
    }

    /**
     * 在容器中绑定一个回调
     * @param  string  $method
     * @param  \Closure  $callback
     * @return void
     */
    public function bindMethod($method, $callback)
    {
        $this->methodBindings[$method] = $callback;
    }

    /**
     *
     * @param  string  $method
     * @param  mixed  $instance
     * @return mixed
     */
    public function callMethodBinding($method, $instance)
    {
        return call_user_func($this->methodBindings[$method], $instance, $this);
    }

    /**
     * 在容器中添加上下文相关的绑定
     *
     * @param  string  $concretex
     * @param  string  $abstract
     * @param  \Closure|string  $implementation
     * @return void
     */
    public function addContextualBinding($concrete, $abstract, $implementation)
    {
        $this->contextual[$concrete][$this->getAlias($abstract)] = $implementation;
    }

    /**
     * 如果类没有被绑定过，则绑定
     *
     * @param  string  $abstract
     * @param  \Closure|string|null  $concrete
     * @param  bool  $shared
     * @return void
     */
    public function bindIf($abstract, $concrete = null, $shared = false)
    {
        if (! $this->bound($abstract)) {
            $this->bind($abstract, $concrete, $shared);
        }
    }

    /**
     * 绑定单例
     *
     * @param  string|array  $abstract
     * @param  \Closure|string|null  $concrete
     * @return void
     */
    public function singleton($abstract, $concrete = null)
    {
        $this->bind($abstract, $concrete, true);
    }

    /**
     * 在容器中扩展一个抽象类
     * @param  string    $abstract
     * @param  \Closure  $closure
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    public function extend($abstract, Closure $closure)
    {
        $abstract = $this->getAlias($abstract);

        if (isset($this->instances[$abstract])) {
            $this->instances[$abstract] = $closure($this->instances[$abstract], $this);

            $this->rebound($abstract);
        } else {
            $this->extenders[$abstract][] = $closure;

            if ($this->resolved($abstract)) {
                $this->rebound($abstract);
            }
        }
    }

    /**
     * 注册共享实例
     * @param  string  $abstract
     * @param  mixed   $instance
     * @return void
     */
    public function instance($abstract, $instance)
    {
        $this->removeAbstractAlias($abstract);

        $isBound = $this->bound($abstract);
        unset($this->aliases[$abstract]);


        $this->instances[$abstract] = $instance;
        if ($isBound) {
            $this->rebound($abstract);
        }
    }

    /**
     * 从别名数组中移出，避免重复绑定
     * @param  string  $searched
     * @return void
     */
    protected function removeAbstractAlias($searched)
    {
        if (! isset($this->aliases[$searched])) {
            return;
        }

        foreach ($this->abstractAliases as $abstract => $aliases) {
            foreach ($aliases as $index => $alias) {
                if ($alias == $searched) {
                    unset($this->abstractAliases[$abstract][$index]);
                }
            }
        }
    }

    /**
     * 为绑定的类设置tag
     *
     * @param  array|string  $abstracts
     * @param  array|mixed   ...$tags
     * @return void
     */
    public function tag($abstracts, $tags)
    {
        $tags = is_array($tags) ? $tags : array_slice(func_get_args(), 1);

        foreach ($tags as $tag) {
            if (! isset($this->tags[$tag])) {
                $this->tags[$tag] = [];
            }

            foreach ((array) $abstracts as $abstract) {
                $this->tags[$tag][] = $abstract;
            }
        }
    }

    /**
     * 根据tag获取所有实例
     *
     * @param  string  $tag
     * @return array
     */
    public function tagged($tag)
    {
        $results = [];

        if (isset($this->tags[$tag])) {
            foreach ($this->tags[$tag] as $abstract) {
                $results[] = $this->make($abstract);
            }
        }

        return $results;
    }

    /**
     * 设置别名
     *
     * @param  string  $abstract
     * @param  string  $alias
     * @return void
     */
    public function alias($abstract, $alias)
    {
        $this->aliases[$alias] = $abstract;

        $this->abstractAliases[$abstract][] = $alias;
    }

    /**
     *
     * @param  string    $abstract
     * @param  \Closure  $callback
     * @return mixed
     */
    public function rebinding($abstract, Closure $callback)
    {
        $this->reboundCallbacks[$abstract = $this->getAlias($abstract)][] = $callback;

        if ($this->bound($abstract)) {
            return $this->make($abstract);
        }
    }

    /**
     *
     * @param  string  $abstract
     * @param  mixed   $target
     * @param  string  $method
     * @return mixed
     */
    public function refresh($abstract, $target, $method)
    {
        return $this->rebinding($abstract, function ($app, $instance) use ($target, $method) {
            $target->{$method}($instance);
        });
    }

    /**
     *
     * @param  string  $abstract
     * @return void
     */
    protected function rebound($abstract)
    {
        $instance = $this->make($abstract);

        foreach ($this->getReboundCallbacks($abstract) as $callback) {
            call_user_func($callback, $this, $instance);
        }
    }

    /**
     *
     * @param  string  $abstract
     * @return array
     */
    protected function getReboundCallbacks($abstract)
    {
        if (isset($this->reboundCallbacks[$abstract])) {
            return $this->reboundCallbacks[$abstract];
        }

        return [];
    }

    /**
     *
     * @param  \Closure  $callback
     * @param  array  $parameters
     * @return \Closure
     */
    public function wrap(Closure $callback, array $parameters = [])
    {
        return function () use ($callback, $parameters) {
            return $this->call($callback, $parameters);
        };
    }

    /**
     *
     * @param  callable|string  $callback
     * @param  array  $parameters
     * @param  string|null  $defaultMethod
     * @return mixed
     */
    public function call($callback, array $parameters = [], $defaultMethod = null)
    {
        return BoundMethod::call($this, $callback, $parameters, $defaultMethod);
    }

    /**
     *
     * @param  string  $abstract
     * @return \Closure
     */
    public function factory($abstract)
    {
        return function () use ($abstract) {
            return $this->make($abstract);
        };
    }

    /**
     *
     * @param  string  $abstract
     * @param  array  $parameters
     * @return mixed
     */
    public function makeWith($abstract, array $parameters)
    {
        return $this->resolve($abstract, $parameters);
    }

    /**
     *
     * @param  string  $abstract
     * @return mixed
     */
    public function make($abstract)
    {
        return $this->resolve($abstract);
    }

    /**
     *
     * @param  string  $abstract
     * @param  array  $parameters
     * @return mixed
     */
    protected function resolve($abstract, $parameters = [])
    {
        // 获取别名
        $abstract = $this->getAlias($abstract);
        // Contextual 上下文关系的
        $needsContextualBuild = ! empty($parameters) || ! is_null(
            $this->getContextualConcrete($abstract)
        );

        if (isset($this->instances[$abstract]) && ! $needsContextualBuild) {
            return $this->instances[$abstract];
        }

        $this->with[] = $parameters;

        $concrete = $this->getConcrete($abstract);


        if ($this->isBuildable($concrete, $abstract)) {
            $object = $this->build($concrete);
        } else {
            $object = $this->make($concrete);
        }

        foreach ($this->getExtenders($abstract) as $extender) {
            $object = $extender($object, $this);
        }

        // 单例共享，不再创建新的对象
        if ($this->isShared($abstract) && ! $needsContextualBuild) {
            $this->instances[$abstract] = $object;
        }

        $this->fireResolvingCallbacks($abstract, $object);

        $this->resolved[$abstract] = true;

        array_pop($this->with);

        return $object;
    }

    /**
     *
     * @param  string  $abstract
     * @return mixed   $concrete
     */
    protected function getConcrete($abstract)
    {
        if (! is_null($concrete = $this->getContextualConcrete($abstract))) {
            return $concrete;
        }

        if (isset($this->bindings[$abstract])) {
            return $this->bindings[$abstract]['concrete'];
        }

        return $abstract;
    }

    /**
     * 根据契约获取上下文相关的实体类绑定
     *
     * @param  string  $abstract
     * @return string|null
     */
    protected function getContextualConcrete($abstract)
    {
        if (! is_null($binding = $this->findInContextualBindings($abstract))) {
            return $binding;
        }

        if (empty($this->abstractAliases[$abstract])) {
            return;
        }

        foreach ($this->abstractAliases[$abstract] as $alias) {
            if (! is_null($binding = $this->findInContextualBindings($alias))) {
                return $binding;
            }
        }
    }

    /**
     * 根据契约类寻找与实体类相关的上下文
     *
     * @param  string  $abstract
     * @return string|null
     */
    protected function findInContextualBindings($abstract)
    {
        if (isset($this->contextual[end($this->buildStack)][$abstract])) {
            return $this->contextual[end($this->buildStack)][$abstract];
        }
    }

    /**
     * 判断给定的类是否可以被实例化
     *
     * @param  mixed   $concrete
     * @param  string  $abstract
     * @return bool
     */
    protected function isBuildable($concrete, $abstract)
    {
        return $concrete === $abstract || $concrete instanceof Closure;
    }

    /**
     * 实例化对象
     *
     * @param  string  $concrete
     * @return mixed
     *
     * @throws Exception
     */
    public function build($concrete)
    {
        if ($concrete instanceof Closure) {
            return $concrete($this, $this->getLastParameterOverride());
        }

        $reflector = new ReflectionClass($concrete);

        if (! $reflector->isInstantiable()) {
            return $this->notInstantiable($concrete);
        }

        $this->buildStack[] = $concrete;

        $constructor = $reflector->getConstructor();

        if (is_null($constructor)) {
            array_pop($this->buildStack);

            return new $concrete;
        }

        $dependencies = $constructor->getParameters();

        $instances = $this->resolveDependencies(
            $dependencies
        );

        array_pop($this->buildStack);

        return $reflector->newInstanceArgs($instances);
    }

    /**
     *
     * @param  array  $dependencies
     * @return array
     */
    protected function resolveDependencies(array $dependencies)
    {
        $results = [];

        foreach ($dependencies as $dependency) {

            if ($this->hasParameterOverride($dependency)) {
                $results[] = $this->getParameterOverride($dependency);

                continue;
            }

            $results[] = is_null($class = $dependency->getClass())
                            ? $this->resolvePrimitive($dependency)
                            : $this->resolveClass($dependency);
        }

        return $results;
    }

    /**
     * Determine if the given dependency has a parameter override from makeWith.
     *
     * @param  \ReflectionParameter  $dependency
     * @return bool
     */
    protected function hasParameterOverride($dependency)
    {
        return array_key_exists(
            $dependency->name, $this->getLastParameterOverride()
        );
    }

    /**
     *
     * @param  \ReflectionParameter  $dependency
     * @return mixed
     */
    protected function getParameterOverride($dependency)
    {
        return $this->getLastParameterOverride()[$dependency->name];
    }

    /**
     *
     * @return array
     */
    protected function getLastParameterOverride()
    {
        return count($this->with) ? end($this->with) : [];
    }

    /**
     *
     * @param  \ReflectionParameter  $parameter
     * @return mixed
     *
     * @throws Exception
     */
    protected function resolvePrimitive(ReflectionParameter $parameter)
    {
        if (! is_null($concrete = $this->getContextualConcrete('$'.$parameter->name))) {
            return $concrete instanceof Closure ? $concrete($this) : $concrete;
        }

        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        $this->unresolvablePrimitive($parameter);
    }

    /**
     *
     * @param  \ReflectionParameter  $parameter
     * @return mixed
     *
     * @throws Exception
     */
    protected function resolveClass(ReflectionParameter $parameter)
    {
        try {
            return $this->make($parameter->getClass()->name);
        }

        catch (Exception $e) {
            if ($parameter->isOptional()) {
                return $parameter->getDefaultValue();
            }

            throw $e;
        }
    }

    /**
     *
     * @param  string  $concrete
     * @return void
     *
     */
    protected function notInstantiable($concrete)
    {
        if (! empty($this->buildStack)) {
            $previous = implode(', ', $this->buildStack);

            $message = "Target [$concrete] is not instantiable while building [$previous].";
        } else {
            $message = "Target [$concrete] is not instantiable.";
        }

        throw new Exception($message);
    }

    /**
     *
     * @param  \ReflectionParameter  $parameter
     * @return void
     *
     * @throws Exception
     */
    protected function unresolvablePrimitive(ReflectionParameter $parameter)
    {
        $message = "Unresolvable dependency resolving [$parameter] in class {$parameter->getDeclaringClass()->getName()}";

        throw new Exception($message);
    }

    /**
     *
     * @param  string    $abstract
     * @param  \Closure|null  $callback
     * @return void
     */
    public function resolving($abstract, Closure $callback = null)
    {
        if (is_string($abstract)) {
            $abstract = $this->getAlias($abstract);
        }

        if (is_null($callback) && $abstract instanceof Closure) {
            $this->globalResolvingCallbacks[] = $abstract;
        } else {
            $this->resolvingCallbacks[$abstract][] = $callback;
        }
    }

    /**
     *
     * @param  string   $abstract
     * @param  \Closure|null $callback
     * @return void
     */
    public function afterResolving($abstract, Closure $callback = null)
    {
        if (is_string($abstract)) {
            $abstract = $this->getAlias($abstract);
        }

        if ($abstract instanceof Closure && is_null($callback)) {
            $this->globalAfterResolvingCallbacks[] = $abstract;
        } else {
            $this->afterResolvingCallbacks[$abstract][] = $callback;
        }
    }

    /**
     *
     * @param  string  $abstract
     * @param  mixed   $object
     * @return void
     */
    protected function fireResolvingCallbacks($abstract, $object)
    {
        $this->fireCallbackArray($object, $this->globalResolvingCallbacks);

        $this->fireCallbackArray(
            $object, $this->getCallbacksForType($abstract, $object, $this->resolvingCallbacks)
        );

        $this->fireAfterResolvingCallbacks($abstract, $object);
    }

    /**
     *
     * @param  string  $abstract
     * @param  mixed   $object
     * @return void
     */
    protected function fireAfterResolvingCallbacks($abstract, $object)
    {
        $this->fireCallbackArray($object, $this->globalAfterResolvingCallbacks);

        $this->fireCallbackArray(
            $object, $this->getCallbacksForType($abstract, $object, $this->afterResolvingCallbacks)
        );
    }

    /**
     *
     * @param  string  $abstract
     * @param  object  $object
     * @param  array   $callbacksPerType
     *
     * @return array
     */
    protected function getCallbacksForType($abstract, $object, array $callbacksPerType)
    {
        $results = [];

        foreach ($callbacksPerType as $type => $callbacks) {
            if ($type === $abstract || $object instanceof $type) {
                $results = array_merge($results, $callbacks);
            }
        }

        return $results;
    }

    /**
     *
     * @param  mixed  $object
     * @param  array  $callbacks
     * @return void
     */
    protected function fireCallbackArray($object, array $callbacks)
    {
        foreach ($callbacks as $callback) {
            $callback($object, $this);
        }
    }

    /**
     * 获取容器绑定
     *
     * @return array
     */
    public function getBindings()
    {
        return $this->bindings;
    }

    /**
     * 获取别名
     *
     * @param  string  $abstract
     * @return string
     *
     * @throws \LogicException
     */
    public function getAlias($abstract)
    {
        if (! isset($this->aliases[$abstract])) {
            return $abstract;
        }

        if ($this->aliases[$abstract] === $abstract) {
            throw new LogicException("[{$abstract}] is aliased to itself.");
        }

        return $this->getAlias($this->aliases[$abstract]);
    }

    /**
     *
     * @param  string  $abstract
     * @return array
     */
    protected function getExtenders($abstract)
    {
        $abstract = $this->getAlias($abstract);

        if (isset($this->extenders[$abstract])) {
            return $this->extenders[$abstract];
        }

        return [];
    }

    /**
     *
     * @param  string  $abstract
     * @return void
     */
    public function forgetExtenders($abstract)
    {
        unset($this->extenders[$this->getAlias($abstract)]);
    }

    /**
     * 清理旧的实例
     * @param  string  $abstract
     * @return void
     */
    protected function dropStaleInstances($abstract)
    {
        unset($this->instances[$abstract], $this->aliases[$abstract]);
    }

    /**
     * 从实例缓存中移除一个实例
     * @param  string  $abstract
     * @return void
     */
    public function forgetInstance($abstract)
    {
        unset($this->instances[$abstract]);
    }

    /**
     * 从容器中清除所有实例
     * @return void
     */
    public function forgetInstances()
    {
        $this->instances = [];
    }

    /**
     * 清除所有的实例绑定容器
     * @return void
     */
    public function flush()
    {
        $this->aliases = [];
        $this->resolved = [];
        $this->bindings = [];
        $this->instances = [];
        $this->abstractAliases = [];
    }

    /**
     * 获取单例
     *
     * @return static
     */
    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    /**
     * 设置共享实例
     * @param  $container
     * @return static
     */
    public static function setInstance(ContainerContract $container = null)
    {
        return static::$instance = $container;
    }

    /**
     *
     * @param  string  $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return $this->bound($key);
    }

    /**
     *
     * @param  string  $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->make($key);
    }

    /**
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public function offsetSet($key, $value)
    {
        $this->bind($key, $value instanceof Closure ? $value : function () use ($value) {
            return $value;
        });
    }

    /**
     *
     * @param  string  $key
     * @return void
     */
    public function offsetUnset($key)
    {
        unset($this->bindings[$key], $this->instances[$key], $this->resolved[$key]);
    }

    /**
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this[$key];
    }

    /**
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this[$key] = $value;
    }
}
