Feature: Project events
    In order to maintain a querable representation of entities
    I should be able to project events on aggeregate tables

@database
Scenario: Get new myaggregate in all myaggregates
    Given I have access to the event store
    And A new myaggregate created event has been added to event store
    When I query the api for all myaggregates
    Then the new myaggregate should be contained in result

@database
Scenario: Get new myaggregate by id
    Given I have access to the event store
    And A new myaggregate created event has been added to event store
    When I query the api for myaggregate by id
    Then the new myaggregate should returned

@database
Scenario: Activate aggregate
    Given I have access to the event store
    And A new myaggregate created event has been added to event store
    And An aggregate activated event for this aggregate has been added to event store
    When I query the api for myaggregate by id
    Then the activated myaggregate should returned

@database
Scenario: Out of order event
    Given I have access to the event store
    And A new myaggregate created event has been added to event store
    And An out of order aggregate activated event for this aggregate has been added to event store
    When I query the api for myaggregate by id
    Then the not activated myaggregate should returned
    And a new EventApplyFailedEvent should be added to event store

@database
Scenario: Restoring order event
    Given I have access to the event store
    And A new myaggregate created event has been added to event store
    And An out of order aggregate activated event for this aggregate has been added to event store
    And A Restoring created event has been added to event store
    When I query the api for myaggregate by id
    Then the restored myaggregate should returned