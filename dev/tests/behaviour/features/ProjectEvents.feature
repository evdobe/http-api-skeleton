Feature: Project events
    In order to maintain a querable representation of entities
    I should be able to project events on aggeregate tables

Scenario: Project myaggregate created event
    Given I have access to the event store
    When A new myaggregate created event is persisted in event store
    Then the  created event should be projected on myaggregate db table