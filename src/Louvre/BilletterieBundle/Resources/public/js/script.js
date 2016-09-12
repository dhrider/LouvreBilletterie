$(document).ready(function() {
    // Récupération de la date de visite du 1er => 2ème onglet
    $(document).on( 'shown.bs.tab', 'a', function (e) {
        if ($(e.target)[0].id == 'ongletBillet') {
            var dateRecup = $(e.relatedTarget).parent().closest('#corpsAchat').find('#dateVisite')[0].value;
            $(e.target).parent().closest('#corpsAchat').find('#dateChoisie')[0].innerHTML = "Billet(s) pour le : "
                + dateRecup;
        }
    });
    
    
    

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
    $('.datepicker').datepicker({
        minDate: new Date(),
        maxDate: new Date(2016, 12, 31),
        dateFormat: "dd/mm/yy",
        beforeShowDay: disableSundayTuesdayDatePicker
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
        
    });

    
    
    
    // bouton supprimer billet
    $(document).on('click', '.delete-billet', function(e) {
        e.preventDefault();
        $(e.target).closest('.billet').remove();
    });
});