<?php

namespace HongXunPan\Tools\Abstracts;

abstract class SingletonAbstract
{
    protected static $instance;

    protected function __construct()
    {
        //
    }

    /**
     * @return static
     */
    final public static function getInstance()
    {
        $class = get_called_class();
        if (!isset(static::$instance[$class])) {
            static::$instance[$class] = new $class();
        }
        return static::$instance[$class];
    }

    final protected function __clone()
    {
        //
    }
}