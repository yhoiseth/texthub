@prepare_database @clean_files
Feature: Register
  In order to use the app
  As a website visitor
  I need to be able to create a user account

  Scenario: Happy path
    Given I am on "/"
    When I click "Register"
    And I fill in "Name" with "Marcus Aurelius"
    And I fill in "Username" with "marelius"
    And I fill in "Email" with "marcus@aurelius.com"
    And I fill in "Password" with "take it easy"
    And I fill in "Repeat password" with "take it easy"
    And I press the "Register" button
    Then I should be logged in
    And I should be redirected to "/register/confirmed"
    And I should have a Git repository

  Scenario: Existing username
    Given I am on "/"
    When I click "Register"
    And I fill in "Name" with "Marcus Aurelius"
    And I fill in "Username" with "marcus-aurelius"
    And I fill in "Email" with "marcus@aurelius.com"
    And I fill in "Password" with "Serenity now"
    And I fill in "Repeat Password" with "take it easy"
    And I press the "Register" button
    Then I should be logged in
    
    When I click "Logout"
    And I fill in "Name" with "Marcus Aurelius"
    And I fill in "Username" with "marcus-aurelius"
    Then I should see "Username not available"
    And I fill in "Email" with "marcus2@aurelius.com"
    And I fill in "Password" with "take it easy"
    And I fill in "Repeat Password" with "take it easy"
    And I press the "Register" button
    Then I should not be logged in
