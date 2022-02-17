<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Behat\Tester\Exception\PendingException;
use Curl\Curl;
use Assert\Assertion;
use Assert\Assert;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Hook\Scope\AfterScenarioScope;

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

    protected Curl $curl;

    protected int $port;

    protected string $basePath;

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
        $this->port = getenv('HTTP_PORT');
        $this->basePath = getenv('BASE_HTTP_PATH');
    }

    /**
      * @BeforeScenario @database
      */
      public function cleanDB(BeforeScenarioScope $scope)
      {
        $stmt = $this->con->prepare('DELETE FROM "event"');
        $stmt->execute(); 
        $stmt = $this->con->prepare('DELETE FROM "my_aggregate"');
        $stmt->execute(); 
      }

    /**
     * @Given I have access to the event store
     */
    public function iHaveAccessToTheEventStore()
    {
        $stmt = $this->con->query('SELECT to_regclass(\'event\');');
        $table = $stmt->fetch();

        Assert::that($table)->notEmpty();
    }

    /**
     * @Given A new myaggregate created event has been added to event store
     */
    public function aNewMyaggregateCreatedEventHasBeenAddedToEventStore()
    {
        $eventData = json_decode(file_get_contents('/contracts/db/event/MyAggregate/Created.json'), true);
        $stmt = $this->con->prepare(self::RECEIVED_EVENT_INSERT_SQL);
        $stmt->execute($eventData);
        $this->lastEventId = $this->con->lastInsertId();
        sleep(1);
    }

    /**
     * @When I query the api for all myaggregates
     */
    public function iQueryTheApiForAllMyaggregates()
    {
        $this->curl = new Curl();
        $this->curl->get('http://http-api:'.$this->port.$this->basePath.'/my-aggregate');
    }

    /**
     * @Then the new myaggregate should be contained in result
     */
    public function theNewMyaggregateShouldBeContainedInResult()
    {
        $aggregateData = json_decode(file_get_contents('/contracts/api/my-aggregate.json'), true);
        Assert::that($this->curl->error)->noContent();
        Assert::that($this->curl->getHttpStatus())->eq(200);
        Assert::that($this->curl->response)->notEmpty();
        Assert::that('Content-Type: application/json')->inArray($this->curl->response_headers);
        Assert::that($this->curl->response)->isJsonString();
        Assert::that($aggregateData)->inArray(json_decode($this->curl->response, true));
    }

    /**
     * @When I query the api for myaggregate by id
     */
    public function iQueryTheApiForMyaggregateById()
    {
        $eventData = json_decode(file_get_contents('/contracts/db/event/MyAggregate/Created.json'), true);
        $this->curl = new Curl();
        $this->curl->get('http://http-api:'.$this->port.$this->basePath.'/my-aggregate/'.$eventData[':aggregate_id']);
    }

    /**
     * @Then the new myaggregate should returned
     */
    public function theNewMyaggregateShouldReturned()
    {
        $aggregateData = json_decode(file_get_contents('/contracts/api/my-aggregate.json'), true);
        Assert::that($this->curl->error)->noContent();
        Assert::that($this->curl->getHttpStatus())->eq(200);
        Assert::that($this->curl->response)->notEmpty();
        Assert::that('Content-Type: application/json')->inArray($this->curl->response_headers);
        Assert::that($this->curl->response)->isJsonString();
        Assert::that(json_decode($this->curl->response, true))->eq($aggregateData);
    }

}
