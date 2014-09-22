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
    'service',
    'plans',
    'support',
    'contacts',
    'company'
);


$pages->get('/{_locale}/', function() use ($app) {
    return $app['twig']->render('/pages/temporary.twig', array (
         'index' =>  Data\Translates::pageTranslates($app['locale'], 'home'),
          'data' => Data\Translates::translateArray($app['locale']),
      ));
})
  ->assert('_locale', 'en')
  ->bind('home');


$pages->get('/{_locale}/plan/{plan}/', function($plan) use ($app) {
    return $app['twig']->render('/pages/plan_form.twig', array (
         'data' => Data\Translates::translateArray($app['locale']),
         'countries' => Data\Translates::countryList(),
         'current_plan' => $plan
      ));
})
    ->assert('_local', 'en')
    ->bind('currentPlan');


$createRoute = function ($routeName, $app) use ($app) {
    return function () use ($app, $routeName) {
        return $app['twig']->render('/pages/' . $routeName . '.twig', array(
            'index' => Data\Translates::pageTranslates($app['locale'], $routeName),
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
