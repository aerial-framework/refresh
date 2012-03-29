Feature: Testing the REST functionality of Aerial Framework
          with Slim Framework and Guzzle

    Scenario: Fetching a simple JSON response
      Given that I pass no parameters
      When I call "/internal/test/simple"
      Then the response is JSON
      Then the response status code should be 200