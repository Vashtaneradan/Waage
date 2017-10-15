<?php
declare(strict_types=1);

namespace Kanti\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class SessionController extends AbstractController
{
    public function login(ServerRequestInterface $request, ResponseInterface $response)
    {
        return $this->render($response);
    }

    public function create(ServerRequestInterface $request, ResponseInterface $response)
    {
        //@todo
        return $this->redirect($response, 'afterCreate');
    }

    public function destroy(ServerRequestInterface $request, ResponseInterface $response)
    {
        //@todo
        return $this->redirect($response, 'afterDestroy');
    }
}
