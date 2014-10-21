<?php

/**
 * @var \Silex\Application $app
 */

require_once ROOT . '/app/data/control/data.php';
require_once ROOT . '/app/data/control/validate.php';

$app->mount('/', require ROOT . '/app/controllers/pages.php');
$app->mount('/{_locale}/api/', require ROOT . '/app/controllers/api.php');

