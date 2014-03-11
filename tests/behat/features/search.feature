Feature: L10n plugin standart features BDD
  Test for Sphinx search topic

  Scenario: Sphinx search topic not result
    Given I am on homepage
    Then I should see "Sony MicroVault Mach USB 3.0 flash drive"
    When I rotate sphinx

    When I go to "/en/search/topics/?q=abra+cadabra"
    Then I should see "Thats strange. No results found."


  Scenario: Sphinx search topic
    Given I am on homepage
    Then I should see "Sony MicroVault Mach USB 3.0 flash drive"
    When I rotate sphinx

    When I go to "/en/search/topics/?q=sony+microvault"
    Then I should see "Sony MicroVault Mach USB 3.0 flash drive"
    And I should see "Want more speeds and better protection for your data? The Sony MicroVault Mach flash USB 3.0 drive is what you need. It offers the USB 3.0 interface that delivers data at super high speeds of up to 5Gbps. It’s also backward compatible with USB 2.0"


  Scenario: Create text snippet
    Given I am on homepage
    Then I should see "Sony MicroVault Mach USB 3.0 flash drive"
    When I rotate sphinx

    When I go to "/en/search/topics/?q=Toshiba"
    Then I should see "Toshiba is to add a new… mass which is known as Toshiba AT330. The device is equipped… 1920 x 1200 pixels. The Toshiba AT330 tablet is currently at…"