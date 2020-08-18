@regression
@ticket-BAP-12394
@fixture-OroTaskBundle:LoadTaskEntitiesFixture.yml

Feature: Task activity actions
  To check if the task can work with contexts
  As an administrator
  I create a new entity and add it to the task context field and see it on the task view page,
  after remove context I see the successful message

  Scenario: Create Test entity
    Given I login as administrator
    And I go to System/Entities/Entity Management
    And I click "Create Entity"
    When I fill form with:
      | Name         | testEntity    |
      | Label        | Test Entity   |
      | Plural Label | Test Entities |
    And I check "Tasks"
    And I save and close form
    Then I should see "Entity saved" flash message
    When I click "Create Field"
    And I fill form with:
      | Field name | Name   |
      | Type       | String |
    And I click "Continue"
    And I fill form with:
      | Searchable | Yes |
    And I save and close form
    Then I should see "Field saved" flash message
    And I should see "Update Schema"
    When I click update schema
    And I should see Schema updated flash message

  Scenario: Create a record for Test entity
    When I go to System/Entities/Test Entity
    And I click "Create Test Entity"
    When I fill form with:
      | Name | Test Record |
    And I save and close form
    Then I should see "Entity saved" flash message

  Scenario: Add task context
    Given I go to Activities/Tasks
    And I click "edit" on first row in grid
    And fill "Task Form" with:
      | Context | [TestRecord (Test Entity)] |
    And press "Save and Close"
    Then I should see "Task saved" flash message

  Scenario: Remove context from view page
    When I press "Task View Delete Test Context"
    Then I should see "The context has been removed" flash message
