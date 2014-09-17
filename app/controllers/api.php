<?php

/**
 * @var \Silex\Application $app
 * $var \Silex\ControllerCollection $api
 */
use Symfony\Component\HttpFoundation\Response;

use app\data\Data;
use lib\Signature;

$api = $app['controllers_factory'];

$api->post('/payment/', function() use ($app) {
    $generator = new lib\Signature\SignatureGenerator('A4KedhE55XeqWHXN3+exDKES4p8=');
    $site_id = 245;
    $external_id = rand();
    $currency = 'EUR';
    $amount = (int)$_POST['plan'];

    $requestParams = array (
        'amount' => $amount,
        'currency' => $currency,
        'site_id' => $site_id,
        'external_id' => $external_id,
    );

    $signature = $requestParams['signature'] = $generator->assemble($requestParams);
    $src = 'https://terminal-sandbox.ecommpay.com/?site_id=' . $site_id . '&amount=' . $amount . '&currency=' . $currency . '&external_id=' . $external_id . '&signature=' . $signature;
    return $src;

})->assert('_locale', 'en');

return $api;