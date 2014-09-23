<?php

/**
 * @var \Silex\Application $app
 * $var \Silex\ControllerCollection $api
 */
use Symfony\Component\HttpFoundation\Response;

use app\data\Data;
use lib\Signature;
use App\Plans;

$api = $app['controllers_factory'];

$api->post('/payment/',
    function() use ($app) {
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
        $src = 'https://terminal-sandbox.ecommpay.com/?
                site_id=' . $site_id .
                '&amount=' . $amount .
                '&currency=' . $currency .
                '&external_id=' . $external_id .
                '&signature=' . $signature;


//        $oder = App\Plans\Order::createOrder();
        return $src;
    }
)->assert('_locale', 'en');



$api->post('/email/',
    function() use ($app) {
        $translates = Data\Translates::translateArray($app['locale']);
        $data = $_POST;
        $client_email = trim($data['email']);
        $client_name  = trim($data['name']);
        $message_text = trim($data['text']);

        echo '<pre>';
        print_r($_POST);

//        if (!empty($client_email) && !empty($client_name) && !empty($message_text)) {
//
//            $mails = array(
//              'ka@vcgworld.com'
//            );
//
//            $message = \Swift_Message::newInstance()
//              ->setBody(
//                $app['twig']->render('/mail/mail.twig', array('data' => $data, 'translation' => $translates)),
//                'text/html'
//              )
//              ->setSubject($translates['contacts'] . ': barniarabia.com')
//              ->setReplyTo($client_email, $client_name)
//              ->setFrom('nevedimkax@gmail.com', $client_name)
//              ->setTo($mails);
//
//            return $app['mailer']->send($message);
//        } else {
//            return false;
//        }
    }
)->assert('_locale', 'en');



return $api;