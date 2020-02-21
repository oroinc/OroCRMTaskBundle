@ticket-BAP-10958
@automatically-ticket-tagged
Feature: User task activity
  In order to have ability assign task to user
  As OroCRM sales rep
  I need to have task activity functionality in user view page

Scenario: Add task to user entity
  Given the following users:
    | firstName | lastName | email               | username | organization  | organizations   | owner          | businessUnits    |
    | Theresa   | Peters   | theresa@example.com | theresa  | @organization | [@organization] | @business_unit | [@business_unit] |
    | Jeremy    | Zimmer   | jeremy@example.com  | jeremy   | @organization | [@organization] | @business_unit | [@business_unit] |
    | Charlie   | Sheen    | charlie@example.com | charlie  | @organization | [@organization] | @business_unit | [@business_unit] |
    | Jesse     | Keenan   | jesse@example.com   | jesse    | @organization | [@organization] | @business_unit | [@business_unit] |
  And I login as administrator
  And I go to System/Entities/Entity Management
  And filter Name as is equal to "User"
  And click Edit User in grid
  And check "Tasks"
  When I save and close form
  And click update schema
  Then I should see Schema updated flash message

  Scenario: Assign task to the user
    When I go to System/User Management/Users
    And click view Jesse in grid
    And follow "More actions"
    And follow "Assign task"
    And Assigned To field should has Jesse Keenan value
    And fill "Task Form" with:
      | Subject     | Meet with John       |
      | Description | Discuss an offer     |
      | Due date    | <DateTime:+ 2 day>   |
      | Priority    | normal               |
    And set Reminders with:
      | Method        | Interval unit | Interval number |
      | Email         | days          | 2               |
      | Flash message | minutes       | 10              |
    When press "Create Task"
    Then I should see "Task created successfully" flash message
    And should see "Meet with John" task in activity list
    And I should see "Meet with John" in grid "User Tasks Grid"

Scenario: Add task
  Given I go to System/User Management/Users
  And click view Charlie in grid
  And follow "More actions"
  And follow "Add task"
  And fill "Task Form" with:
    | Subject     | Contact with Charlie |
    | Description | Offer him a new role |
    | Due date    | <DateTime:+ 1 day>   |
    | Priority    | high                 |
    | Assigned To | theresa              |
  And set Reminders with:
    | Method        | Interval unit | Interval number |
    | Email         | days          | 1               |
    | Flash message | minutes       | 30              |
  When click "Create Task"
  Then I should see "Task created successfully" flash message
  And should see "Contact with Charlie" task in activity list

Scenario: View Task on User's view page
  When I collapse "Contact with Charlie" in activity list
  Then I should see task activity with:
    | Subject     | Contact with Charlie |
    | Description | Offer him a new role |
    | Priority    | High                 |
    | Assigned To | Theresa Peters       |
  And should see charlie in Contexts

Scenario: View Task on task view page
  When I click "View task" on "Contact with Charlie" in activity list
  Then the url should match "/task/view/\d+"
  And I should see task with:
    | Subject     | Contact with Charlie |
    | Description | Offer him a new role |
    | Priority    | High                 |
    | Created By  | John Doe             |
  And Theresa Peters should be an owner

Scenario: Edit Task
  Given I go to System/User Management/Users
  And click view Charlie in grid
  When I click "Update task" on "Contact with Charlie" in activity list
  And fill "Task Form" with:
    | Subject     | Sign a contract with Charlie             |
    | Description | Prepare and sign contract about new role |
    | Due date    | <DateTime:+ 7 days>                      |
    | Priority    | Normal                                   |
    | Assigned To | Jeremy                                   |
  And set Reminders with:
    | Method        | Interval unit | Interval number |
    | Email         | weeks         | 3               |
    | Flash message | hours         | 1               |
  And click "Update Task"
  Then I should see "Task created successfully" flash message
  When I collapse "Sign a contract with Charlie" in activity list
  Then I should see task activity with:
    | Subject     | Sign a contract with Charlie             |
    | Description | Prepare and sign contract about new role |
    | Priority    | Normal                                   |
    | Assigned To | Jeremy Zimmer                            |

Scenario: My task
  Given I click My Tasks in user menu
  And there is no records in grid
  And I go to Activities/Tasks
  And number of records should be 2
  And I click edit Sign a contract with Charlie in grid
  And fill in "Assigned To" with "John Doe"
  When I save and close form
  And click My Tasks in user menu
  Then number of records should be 1

Scenario: Delete Task
  Given I go to System/User Management/Users
  And click view Charlie in grid
  When I click "Delete task" on "Sign a contract with Charlie" in activity list
  And confirm deletion
  Then I should see "Activity item deleted" flash message
  And I see no records in activity list
