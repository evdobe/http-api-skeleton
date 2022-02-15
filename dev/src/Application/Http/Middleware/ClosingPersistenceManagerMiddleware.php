<?php declare(strict_types=1);

namespace Application\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use Application\Persistence\Manager;

class ClosingPersistenceManagerMiddleware implements MiddlewareInterface
{
    public function __construct(protected Manager $manager)
    {

    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->manager->open();
        
        try {
            return $handler->handle($request);
        } finally {
            $this->manager->close();
        }
    }
}
