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
    $('.add-billet').click(function (e) {
        e.preventDefault();
        var billets = $('#Billets');
        var billet = billets.data('prototype').replace(/__name__/g,(billets.data('index')+1));
        var newBillet = $('<div class="billet"></div>').html(billet);
        var btnDelete = $('<a class="btn btn-primary delete-billet" href="#">Supprimer le billet billet</a>');

        
        newBillet.append(btnDelete);
        newBillet.appendTo(billets);

        $('.date').val(dateSelectionnee);
    });

    
    
    
    // bouton supprimer billet
    $(document).on('click', '.delete-billet', function(e) {
        e.preventDefault();
        $(e.target).closest('.billet').remove();
    });
    
    
    
    
    // changement du champ Tarif dynamique
    // en fonction de la date de naissance
    $(document).on('change', '.naissance', function(e) {
        var splitDate = ($('.dateVisite')[0].value).split('-');
        var dateInverse = splitDate.reverse().join('-');
        changeTarif(e.target.value, dateInverse);
    });

    // en fonction du type
    $('.choixType').change(function () {

    });

    // en fonction du choix reduit
    $('.choixReduit').change(function () {

    });

    // fonction gérant la requète AJAX
    function changeTarif(dateN, dateV, typ, red) {
        $.ajax({
            url: 'achat/remplitarif',
            type: 'POST',
            data: {
                naissance: dateN,
                dateVisite: dateV,
                type: typ,
                reduit: red
            },
            dataType: 'json',
            success: function (reponse) {
                console.log(reponse);
                $.each(reponse, function (index, element) {
                    $('.tarif').val(element.nom + " - " + element.tarif + " €");
                    //console.log(element);
                    //$('.tarif').val(element);
                });
            },
            error: function () {
                alert('erreur retour json');
            }
        });
    }
});