@regression
@ticket-BB-16918
@fixture-OroTaskBundle:TaskInActivityListFixture.yml
Feature: Task actions on activity list
  In order to manage the activity lists
  As a Administrator
  I need to be able to work with entity`s workflow in the activity list

  Scenario: Render only allowed actions for task activity
    Given I login as administrator
    When I go to Sales/ Opportunities
    And I click view "Opportunity 1" in grid
    Then I should see only following actions on "Task For Charlie" in activity list:
      | Add Context    |
      | View Task      |
      | Update Task    |
      | Delete Task    |
      | Start progress |
      | Close          |
    Then I should see only following actions on "Task For Admin" in activity list:
      | Add Context    |
      | View Task      |
      | Update Task    |
      | Delete Task    |
      | Start progress |
      | Close          |
    When I click "More actions"
    And I click "Add task"
    And I fill form with:
      | Subject     | Second Charlie Task |
      | Assigned To | Charlie Sheen       |
    And I click "Create Task"
    Then I should see only following actions on "Second Charlie Task" in activity list:
      | Add Context    |
      | View Task      |
      | Update Task    |
      | Delete Task    |
      | Start progress |
      | Close          |

  Scenario: Check correct render task transitions after change permission
    When I go to System/ User Management/ Roles
    And I click edit Administrator in grid
    And select following permissions:
      | Task Flow | Perform transitions:User |
    And I save and close form

    And I go to Sales/ Opportunities
    And I click view "Opportunity 1" in grid
    Then I should see only following actions on "Task For Charlie" in activity list:
      | Add Context |
      | View Task   |
      | Update Task |
      | Delete Task |
    Then I should see only following actions on "Task For Admin" in activity list:
      | Add Context    |
      | View Task      |
      | Update Task    |
      | Delete Task    |
      | Start progress |
      | Close          |
    Then I should see only following actions on "Second Charlie Task" in activity list:
      | Add Context |
      | View Task   |
      | Update Task |
      | Delete Task |
    When I click "More actions"
    And I click "Add task"
    And I fill form with:
      | Subject     | Third Charlie Task |
      | Assigned To | Charlie Sheen      |
    And I click "Create Task"
    Then I should see only following actions on "Third Charlie Task" in activity list:
      | Add Context |
      | View Task   |
      | Update Task |
      | Delete Task |
