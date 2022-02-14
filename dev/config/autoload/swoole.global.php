<?php declare(strict_types=1);
use Laminas\Stdlib\ArrayUtils\MergeReplaceKey;
use Mezzio\Swoole\Event;

return [
    'mezzio-swoole' => [
        'swoole-http-server' => [
            'host' => '0.0.0.0',
            'port' => (int)getenv('HTTP_PORT'),
            'listeners' => [
                Event\RequestEvent::class => new MergeReplaceKey([
                    Event\RequestHandlerRequestListener::class,
                ]),
            ],
        ]
    ]
];