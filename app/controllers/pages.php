<?php

/**
 * @var \Silex\Application $app
 * @var \Silex\ControllerCollection $pages
 */

use app\data\Data;

use Silex\Application\UrlGeneratorTrait;
use Symfony\Component\HttpFoundation\Response;

$pages = $app['controllers_factory'];

$pages->get('/', function() use ($app) {
    return $app->redirect($app['url_generator']->generate('home'));
});


$routes = array (
    'service',
    'plans',
    'support',
    'contacts',
    'company'
);

$createRoute = function ($routeName, $app) use ($app) {
    return function () use ($app, $routeName) {
        return $app['twig']->render($routeName.'.twig', array(
            'index' => '',
            'data' => Data\Translates::translateArray($app['locale']),
          ));
    };
};


foreach ($routes as $routeName) {
    $pages->get('/{_locale}/'.$routeName.'/', $createRoute($routeName, $app))
      ->assert('_locale', 'en')
      ->bind($routeName);
}

return $pages;
