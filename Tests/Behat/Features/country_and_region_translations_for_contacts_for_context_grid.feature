@fixture-OroLocaleBundle:ZuluLocalization.yml
@fixture-OroAddressBundle:CountryNameTranslation.yml
@fixture-OroContactBundle:LoadContactEntitiesFixture.yml
@fixture-OroTaskBundle:LoadTaskEntitiesFixture.yml
Feature: Country and region translations for contacts for context grid
  In order to manage Tasks
  As a Administrator
  I want to see translated country and region names in UI

  Scenario: Feature Background
    Given I login as administrator
    And I go to System / Configuration
    And I follow "System Configuration/General Setup/Localization" on configuration sidebar
    And I fill form with:
      | Enabled Localizations | [English, Zulu_Loc] |
      | Default Localization  | Zulu_Loc            |
    And I submit form

  Scenario: Check tasks UI
    Given go to Activities/ Tasks
    And click view "Test" in grid
    When click "Add Context"
    And I select "Contact" context
    And number of records in "Add Context Contact Grid" should be 2
    Then should see following "Add Context Contact Grid" grid:
      | First name   | Last name    | Email          | Phone      | Country           | State       |
      | TestContact1 | TestContact1 | test1@test.com | 5556668888 | GermanyZulu       | BerlinZulu  |
      | TestContact2 | TestContact2 | test2@test.com | 5556669999 | United StatesZulu | FloridaZulu |
    When I click "maximize"
    And I show filter "Country" in "Add Context Contact Grid" grid
    And I show filter "State" in "Add Context Contact Grid" grid
    And I check "United StatesZulu" in Country filter in "Add Context Contact Grid"
    Then should see following "Add Context Contact Grid" grid:
      | First name   | Last name    | Email          | Phone      | Country           | State       |
      | TestContact2 | TestContact2 | test2@test.com | 5556669999 | United StatesZulu | FloridaZulu |
    And number of records in "Add Context Contact Grid" should be 1
    And I reset "Country" filter in "Add Context Contact Grid"
    When filter State as contains "BerlinZulu" in "Add Context Contact Grid"
    Then should see following "Add Context Contact Grid" grid:
      | First name   | Last name    | Email          | Phone      | Country     | State      |
      | TestContact1 | TestContact1 | test1@test.com | 5556668888 | GermanyZulu | BerlinZulu |
    And number of records in "Add Context Contact Grid" should be 1
