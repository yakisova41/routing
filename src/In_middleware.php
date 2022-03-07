<?php
namespace Yakisova41\Routing;

class In_middleware{
    public function notfound($notfound = false){
        header("HTTP/1.1 404 Not Found");
        if(!$notfound){
            print('<h1>Not Found</h1><p>The requested URL was not found on this server</p><hr><p>Yakisova41/routing</p>');
        }else{
            $notfound();
        }
    }
}