<?php

require_once "vendor/autoload.php";

$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();


// pass the constructed TemplatePaths instance to the View
$view = new \TYPO3Fluid\Fluid\View\TemplateView();
$paths = $view->getTemplatePaths();
$paths->setTemplateRootPaths([__DIR__ . '/Resources/Private/Templates/']);
$paths->setLayoutRootPaths([__DIR__ . '/Resources/Private/Layouts/']);
$paths->setPartialRootPaths([__DIR__ . '/Resources/Private/Partials/']);

$view->assign('hello', 'World');
echo $view->render();
