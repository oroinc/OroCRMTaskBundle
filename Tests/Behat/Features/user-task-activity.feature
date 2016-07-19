Feature: User task activity
  In order to have ability assign task to user
  As OroCRM sales rep
  I need to have task activity functionality in user view page

Background:
  Given I login as "admin" user with "admin" password

Scenario: Add task to user entity
  Given I go to System/Entities/Entity Management
  And filter Name as is equal to "User"
  And click Edit User in grid
  And check "Tasks"
  When I save and close form
  And click update schema
  Then I should see "Schema updated" flash message

Scenario: Add task
  Given the following users:
    | firstName | lastName | email              | username | organization  | organizations   | owner          | businessUnits    |
    | Theresa   | Peters   | theresa@peters.com | theresa  | @organization | [@organization] | @business_unit | [@business_unit] |
    | Jeremy    | Zimmer   | jeremy@zimmer.com  | jeremy   | @organization | [@organization] | @business_unit | [@business_unit] |
    | Charlie   | Sheen    | charlie@sheen.com  | charlie  | @organization | [@organization] | @business_unit | [@business_unit] |
  And I go to System/User Management/Users
  And click view Charlie in grid
  And follow "More actions"
  And follow "Add task"
  And fill form with:
    | Subject     | Contact with Charlie |
    | Description | Offer him a new role |
    | Due date    | 2017-08-24           |
    | Priority    | high                 |
    | Assigned To | theresa              |
  And set Reminders with:
    | Method        | Interval unit | Interval number |
    | Email         | days          | 1               |
    | Flash message | minutes       | 30              |
  When press "Create Task"
  Then I should see "Task created successfully" flash message
  And should see "Contact with Charlie" task in activity list

Scenario: View Task in Contact page
  Given I go to System/User Management/Users
  And click view Charlie in grid
  When I collapse "Contact with Charlie" in activity list
  Then I should see task activity with:
    | Subject     | Contact with Charlie |
    | Description | Offer him a new role |
    | Priority    | High                 |
    | Assigned To | Theresa Peters       |
  And should see charlie in Contexts

Scenario: View Task in task view page
  Given I go to System/User Management/Users
  And click view Charlie in grid
  When I click "View task" on "Contact with Charlie" in activity list
  Then the url should match "/task/view/\d+"
  And I should see task with:
    | Subject     | Contact with Charlie |
    | Description | Offer him a new role |
    | Priority    | High                 |
    | Assigned To | Theresa Peters       |

Scenario: Edit Task
  Given I go to System/User Management/Users
  And click view Charlie in grid
  When I click "Update task" on "Contact with Charlie" in activity list
  And fill form with:
    | Subject     | Sign a contract with Charlie             |
    | Description | Prepare and sign contract about new role |
    | Due date    | 2017-09-01                               |
    | Priority    | Normal                                   |
    | Assigned To | Jeremy                                   |
  And set Reminders with:
    | Method        | Interval unit | Interval number |
    | Email         | weeks         | 3               |
    | Flash message | hours         | 1               |
  And press "Update Task"
  Then I should see "Task created successfully" flash message
  When I collapse "Sign a contract with Charlie" in activity list
  Then I should see task activity with:
    | Subject     | Sign a contract with Charlie             |
    | Description | Prepare and sign contract about new role |
    | Priority    | Normal                                   |
    | Assigned To | Jeremy Zimmer                            |

Scenario: Delete Task
  Given I go to System/User Management/Users
  And click view Charlie in grid
  When I click "Delete task" on "Sign a contract with Charlie" in activity list
  And confirm deletion
  Then I should see "Activity item deleted" flash message
  And there is no records in activity list
