<?php
namespace Yakisova41\Routing;
use Yakisova41\Routing\Module;

class Routing
{
    //Per-instance routing tree
    public $routes = ['GET'=>[],'POST'=>[]];



    /**
     * --------------------------------------------------------------------------
     * option and register it in a member variable.
     * There are two types of member variable "MODE": "MAIN" and "MODULE"
     * use "MODULE" when calling in a module.
     * --------------------------------------------------------------------------
     */
    public function __construct($options = [])
    {
        if(!isset($options['REQUEST_URI'])){
            $options['REQUEST_URI'] = $_SERVER['REQUEST_URI'];
        }

        if(!isset($options['REQUEST_METHOD'])){
            $options['REQUEST_METHOD'] = $_SERVER['REQUEST_METHOD'];
        }

        if(!isset($options['MODE'])){
            $options['MODE'] = 'MAIN';
        }

        if($options['MODE'] === 'MAIN'){
            $this->access_path = $options['REQUEST_URI'];
            $this->access_method = $options['REQUEST_METHOD'];
        }

        $this->mode = $options['MODE'];
    }



    /**
     * --------------------------------------------------------------------------
     * Register in the routing tree
     * --------------------------------------------------------------------------
     */
    public function route($path, $methods, $callback, $vars = [])
    {
        foreach($methods as $method)
        {
            $this->routes[$method][] = ['path'=>$path,'callback'=>$callback,'vars'=>$vars];
        }
    }



    /**
     * --------------------------------------------------------------------------
     * Loading Modules
     * --------------------------------------------------------------------------
     */
    public function use($module_path)
    {
        require_once $module_path;
    }



    /**
     * --------------------------------------------------------------------------
     * Execute routing, Router class returns a matched Route 
     * if a match is found, or nothing if no match is found.
     * --------------------------------------------------------------------------
     */
    public function run($notfound = false, $vars = false)
    {
        $this->main_only(function($vars)
        {
            Module::export($this); 
            
            $router = Router::start(Module::modules(), $this->access_path, $this->access_method);

            $notfound = $vars[0];

            if($notfound === false)
            {
                $notfound = function()
                {
                    print('<h1>Not Found</h1><p><b>'.$this->access_path.'</b> was not found on this server</p><hr><p>Yakisova41/routing</p>');
                };
            }

            if($router === false)
            {
                header('HTTP/1.1 404 Notfound');
                $notfound($vars[1]);
                return false;
            }
            else
            {
                $router[0]($router[1], $router[2]);
                return true;
            }
        },[$notfound,$vars]);
    }



    /**
     * --------------------------------------------------------------------------
     * Execute callback only when the mode of the instance is MAIN
     * --------------------------------------------------------------------------
     */
    private function main_only($callback,$vars = [])
    {
        if($this->mode === 'MAIN')
        {
            $callback($vars);
        }
        else
        {
            echo 'MAIN only';
        }
    }
}
