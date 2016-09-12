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
    
    
    
    
    // Gestion du bouton "Suivant"
    $('#btnSuivant').click(function () { // A chaque click du bouton suivant
        // On recherche la balise suivante
        var ongletActif = $('.nav-tabs > .active').next('li').find('a');

        // On affiche l'onglet suivant en triggant le click su la balise <a>
        ongletActif.trigger('click');

        // On n'affiche pas le bouton précédent si on se trouve sur l'onglet "Jour de la visite"
        if (ongletActif[0].hash != "#jour" && $('#dateVisite')[0].value !== "") {
            $('#btnPrecedent').show();
        }

        // Si l'onglet suivant est le dernier
        if (ongletActif[0].hash == "#paiement") {
            // on change le texte du bouton
            $('#btnSuivant')[0].innerText = "Payer";
        }
    });
    
    
    
    
    // Gestion du bouton "Précédent"
    $('#btnPrecedent').click(function () { // A chaque click du bouton précédent
        // On recherche la balise précédente
        var ongletActif = $('.nav-tabs > .active').prev('li').find('a');

        // On affiche l'onglet précédent en triggant le click su la balise <a>
        ongletActif.trigger('click');

        // Si l'onglet actif est le 1er
        if (ongletActif[0].hash == "#jour") {
            $('#btnPrecedent').hide(); // on cache le bouton "précédent"
        }

        // Si l'onglet actif n'est pas l'onglet "paiement"
        if (ongletActif[0].hash != "#paiement") {
            $('#btnSuivant')[0].innerText = "Suivant"; // On change le texte du bouton
        }
    });

    
    
    
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
    
    
    
    
    // variables type de billet et tarif réduit
    var selectType = "";
    var selectReduit = false;
    
    
    
    
    // affichage du prix du billet dynamique
    // en fonction du type
    $(document).on('change', '.choixType', function (e) {
        e.preventDefault();          
        if (selectType !== 'selection') {
            selectType = $(e.target)[0].value;
            //$('.montantBillet')[0].value =  selectType;
        }
    });
    // en fonction du checkbox "réduit"
    $(document).on('change', '.choixReduit', function (e) {
        e.preventDefault();
        selectReduit = $(e.target)[0].value;
        console.log(selectReduit);
    });
});