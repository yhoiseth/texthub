@prepare_database @clean_files
Feature: Register
  In order to use the app
  As a website visitor
  I need to be able to create a user account

  Scenario Outline: Happy path
    Given I am on "/"
    When I click "Register"
    And I fill in "Name" with "Marcus Aurelius"
    And I fill in "Username" with "<username>"
    And I fill in "Email" with "marcus@aurelius.com"
    And I fill in "Password" with "take it easy"
    And I fill in "Repeat password" with "take it easy"
    And I press the "Register" button
    Then I should be logged in
    And I should be redirected to "/register/confirmed"
    And I should have a Git repository

    Examples:
      | username        |
      | maurelius       |
      | marcus-aurelius |
      | m               |
      | ma              |
      | m-a             |
      | m-to-the-arcus  |
      | marcus-121      |
      | marcus121       |
      | 121-marcus      |
      | 121marcus       |
      | 121             |
      | 0               |

  @watch
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
    And I should see "This value is already used"

  Scenario: Existing email
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
    And I fill in "Username" with "marcus-aurelius-2"
    And I fill in "Email" with "marcus@aurelius.com"
    And I fill in "Password" with "take it easy"
    And I fill in "Repeat password" with "take it easy"
    And I press the "Register" button
    Then I should not be logged in
    And I should see "This value is already used"

  Scenario Outline: Illegal characters or character order
    Given I am on "/"
    When I click "Register"
    And I fill in "Name" with "Marcus Aurelius"
    And I fill in "Username" with "<username>"
    And I fill in "Email" with "marcus@aurelius.com"
    And I fill in "Password" with "take it easy"
    And I fill in "Repeat password" with "take it easy"
    And I press the "Register" button
    Then I should not be logged in

    Examples:
      | username        |
      | @               |
      | å               |
      | A               |
      | -               |
      | marcus-         |
      | .               |
      | ...             |
      | /               |
      | marcus aurelius |
      |                 |


  @watch
  Scenario Outline: Reserved usernames
    Given I am on "/"
    When I click "Register"
    And I fill in "Name" with "Marcus Aurelius"
    And I fill in "Username" with "<username>"
    And I fill in "Email" with "marcus2@aurelius.com"
    And I fill in "Password" with "take it easy"
    And I fill in "Repeat password" with "take it easy"
    And I press the "Register" button
    Then I should not be logged in

    Examples:
      | username      |
      | login         |
      | log-in        |
      | login_check   |
      | log-in_check  |
      | log-in-check  |
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
