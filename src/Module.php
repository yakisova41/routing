<?php
namespace Yakisova41\Routing;

/**
 * Classes that bring each module together
 */
class Module
{
    public static $modules = [];

    public static function export($module_obj)
    {
        self::$modules[] = $module_obj;
    }

    public static function modules()
    {
        return self::$modules;
    }
}