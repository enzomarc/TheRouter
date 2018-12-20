# The Router Documentation
## Installation
Add the latest version of the-router project running this command.

    composer require focus237/the-router
 
## Features
-   Basic routing (`GET`,  `POST`).
-   Regular Expression Constraints for parameters.
-   Named routes.
-   Generating url to routes.
-   Namespaces.
-   Optional parameters
-   Sub-domain routing
-   Custom boot managers to rewrite urls to "nicer" ones.
-   Input manager; easily manage  `GET`,  `POST`.

## Server Setup

### Setting up Apache
Nothing special is required for Apache to work. We've include the  `.htaccess`  file in the  `root`  folder. If rewriting is not working for you, please check that the  `mod_rewrite`  module (htaccess support) is enabled in the Apache configuration.

#### .htaccess example

Below is an example of an working  `.htaccess`  file used by the-router.

Simply create a new  `.htaccess`  file in your root directory and paste the contents below in your newly created file. This will redirect all requests to your  `index.php`  file. According your `index.php` is in `public` folder, you'll write something like this
```
RewriteEngine on
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^(.*)$ /public/index.php?url=$1 [QSA,L]
```
### Setting up Ngninx
If you are using Nginx please make sure that url-rewriting is enabled.

You can easily enable url-rewriting by adding the following configuration for the Nginx configuration-file for the demo-project.

```
location / {
    try_files $uri $uri/ /public/index.php?$query_string;
}
```

## Configuration
Create a new file, name it  `routes.php`  and place it in your library folder. This will be the file where you define all the routes for your project.

**WARNING: NEVER PLACE YOUR ROUTES.PHP IN YOUR PUBLIC FOLDER!**

In your  `index.php`  require your newly-created  `routes.php`  and call the  `$router->run()`  method. This will trigger and do the actual routing of the requests.

It's not required, but you can set `TheRouter::setDefaultNamespace('\Controllers\Path');`  to prefix all routes with the namespace to your controllers. This will simplify things a bit, as you won't have to specify the namespace for your controllers on each route. The default namespace for controllers is  `App\Controller` .

**This is an example of a basic  `index.php`  file:**

    <?php
    
    use TheRouter\Router\Router;
    
    $router = new Router();
    
    /* Load external routes file */
    require_once 'routes.php';
    
    /**
    * The default namespace for route-callbacks, so we don't have to specify it each time.
    * Can be overwritten by using the namespace config option on your routes.
    */
    TheRouter::setDefaultNamespace('App\Controller');
    
    /* Start the routing */
    $router->run();

## Routes
Remember the `routes.php` file you required in your `index.php`? This file be where you place all your custom rules for routing.

### Basic Routing
Below is a very basic example of setting up a route. First parameter is the url which the route should match - next parameter is a `Closure` or callback function that will be triggered once the route matches.

    $router->get('/', function() {
	    return 'Hello world';
    });

or you can call controller action by using `ControllerName@Function` :

    $router->get('/', 'DefaultController@index');

#### Available methods
Here you can see a list over all available routes:

    $router->get($url, $callback, $name);
    $router->post($url, $callback, $name);

 `$name` is the route name. See named routes section for more informations. 

### Routes parameters
You'll properly wondering by know how you parse parameters from your urls. For example, you might want to capture the users id from an url. You can do so by defining route-parameters.

    $router->get('/user/{id}', function ($id) {
		return 'User with id: ' . $id;
	});
You may define as many route parameters as required by your route:

    $router->get('/posts/{post}/comments/{comment}', function ($post, $comment) {
		// ...
	});

#### Regular expression constraints

You may constrain the format of your route parameters using the where method on a route instance. The where method accepts the name of the parameter and a regular expression defining how the parameter should be constrained:

    $router->get('/user/{name}', function ($name) {
      
	    // ... do stuff
      
    })->where('name', '[A-Za-z]+');
    
    $router->get('/user/{id}', function ($id) {
      
	    // ... do stuff
      
    })->where('id', '[0-9]+');
    
    $router->get('/user/{id}/{name}', function ($id, $name) {
      
	    // ... do stuff
      
    })->where(['id' => '[0-9]+', 'name' => '[a-z]+']);

### Named Routes
Named routes allow the convenient generation of URLs or redirects for specific routes. There is two ways for defining route name.

#### Chaining
You may specify a name for a route by chaining the name method onto the route definition (in this example, `profile` is the route name):

    $router->get('/user/profile', function () {
		// Your code here...
	})->name('profile');

#### Name parameter
You may specify a name for a route by adding it next to the callback of the route (in this example, `profile` is the route name):

    $router->get('/user/profile', function () {
	    // Your code here...
    }, 'profile');

### Generating URLs to named routes

Once you have assigned a name to a given route, you may use the route's name when generating URLs or redirects via the global `$router->url('route_name')` function:

    // Generating URLs...
	$url = $router->url('profile');

If the named route defines parameters, you may pass the parameters as the second argument to the `url` function. The given parameters will automatically be inserted into the URL in their correct positions:

    $router->get('/user/{id}/profile', function ($id) {
		//
	})->name('profile');
	
	$url = $router->url('profile', ['id' => 1]);

