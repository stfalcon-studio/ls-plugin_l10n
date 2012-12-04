Feature: L10n plugin standart features BDD
  Test for topic translation of l10n

    Scenario: Check for activity of plugin
      Then check is plugin active "l10n"

    Scenario: Check for topic translate language
      Then I want to login as "admin"

      Given I am on "/en/blog/3.html"
      And I should see in element "sidebar" values:
        | value  |
        | /topic/add/translate/3" alt="Translate" title="Translate">Translate</a> |

      Given I am on "/en/topic/add/translate/3"
      And I should see in element "content" values:
        | value  |
        | <option value="russian" |
        | Russian |
      And I should not see "<option value=\"english\">"

      Given I am on "/en/topic/edit/3"
      And I should see in element "content" values:
        | value  |
        | <option value="russian" |
        | Russian |
        | <option value="english" |
        | English |