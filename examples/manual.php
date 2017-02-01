<?php
use Opulence\Router\RegexRouteTemplate;
use Opulence\Router\Route;
use Opulence\Router\RouteAction;
use Opulence\Router\Router;

// Create a route manually
$route = new Route(
    ['GET'],
    new RouteAction(null, null, function ($request, $routeVars) {
        return "Hello, {$routeVars['userId']}";
    }),
    new RegexRouteTemplate('users\/(?P<userId>\d+)', 'example\.com'),
    true,
    ['MiddlewareClass'],
    'MyProfile'
);

// Actually route the request
$router = new Router([$route]);
$matchedRoute = $router->route($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
