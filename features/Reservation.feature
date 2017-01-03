Feature: Reservation
  Permet de vérifier le bon déroulement d'une réservation de billets

  @javascript
  Scenario: Reservation de billets
    Given I am on the homepage
    Then I click on "pageAchat"
    Then I am on "/achat"
    Then I select the date "28"
    Then I fill in "reservation_billets_0_nom" with "Bordmann"
    Then I fill in "reservation_billets_0_prenom" with "Philippe"
    Then I select "FR" from "reservation_billets_0_pays"
    Then I fill in "reservation_billets_0_dateNaissance" with "11/06/1975"
    Then I select "demiJournee" from "reservation_billets_0_type"
    Then I check "reservation_billets_0_reduit"
    Then I should see "Un document validant l'accès au tarif réduit vous sera demandé à la présentation de votre billet !"
    Then I fill in "reservation_email_first" with "p_bordmann@orange.fr"
    Then I fill in "reservation_email_second" with "p_bordmann@orange.fr"
    Then I submit the form "reservation"
    Then I wait for 2 seconds
    Then I should see "5 €"
