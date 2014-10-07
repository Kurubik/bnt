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
        $data = $_POST;
        if ($data['plan_type'] == 1) {
            unset($data['company']);
        }
        $validate = Action\ValidateAction::validate($data);

        if ($validate) {

            $order_type =  App\Plans\Order::getPlanType($data['plan_type'], $app['db']);
            $generator = new lib\Signature\SignatureGenerator($app['salt']);

            $lang = $app['locale'];
            $success = $app['current_host'] . '/success/';
            $fail = $app['current_host'] . '/fail/';
            $callback_method = 4;
            $site_full_name = $data['name'] . ' ' . $data['surname'];
            $email = $data['email'];
            $phone = $data['phone'];
            $country = Data\Translates::getCountryShortName($data['country']);
            $city = $data['city'];
            $postal = $data['zip'];
            $address = $data['address'];
            $description = 'Plan ' . $data['plan'] . ',  ' . 'Sim ' . $data['sim'] . ',  ' . 'Order ' . $order_type;

            $site_id = $app['site_id'];
            $currency = 'EUR';
            $amount = App\Plans\Order::getPlanPrice($_POST['plan'], $app['db']);
            $external_id = App\Plans\Order::createOrder($app['db'], $_POST);

            $requestParams = array(
                'amount' => $amount,
                'currency' => $currency,
                'site_id' => $site_id,
                'external_id' => $external_id,
                'language' => $lang,
//                'success_url' => $success,
//                'decline_url' => $fail,
                'callback_method' => $callback_method,
                'site_full_name' => $site_full_name,
                'site_email' => $email,
                'site_phone' => $phone,
                'delivery_full_name' => $site_full_name,
                'delivery_country' => $country,
                'delivery_city' => $city,
                'delivery_address' => $address,
                'delivery_postal' => $postal,
                'delivery_phone' => $phone,
                'description' => $description
            );

            $signature = $requestParams['signature'] = $generator->assemble($requestParams);
            $signature_array = array ('signature' => $signature);
            $requestData = array_merge ($requestParams, $signature_array);

            $finalRequest = $app['ecommpay'] . '?' . http_build_query($requestData);
            return $finalRequest;
        } else {
            return false;
        }
    }
)->assert('_locale', 'en|ru');



$api->post('/email/{name}/',
    function($name) use ($app) {
        $translates = Data\Translates::translateEmail($app['locale']);
        $data = $_POST;
        $company = trim($data['company']);
        $client_email = trim($data['email']);
        $client_name  = trim($data['name']);

        if (empty($company) && $name === 'bank' || $data['plan_type'] == 1) {
            unset($data['company']);
        }

        // TODO use silex/symphony validator !!
        $validate = Action\ValidateAction::validate($data);

        if ($validate) {

            if (isset($data['plan_type'])) {
                $data['plan_type'] = App\Plans\Order::getPlanType($data['plan_type'], $app['db']);
            }

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
                'ka@vcgworld.com',
//                'dv@dialoq.com'
            );

            $message = \Swift_Message::newInstance()
              ->setBody(
                $app['twig']->render('/mail/'. $name .'.twig',
                    array(
                            'data' => $data,
                            'translation' => $translates
                        )
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
)->assert('_locale', 'en|ru');



return $api;