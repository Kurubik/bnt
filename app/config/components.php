<?php

/**
 * @var \Silex\Application $app
 */

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());


$app->register(new Silex\Provider\TwigServiceProvider(), array (
    'twig.path' => ROOT . '/app/templates',
    'twig.options' => array (
//       'cache' => ROOT . '/app/cache/twig,
//       'debug' => false,
       'auto_reload' => false,
       'strict_variables' => false,
       'optimizations' => -1,
    )
));


$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
      'db.options' => array (
        'driver' => 'pdo_mysql',
        'dbname' => '',
        'user' => 'test',
        'password' => '',
        'charset' => 'utf8',
        'host' => '192.168.1.33'
      )
));


$app->register(new Silex\Provider\HttpCacheServiceProvider(), array(
    'http_cache.cache_dir' => ROOT .'/app/cache/http/',
    'http_cache.esi'       => null
));

$app->register(new Silex\Provider\SwiftmailerServiceProvider());



$app['swiftmailer.options'] = array(
    'host' => 'smtp.gmail.com',
    'port' => '465',
    'username' => '',
    'password' => '',
    'encryption' => 'ssl',
    'auth_mode'  => 'login'
);

