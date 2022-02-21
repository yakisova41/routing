# Yakisova41/routing
## Usage
```sh
composer require yakisova41/routing
```
```php
require_once 'vendor/autoload.php';

use Yakisova41\Routing\Routing;

//The first argument is the request path and the second argument is the request method
$router = new Routing(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH),$_SERVER['REQUEST_METHOD']);

/*    Defining Routing
--------------------------
first parameter
|Please enter a path
|{enclose in brackets} parameter can be set

second parameter
|Enter the method as an array in all capital letters

third parameter
|Write the process
*/

$router->route('/',['GET'],function(){
    echo 'Toppage!!!';
});

$router->route('/page/{id}',['GET'],function($param){
    echo 'page';
    echo $param['id'];
});

$router->route('/all',['GET','POST'],function(){
    echo 'Both post and get are welcome!';
});

/*    Start routing
--------------------------
 The argument specifies the behavior at 404, and can be omitted (in that case, the default 404 page will be displayed)
*/
$router->run(function(){
    echo '404 not found';
});
```
## htaccess example
```
DirectoryIndex index.php
RewriteEngine on
<IfModule mod_rewrite.c>
      RewriteCond %{REQUEST_FILENAME} !-f
      RewriteCond %{REQUEST_FILENAME} !-d
      RewriteRule ^(.+)$ index.php [L]
</IfModule>
```
