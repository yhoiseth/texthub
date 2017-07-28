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
    And "h1" should contain "Meditations Revisited"
    And I click "Edit title"
    And the "Title" field should contain "Meditations Revisited"
    And "Meditations Revisited" should be selected

  Scenario: Happy path
    When I fill in the title field in the edit text form with "Something else"
    And I click "Save"
    Then the text title should be updated from "Meditations Revisited" to "Something else"
    And the slug should be updated from "meditations-revisited" to "something-else"
    And the filename should be updated from "meditations-revisited.md" to "something-else.md"
#    And all the files in the main repository of "marcus-aurelius" should be committed
#    And I should be redirected to "/marcus-aurelius/something-else"
#
#    When I visit "/marcus-aurelius/meditations-revisited"
#    Then I should be redirected to "/marcus-aurelius/something-else"
#
#    When I visit "/marcus-aurelius/meditations-revisited/_edit"
#    Then I should be redirected to "/marcus-aurelius/something-else/_edit"

