<?php declare(strict_types=1);

namespace Domain;

use DateTimeImmutable;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Embeddable;
use JsonSerializable;

#[Embeddable]
class Status implements JsonSerializable
{
    public function __construct(
        #[Column(type: 'string', enumType: StatusCode::class)]
        private readonly StatusCode $code,

        #[Column(type: 'string', nullable: true)]
        private readonly ?string $by,

        #[Column(type:'datetime_immutable')]
        private readonly DateTimeImmutable $at
    )
    {
        
    }

    public function jsonSerialize(): mixed
    {
        return [
            'code' => $this->code->value,
            'by' => $this->by,
            'at' => $this->at->format('Y-m-d H:i:s')
        ];
    }

}
