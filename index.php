<?php
define ('ROOT', __DIR__);

require_once  ROOT . '/vendor/autoload.php';


$app = new Silex\Application();
$app['debug'] = true;

$app['default_locale'] = 'en';


require ROOT . '/app/config/components.php';
require ROOT . '/app/config/routes.php';

//$app['http_cache']->run();
$app->run();
