<?php

/**
 * @var \Silex\Application $app
 * @var \Silex\ControllerCollection $pages
 */

use app\data\Data;

$pages = $app['controllers_factory'];

$pages->get('/', function() use ($app) {
    return $app->redirect($app['url_generator']->generate('home'));
});

$routes = array (

    'media',
    'investors',
    'partners_n_suppliers',
    'career',
    'contacts',
    'products_n_services',
    array('project_ecofuel', 'our_projects'),
    array('project_ecoglycol', 'our_projects'),
    array('corporate_mission_and_vision', 'corporate_profile'),
    array('corporate_board_of_directors', 'corporate_profile'),
    array('sustainability_enviromental_impact','sustainability_information'),
    array('sustainability_infrastructure','sustainability_information'),
    array('sustainability_economical_impact', 'sustainability_information')
);


$pages->get('/{_locale}/',
    function() use ($app) {
        return $app['twig']->render('/pages/home.twig', array (
            'index' =>  Data\Translates::pageTranslates($app['locale'], 'home'),
            'data' => Data\Translates::translateArray($app['locale']),
        ));
    }
)
    ->assert('_locale', 'en|ru|de|cn')
    ->bind('home');

$createRoute = function ($routeName, $app) use ($app) {
    return function () use ($app, $routeName) {
        return $app['twig']->render('/pages/' . $routeName.'.twig', array(
            'index' =>  Data\Translates::pageTranslates($app['locale'], $routeName),
            'data' => Data\Translates::translateArray($app['locale']),
        ));
    };
};

foreach ($routes as $route) {

    if (is_array($route)) {        
        $routeName = $route[0];
        $parentName = $route[1];
    } else {       
        $routeName = $route;
        $parentName = '';
    }

    
    //$path = "/{_locale}/" . ($parentName !== '' ? "$parentName/" : "") . "$routeName/";   
    $path = "/{_locale}/" . ($parentName !== '' ? "{parent}/" : "") . "$routeName/";   

    $app->get($path, $createRoute($routeName, $app))
        ->value('menu', $routeName)
        ->value('parent', ($parentName !== '' ? $parentName : $routeName))
        ->bind($routeName);
}

return $pages;
