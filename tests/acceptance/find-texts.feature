@prepare_database @clean_files @text
Feature: Find texts
  In order to find texts
  As a web visitor
  I need to be able to find texts

  Background:
    Given the users
      | name            | username        |
      | Marcus Aurelius | marcus-aurelius |
      | Zeno of Citium  | zeno            |
      | Seneca          | seneca          |

    And the texts
      | title                    | body                                                 | owner           |
      | The first text by Marcus | I'm Marcus. This is my first text.                   | marcus-aurelius |
      | The first text by Zeno   | I like writing.                                      | zeno            |
      | I'm last                 | I wrote this after the other guys wrote their texts. | seneca          |

    And I visit "/logout"
    And I visit "/"

    @watch
  Scenario: No search
    Then I should see "The first text by Marcus"
    Then I should see "The first text by Zeno"
    Then I should see "I'm last"
    Then I should see "Marcus Aurelius"
    Then I should see "Zeno of Citium"
    Then I should see "Seneca"
    And I see "I'm last" before the other texts

  Scenario: Visit text
    When I click "The first text by Marcus"
    Then I should be redirected to "/marcus-aurelius/the-first-text-by-marcus"

  Scenario: Visit user
    When I click "Zeno of Citium"
    Then I should be redirected to "/zeno"

  Scenario: Search for user
    When I fill in "Search" with "sen"
    Then I should see "The first text by Zeno"
    Then I should see "I'm last"
    Then I should see "Zeno of Citium"
    Then I should see "Seneca"
    But I should not see "The first text by Marcus"
    But I should not see "Marcus Aurelius"

  Scenario: Search for title
    When I fill in "Search" with "first"
    Then I should see "The first text by Zeno"
    Then I should see "The first text by Marcus"
    But I should not see "I'm last"

  Scenario: Search in body
    When I fill in "Search" with "writing"
    Then I should see "The first text by Zeno"
    But I should not see "I'm last"
    But I should not see "The first text by Marcus"
