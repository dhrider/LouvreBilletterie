Feature: Reservation
  Permet de vérifier que le bonne date est sélecttionnée

  @javascript
  Scenario: Reservation de billets
    Given I am on the homepage
    Then I wait for 1 seconds
    Then I should see "Visites guidées"
