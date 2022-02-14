Feature: Ping
    In order to check service health
    As an administrator
    I should be able to ping the http server

Scenario: Ping
    Given The service port is defined
    And Healthcheck path is defined
    When I do http get on healthcheck path
    Then I should get an ack response
