<?php declare(strict_types=1);

namespace Application\Http\Handler;

use Application\Http\Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Application\Persistence\Manager;
use Application\Persistence\Repository;
use Domain\MyAggregate;
use Laminas\Diactoros\Response\EmptyResponse;

class MyAggregateByIdHandler  implements RequestHandlerInterface
{

    private Repository $myAggregateRepository;

    public function __construct(Manager $manager)
    {
        $this->myAggregateRepository = $manager->getRepository(MyAggregate::class);
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $myAggregate = $this->myAggregateRepository->find(id: $request->getAttribute('aggregateId'));
        if (!$myAggregate){
            return (new EmptyResponse())->withStatus(404);
        }
        return new Response($myAggregate);
    }
}
