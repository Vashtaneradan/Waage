<?php
declare(strict_types=1);

namespace Kanti\Startup;

use Kanti\Controller\AbstractController;
use League\Container\Container;
use League\Container\ReflectionContainer;
use League\Route\RouteCollection;
use League\Route\RouteCollectionInterface;
use Monolog\Logger;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use TYPO3Fluid\Fluid\Core\Cache\SimpleFileCache;
use TYPO3Fluid\Fluid\View\AbstractTemplateView;
use TYPO3Fluid\Fluid\View\TemplateView;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;
use Zend\Diactoros\Response;
use Zend\Diactoros\Response\SapiEmitter;
use Zend\Diactoros\ServerRequestFactory;

class Application
{
    /**
     * @var Container
     */
    protected $container;

    public function __construct()
    {
        $this->container = new Container();
        $this->container->delegate(new ReflectionContainer());
    }

    public function registerErrorHandler()
    {
        $whoops = $this->container->get(Run::class);
        $whoops->pushHandler($this->container->get(PrettyPageHandler::class));
        $whoops->register();
        return $this;
    }

    public function configureLogger()
    {
        $this->container->add(LoggerInterface::class, Logger::class)->withArguments([
            'name' => 'MainLog',
            'handlers' => [],
            'processors' => [],
        ]);
        return $this;
    }

    public function configureControllers()
    {
        $this->container->inflector(AbstractController::class)
            ->invokeMethod('setContainer', [$this->container])
            ->invokeMethod('setView', [AbstractTemplateView::class]);
        return $this;
    }

    public function configureTemplate()
    {
        $this->container->add(AbstractTemplateView::class, function () {
            $view = new TemplateView();
            $paths = $view->getTemplatePaths();
            $paths->setTemplateRootPaths([getcwd() . '/Resources/Private/Templates/']);
            $paths->setLayoutRootPaths([getcwd() . '/Resources/Private/Layouts/']);
            $paths->setPartialRootPaths([getcwd() . '/Resources/Private/Partials/']);
            if (is_file(getcwd() . '/Cache')) {
                unlink(getcwd() . '/Cache/');
            }
            if (!is_dir(getcwd() . '/Cache')) {
                mkdir(getcwd() . '/Cache/');
            }
            $view->setCache(new SimpleFileCache(getcwd() . '/Cache/'));
            return $view;
        });
        return $this;
    }

    public function configureRouting()
    {
        $this->container->add(RouteCollectionInterface::class, function () {
            $route = new RouteCollection($this->container);
            //$route->middleware(function () {});
            $this->container->get(Routes::class)->configure($route);
            return $route;
        });
        $this->container->add(ResponseInterface::class, Response::class);
        $this->container->add(ServerRequestInterface::class, function () {
            $path = dirname(substr($_SERVER["SCRIPT_FILENAME"], strlen($_SERVER["DOCUMENT_ROOT"])));
            if ($path && $path != '.' && $path != '..') {
                $_SERVER['REQUEST_URI'] = str_replace($path, '', $_SERVER['REQUEST_URI']);
            }
            return ServerRequestFactory::fromGlobals(
                $_SERVER,
                $_GET,
                $_POST,
                $_COOKIE,
                $_FILES
            );
        });
        return $this;
    }

    public function dispatchRouting()
    {
        $route = $this->container->get(RouteCollectionInterface::class);
        $serverRequest = $this->container->get(ServerRequestInterface::class);
        $response = $this->container->get(ResponseInterface::class);
        $emitter = $this->container->get(SapiEmitter::class);

        $response = $route->dispatch($serverRequest, $response);
        $emitter->emit($response);
    }
}
