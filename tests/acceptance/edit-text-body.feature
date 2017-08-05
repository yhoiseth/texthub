@prepare_database @clean_files @text @watch
Feature: Edit text body
  In order to improve my texts
  As a logged in user
  I need to be able to edit my text bodies

  Background:
    Given I am on "/"
    And I click "Register"
    And I fill in "Name" with "Marcus Aurelius"
    And I fill in "Username" with "marcus-aurelius"
    And I fill in "Email" with "marcus@aurelius.com"
    And I fill in "Password" with "take it easy"
    And I fill in "Repeat password" with "take it easy"
    And I press ENTER
    And I click "New text"
    And I wait "1" seconds
    And I fill in "Title" with "Meditations Revisited"
    And I click "Let's go"

  Scenario: Happy path
    Given I am on "/marcus-aurelius/meditations-revisited/_edit"
    Then I should see "All changes saved"
    And I should not see "Saving draft…"
    And I should not see "Save text"

    When I fill in the body field with "# This is my level 1 heading"
    Then I should not see "Save text"
    And I should see "Saving draft…"

    When I wait "2" seconds
    Then I should see "Draft saved"
    And the text file should be saved

    When I click "Save text"
    Then I should be redirected to "/marcus-aurelius/meditations-revisited"
    And I should see "Text saved"
    And the text should be committed
    And the "h1" element should contain "Meditations Revisited"
    And the page title should contain "Meditations Revisited"
    And the "h2" element should contain "This is my level 1 heading"
