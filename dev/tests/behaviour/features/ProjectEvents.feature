Feature: Project events
    In order to maintain a querable representation of entities
    I should be able to project events on aggeregate tables

Scenario: Get new myaggregate in all myaggregates
    Given I have access to the event store
    And A new myaggregate created event has been added to event store
    When I query the api for all myaggregates
    Then the new myaggregate should be contained in result

Scenario: Get new myaggregate by id
    Given I have access to the event store
    And A new myaggregate created event has been added to event store
    When I query the api for myaggregate by id
    Then the new myaggregate should returned