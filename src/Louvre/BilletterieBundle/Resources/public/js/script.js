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

    // affichage du datePicker centré
    var largeurContainer = $('.datepicker').width();
    var largeurDatePicker = $('.ui-datepicker').width();
    var hauteurDatePicker = $('.ui-datepicker').height();
    var leftPos = (largeurContainer - largeurDatePicker) / 2;
    $('.ui-datepicker').css({
        left:leftPos,
        position: 'absolute'
    });
    $('#dateVisite').css({'height': hauteurDatePicker});
    

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
        var btnDelete = $('<a class="btn btn-primary delete-billet" href="#">Supprimer le billet billet</a>');
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
        if (e.target.value === 'demiJournee') { // si on sélectionne demi-journée
            TypeDemiJournee = true;
            $('.montant').each( function () {
                if (idType === idExtract($(this)[0].id)) {
                    var m = $(this).val();
                    // on divise le tarif par 2 si celui-ci ne vaut pas 0 (tarif gratuit)
                    if (m !== 0) {
                        $(this).val(m / 2);
                    }
                }
            });
        }
        else { // si on sélectionne journée
            TypeDemiJournee = false;
            $('.montant').each( function () {
                if (idType === idExtract($(this)[0].id)) {
                        var m = $(this).val();
                        $(this).val(m*2); // on multiplie le tarif par 2
                }
            });
        }
    });

    // en fonction du choix reduit
    $(document).on('change', '.choixReduit', function (e) {
        var idReduit = idExtract(e.target.id);
        var dateV = "";
        var dateN = "";

        // les dates récupérées serviront si on décoche la tarif réduit
        // on stocke la date de visite
        $('.dateVisite').each(function () {
            if (idReduit === idExtract($(this)[0].id)) {
                var splitDate = $(this).val().split('-');
                dateV = splitDate.reverse().join('-');
            }
        });

        // on stocke la date de naissance
        $('.naissance').each(function () {
            if (idReduit === idExtract($(this)[0].id)) {
                dateN = $(this).val();
            }
        });

        if (e.target.checked) { // si on coche
            reduit = "oui";
        }
        else { // si on décoche
            reduit = "non";
        }

        changeTarif(dateN,dateV,reduit,e.target.id);
    });


    ////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////


    $(document).on('click', '.nav-tabs', function (e) {
        if (e.target.id === "paiement") {

            console.log("onglet paiement");
        }
    })



    ////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////


    // REQUETES AJAX //

    // Récupération des tarifs
    function changeTarif(dateN, dateV,reduit, event) {
        $.ajax({
            url: 'achat/remplitarif',
            type: 'POST',
            data: {
                naissance: dateN,
                dateVisite: dateV,
                reduit: reduit
            },
            dataType: 'json',
            success: function (reponse) {
                var idBillet = idExtract(event);
                $.each(reponse, function (index, element) {
                    $('.montant').each( function () {
                        if (idExtract($(this)[0].id) === idBillet) {
                            if (!TypeDemiJournee) {
                                $(this).val(parseInt(element.tarif));
                            }
                            else {
                                $(this).val(parseInt(element.tarif / 2));
                            }
                        }
                    });
                    $('.tarif').each( function () {
                        if (idExtract($(this)[0].id) === idBillet) {
                            $(this).val(parseInt(element.id));
                        }
                    });
                });
            },
            error: function (reponse) {
                alert(reponse.responseText);
            }
        });
    }

    // Récupération de la réservation lors du paiement
    function recapReservation(dateR, event) {
        $.ajax({
           url: 'achat/recapReservation',
            type: 'POST',
            data: {
               dateReservation: dateR
            },
            dataType: 'json',
            success: function (reponse) {

            },
            error: function (reponse) {
                alert(reponse.responseText);
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
});