<?php declare(strict_types=1);

namespace Application\Http;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface; 

class Response extends JsonResponse implements ResponseInterface
{

}
