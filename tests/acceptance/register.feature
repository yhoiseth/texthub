@prepare_database @clean_files
Feature: Register
  As a visitor
  I need to be able to register

  Scenario: Happy path
    Given I am on "/"
    When I click "Register"
    And I fill in the "Name" field with "Marcus Aurelius"
    And I fill in the "Username" field with "marelius"
    And I fill in the "Email" field with "marcus@aurelius.com"
    And I fill in the "Password" field with "take it easy"
    And I fill in the "Repeat password" field with "take it easy"
    And I press the "Register" button
    Then I should be logged in
    And I should be redirected to "/register/confirmed"
    And I should have a Git repository
