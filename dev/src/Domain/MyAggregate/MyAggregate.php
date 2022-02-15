<?php declare(strict_types=1);

namespace Domain\MyAggregate;

use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Id;

#[Entity()]
#[Table(name:"my_aggregate")]
class MyAggregate
{
    public function __construct(

        #[Column(type:"integer")]
        #[Id]
        private int $id,

        #[Column(type:"json")]
        private array $data
    ){
        
    }

}
