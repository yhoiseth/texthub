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

    And I am logged out
    And I visit "/"

  Scenario: No search
    Then I should see
      | text                     |
      | The first text by Marcus |
      | The first text by Zeno   |
      | I'm last                 |
      | Marcus Aurelius          |
      | Zeno of Citium           |
      | Seneca                   |
    And I see "I'm last" before the other texts

  Scenario: Visit text
    When I click on "The first text by Marcus"
    Then I am redirected to "/marcus-aurelius/the-first-text-by-marcus"

  Scenario: Visit user
    When I click on "Zeno of Citium"
    Then I am redirected to "/zeno"

  Scenario: Search for user
    When I fill "Search" with "sen"
    Then I should see
      | text                   |
      | The first text by Zeno |
      | I'm last               |
      | Zeno of Citium         |
      | Seneca                 |
    But I should not see
      | text                     |
      | The first text by Marcus |
      | Marcus Aurelius          |

  Scenario: Search for title
    When I fill "Search" with "first"
    Then I should see
      | text                     |
      | The first text by Zeno   |
      | The first text by Marcus |
    But I should not see
      | text     |
      | I'm last |

  Scenario: Search in body
    When I fill "Search" with "writing"
    Then I should see
      | text                   |
      | The first text by Zeno |
    But I should not see
      | text                     |
      | I'm last                 |
      | The first text by Marcus |
