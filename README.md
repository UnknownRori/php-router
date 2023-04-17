# PHP-Router

A simple dynamic route matcher for php, motivation for making these package is that my lightweight framework [Rin](https://github.com/UnknownRori/Rin) is suck at the routing and i want it to be some kind package, not only that i want to learn string matching and optimization that can be done in string operation.

The `Routes` in here are using Builder pattern so registering will not be very painfull, i plan to support some kind `Container` that implement `PSR-11` for dependency injection and also support array too and make the package feel like using [Laravel](https://laravel.com/) Routing.

Theres are some drawback on the Route matching, the URL passed to `RouteResolver` must not contain a query or the host name, regarding the query you probably can abuse it, but it's undefined behavior.

Also check out my other framework [UnknownRori-PHP](https://github.com/UnknownRori/UnknownRori-PHP).

## Usage

install through composer `composer require unknownrori/router`

It's nearly the same thing on Laravel, but it's feel more like standalone thing

```php
<?php

use UnknownRori\Router\RouteResolver;
use UnknownRori\Router\Routes;

// Create new instance of `Routes`
$routes = new Routes();

// Added default constraint
// It will added alphanum, num, alpha
// Or you can added it by yourself by calling method `constraint` and passing key and a callable the callable should return a bool
$routes->addDefaultConstraints(); 

// Register some routes and use anonymous function as callback
$routes->get("/", fn (string $url) => $url)->name('home');
$routes->get("/posts", fn (string $url) => $url)->name('posts');
$routes->get("/users/{name}", fn (string $name) => "User name called {$name}")
    ->name('posts.show')
    ->where(['name' => 'alphanum']);

// Create instance of `RouteSolver`
$resolver = new RouteResolver($routes);

// Resolve the URL
// Make sure the URL is looks like this, so you might need to cut some host name and the other stuff to be able properly match the route
$result = $resolver->resolve('GET', '/users/UnknownRori');

// Echo the result
echo $result;
```

## 🛠️ Development

```bash
# clone the repository
> git clone https://github.com/UnknownRori/php-routes

# enter directory
> cd php-routes

# install dependency
> composer install

# do some test
> composer run test
```

## 🌟 Contribution

Feel free to contribute, send pull request or issue and i will take a look
