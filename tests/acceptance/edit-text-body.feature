@prepare_database @clean_files @text
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
