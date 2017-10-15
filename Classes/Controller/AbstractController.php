<?php
declare(strict_types=1);

namespace Kanti\Controller;

use League\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use TYPO3Fluid\Fluid\View\AbstractTemplateView;

abstract class AbstractController
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var AbstractTemplateView
     */
    protected $view;

    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function setView(AbstractTemplateView $view)
    {
        $this->view = $view;

        $ControllerName = get_class($this);
        $ControllerName = str_replace('Controller', '', substr($ControllerName, strrpos($ControllerName, '\\') + 1));

        $context = $this->view->getRenderingContext();
        $context->setControllerName($ControllerName);
    }

    protected function render(ResponseInterface $response)
    {
        $result = $this->view->render(ucfirst(debug_backtrace()[1]['function']));
        $response->getBody()->write($result);
        return $response;
    }

    protected function redirect(ResponseInterface $response, string $target)
    {
        //@todo
        $response->getBody()->write($target);
        return $response;
    }
}
