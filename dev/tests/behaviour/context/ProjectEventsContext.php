<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Behat\Tester\Exception\PendingException;
use Curl\Curl;
use Assert\Assertion;
use Assert\Assert;

/**
 * Defines application features from the specific context.
 */
class ProjectEventsContext implements Context
{
    const RECEIVED_EVENT_INSERT_SQL = 'INSERT INTO event 
        (name, "channel", "correlation_id", "aggregate_id", "aggregate_version", data, "timestamp", "received_at") 
        VALUES (:name, :channel, :correlation_id, :aggregate_id, :aggregate_version, :data, :timestamp, :received_at)';

    protected PDO $con;

    protected int $lastEventId;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
        $this->con = new PDO("pgsql:host=".getenv('DB_HOST').";dbname=".getenv('DB_NAME'), getenv('DB_USER'), getenv('DB_PASSWORD'));
        $stmt = $this->con->prepare('TRUNCATE TABLE "event"');
        $stmt->execute(); 
    }

    /**
     * @Given I have access to the event store
     */
    public function iHaveAccessToTheEventStore()
    {
        $stmt = $this->con->query('SELECT to_regclass("public.event");');
        $table = $stmt->fetch();

        Assert::that($table)->notEmpty();
    }

    /**
     * @When A new myaggregate created event is persisted in event store
     */
    public function aNewMyaggregateCreatedEventIsPersistedInEventStore()
    {
        $stmt = $this->con->prepare(self::RECEIVED_EVENT_INSERT_SQL);
        $stmt->execute(
            [
                ':name' => 'MyaggregateCreated',
                ':channel' => 'MyEventChannel',
                ':correlation_id' => 15,
                ':aggregate_id' => 2,
                ':aggregate_version' => 1,
                ':data' => '{"akey":"avalue"}',
                ':timestamp' => '2022-01-28 12:23:56',
                ':received_at' => '2022-01-28 12:25:56',
            ]
        );
        $this->lastEventId = $this->con->lastInsertId();
    }

    /**
     * @Then the  created event should be projected on myaggregate db table
     */
    public function theCreatedEventShouldBeProjectedOnMyaggregateDbTable()
    {
        $stmt = $this->con->prepare('SELECT * FROM "myaggregate" where id=:id;');
        $stmt->execute(['id' => $this->lastEventId]); 
        $aggregate = $stmt->fetch();

        Assert::that($$aggregate)->notEmpty();
    }


}
