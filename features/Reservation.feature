Feature: Reservation
  Permet de vérifier le bon déroulement d'une réservation de billets

  @javascript
  Scenario: normal_reduit
    Given I am on the homepage
    Then I click on "pageAchat"
    Then I am on "/achat"
    Then I select the date "28"
    Then I fill in the following:
      | reservation_billets_0_nom            | Bordmann             |
      | reservation_billets_0_prenom         | Philippe             |
      | reservation_billets_0_dateNaissance  | 11/06/1975           |
      | reservation_email_first              | p_bordmann@orange.fr |
      | reservation_email_second             | p_bordmann@orange.fr |
    Then I select "FR" from "reservation_billets_0_pays"
    Then I select "journee" from "reservation_billets_0_type"
    Then I check "reservation_billets_0_reduit"
    Then I should see "Un document validant l'accès au tarif réduit vous sera demandé à la présentation de votre billet !"
    Then I submit the form "reservation"
    Then I should see "10 €"
    Then I wait for 2 seconds

  @javascript
  Scenario: normal_demi_journee
    Given I am on the homepage
    Then I click on "pageAchat"
    Then I am on "/achat"
    Then I select the date "28"
    Then I fill in the following:
      | reservation_billets_0_nom            | Bordmann             |
      | reservation_billets_0_prenom         | Philippe             |
      | reservation_billets_0_dateNaissance  | 11/06/1975           |
      | reservation_email_first              | p_bordmann@orange.fr |
      | reservation_email_second             | p_bordmann@orange.fr |
    Then I select "FR" from "reservation_billets_0_pays"
    Then I select "demiJournee" from "reservation_billets_0_type"
    Then I submit the form "reservation"
    Then I should see "8 €"