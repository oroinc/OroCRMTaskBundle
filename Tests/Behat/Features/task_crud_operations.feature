@fixture-OroUserBundle:users.yml
Feature: Task CRUD operations
  In order to manage Tasks
  As Administrator
  I need to be able to view, create, edit and delete Tasks

  Scenario: Create new Task
    Given I login as administrator
    And I go to Activities/Tasks
    And press "Create Task"
    And fill form with:
      | Subject     | Contact with Charlie |
      | Description | Offer him a new role |
      | Due date    | <DateTime:+ 1 day>   |
      | Priority    | high                 |
      | Assigned To | charlie              |
    And set Reminders with:
      | Method        | Interval unit | Interval number |
      | Email         | days          | 1               |
      | Flash message | minutes       | 30              |
    When I press "Save and Close"
    Then I should see "Task saved" flash message

  Scenario: View Task in task index page
    Given I go to Activities/Tasks
    When I click view "Contact with Charlie" in "Tasks Grid"
    And I should see task with:
      | Subject     | Contact with Charlie |
      | Description | Offer him a new role |
      | Priority    | High                 |
      | Created By  | John Doe             |
    And Charlie Sheen should be an owner

  Scenario: Edit Task
    Given I go to Activities/Tasks
    When I click view "Contact with Charlie" in "Tasks Grid"
    And I press "Edit Task"
    And fill form with:
      | Subject     | Sign a contract with Charlie             |
      | Description | Prepare and sign contract about new role |
      | Due date    | <DateTime:+ 7 days>                      |
      | Priority    | Normal                                   |
      | Assigned To | Megan                                    |
    And set Reminders with:
      | Method        | Interval unit | Interval number |
      | Email         | weeks         | 3               |
      | Flash message | hours         | 1               |
    And press "Save and Close"
    Then I should see "Task saved" flash message

  Scenario: Delete Task
    Given I go to Activities/Tasks
    When I keep in mind number of records in list
    And I click Delete "Sign a contract with Charlie" in "Tasks Grid"
    And confirm deletion
    Then I should see "Task Deleted" flash message
    And the number of records decreased by 1
    And I should not see "Sign a contract with Charlie"
