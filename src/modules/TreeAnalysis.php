<?php
use Yakisova41\ModuleLoader\Loader;

Loader::exportDefault(function($routes, $options)
{
    $PathDecomposition = Loader::import(__DIR__.'/PathDecomposition.php');
    $path_parameter_verification = Loader::import(__DIR__.'/ParameterVerification.php');
    

    foreach($routes[$options['requestMethod']] as $routeKey => $route)
    {
        $isMatch = false;
        $returnData = 
        [
            'matchedTree'=>$route,
            'requestUri'=>$options['requestUri'],
            'requestMethod'=>$options['requestMethod']
        ];

        if($options['requestUri'] === $route['routePath'])
        {
            $isMatch =  true;
        }

        $parameter_datas =  $PathDecomposition($route['routePath']);
        $params = $path_parameter_verification($route['routePath'], $parameter_datas, $options['requestUri']);

        if($params !== false){
            $returnData['parameters'] = $params;
            $isMatch =  true;
        }

        if($isMatch){
            return $returnData;
        }
    }
    

    return false;
});
