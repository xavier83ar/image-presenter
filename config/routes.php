<?php
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

Router::plugin('ImagePresenter', ['path' => '/image-presenter'], function (RouteBuilder $routes) {
    $routes->fallbacks(Router::defaultRouteClass());
});
