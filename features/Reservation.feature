Feature: Reservation
  Permet de vérifier que le bonne date est sélecttionnée

  @javascript
  Scenario: Reservation de billets
    Given I am on the homepage
    Then I click on "AchetezDesBillets"
    Then I fill in "dateVisite" with "29"
    Then I should see "reservation"
