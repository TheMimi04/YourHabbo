$(document).ready(function() {
    $('#addrept').on('submit', function(e) {
        e.preventDefault();

        var $this = $(this);

        $.ajax({
            url: $this.attr('action'),
            type: $this.attr('method'),
            data: $this.serialize(),
            dataType: 'json',
            success: function(json) {
                if (json.response === 'success') {
                    $.notify({
                    message: 'Réponse ajouté',
					});
					$('#repo').prepend( json.repo );
					$('#rep').val('');
                } else {
                    var error = json.response;
                    $.notify({
                    message: error,
					});

                }
            }

        });
    });
	$('#rep').keypress(function(e){
    if(e.keyCode == 13){
        e.preventDefault(); // Annule la saisie d'un retour chariot
        $(this).parents('form').submit(); // Soumet le formulaire
    }
});
});
$(document).ready(function() {
    $('#closete').on('submit', function(e) {
        e.preventDefault();

        var $this = $(this);

        $.ajax({
            url: $this.attr('action'),
            type: $this.attr('method'),
            data: $this.serialize(),
            dataType: 'json',
            success: function(json) {
                if (json.response === 'success') {
                    $.notify({
                    message: 'Ticket fermé',
					});
                } else {
                    var error = json.response;
                    $.notify({
                    message: error,
					});

                }
            }

        });
    });
});