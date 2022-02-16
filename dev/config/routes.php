<?php

declare(strict_types=1);

use Application\Http\Application;
use Application\Http\Handler\MyAggregateByIdHandler;
use Application\Http\Handler\MyAggregateHandler;
use Application\Http\Handler\PingHandler;

/**
 * FastRoute route configuration
 *
 * @see https://github.com/nikic/FastRoute
 *
 * Setup routes with a single request method:
 *
 * $app->get('/', App\Handler\HomePageHandler::class, 'home');
 * $app->post('/album', App\Handler\AlbumCreateHandler::class, 'album.create');
 * $app->put('/album/{id:\d+}', App\Handler\AlbumUpdateHandler::class, 'album.put');
 * $app->patch('/album/{id:\d+}', App\Handler\AlbumUpdateHandler::class, 'album.patch');
 * $app->delete('/album/{id:\d+}', App\Handler\AlbumDeleteHandler::class, 'album.delete');
 *
 * Or with multiple request methods:
 *
 * $app->route('/contact', App\Handler\ContactHandler::class, ['GET', 'POST', ...], 'contact');
 *
 * Or handling all request methods:
 *
 * $app->route('/contact', App\Handler\ContactHandler::class)->setName('contact');
 *
 * or:
 *
 * $app->route(
 *     '/contact',
 *     App\Handler\ContactHandler::class,
 *     Mezzio\Router\Route::HTTP_METHOD_ANY,
 *     'contact'
 * );
 */

return static function (Application $app): void {

    $basePath = getenv('BASE_HTTP_PATH');

    $app->get(getenv('HEALTHCHECK_HTTP_PATH'), PingHandler::class, 'ping');
    $app->get($basePath.'/my-aggregate', MyAggregateHandler::class, 'my-aggregate-get-all');
    $app->get($basePath.'/my-aggregate/{aggregateId}', MyAggregateByIdHandler::class, 'my-aggregate-get-by-id');
};
