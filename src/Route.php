<?php
namespace Yakisova41\Routing;

class Route
{
    private static $routes = [];

    public static function put(string $RoutingPath, array $RoutingMethods, object $RoutingCallback)
    {
        foreach($RoutingMethods as $RoutingMethod)
        {
            self::$routes[$RoutingMethod][] = [
                'routePath'=>$RoutingPath,
                'routeCallback'=>$RoutingCallback
            ];
        }
    }

    public static function export()
    {
        return self::$routes;
    }
}