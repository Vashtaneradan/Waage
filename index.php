<?php
declare(strict_types=1);

namespace Kanti;

use League\Container\Container;
use League\Container\ReflectionContainer;
use League\Route\RouteCollection;
use League\Route\RouteGroup;
use Monolog\Logger;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use TYPO3Fluid\Fluid\View\TemplateView;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;

require_once "vendor/autoload.php";

//setup Container and DI
$container = new Container();
$container->delegate(new ReflectionContainer());

//Logging
$container->add(LoggerInterface::class, Logger::class)->withArguments([
    'name' => 'MainLog',
    'handlers' => [],
    'processors' => [],
]);

//register stuff
$container->add(TemplateView::class)->withArgument(null);

//error handling
$whoops = $container->get(Run::class);
$whoops->pushHandler($container->get(PrettyPageHandler::class));
$whoops->register();

$container->add(ResponseInterface::class, Response::class);
$container->add(ServerRequestInterface::class, function () {
    return ServerRequestFactory::fromGlobals(
        $_SERVER,
        $_GET,
        $_POST,
        $_COOKIE,
        $_FILES
    );
});


//routing
$route = new RouteCollection();
$route->group('/Waage/', function (RouteGroup $route) use ($container) {
    $route->map('GET', '/', function (ServerRequestInterface $request, ResponseInterface $response) use ($container) {
        // templating
        $view = $container->get(TemplateView::class);
        $paths = $view->getTemplatePaths();
        $paths->setTemplateRootPaths([__DIR__ . '/Resources/Private/Templates/']);
        $paths->setLayoutRootPaths([__DIR__ . '/Resources/Private/Layouts/']);
        $paths->setPartialRootPaths([__DIR__ . '/Resources/Private/Partials/']);

        $view->assign('hello', 'World');

        $response->getBody()->write($view->render());

        return $response;
    });
});
$response = $route->dispatch($container->get(ServerRequestInterface::class), $container->get(ResponseInterface::class));
$container->get(Response\SapiEmitter::class)->emit($response);
