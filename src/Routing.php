<?php
namespace Yakisova41\routing;

use Yakisova41\routing\Responser;

class Routing
{
    private $request_path;
    private $request_method;

    private $routing_tree;

    public function __construct($request_path = false, $request_method = false)
    {
        /*
        *Checks if the request path and request method are specified as arguments,and if not,
        *specifies them automatically, and then assigns them to member functions.  
        */
        if($request_path === false)
        {
            $request_path = $_SERVER['REQUEST_URI'];
        }
        if($request_method === false)
        {
            $request_method = $_SERVER['REQUEST_METHOD'];
        }   
        $this->request_path = $request_path;
        $this->request_method = $request_method;  

        //Initialization of routing tree
        $this->routing_tree = ['GET'=>[],'POST'=>[]];
    }


    /*
    *--------------------------------------------------------------------------  
    *Define routing.
    *The first argument is the path
    *Second argument is a method
    *Third argument: function or anonymous function to execute
    *The fourth argument can be any variable you wish to pass.
    *--------------------------------------------------------------------------         
    */    
    public function route($path, $methods, $function, $passing_variable = false)
    {
        foreach($methods as $method)
        {
            $this->routing_tree[$method][] = [
                'path'=>$path,
                'function'=>$function,
                'vars'=>$passing_variable,
                'file'=>false,
                'var_names'=>false
            ];
        }
    }


    /* 
    *--------------------------------------------------------------------------   
    *Performs routing.
    *Locates the desired tree in the array assigned to the 
    *routing_tree member function and executes an unnamed function in the tree.
    *--------------------------------------------------------------------------   
    */    
    public function run($when_notfound_function = false, $when_notfound_function_passvar = false)
    {
        /*
        *If the argument specifying the behavior on 404 is false,
        *assign an anonymous function to when_notfound_function that displays the default 404 page.
        */
        if(!$when_notfound_function)
        {
            $when_notfound_function_passvar = $this->request_path;
            $when_notfound_function = function($request){
                print('<h1>Not Found</h1><p><b>'.$request.'</b> was not found on this server</p><hr><p>Yakisova41/routing</p>');
            };
        }


        /*
        *Define notfounder so that notfound can be called from within the middleware
        */
        $notfounder = new class($when_notfound_function, $when_notfound_function_passvar){
            public function __construct($when_notfound_function, $when_notfound_function_passvar)
            {
                $this->function = $when_notfound_function;
                $this->var = $when_notfound_function_passvar;
            }

            public function notfound($function = false, $var = false)
            {
                if(!$function){
                    $function = $this->function;
                    $var = $this->var;
                }
                
                header('HTTP/1.1 404 Notfound');
                $function($var);
            }
        };

        /*
        *The GET and POST arrays in the routing tree, whichever array matches the current method
        */
        $routing_tree_matchmethod = $this->routing_tree[$this->request_method];
        
        foreach($routing_tree_matchmethod as $this_tree)
        {
            $parameter_datas = $this->path_parameter_decomposition($this_tree['path']);

            $params = $this->path_parameter_verification($this_tree['path'], $parameter_datas, $this->request_path);

            if($params !== false)
            {
                $this_tree['function']($notfounder, $this_tree['vars'], $params, $this_tree['file'], $this_tree['var_names']);
                return true;
            }
            elseif($this_tree['path'] === $this->request_path)
            {
                $this_tree['function']($notfounder, $this_tree['vars'], false, $this_tree['file'], $this_tree['var_names']);
                return true;
            }
        }

        /**
         * If a middleware call occurs once in foreach and no return is made, a 404 status code 
         * is sent and the process specified in when_notfound_function is executed.
         */
        header('HTTP/1.1 404 Notfound');
        $when_notfound_function($when_notfound_function_passvar);
    }


    /*
    *-------------------------------------------
    *Registering external files with routingtree
    *-------------------------------------------
    */
    public function require($path, $methods, $function_file, $passing_variable = false, $varnames = ['notfounder','vars','params'])
    {
        $function = function($notfounder, $vars, $params, $function_file, $varnames)
        {
            /*
            *If a variable name is specified in the fifth argument, the notfounder, vars, and params, variables are renamed.
            */
            $notfounder_name = $varnames[0];
            $vars_name = $varnames[1];
            $params_name = $varnames[2];

            $$notfounder_name = $notfounder;
            $$vars_name = $vars;
            $$params_name = $params;
            
            require_once $function_file;
        };

        foreach($methods as $method)
        {
            $this->routing_tree[$method][] = [
                'path'=>$path,
                'function'=>$function,
                'vars'=>$passing_variable,
                'file'=>$function_file,
                'var_names'=>$varnames,
            ];
        }
    }


    /* 
    *--------------------------------------------------------------------------   
    *Decompose a path with path parameters and return them as data
    *--------------------------------------------------------------------------   
    */
    private function path_parameter_decomposition($path)
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
    private function path_parameter_verification($path, $parameter_datas, $reqpath)
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
