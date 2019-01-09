$(document).ready(function() {
    $('#options').on('submit', function(e) {
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
                    message: 'Options livré',
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