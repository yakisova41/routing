# Yakisova41/routing
## Getting Started
```sh
composer require yakisova41/routing
```
```php
use Yakisova41\routing\Routing;

$router = new Routing($_SERVER['REQUEST_URI'],$_SERVER['REQUEST_METHOD']);

$router->route('/',['GET'],function($notfound){
    echo 'get';
});


$router->route('/',['POST'],function($notfound){
    echo 'post';
});

$router->route('/',['GET','POST'],function($notfound){
    echo 'get and post';
});


$var = 'ABCD';
$router->route('/',['GET'],function($notfound, $variable){
    echo 'this is ABCD ==>>> '.$variable;
},$var);


$router->route('/user/{id}',['GET'],function($notfound, $var, $param){
    echo 'your id is'.$param['id'];

    if($param['id'] != 1)
    {
        $notfound->notfound(function($id){
            echo 'user page only notfound page';
            echo $id.'was not found';
        },$param['id']);
    }
});

$router->require('/blog/{number}',['GET'],'blogtemplage.php',$var,['notfound','vars','params']);


$router->run();

$router->run(function(){
    echo 'custom notfound page';
});

$var = 'custom';
$router->run(function($var){
    echo $var.' notfound page';
},$var);