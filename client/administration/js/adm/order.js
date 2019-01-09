$(document).ready(function() {
    $('#order').on('submit', function(e) {
        e.preventDefault();

        var $this = $(this);

        $.ajax({
            url: $this.attr('action'),
            type: $this.attr('method'),
            data: $this.serialize(),
            dataType: 'json',
            success: function(json) {
                   var error = json.response;
                   $.notify({
                   message: error,
				});

            }

        });
	});
	$('#changer').on('submit', function(e) {
        e.preventDefault();

        var $this = $(this);

        $.ajax({
            url: $this.attr('action'),
            type: $this.attr('method'),
            data: $this.serialize(),
            dataType: 'json',
            success: function(json) {
                   var error = json.response;
                   $.notify({
                   message: error,
				});

            }

        });
	});
	$('#changevalid1').on('submit', function(e) {
        e.preventDefault();

        var $this = $(this);

        $.ajax({
            url: $this.attr('action'),
            type: $this.attr('method'),
            data: $this.serialize(),
            dataType: 'json',
            success: function(json) {
                   var error = json.response;
                   $.notify({
                   message: error,
				});

            }

        });
	});
});