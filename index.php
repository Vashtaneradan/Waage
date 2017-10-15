<?php
declare(strict_types=1);

require_once "vendor/autoload.php";

(new \Kanti\Startup\Application())
    ->configureLogger()
    ->configureControllers()
    ->configureTemplate()
    ->configureRouting()
    ->dispatchRouting();
