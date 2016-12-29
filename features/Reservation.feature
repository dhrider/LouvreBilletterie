Feature: Reservation
  Permet de vérifier que le bonne date est sélecttionnée

  @javascript
  Scenario: Reservation de billets
    Given I am on the homepage
    Then I click on "Achetez des billets"
    Then I click on "Date active"
    Then I should see "reservation"
