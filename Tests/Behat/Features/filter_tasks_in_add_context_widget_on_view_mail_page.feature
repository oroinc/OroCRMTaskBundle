@regression
@automatically-ticket-tagged
@fixture-OroEmailBundle:my-emails.yml
@fixture-OroTaskBundle:LoadTaskEntitiesFixture.yml
Feature: Filter tasks in "Add Context" widget on view mail page

  Scenario: Filter tasks by Assigned To in Add Context widget
    Given I login as administrator
    And I click My Emails in user menu
    And I click "View" on first row in grid
    And I click "Add Context"
    And I select "Task" context
    And I should see "Assigned To"

    And I choose "Charlie Sheen" in the Assigned To filter
    Then number of records should be 0

    And I choose "John Doe" in the Assigned To filter
    Then number of records should be 1
