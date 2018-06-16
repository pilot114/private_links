<?php

include '../vendor/autoload.php';

use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use App\Controllers\BaseController;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

function handle($uri)
{
    $routesDir = (new BaseController())->config['root_dir'] . '/config';
    $loader = new YamlFileLoader(new FileLocator([$routesDir]));
    $routeCollection = $loader->load('routes.yml');

    $context = new RequestContext();
    $matcher = new UrlMatcher($routeCollection, $context);

    try {
        $parameters = $matcher->match($uri);
        list($controller, $action) = explode('::', $parameters['_controller']);
        $controller = 'App\Controllers\\' . $controller;

        return (new $controller())->$action($parameters);

    } catch (ResourceNotFoundException $e) {
        return new \Symfony\Component\HttpFoundation\Response('404');
    }
}

$response = handle($_SERVER['REQUEST_URI']);
$response->send();