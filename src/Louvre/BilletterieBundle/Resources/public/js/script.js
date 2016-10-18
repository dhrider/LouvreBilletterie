$(document).ready(function() {
    // GESTION DU DATEPICKER //

    // Exclusion des dimanche du datepicker
    function disableSundayTuesdayDatePicker(date) {
        // on récupère le jour de la date
        var day = date.getDay();
        // Si c'est un dimanche OU un mardi
        if (day == 0 || day == 2) {
            return [false]; // on retourne false pour ne pas afficher ces jours
        }
        else {
            return [true]; // tous les autres jours sont affichés
        }
    }

    // initialisation du DatePicker
    var dateSelectionnee = "";
    $('.datepicker').datepicker({
        minDate: new Date(), // pas de date antérieure à celle du jour
        maxDate: new Date(2017, 12, 31),
        dateFormat: "dd-mm-yy",
        beforeShowDay: disableSundayTuesdayDatePicker, // on exécute la fonction de désactivation des jours
        // en fonction de la date sélectionnée
        onSelect: function (dateText) {
            // on recherche l'onglet actif
            var ongletActif = $('.nav-tabs > .active').next('li').find('a');
            // on simule le click sur cet onglet
            ongletActif.trigger('click');
            // on récupère la date et on la l'applique au champ caché "date visit" du formulaire
            dateSelectionnee = dateText;
            $('.date').val(dateSelectionnee);
            $('#dateV')[0].innerText = dateText; // on l'affiche au dessus du formulaire
        }
    });




    ////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////




    // GESTION DE L'AJOUT DE LA SUPPRESSION D'UN BILLET //

    // bouton ajouter Billet
    var compteurBillet = 1; // compteur servant à identifier le billet
    // à chaque clique du bouton ajouter billet
    $(document).on('click', '.add-billet', function (e) {
        e.preventDefault();
        // on récupère la div d'id Billet
        var billets = $('#Billets');
        // on définit le numéro du billet qui servira pour l'identifier
        var billet = billets.data('prototype').replace(/__name__/g,(billets.data('index')+compteurBillet));
        var newBillet = $('<div class="billet"></div>').html(billet);
        var btnDelete = $('<a class="btn btn-danger delete-billet" href="#">Supprimer billet</a>');
        compteurBillet++;

        newBillet.append(btnDelete); // on ajoute le bouton supprimer au nouveau billet
        newBillet.appendTo(billets); // on ajoute le nouveau billet à la liste des billets

        $('.date').val(dateSelectionnee); // on affecte la date de visite au champ caché
    });

    // bouton supprimer billet
    $(document).on('click', '.delete-billet', function(e) {
        e.preventDefault();
        $(e.target).closest('.billet').remove();
    });




    ////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////



    
    // CHANGEMENT DU TARIF DYNAMIQUE //

    var reduit = "non";
    var TypeDemiJournee = false;

    // en fonction de la date de naissance
    $(document).on('blur', '.naissance', function(e) { // quand on change la date
        // on récupère la date de visite et on l'inverse pour la mettre au format voulu
        var splitDate = ($('.dateVisite')[0].value).split('-');
        var dateInverse = splitDate.reverse().join('-');

        // on change le tarif
        changeTarif(e.target.value, dateInverse,reduit, e.target.id);
    });

    // en fonction du type
    $(document).on('change', '.choixType', function (e) {
        var idType = idExtract(e.target.id);
        var montant = $('#reservation_billets_' + idType + '_montant').val();

        if (e.target.value === 'demiJournee') { // si on sélectionne demi-journée
            TypeDemiJournee = true;
            // on divise le tarif par 2 si celui-ci ne vaut pas 0 (tarif gratuit)
            if (montant !== 0) {
                $('#reservation_billets_' + idType + '_montant').val(montant / 2);
            }
        }
        else { // si on sélectionne journée
            TypeDemiJournee = false;
            $('#reservation_billets_' + idType + '_montant').val(montant * 2);
        }
    });

    // en fonction du choix reduit
    $(document).on('change', '.choixReduit', function (e) {
        var idReduit = idExtract(e.target.id);
        var dateVisite = $('#reservation_dateReservation').val().split('-').reverse().join('-');
        var dateNaissance = $('#reservation_billets_'+idReduit+'_dateNaissance').val();

        if (e.target.checked) { // si on coche
            reduit = "oui";
        }
        else { // si on décoche
            reduit = "non";
        }

        changeTarif(dateNaissance,dateVisite,reduit,e.target.id);
    });




    ////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////




    // GESTION DU PAIEMENT //

    // Affichage de l'onglet paiement après la soumission du formulaire des billets
    var idReservation = "";
    if (window.location.pathname !== "") {
        idReservation = (window.location.pathname).toString().match(/\d+/)[0];
        if (idReservation !== "") {
            $('#ongletPaiement').tab('show');
            afficheReservation(parseInt(idReservation));
        }
    }

    //Gestion du bouton payer
    $(document).on('click', '#btnPayer' ,function (e) {
        e.preventDefault();
        var emailReservation = $('#email').val();
        console.log(emailReservation);
        payerReservation(emailReservation, totalReservation, idReservation);
    });




    ////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////




    // REQUETES AJAX //

    // Récupération des tarifs
    function changeTarif(dateN, dateV,reduit, event) {
        $.ajax({
            url: '/achat/remplitarif',
            type: 'POST',
            data: {
                naissance: dateN,
                dateVisite: dateV,
                reduit: reduit
            },
            dataType: 'json',
            success: function (reponse) {
                var idBillet = idExtract(event);
                var montant = 0;

                if (!TypeDemiJournee) {
                    montant = parseInt(reponse.tarif);
                }
                else {
                    montant = parseInt(reponse.tarif / 2);
                }

                $('#reservation_billets_'+idBillet+'_montant').val(montant);
                $('#reservation_billets_'+idBillet+'_tarif').val(parseInt(reponse.id));
            },
            error: function () {
                console.log("Erreur réception du tarif");
            }
        });
    }

    // Affichage du récapitulatif de la réservation dans l'onglet paiement
    function  afficheReservation(idReservation) {
        $.ajax({
            url: '/achat/recapReservation',
            type: 'POST',
            data: {
                idReservation: idReservation
            },
            dataType: 'json',
            success: function (response) {
                $.each(response, function (index, element) {
                    createLigneTableauRecap(element);

                })
                $('#montantTotal').html(totalReservation + " €");
            },
            error: function (response) {
                console.log(response.text);
            }
        });
    }

    // Envoi du paiement
    function payerReservation (email, montant, idresa) {
        $.ajax({
            url: '/achat/prepare',
            type: 'POST',
            data: {
                email: email,
                montant: montant,
                idresa: idresa
            },
            dataType: 'json',
            success: function (response) {
               console.log(response.text);
            },
            error: function (response) {
                console.log(response.text);
            }
        });
    }




    ////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////




    // FONCTIONS DIVERSES //

    // function d'extraction du numéro de l'id du billet
    function idExtract(id) {
        return (/([0-9])/.exec(id))[0];
    }

    // function création d'une ligne du tableau de récapitulation des billets
    var numeroLigne = 1;
    var totalReservation = 0;
    function createLigneTableauRecap(element) {
        if (element.reduit == false) {
            element.reduit = "non";
        }
        else {
            element.reduit = "oui";
        }

        $('#ligneTableauRecap').append(
            '<tr>' +
                '<td>' + numeroLigne     + '</td>' +
                '<td>' + element.nom     + '</td>' +
                '<td>' + element.type    + '</td>' +
                '<td>' + element.reduit  + '</td>' +
                '<td>' + element.montant + '</td>' +
            '</tr>'
        );

        numeroLigne++;
        totalReservation = totalReservation + element.montant;
    }
});