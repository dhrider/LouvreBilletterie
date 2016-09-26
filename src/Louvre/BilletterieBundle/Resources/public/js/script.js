$(document).ready(function() {
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
            $('#dateV')[0].innerText = dateSelectionnee; // on l'affiche au dessus du formulaire
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
    

    
    
    
    // bouton ajouter Billet
    var compteurBillet = 1;
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

        newBillet.append(btnDelete);
        newBillet.appendTo(billets);

        $('.date').val(dateSelectionnee);
    });

    $('.add-billet').click(function (e) {

    });

    
    
    
    // bouton supprimer billet
    $(document).on('click', '.delete-billet', function(e) {
        e.preventDefault();
        $(e.target).closest('.billet').remove();
    });


    
    
    // changement du champ Tarif dynamique
    // en fonction de la date de naissance
    $(document).on('change', '.naissance', function(e) { // quand on change la date
        console.log(e);
        var splitDate = ($('.dateVisite')[0].value).split('-');
        var dateInverse = splitDate.reverse().join('-');
        var reduit = "non";

        changeTarifBase(e.target.value, dateInverse,reduit, e.target.id);

        // on remet le type sur journée quand on change de date de naissance
        $('.choixType').each(function () {
            if (idExtract($(this)[0].id) === idExtract(e.target.id)) {
                $(this).val('journee');
            }
        });

    });
    // en fonction du type
    $(document).on('change', '.choixType', function (e) {
        var idType = idExtract(e.target.id);
        if (e.target.value === 'demiJournee') {
            $('.montant').each( function () {
                if (idType === idExtract($(this)[0].id)) {
                    var m = $(this).val();
                    if (m !== 0) {
                        $(this).val(m / 2);
                    }
                }
            });
        }
        else {
            $('.montant').each( function () {
                if (idType === idExtract($(this)[0].id)) {
                        var m = $(this).val();
                        $(this).val(m*2);
                }
            });
        }
    });
    // en fonction du choix reduit
    $(document).on('change', '.choixReduit', function (e) {
        if (e.target.checked) {


        }
    });
    // fonction gérant la requète AJAX
    function changeTarifBase(dateN, dateV,reduit, event) {
        $.ajax({
            url: 'achat/remplitarif',
            type: 'POST',
            data: {
                naissance: dateN,
                dateVisite: dateV,
                tarifReduit: reduit
            },
            dataType: 'json',
            success: function (reponse) {
                var idNaissance = idExtract(event);
                $.each(reponse, function (index, element) {
                    $('.montant').each( function () {
                        if (idExtract($(this)[0].id) === idNaissance) {
                            $(this).val(element.tarif);
                        }
                    });
                    $('.tarif').each( function () {
                        if (idExtract($(this)[0].id) === idNaissance) {
                            $(this).val(element.nom + " - " + element.tarif + " €");
                        }
                    });
                });
            },
            error: function (reponse) {
                console.log(reponse.responseText);
            }
        });
    }
    // function d'extraction du numéro de l'id du billet
    function idExtract(id) {
        return (/([0-9])/.exec(id))[0];
    }
});