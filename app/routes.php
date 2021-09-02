<?php

use Symfony\Component\Routing;

$routes = new Routing\RouteCollection();

$routes->add('home', new Routing\Route('/', [
    '_controller' => ['App\\Controllers\\Homepage', 'index'],
]));

$routes->add('blog_post', new Routing\Route('post/{url}', [
    '_controller' => ['App\\Controllers\\PostDetails', 'index'],
]));

$routes->add('about', new Routing\Route('/about', [
    '_controller' => ['App\\Controllers\\About', 'index'],
]));

return $routes;
