<?php

/**
 * @var \Silex\Application $app
 */


$app->register(new Silex\Provider\UrlGeneratorServiceProvider());


$app->register(new Silex\Provider\TwigServiceProvider(), array (
    'twig.path' => ROOT . '/app/views',
    'twig.options' => array (
//       'cache' => ROOT . '/app/cache/twig.cache',
       'debug' => false,
       'auto_reload' => false,
       'strict_variables' => false,
       'optimizations' => -1,
    )
));


$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
      'db.options' => array (
        'driver' => 'pdo_mysql',
        'dbname' => 'dialoq',
        'user' => 'root',
        'password' => '',
        'charset' => 'utf8',
        'host' => '127.0.0.1'
      )
));

$app->register(new Silex\Provider\SwiftmailerServiceProvider());

$app->register(new Silex\Provider\HttpCacheServiceProvider(), array(
    'http_cache.cache_dir' => ROOT .'/app/cache/http/',
    'http_cache.esi'       => null
));

$app['swiftmailer.options'] = array(
    'host' => 'smtp.gmail.com',
    'port' => '465',
    'username' => '',
    'password' => '',
    'encryption' => 'ssl',
    'auth_mode'  => 'login'
);

