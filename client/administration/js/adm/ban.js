$(document).ready(function() {
    $('#ban').on('submit', function(e) {
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
                    message: 'Bannissement effectué',
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