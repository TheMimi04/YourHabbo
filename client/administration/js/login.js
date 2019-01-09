$(document).ready(function() {
    $('#login').on('submit', function(e) {
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
                    message: 'Connexion à l\'administration réussi',
					});
					window.setTimeout(function() {
                            window.location = "index";
                        }, 1500);
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