<?php

namespace AspectOverride\Facades;

/**
 * @method static void setForClass(string $class, string $method, callable $fn)
 * @method static callable|null getForClass(string $class, string $method)
 * @method static void removeForClass(string $class, string $method)
 * @method static void setForFunction(string $fnName, callable $fn)
 * @method static callable|null getForFunction(string $fn)
 * @method static array getFunctions()
 * @method static void removeForFunction(string $fn)
 * @method static void clean()
 */
class Registry
{
    /** @var \AspectOverride\Core\Registry */
    private static $instance;

    /**
     * @param mixed $name
     * @param mixed $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        if (!isset(self::$instance)) {
            self::$instance = new \AspectOverride\Core\Registry();
        }
        return self::$instance->$name(...$arguments);
    }
}