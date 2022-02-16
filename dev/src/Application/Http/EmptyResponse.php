<?php declare(strict_types=1);

namespace Application\Http;

use Psr\Http\Message\ResponseInterface; 
use Laminas\Diactoros\Response\EmptyResponse;

class Response extends EmptyResponse implements ResponseInterface
{

}
