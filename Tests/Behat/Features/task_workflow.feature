@fixture-OroTaskBundle:tasks.yml
Feature: Task workflow
  In order to have ability assign task to user
  As administrator
  I need to have task activity functionality in user view page

Scenario: Move task thru the workflow
  Given I login as administrator
  And I go to Activities/Tasks
  And I click view Meet James in grid
  Then I should see "Open" green status
  When I click "Start progress"
  Then I should see "In Progress" yellow status
  When I click "Close"
  Then I should see "Closed" gray status
  When I click "Reopen"
  Then I should see "Open" green status
  When I click "Start progress"
  Then I should see "In Progress" yellow status
  When I click "Stop progress"
  Then I should see "Open" green status
