<?php
use Yakisova41\ModuleLoader\Loader;

Loader::exportDefault(function($RoutePath)
{
    $splited_paths = explode('/',$RoutePath);
        
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
});