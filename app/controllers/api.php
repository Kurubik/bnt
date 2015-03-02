<?php

/**
 * @var \Silex\Application $app
 * $var \Silex\ControllerCollection $api
 */
use Symfony\Component\HttpFoundation\Response;

use app\data\Data;

$api = $app['controllers_factory'];

$api->get('/managers/', function() use ($app) {
    
});



return $api;