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

    protected string $host;

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
        $this->host = getenv('HTTP_HOST')?:'localhost';
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
        $this->curl->get('http://'.$this->host.':'.$this->port.$this->basePath.'/my-aggregate');
    }

    /**
     * @Then the new myaggregate should be contained in result
     */
    public function theNewMyaggregateShouldBeContainedInResult()
    {
        $aggregateData = json_decode(file_get_contents('/contracts/api/my-aggregate-new.json'), true);
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
        $this->curl->get('http://'.$this->host.':'.$this->port.$this->basePath.'/my-aggregate/'.$eventData[':aggregate_id']);
    }

    /**
     * @Then the new myaggregate should returned
     */
    public function theNewMyaggregateShouldReturned()
    {
        $aggregateData = json_decode(file_get_contents('/contracts/api/my-aggregate-new.json'), true);
        Assert::that($this->curl->error)->noContent();
        Assert::that($this->curl->getHttpStatus())->eq(200);
        Assert::that($this->curl->response)->notEmpty();
        Assert::that('Content-Type: application/json')->inArray($this->curl->response_headers);
        Assert::that($this->curl->response)->isJsonString();
        Assert::that(json_decode($this->curl->response, true))->eq($aggregateData);
    }

    /**
     * @Given An aggregate activated event for this aggregate has been added to event store
     */
    public function anAggregateActivatedEventForThisAggregateHasBeenAddedToEventStore()
    {
        $eventData = json_decode(file_get_contents('/contracts/db/event/MyAggregate/Activated.json'), true);
        $stmt = $this->con->prepare(self::RECEIVED_EVENT_INSERT_SQL);
        $stmt->execute($eventData);
        $this->lastEventId = $this->con->lastInsertId();
        sleep(1);
    }

    /**
     * @Then the activated myaggregate should returned
     */
    public function theActivatedMyaggregateShouldReturned()
    {
        $aggregateData = json_decode(file_get_contents('/contracts/api/my-aggregate-active.json'), true);
        Assert::that($this->curl->error)->noContent();
        Assert::that($this->curl->getHttpStatus())->eq(200);
        Assert::that($this->curl->response)->notEmpty();
        Assert::that('Content-Type: application/json')->inArray($this->curl->response_headers);
        Assert::that($this->curl->response)->isJsonString();
        Assert::that(json_decode($this->curl->response, true))->eq($aggregateData);
    }

    /**
     * @Given An out of order aggregate activated event for this aggregate has been added to event store
     */
    public function anOutOfOrderAggregateActivatedEventForThisAggregateHasBeenAddedToEventStore()
    {
        $eventData = json_decode(file_get_contents('/contracts/db/event/MyAggregate/OutOfOrderActivated.json'), true);
        $stmt = $this->con->prepare(self::RECEIVED_EVENT_INSERT_SQL);
        $stmt->execute($eventData);
        $this->lastEventId = $this->con->lastInsertId();
        sleep(1);
    }

    /**
     * @Then the not activated myaggregate should returned
     */
    public function theNotActivatedMyaggregateShouldReturned()
    {
        $this->theNewMyaggregateShouldReturned();
    }

    /**
     * @Then a new EventApplyFailedEvent should be added to event store
     */
    public function aNewEventapplyfailedeventShouldBeAddedToEventStore()
    {
        $eventData = json_decode(file_get_contents('/contracts/db/event/MyAggregate/EventApplyFailed.json'), true);
        $stmt = $this->con->prepare("SELECT * FROM event where name=:name and aggregate_id = :aggregate_id and aggregate_version = :aggregate_version");
        $stmt->execute($eventData);
        $event = $stmt->fetch();

        Assert::that($event)->notEmpty();
        
        Assert::that($event['correlation_id'])->eq($this->lastEventId);
        Assert::that($event['timestamp'])->notEmpty();
        Assert::that($event['data'])->notEmpty();
        Assert::that(json_decode($event['data'], true)['exception'])->notEmpty();
        Assert::that(json_decode($event['data'], true)['exception']['class'])->notEmpty();
        Assert::that(json_decode($event['data'], true)['exception']['code'])->notEmpty();
        Assert::that(json_decode($event['data'], true)['exception']['trace'])->notEmpty();
    }

    /**
     * @Given A Restoring created event has been added to event store
     */
    public function aRestoringCreatedEventHasBeenAddedToEventStore()
    {
        $eventData = json_decode(file_get_contents('/contracts/db/event/MyAggregate/OrderRestoreCreated.json'), true);
        $stmt = $this->con->prepare(self::RECEIVED_EVENT_INSERT_SQL);
        $stmt->execute($eventData);
        $this->lastEventId = $this->con->lastInsertId();
        sleep(1);
    }

    /**
     * @Then the restored myaggregate should returned
     */
    public function theRestoredMyaggregateShouldReturned()
    {
        $aggregateData = json_decode(file_get_contents('/contracts/api/my-aggregate-active-restored.json'), true);
        Assert::that($this->curl->error)->noContent();
        Assert::that($this->curl->getHttpStatus())->eq(200);
        Assert::that($this->curl->response)->notEmpty();
        Assert::that('Content-Type: application/json')->inArray($this->curl->response_headers);
        Assert::that($this->curl->response)->isJsonString();
        Assert::that(json_decode($this->curl->response, true))->eq($aggregateData);
    }



}
