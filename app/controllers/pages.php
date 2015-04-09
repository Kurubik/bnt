<?php

/**
 * @var \Silex\Application $app
 * @var \Silex\ControllerCollection $pages
 */

use app\data\Data;

$pages = $app['controllers_factory'];

$pages->get('/', function() use ($app) {
    return $app->redirect($app['url_generator']->generate('home'));
});

$routes = array (
    'project_ecofuel',
    'corporate_mission_and_vision',
    'corporate_board_of_directors_and_management',
);

$pages->get('/{_locale}/',
    function() use ($app) {
        return $app['twig']->render('/pages/home.twig', array (
            'index' =>  Data\Translates::pageTranslates($app['locale'], 'home'),
            'data' => Data\Translates::translateArray($app['locale']),
        ));
    }
)
  ->assert('_locale', 'en')
  ->bind('home');

$createRoute = function ($routeName, $app) use ($app) {
    return function () use ($app, $routeName) {
        return $app['twig']->render('/pages/' . $routeName.'.twig', array(
            'index' =>  Data\Translates::pageTranslates($app['locale'], $routeName),
            'data' => Data\Translates::translateArray($app['locale']),
        ));
    };
};

foreach ($routes as $routeName) {
    $app->get('/{_locale}/'.$routeName.'/', $createRoute($routeName, $app))
        ->value('menu', $routeName)
        ->bind($routeName);
}

return $pages;
