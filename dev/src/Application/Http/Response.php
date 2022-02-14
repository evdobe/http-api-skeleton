<?php declare(strict_types=1);

namespace Application\Http;

use Psr\Http\Message\ResponseInterface; 
use Laminas\Diactoros\Response\JsonResponse;

class Response extends JsonResponse implements ResponseInterface
{

}
