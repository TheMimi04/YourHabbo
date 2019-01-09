$(document).ready(function() {
    $('#search').on('submit', function(e) {
        e.preventDefault();

        var $this = $(this);

        $.ajax({
            url: $this.attr('action'),
            type: $this.attr('method'),
            data: $this.serialize(),
            dataType: 'html',
			success: function(rech){
				$('#result').html(rech);
		   }

        });
    });
});