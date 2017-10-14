<?php
// bootstrap.php
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

require_once "vendor/autoload.php";

$whoops = new \Whoops\Run;
//$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->pushHandler(new \Whoops\Handler\PlainTextHandler);
$whoops->register();



// Create a simple "default" Doctrine ORM configuration for Annotations
$isDevMode = true;
$config = Setup::createAnnotationMetadataConfiguration([__DIR__ . "/src/Domain/Model/"], $isDevMode);

// database configuration parameters
$conn = [
    'driver' => 'pdo_sqlite',
    'path' => __DIR__ . '/Resources/Private/Database/db.sqlite',
];

// obtaining the entity manager
$entityManager = EntityManager::create($conn, $config);


function x()
{
// pass the constructed TemplatePaths instance to the View
    $view = new \TYPO3Fluid\Fluid\View\TemplateView();
    $paths = $view->getTemplatePaths();
    $paths->setTemplateRootPaths([__DIR__ . '/Resources/Private/Templates/']);
    $paths->setLayoutRootPaths([__DIR__ . '/Resources/Private/Layouts/']);
    $paths->setPartialRootPaths([__DIR__ . '/Resources/Private/Partials/']);

    $view->assign('hello', 'World');
    echo $view->render();
}