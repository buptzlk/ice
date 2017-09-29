<?php
use Framework_Library_Concrete_Container_Container as Container;

abstract class Framework_Library_Concrete_Facade_Facade
{
    public static $app;

    public static function getFacadeRoot()
    {
        self::$app = Container::getInstance();
        return static::$app[static::getFacadeAccessor()];
    }

    /**
     * 获取门面名称
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    protected static function getFacadeAccessor()
    {
        throw new RuntimeException('Facade does not implement getFacadeAccessor method.');
    }

    /**
     * 静态调用目标类函数
     *
     * @param  string  $method
     * @param  array   $args
     * @return mixed
     *
     * @throws \RuntimeException
     */
    public static function __callStatic($method, $args)
    {
        $instance = static::getFacadeRoot();

        if (! $instance) {
            throw new RuntimeException('A facade root has not been set.');
        }

        return $instance->$method(...$args);
    }
}

