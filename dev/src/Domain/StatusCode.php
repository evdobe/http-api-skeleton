<?php declare(strict_types=1);
namespace Domain;

enum StatusCode: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';

}
