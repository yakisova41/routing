<?php
namespace Yakisova41\Routing;
use Yakisova41\ModuleLoader\Loader;
use  Yakisova41\Routing\Route;

class Routing
{
    private static $options = [];

    public static function listen(array | bool $options = false)
    {
        /**
         * Setting the options
         */
        self::setOptions($options, 'requestUri', $_SERVER['REQUEST_URI']);
        self::setOptions($options, 'requestMethod', $_SERVER['REQUEST_METHOD']);

        /**
         * Turn the routing tree
         */
        $routes = Route::export();

        $TreeAnalysis = Loader::import(__DIR__.'/modules/TreeAnalysis.php');
        $AnalysisResult = $TreeAnalysis($routes, self::$options);

        if($AnalysisResult !== false)
        {
            $matchedTree = $AnalysisResult['matchedTree'];
            $matchedTree['routeCallback']($AnalysisResult);

            return true;
        }
        else
        {
            return false;
        }
    }

    private static function setOptions(array $optionitem, string $optionname, mixed $defaultOption)
    {
        if(isset($optionitem[$optionname])){
            self::$options[$optionname] = $optionitem[$optionname];
        }
        else
        {
            self::$options[$optionname] = $defaultOption;
        }
    }
}