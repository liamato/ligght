#ligght
###The simple PHP router

ligght is a PHP micro router that helps you to easily route your _HTTP Requests_.
ligght is the easiest and fastest way to create an API.


##Features

* Standard HTTP Methods
 * GET
 * POST
 * PUT
 * DELETE
 * PATCH
 * OPTIONS
 * HEAD
 * DEBUG
* Custom HTTP Methods using `\ligght\Interfaces\HttpMethod` interface
* Parameter routing



##Getting Started

###Required

* PHP >= 5.3.*
* Apache `mod_rewrite`

###Configure evironment

You must include that snippet in `.htaccess` file:

```
Options -MultiViews
RewriteEngine On

RewriteBase /

RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f

RewriteRule .* index.php?$0 [PT,L]
```

Or copy [this](./.htaccess) .htaccess file to the same folder of your index.php

###Hello World Tutorial

Set a route like this:

```php
\ligght\Router::getInstance()->route(
    \ligght\Router::GET, 
    '/hello/:name',
    function($name){
        print "Hello, $name";
    },
    array(':name')
);
```

And run the router:

```php
\ligght\Router::getInstance()->run();
```

####Style Guide

Full coded with [PSR-2](http://www.php-fig.org/psr/psr-2/) Coding Style