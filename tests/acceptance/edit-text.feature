@prepare_database @clean_files @watch
Feature: Edit text
  In order to improve my texts
  As a logged in user with one or more texts
  I need to be able to edit my texts

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
    Given "h1" should contain "Meditations Revisited"
    When I click "Edit title"
    Then the "Title" field should contain "Meditations Revisited"
