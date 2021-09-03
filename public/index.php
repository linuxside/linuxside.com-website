<?php

$root = realpath(__DIR__ . '/..');

require_once $root . '/vendor/autoload.php';

use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing;
use App\Content\BlogPosts;
use App\Controllers\NotFound;

Debug::enable();
container()->setParameter('root', $root);

$config = include $root . '/app/config.php';
container()->setParameter('config', $config);

$request = Request::createFromGlobals();
container()->set('request', $request);
$routes = include $root . '/app/routes.php';
container()->set('routes', $routes);

$context = new Routing\RequestContext();
$context->fromRequest($request);
container()->set('context', $context);

try {
    BlogPosts::getInstance()->loadContentFromCache();

    $matcher = new Routing\Matcher\UrlMatcher($routes, $context);

    $request->attributes->add(
        $matcher->match($request->getPathInfo())
    );

    $controller = $request->attributes->get('_controller')[0];
    $method = $request->attributes->get('_controller')[1];

    $response = call_user_func([new $controller, $method], $request);
} catch (Routing\Exception\ResourceNotFoundException $e) {
    $response = call_user_func([new NotFound, 'index'], $request);
} catch (Throwable $e) {
    $response = new Response(sprintf(
        'An error occurred: <b>%s</b><br />File: <b>%s</b><br />Line: <b>%d</b>',
        $e->getMessage(), $e->getFile(), $e->getLine()
    ), Response::HTTP_INTERNAL_SERVER_ERROR);
}

$response->send();
