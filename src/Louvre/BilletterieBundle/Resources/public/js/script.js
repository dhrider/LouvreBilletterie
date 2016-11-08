$(document).ready(function() {
    // GESTION DU DATEPICKER //

    // Tableau des jours fériés
    var joursFeries = [
        "1-1",
        "1-5",
        "8-5",
        "14-7",
        "15-8",
        "1-11",
        "11-11",
        "25-12"
    ];

    function disableJoursFeriesDimancheMardi(date) {
        // on récupère le numéro du jour de la semaine
        var day = date.getDay();
        var m = date.getMonth();
        var d = date.getDate();
        var currentDate = d + '-' + (m+1);

        // Si c'est un dimanche OU un mardi
        if (day == 0 || day == 2) {
            return [false]; // on retourne false pour ne pas afficher ces jours
        }
        else { // Sinon on vérifie que ce n'est pas un jour férié
            for (var i = 0; i < joursFeries.length; i++) {
                if ($.inArray(currentDate, joursFeries) != -1) {
                    return [false];
                }
            }
            return [true]; // tous les autres jours sont affichés
        }
    }

    // initialisation du DatePicker
    var dateSelectionnee = "";
    $('.datepicker').datepicker({
        minDate: new Date(), // pas de date antérieure à celle du jour
        dateFormat: "dd-mm-yy",
        beforeShowDay: disableJoursFeriesDimancheMardi, // on exécute la fonction de désactivation des jours
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
            $('#liBillet').removeClass('disabled');
        }
    });




    ////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////




    // GESTION DE L'AJOUT DE LA SUPPRESSION D'UN BILLET //

    // bouton ajouter Billet
    var compteurBillet = $('form .billet').length; // compteur servant à identifier le billet
    // à chaque clique du bouton ajouter billet
    $(document).on('click', '.add-billet', function (e) {
        e.preventDefault();
        // on récupère la div d'id Billet
        var billets = $('#Billets');
        // on définit le numéro du billet qui servira pour l'identifier
        var billet = billets.data('prototype').replace(/__name__/g,(billets.data('index')+ compteurBillet));
        var newBillet = $('<div class="billet"></div>').html(billet);
        var btnDelete = $('<a class="btn btn-danger delete-billet btn-sm" href="#">Supprimer billet</a>');
        compteurBillet++;

        newBillet.append(btnDelete); // on ajoute le bouton supprimer au nouveau billet
        newBillet.appendTo(billets); // on ajoute le nouveau billet à la liste des billets


    });

    // bouton supprimer billet
    $(document).on('click', '.delete-billet', function(e) {
        e.preventDefault();
        $(e.target).closest('.billet').remove();
    });





    ////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////


    // Message d'avertissement lors de la sélection du choix réduit
    $(document).on('click', '.choixReduit', function (e) {
        console.log($(e.target).is(':checked'));
        if ($(e.target).is(':checked')) {
            $('#checkReduit').removeClass('hidden');
        }
        else {
            $('#checkReduit').addClass('hidden');
        }
    });


    ////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////




    // GESTION DU PAIEMENT //

    // Affichage de l'onglet paiement après la soumission du formulaire des billets
    if (window.location.hash == "#paiement") {
        $('#ongletPaiement').tab('show');
        $('#liPaiement').removeClass('disabled');
        $('#liBillet').removeClass('disabled');
    }

    if (window.location.hash == "#confirmation") {
        $('#ongletConfirmation').tab('show');
        $('#liJour').addClass('disabled');
        $('#liBillet').addClass('disabled');
        $('#liPaiement').addClass('disabled');
    }
});