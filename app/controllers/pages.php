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

return $pages;
