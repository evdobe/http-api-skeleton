<?php declare(strict_types=1);

namespace Infrastructure\Http\Adapter\Swoole;

use Application\Http\Server as HttpServer;

use Swoole\Http\Server as SwooleServer;

use Application\Execution\Process;

class Server implements HttpServer
{
    public function __construct(protected SwooleServer  $delegate){
        
    }

    public function addProcess(Process $process): bool{
        return (boolean)$this->delegate->addProcess($process->getDelegate());
    }

    public function on(string $eventName, callable $callback): void
    {
        $swooleCallbak = match ($eventName){
            'start' => function(SwooleServer $server) use ($callback){
                $callback($this);
            },
            default => $callback
        };
        $this->delegate->on(event_name: $eventName, callback: $swooleCallbak);
    }
}
