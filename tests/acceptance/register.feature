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
    And I fill in "Password" with "take it easy"
    And I fill in "Repeat password" with "take it easy"
    And I press the "Register" button
    Then I should be logged in

    When I click "Marcus Aurelius"
    And I click "Log out"
    And I click "Register"
    And I fill in "Name" with "Marcus Aurelius"
    And I fill in "Username" with "marcus-aurelius"
    And I fill in "Email" with "marcus2@aurelius.com"
    And I fill in "Password" with "take it easy"
    And I fill in "Repeat password" with "take it easy"
    And I press the "Register" button
    Then I should not be logged in
    And I should see "The username is already used"

  @watch
  Scenario: Illegal characters
    Given I am on "/"
    When I click "Register"
    And I fill in "Name" with "Marcus Aurelius"
    And I fill in "Username" with "@@@@@"
    And I fill in "Email" with "marcus@aurelius.com"
    And I fill in "Password" with "take it easy"
    And I fill in "Repeat password" with "take it easy"
    And I press the "Register" button
    Then I should not be logged in
    And I should see "Illegal character(s) in username – use the letters a-z, digits 0-9 and dashes (-) only"

  Scenario Outline: Reserved usernames
    Given I am on "/"
    When I click "Register"
    And I fill in "Name" with "Marcus Aurelius"
    And I fill in "Username" with "<username>"
    And I fill in "Email" with "marcus2@aurelius.com"
    And I fill in "Password" with "take it easy"
    And I fill in "Repeat Password" with "take it easy"
    And I press the "Register" button
    Then I should not be logged in
    And I should see "The username is already used"

    Examples:
      | username      |
      | login         |
      | login_check   |
      | logout        |
      | profile       |
      | register      |
      | resetting     |
      | notifications |
      | settings      |
      | support       |
      | terms         |
      | user          |
      | webhook       |
      | api           |
      | help          |
      | about         |
      | pricing       |
      | product       |
      | new           |
      | jobs          |
      | integrations  |
      | add-ons       |
      | templates     |
      | themes        |
      | blog          |
      | news          |
      | downloads     |
      | press         |
      | social        |
      | documentation |
      | customers     |
      | case-studies  |
      | references    |
      | open-source   |
      | contact       |
      | privacy       |
      | careers       |
      | developers    |
      | team          |
      | app           |
      | system        |
      | dashboard     |
      | preferences   |
      | analytics     |
      | search        |
      | users         |
      | organizations |
      | organisations |
      | publishers    |
      | teams         |
