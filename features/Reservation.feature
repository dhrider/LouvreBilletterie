Feature: Reservation
  Permet de vérifier que le bonne date est sélecttionnée

  @javascript
  Scenario: Reservation de billets
    Given I am on the homepage
    Then I select "pageAchat"
    Then I should see "Choisissez la date de votre réservation"
