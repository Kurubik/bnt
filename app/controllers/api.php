<?php

/**
 * @var \Silex\Application $app
 * $var \Silex\ControllerCollection $api
 */
use Symfony\Component\HttpFoundation\Response;

use app\data\Data;
use app\Action;
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



$api->post('/email/{name}/',
    function($name) use ($app) {
        $translates = Data\Translates::translateEmail($app['locale']);
        $data = $_POST;
        $company = trim($data['company']);
        $client_email = trim($data['email']);
        $client_name  = trim($data['name']);

        if (empty($company) && $name === 'bank') {
            unset($data['company']);
        }

        // TODO use silex/symphony validator !!
        $validate = Action\ValidateAction::validateEmail($data);

        if ($validate) {

            $subject = '';
            switch ($name) {
                case 'bank':
                    $subject = $translates['order'];
                    break;
                case 'business':
                    $subject = $translates['business'];
                    break;
            }

            $mails = array(
              'ka@vcgworld.com'
            );

            $message = \Swift_Message::newInstance()
              ->setBody(
                $app['twig']->render('/mail/'. $name .'.twig',
                    array(
                        'data' => $data,
                        'translation' => $translates)
                    ),
                    'text/html'
              )
              ->setSubject($subject . ': dialoq.com')
              ->setReplyTo($client_email, $client_name)
              ->setFrom('dialoqtest@gmail.com', $client_name)
              ->setTo($mails);

              return $app['mailer']->send($message);
        } else {
            return false;
        }
    }
)->assert('_locale', 'en');



return $api;