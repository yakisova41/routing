# Yakisova41/Routing

## About
A composer package that allows you to set up simple routing

## Usage
```bash
composer require yakisova41/routing
```
```php
use Yakisova41\Routing\Routing;
use Yakisova41\Routing\Route;

Route::put('/',['GET'],function(){
    echo 'Hello world!!';
});

Routing::listen();
```

### Set path parameters
```php
Route::put('/page/{Pageid}',['GET'],function($req){
    echo $req['parameters']['Pageid'];
});
```
Executing this code will display the value specified in the path parameter Pageid


### Specify request method
```php
Route::put('/',['POST'],function(){
    echo 'Hello world!!';
});

Route::put('/',['GET', 'POST'],function(){
    echo 'Hello world!!';
});
```
Multiple request methods can be specified in the array
