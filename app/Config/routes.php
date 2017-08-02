<?php

use Lib\RouterLoader;

RouterLoader::init('/^\/route$/', function() {
    $uri = $_POST;
    $api = new Controller\TokensController();
    $api->init($uri);
    exit();
});

RouterLoader::init('/^\/route\/.+$/', function($uri) {
    $api = new Controller\MapPathsController();
    $token = $uri[1];
    $api->getRouteByToken($token);
    exit();
});

RouterLoader::init('/^\/calculatePath\/.+$/', function($uri) {
    $api = new Controller\MapPathsController();
    $token = $uri[1];
    $api->calculatePath($token);
    exit();
});




