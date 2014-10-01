<?php

/**
 * @var \Silex\Application $app
 * @var \Silex\ControllerCollection $pages
 */

use app\data\Data;
use App\Plans;

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


$pages->get('/{_locale}/plan/{plan}/',
    function($plan) use ($app) {
        return $app['twig']->render('/pages/plan_form.twig', array (
            'data' => Data\Translates::translateArray($app['locale']),
            'countries' => Data\Translates::countryList(),
            'current_plan' => $plan
        ));
    }
)
    ->assert('_local', 'en')
    ->bind('currentPlan');



$pages->match('/success/',
    function () use ($app) {
        $generator = new lib\Signature\SignatureGenerator($app['salt']);

        $requestParams = array (
            'amount' => $_POST['amount'],
            'currency' => $_POST['currency'],
            'description' => $_POST['description'],
            'external_id' => $_POST['external_id'],
            'language' => $_POST['language'],
            'payment_type_id' => $_POST['payment_type_id'],
            'real_amount' => $_POST['real_amount'],
            'real_currency' => $_POST['real_currency'],
            'site_id' => $app['site_id'],
            'transaction_id' => $_POST['transaction_id'],
            'type' => $_POST['type'],
        );

        $signature = $requestParams['signature'] = $generator->assemble($requestParams);
        if ($signature === $_POST['signature'] && $_POST['type'] == 1) {
//            App\Plans\Order::orderSuccess($app['db'], $_POST['external_id'], $_POST['type']);
            return $app['twig']->render('/pages/success.twig', array(
                'data' => Data\Translates::translateArray($app['locale']),
              ));
        } else {
            return $app['twig']->render('/pages/fail.twig', array(
                'data' => Data\Translates::translateArray($app['locale']),
              ));
        }
    }
);


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
