<?php
declare(strict_types=1);

namespace Kanti\Startup;

use Kanti\Controller\SessionController;
use Kanti\Controller\UserController;
use League\Route\RouteCollectionInterface;

/**
 * Class Routes
 * @package Kanti\Startup
 */
class Routes
{
    public function configure(RouteCollectionInterface $route)
    {
        $route->map('GET', '/register', UserController::class . '::register');
        $route->map('POST', '/register', UserController::class . '::create');

        $route->map('GET', '/login', SessionController::class . '::login');
        $route->map('POST', '/login', SessionController::class . '::create');

        $route->map('GET', '/logout', SessionController::class . '::destroy');
    }
}
