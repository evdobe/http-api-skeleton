<?php declare(strict_types=1);

namespace Infrastructure\Http\Adapter\MezzioSwoole;

use Application\Http\Application as ApplicationApplication;
use Application\Http\Route as ApplicationRoute;
use Mezzio\Application as MezzioApplication;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Application implements ApplicationApplication
{
 
    public function __construct(protected MezzioApplication $delegate)
    {
        
    }

    public function run(): void
    {
        $this->delegate->run();
    }

    public function pipe(string|array|callable|MiddlewareInterface|RequestHandlerInterface $middlewareOrPath, null|string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware = null): void
    {
        $this->delegate->pipe($middlewareOrPath, $middleware);
    }

    public function get(string $path, string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware, ?string $name = null): ApplicationRoute
    {
        return new Route($this->delegate->get($path, $middleware, $name));
    }
}
