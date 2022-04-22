<?php
namespace Yakisova41\Routing;

class Router
{
    public static function start($modules, $request_path, $request_method)
    {
        $match = false;

        foreach($modules as $module)
        {
            $routes = $module->routes;

            foreach($routes[$request_method] as $route)
            {
                $this_route_path = $route['path'];

                $parameter_datas = self::path_parameter_decomposition($this_route_path);
                $params = self::path_parameter_verification($this_route_path, $parameter_datas, $request_path);

                if($params !== false)
                {
                    $match = [$route['callback'], $route['vars'], $params];
                }
                elseif($this_route_path === $request_path)
                {
                    $match =  [$route['callback'], $route['vars'], false];
                }
            }

            return $match;
        }
    }



    /* 
    *--------------------------------------------------------------------------   
    *Decompose a path with path parameters and return them as data
    *--------------------------------------------------------------------------   
    */
    private static function path_parameter_decomposition($path)
    {
        $splited_paths = explode('/',$path);
        
        $param_datas = [];

        foreach($splited_paths as $key => $splited_path)
        {
            if(preg_match("/{.*}$/", $splited_path))
            {
                $removes = ['{', '}'];
                $path_id = str_replace($removes, '', $splited_path);
                $param_datas[$key] = $path_id;
            }
        }

        return $param_datas;
    }


    
    /* 
    *--------------------------------------------------------------------------   
    *Scrutinizes the disassembled path parameter data, the path, and the
    *current request path, and returns an array of 
    *parameters if a match is found, or false if no match is found
    *--------------------------------------------------------------------------   
    */
    private static function path_parameter_verification($path, $parameter_datas, $reqpath)
    {
        $params =  [];

        $splited_reqpaths = explode('/',$reqpath);

        foreach($parameter_datas as $key => $param_data)
        {
            if(isset($splited_reqpaths[$key]))
            {
                $params[$param_data] = $splited_reqpaths[$key];
                $splited_reqpaths[$key] = '{'.$param_data.'}';

            }

        }

        $maked_path_verification_path = '';

        foreach($splited_reqpaths as $splited_reqpath)
        {
            if($splited_reqpath !== '')
            {
                $maked_path_verification_path = $maked_path_verification_path.'/'.$splited_reqpath;
            }
        }

        if($maked_path_verification_path === $path)
        {
            return $params;
        }
        else
        {
            return false;     
        }
    }
}
