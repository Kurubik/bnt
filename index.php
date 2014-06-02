<?php
require_once 'vendor/autoload.php';

$app = new Silex\Application();

$app['debug'] = true;

$app->register(new Silex\Provider\TwigServiceProvider(), array (
   'twig.path' => __DIR__ . '/app/templates',
   'twig.options' => array (
//       'cache' => __DIR__ . '/app/cache/twig.cache',
//       'debug' => false,
//       'auto_reload' => false,
//       'strict_variables' => false,
//       'optimizations' => -1,
   )
));

$app->get('/', function () use ($app) {
    return $app->redirect('/ru/');
});

$app->get('/{_locale}/', function () use ($app) {
    return $app['twig']->render('index.twig', array(
        'data' => require __DIR__ . '/app/data/data_'.  $app['locale'] .'/data.php',
    ));
})
    ->value('_locale', 'ru')
    ->bind('home');

$routes = array (
    'about',
    'deal',
    'buy',
    'invest',
    'owners',
    'contacts',
    'deal-map',
    'disclaimer',
);

$createRoute = function ($routeName, $app) use ($app) {
    return function () use ($app, $routeName) {
        return $app['twig']->render($routeName.'.twig', array(
            'index' => require __DIR__ . '/app/data_'.  $app['locale'] .'/'. $routeName.'.php',
            'data' => require __DIR__ . '/app/data_'.  $app['locale'] .'/data.php',
        ));
    };
};

foreach ($routes as $routeName) {
    $app->get('/{_locale}/'.$routeName.'/', $createRoute($routeName, $app))
        ->bind($routeName);
}


$app->run();