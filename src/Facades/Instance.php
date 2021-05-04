<?php


namespace AspectOverride\Facades;

use AspectOverride\Core\Configuration;

/**
 * @method static void initialize(Configuration $configuration)
 * @method static Configuration getConfiguration()
 */
class Instance
{
    /** @var \AspectOverride\Core\Instance */
    private static $instance;

    /**
     * @param mixed $name
     * @param mixed $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        if (!isset(self::$instance)) {
            self::$instance = new \AspectOverride\Core\Instance();
        }
        return self::$instance->$name(...$arguments);
    }
}