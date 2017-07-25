@prepare_database @clean_files
Feature: New text
  In order to remember and share my thoughts
  As a logged in user
  I need to be able to create new texts

  Background:
    Given I am on "/"
    And I click "Register"
    And I fill in "Name" with "Marcus Aurelius"
    And I fill in "Username" with "marcus-aurelius"
    And I fill in "Email" with "marcus@aurelius.com"
    And I fill in "Password" with "take it easy"
    And I fill in "Repeat password" with "take it easy"
    And I press ENTER

    @watch
  Scenario: Happy path
    Given I am on "/"
    Then I should not see "Title"
    And I should not see "Let's go!"
    When I click "New text"
    Then the blinking text cursor should be in the "Title" field
    When I fill in "Title" with "Meditations Revisited"
    And I click "Let's go"
    Then the text should be created
    And I should be redirected to "/marcus-aurelius/meditations-revisited"
    And the page title should contain "Meditations Revisited"
