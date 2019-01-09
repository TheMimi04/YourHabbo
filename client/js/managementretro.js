$(document).ready(function() {
    $('#sessionvps').click(function(e) {
        e.preventDefault();

        var $this = $(this);

        $.ajax({
            url: '/core/action/action?to=sessionvps',
            type: 'POST',
            dataType: 'json',
            success: function(json) {
                if (json.response === 'success') {
					$('#errorvps').html('<span style="color: green"><i class="fa fa-check"></i> Commande effectué</span>. Vous serez livré sous 24 à 48 heures');
                } else {
                    var error = json.response;
                    $('#errorvps').html(error);
                }
            }

        });
    });
	
	$('#renouvvps').click(function(e) {
        e.preventDefault();

        var $this = $(this);

        $.ajax({
            url: '/core/action/action?to=renouvvps',
            type: 'POST',
            dataType: 'json',
            success: function(json) {
                if (json.response === 'success') {
					$('#renouvvps').html('<span style="color: green"><i class="fa fa-check"></i> Renouvellement effectué</span>');
					$('#etat').html('<i style="color: green" class="fa fa-check"></i>Active');
                } else {
                    var error = json.response;
                    $('#renouvvps').html(error);
                }
            }

        });
    });
	
	$('#packswf').click(function(e) {
        e.preventDefault();

        var $this = $(this);

        $.ajax({
            url: '/core/action/action?to=packswf',
            type: 'POST',
            dataType: 'json',
            success: function(json) {
                if (json.response === 'success') {
					$('#errorswf').html('<span style="color: green"><i class="fa fa-check"></i> Achat effectué</span>');
                } else {
                    var error = json.response;
                    $('#errorswf').html(error);
                }
            }

        });
    });
	
	$('#renouveler').click(function(e) {
        e.preventDefault();

        var $this = $(this);

        $.ajax({
            url: '/core/action/action?to=renouvellement',
            type: 'POST',
            dataType: 'json',
            success: function(json) {
                if (json.response === 'success') {
					new PNotify({
                        title: 'Renouvellement effectué',
                        text: 'Votre service a bien été renouvelé',
                        addclass: 'bg-success'
                    });
					$('#expiration').html(json.date);
                } else {
                    var error = json.response;
                    new PNotify({
                        title: 'Attention',
                        text: error,
                        addclass: 'bg-danger'
                    });
                }
            }

        });
    });
	
	$('#resilier').click(function(e) {
        e.preventDefault();

        var $this = $(this);

        $.ajax({
            url: '/core/action/action?to=resilier',
            type: 'POST',
            dataType: 'json',
            success: function(json) {
                if (json.response === 'success') {
					new PNotify({
                        title: 'Résiliation effectué',
                        text: 'Votre service a bien été résilié',
                        addclass: 'bg-success'
                    });
					$('#etat').html(json.etat);
                } else {
                    var error = json.response;
                    new PNotify({
                        title: 'Attention',
                        text: error,
                        addclass: 'bg-danger'
                    });
                }
            }

        });
    });
	
	$('#reboot').click(function(e) {
        e.preventDefault();

        var $this = $(this);

        $.ajax({
            url: '/core/action/action?to=reboot',
            type: 'POST',
            dataType: 'json',
            success: function(json) {
                if (json.response === 'success') {
					$('#rebootd').html('<center>Votre rétro sera redémarré sous 24h</center>');
                } else {
                    var error = json.response;
                    new PNotify({
                        title: 'Attention',
                        text: error,
                        addclass: 'bg-danger'
                    });
                }
            }

        });
    });
	$('#cms').click(function(e) {
        e.preventDefault();

        var $this = $(this);

        $.ajax({
            url: '/core/action/action?to=cms',
            type: 'POST',
            dataType: 'json',
            success: function(json) {
                if (json.response === 'success') {
					$('#cmsd').html('<center>Votre CMS sera réinstallé sous 24h</center>');
                } else {
                    var error = json.response;
                    new PNotify({
                        title: 'Attention',
                        text: error,
                        addclass: 'bg-danger'
                    });
                }
            }

        });
    });
	$('#db').click(function(e) {
        e.preventDefault();

        var $this = $(this);

        $.ajax({
            url: '/core/action/action?to=db',
            type: 'POST',
            dataType: 'json',
            success: function(json) {
                if (json.response === 'success') {
					$('#bdd').html('<center>Votre base de données sera réinstallé sous 24h</center>');
                } else {
                    var error = json.response;
                    new PNotify({
                        title: 'Attention',
                        text: error,
                        addclass: 'bg-danger'
                    });
                }
            }

        });
    });
});