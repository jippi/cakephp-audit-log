<?php

use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

Router::prefix('admin', function (RouteBuilder $routes) {
    $routes->plugin('AuditLog', ['path' => '/audit-log'], function (RouteBuilder $routes) {
        $routes->fallbacks(DashedRoute::class);
    });
});
