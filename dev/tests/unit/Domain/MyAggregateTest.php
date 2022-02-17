<?php declare(strict_types=1);

namespace Domain;

use PHPUnit\Framework\TestCase;

final class MyAggregateTest extends TestCase
{
    public function testShouldApplyCreatedEvent(){
        $event = new CreatedEvent(
            id: 12,
            channel: 'achannel',
            correlationId: null,
            aggregateId: 34,
            aggregateVersion: 5,
            data: ['akey' => 'avalue'],
            timestamp: new \DateTimeImmutable('2022-02-16 12:45:23')
        );
        $sut = new MyAggregate([$event]);
        $sutSerialized = $sut->jsonSerialize();

        $this->assertEquals(34, $sutSerialized['id']);
        $this->assertEquals(5, $sutSerialized['version']);
        $this->assertEquals(['akey' => 'avalue'], $sutSerialized['data']);

    }
}
