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
        changeTarifBase(e.target.value, dateInverse, e.target.id);
    });

    // en fonction du type
    $(document).on('change', '.choixType', function (e) {
        var regType = /([0-9])/.exec(e.target.id);
        console.log(e.target.id);
        if (e.target.value === 'demiJournee') {
            $('.montant').each( function () {
                var regMontant = /([0-9])/.exec($(this)[0].id);
                if (regType[0] === regMontant[0]) {
                    var m = $(this).val();
                    $(this).val(m/2);
                }
            });
        }
        else {
            $('.montant').each( function () {
                var regMontant = /([0-9])/.exec($(this)[0].id);
                if (regType[0] === regMontant[0]) {
                        var m = $(this).val();
                        $(this).val(m*2);
                }
            });
        }
    });

    // en fonction du choix reduit
    $('.choixReduit').change(function (e) {

    });

    // fonction gérant la requète AJAX
    function changeTarifBase(dateN, dateV, reduit, event) {
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
                var regNaissance = /([0-9])/.exec(event);
                $.each(reponse, function (index, element) {
                    $('.montant').each( function () {
                        var regTarif = /([0-9])/.exec($(this)[0].id);
                        if (regTarif[0] === regNaissance[0]) {
                            $(this).val(element.tarif);
                        }
                    });
                    $('.tarif').each( function () {
                        var regTarif = /([0-9])/.exec($(this)[0].id);
                        if (regTarif[0] === regNaissance[0]) {
                            $(this).val(element.nom + " - " + element.tarif + " €");
                        }
                    });
                });
            },
            error: function () {
                alert('erreur');
            }
        });
    }

    function changeTarifReduit(event) {

    }
});