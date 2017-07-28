@prepare_database @clean_files
Feature: New text
  In order to remember and share my thoughts
  As a logged in user
  I need to be able to create new texts

  Background:
    Given I am on "/"
    Then I should not see "New text"
    When I click "Register"
    And I fill in "Name" with "Marcus Aurelius"
    And I fill in "Username" with "marcus-aurelius"
    And I fill in "Email" with "marcus@aurelius.com"
    And I fill in "Password" with "take it easy"
    And I fill in "Repeat password" with "take it easy"
    And I press ENTER
    Then I should see "New text"

    @watch
  Scenario: Happy path
    Given I am on "/"
    Then I should not see "Title"
    And I should not see "Let's go!"
    When I click "New text"
    Then the "Title" field should contain "Untitled"
    And "Untitled" should be selected
    When I fill in "Title" with "Meditations Revisited"
    And I click "Let's go"
    Then the text with title "Meditations Revisited" should be created in the main repository of "marcus-aurelius"
    And I should be redirected to "/marcus-aurelius/meditations-revisited/_edit"
    And the page title should contain "Meditations Revisited"
    And all the files in the main repository of "marcus-aurelius" should be committed
    And the last commit should be authored by "marcus-aurelius"

  Scenario: Existing title
    Given I am on "/"
    And I click "New text"
    And I wait "1" seconds
    And I fill in "Title" with "Meditations Revisited"
    And I click "Let's go"

    When I click "New text"
    And I wait "1" seconds
    And I fill in "Title" with "Meditations Revisited"
    And I click "Let's go"

    Then I should be redirected to "/marcus-aurelius/meditations-revisited-2/_edit"

    When I click "New text"
    And I wait "1" seconds
    And I fill in "Title" with "Meditations Revisited"
    And I click "Let's go"

    Then I should be redirected to "/marcus-aurelius/meditations-revisited-3/_edit"

    When I click "New text"
    And I wait "1" seconds
    And I fill in "Title" with "Meditations Revisited"
    And I click "Let's go"

    Then I should be redirected to "/marcus-aurelius/meditations-revisited-4/_edit"

  Scenario: Other user has text with same title
    Given I click "New text"
    And I wait "1" seconds
    And I fill in "Title" with "Meditations Revisited"
    And I click "Let's go"
    And I visit "/logout"

    And I click "Register"
    And I fill in "Name" with "Hadrianus"
    And I fill in "Username" with "hadrianus"
    And I fill in "Email" with "hadrianus@aurelius.com"
    And I fill in "Password" with "take it easy"
    And I fill in "Repeat password" with "take it easy"
    And I press ENTER

    And I click "New text"
    And I wait "1" seconds
    And I fill in "Title" with "Meditations Revisited"
    And I click "Let's go"
    Then I should be redirected to "/hadrianus/meditations-revisited/_edit"
