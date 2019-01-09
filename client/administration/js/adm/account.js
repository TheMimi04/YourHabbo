$(document).ready(function() {
    $('#account1').on('submit', function(e) {
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
                    message: 'Changement du profil effectué',
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
	$('#account2').on('submit', function(e) {
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
                    message: 'Changement des options du profil effectué',
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