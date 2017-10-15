<?php
declare(strict_types=1);

require_once "vendor/autoload.php";

(new \Kanti\Startup\Application())
    ->registerErrorHandler()
    ->configureLogger()
    ->configureControllers()
    ->configureTemplate()
    ->configureRouting()
    ->dispatchRouting();
