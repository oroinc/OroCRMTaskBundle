@fixture-OroLocaleBundle:ZuluLocalization.yml
@fixture-OroAddressBundle:CountryNameTranslation.yml
@fixture-OroTaskBundle:LoadTaskEntitiesFixture.yml
@fixture-OroContactBundle:LoadContactEntitiesFixture.yml
@fixture-OroMagentoBundle:LoadMagentoEntitiesFixture.yml
Feature: Country and region translations for magento order for context grid
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
    When I go to System / Localization / Translations
    And I click "Update Cache"
    Then I should see "Translation Cache has been updated" flash message

  Scenario: Check Tasks UI
    Given go to Activities/ Tasks
    And click view "Test" in grid
    When click "Add Context"
    And I select "Magento Order" context
    And number of records in "Add Context Magento Order Grid" should be 2
    # Billing state is not rendered at any language, but filter work
    And I sort grid by "First Name"
    Then should see following "Add Context Magento Order Grid" grid:
      | First name | Last name | Email         | Billing country   | Billing state |
      | firstName1 | lastName1 | customerEmail | GermanyZulu       | BerlinZulu    |
      | firstName2 | lastName2 | customerEmail | United StatesZulu | FloridaZulu   |
    When I click "maximize"
    And I show filter "Billing country" in "Add Context Magento Order Grid" grid
    And I show filter "Billing state" in "Add Context Magento Order Grid" grid
    And I check "United StatesZulu" in Billing country filter in "Add Context Magento Order Grid"
    Then should see following "Add Context Magento Order Grid" grid:
      | First name | Last name | Email         | Billing country   | Billing state |
      | firstName2 | lastName2 | customerEmail | United StatesZulu | FloridaZulu   |
    And number of records in "Add Context Magento Order Grid" should be 1
    And I reset "Billing country" filter in "Add Context Magento Order Grid"
    When filter Billing state as contains "BerlinZulu" in "Add Context Magento Order Grid"
    Then should see following "Add Context Magento Order Grid" grid:
      | First name | Last name | Email         | Billing country   | Billing state |
      | firstName1 | lastName1 | customerEmail | GermanyZulu       | BerlinZulu    |
    And number of records in "Add Context Magento Order Grid" should be 1

