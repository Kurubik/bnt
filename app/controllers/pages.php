<?php

/**
 * @var \Silex\Application $app
 * @var \Silex\ControllerCollection $pages
 */

use app\data\Data;
use lib\Signature;

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

    $generator = new lib\Signature\SignatureGenerator('A4KedhE55XeqWHXN3+exDKES4p8=');

    $requestParams = array (
        'amount' => 100,
        'site_id' => 245,
        'external_id' => 1,
    );

    $testSignature = $requestParams['signature'] = $generator->assemble($requestParams);

    return $app['twig']->render('/pages/temporary.twig', array (
         'index' =>  Data\Translates::pageTranslates($app['locale'], 'home'),
          'data' => Data\Translates::translateArray($app['locale']),
          'signature' => $testSignature
      ));
})
  ->assert('_lcoale', 'en')
  ->bind('home');


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
