@prepare_database @clean_files @text
Feature: Edit text title
  In order to better describe my text
  As a logged in user with one or more texts
  I need to be able to edit text titles

  In order for URLs to be descriptive as the title changes
  The links need to be updated

  In order for old URLs to keep working
  The old URLs need to redirect to the new ones

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
    And "h1" should contain "Meditations Revisited"

    @watch
  Scenario: Happy path
    When I click "Edit title"
    And the "Title" field should contain "Meditations Revisited"
    And the title field in the edit title form should be selected
    And I fill in the title field in the edit text form with "Something else"
    And I click "Save"
    Then the text title should be updated from "Meditations Revisited" to "Something else"
    And the filename should be updated from "meditations-revisited.md" to "something-else.md"
    And all the files in the main repository of "marcus-aurelius" should be committed
    And I should be redirected to "/marcus-aurelius/something-else"

    When I visit "/marcus-aurelius/meditations-revisited/_edit"
    Then I should be redirected to "/marcus-aurelius/something-else/_edit"

  Scenario: Not found in own repository
    When I visit "/marcus-aurelius/does-not-exist/_edit"
    Then I should see "Not found"

  Scenario: Other user's text
    Given I visit "/logout"
    And I click "Register"
    And I fill in "Name" with "Hadrianus"
    And I fill in "Username" with "hadrianus"
    And I fill in "Email" with "hadrianus@aurelius.com"
    And I fill in "Password" with "take it easy"
    And I fill in "Repeat password" with "take it easy"
    And I press ENTER

    When I visit "/marcus-aurelius/meditations-revisited/_edit"
    Then I should be denied access
