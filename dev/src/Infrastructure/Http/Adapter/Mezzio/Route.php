<?php declare(strict_types=1);

namespace Infrastructure\Http\Adapter\Mezzio;

use Application\Http\Route as ApplicationRoute;
use \Mezzio\Router\Route as MezzioRoute;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;



class Route implements ApplicationRoute
{
    public function __construct(protected MezzioRoute $delegate)
    {
        
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $this->delegate->process($request, $handler);
    }
}
