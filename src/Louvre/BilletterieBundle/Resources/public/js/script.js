$(document).ready(function() {
    // Exclusion des dimanche du datepicker
    function disableSundayTuesdayDatePicker(date) {
        var day = date.getDay();
        if (day == 0 || day == 2) {
            return [false];
        }
        else {
            return [true];
        }
    }
    // initialisation du DatePicker
    var dateSelectionnee = "";
    $('.datepicker').datepicker({
        minDate: new Date(),
        maxDate: new Date(2016, 12, 31),
        dateFormat: "dd-mm-yy",
        beforeShowDay: disableSundayTuesdayDatePicker,
        onSelect: function (dateText) {
            var ongletActif = $('.nav-tabs > .active').next('li').find('a');
            ongletActif.trigger('click');
            dateSelectionnee = dateText;
            $('.date').val(dateSelectionnee);
            $('#dateV')[0].innerText = dateSelectionnee;
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
    $(document).on('click', '.add-billet', function (e) {
        e.preventDefault();
        var billets = $('#Billets');
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
        var splitDate = ($('.dateVisite')[0].value).split('-');
        var dateInverse = splitDate.reverse().join('-');
        var reduit = "non";
        var idNaissance = /([0-9])/.exec(e.target.id);
        changeTarifBase(e.target.value, dateInverse,reduit, e.target.id);

        // on remet le type sur journée quand on change de date de naissance
        $('.choixType').each(function () {
            var idType = /([0-9])/.exec($(this)[0].id);
            if (idType[0] === idNaissance[0]) {
                $(this).val('journee');
            }
        });

    });

    // en fonction du type
    $(document).on('change', '.choixType', function (e) {
        var idType = /([0-9])/.exec(e.target.id);
        if (e.target.value === 'demiJournee') {
            $('.montant').each( function () {
                var idMontant = /([0-9])/.exec($(this)[0].id);
                if (idType[0] === idMontant[0]) {
                    var m = $(this).val();
                    if (m !== 0) {
                        $(this).val(m / 2);
                    }
                }
            });
        }
        else {
            $('.montant').each( function () {
                var idMontant = /([0-9])/.exec($(this)[0].id);
                if (idType[0] === idMontant[0]) {
                        var m = $(this).val();
                        $(this).val(m*2);
                }
            });
        }
    });

    // en fonction du choix reduit
    $(document).on('change', '.choixReduit', function (e) {

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
                var idNaissance = /([0-9])/.exec(event);
                $.each(reponse, function (index, element) {
                    $('.montant').each( function () {
                        var idTarif = /([0-9])/.exec($(this)[0].id);
                        if (idTarif[0] === idNaissance[0]) {
                            $(this).val(element.tarif);
                        }
                    });
                    $('.tarif').each( function () {
                        var idTarif = /([0-9])/.exec($(this)[0].id);
                        if (idTarif[0] === idNaissance[0]) {
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
});