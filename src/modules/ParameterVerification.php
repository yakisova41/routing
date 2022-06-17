<?php
use Yakisova41\ModuleLoader\Loader;

Loader::exportDefault(function($path, $parameter_datas, $reqpath)
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
});
