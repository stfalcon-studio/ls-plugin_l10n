Feature: L10n plugin standart features BDD
  Test for topic translation of l10n

@mink:selenium2
    Scenario: Check for changing block Tags (All/User)
        Given I am on "/login"
        Then I want to login as "admin"

        Given I am on "/ru/index/"
        And I should see in element by css "sidebar .block-content div[data-type='user']" values:
          | value |
          | Нет тегов |

        And I should see in element by css "sidebar .block-content div[data-type='all']" values:
          | value |
          | apple |
          | flash |
          | gadget |
          | ipad |
          | sony |

  Scenario: Check for correctly showing of menu
    Given I am on "/login"
    Then I want to login as "admin"

    Given I am on "/en/settings/l10n/"
    And I should see in element by css "content" values:
      | value |
      | /en/settings/l10n/">Language settings</a> |