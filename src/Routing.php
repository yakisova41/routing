<?php
namespace Yakisova41\Routing;

class Routing{
    public function __construct($requestpath = false,$requestmethod = false){
        if(!$requestpath||!$requestmethod){
            trigger_error("Yakisova41/Routing Specify the request path and request method as arguments. example:\"new Routing(path,method)\"",E_USER_ERROR);      
        }
        else{
            $this->notfound = false;
            $this->reqpath = $requestpath;
            $this->method = $requestmethod;
            $this->routings = ['GET'=>[],'POST'=>[]];
        }
    }

    public function route($path,$method,$function,$ifnotfound = false){
        foreach($method as $key => $val){
            $this->routings[$val][] = ["$path",$function];
        }
    }   

    public function run($notfound = false){
        if($this->check() != true){
            if(!$notfound){
                print('<h1>Not Found</h1><p>The requested URL was not found on this server</p><hr><p>Yakisova41/routing</p>');
            }else{
                $notfound();
            }
        }
    }
    
    private function check(){
        foreach($this->routings[$this->method] as $key => $router){
            if($router[0] == $this->reqpath){
                $router[1](false);
                return true;
            }
            elseif($this->parameter($router[0],$this->reqpath)[0]){
                $router[1]($this->parameter($router[0],$this->reqpath)[1]);
                return true;
            }
        } 
    }
    
    private function parameter($path,$req){
        $plodepath = explode('/',$path);
        $plodereqpath = explode('/',$req);
        $params = [];

        foreach($plodepath as $key => $val){
            if(preg_match("/^{.*}$/",$val)){
                if(isset($plodereqpath[$key])){
                    $paramname = ltrim($val,'{');
                    $paramname = rtrim($paramname,'}');

                    $params[$paramname] =  $plodereqpath[$key];
                    $plodepath[$key] = $plodereqpath[$key];
                }
            }
        }
        
        $makepath = '';
        foreach($plodepath as $key => $val){
            $makepath = $makepath.$val.'/';
        }
        $makepath = substr($makepath,0,-1);

        if($req == $makepath){
            return [true,$params];
        }else{
            return [false];
        }
    }
}
