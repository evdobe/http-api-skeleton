<?php declare(strict_types=1);

namespace Application\Http;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

interface Application
{
    public function run(): void;

    public function pipe(string|array|callable|MiddlewareInterface|RequestHandlerInterface $middlewareOrPath, null|string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware = null): void;

    public function get(string $path, string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware, ?string $name = null): Route;
}
