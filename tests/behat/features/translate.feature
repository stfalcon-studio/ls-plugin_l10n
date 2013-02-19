Feature: L10n plugin standart features BDD
  Test for topic translation of l10n

    Scenario: Check for activity of plugin
      Then check is plugin active "l10n"

    Scenario: Check for topic translate language
      Then I want to login as "admin"

      Given I am on "/en/blog/3.html"
      And I should see in element by css "sidebar" values:
        | value  |
        | /topic/add/translate/3" alt="Translate" title="Translate">Translate</a> |

      Given I am on "/en/topic/add/translate/3"
      And I should see in element by css "content" values:
        | value  |
        | <option value="russian" |
        | Russian |
      And I should not see "<option value=\"english\">"

      Given I am on "/en/topic/edit/3"
      And I should see in element by css "content" values:
        | value  |
        | <option value="russian" |
        | Russian |
        | <option value="english" |
        | English |

  @mink:selenium2
  Scenario: Check for topic translate language

    Given I am on "/login"
    Then I want to login as "admin"

    Given I am on "/en/topic/add"

    When I select "Gadgets" from "blog_id"
    Then I fill in "topic_title" with "tttest"
    Then I fill in "topic_text" with "this topic needed for check topic translation"
    Then I fill in "topic_tags" with "topic test"
    And I press "Publish"
    And I wait "1000"

    Given I am on "/en/blog/gadgets/"
    Then I follow "tttest"
    Then I follow "Translate"
    When I select "Gadgets" from "blog_id"
    Then I fill in "topic_title" with "topic translate"
    Then I fill in "topic_text" with "topic translate bla bla bla"
    Then I fill in "topic_tags" with "topic tag"
    And I press "Save to drafts"

    Given I am on "/ru/topic/saved/"
    And I should see in element by css "content" values:
      | value  |
      | topic translate |


  @mink:selenium2
  Scenario: Check for changing site language on following to home page
    Given I am on homepage
    And I should see in element by css "nav-main" values:
      | value  |
      | Topics |
      | Blogs |
      | People |
      | Stream |

    Then I follow "Your Site"
    And I should see in element by css "nav-main" values:
      | value  |
      | Topics |
      | Blogs |
      | People |
      | Stream |

    Given I am on "/ru/"
    And I should see in element by css "nav-main" values:
      | value  |
      | Топики |
      | Блоги |
      | Люди |
      | Активность |

    Then I follow "Your Site"
    And I should see in element by css "nav-main" values:
      | value  |
      | Топики |
      | Блоги |
      | Люди |
      | Активность |
