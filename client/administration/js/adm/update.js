$(document).ready(function() {
    $('#update').on('submit', function(e) {
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
                    message: 'Mise à jour créée',
					});
					$('#title').val('');
					$('#progression').val('');
					$('#explication').val('');
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
$(document).ready(function() {
    $('#updateupdate').on('submit', function(e) {
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
                    message: 'Mise à jour modifié',
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
$(document).ready(function() {
    $('#supprupdate').on('submit', function(e) {
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
                    message: 'Mise à jour supprimé',
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