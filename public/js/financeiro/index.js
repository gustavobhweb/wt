$(function(){
	$('.btn-liberar-remessa').on('click', function(){
		var $thisContext = $(this);
		liberar_remessa($(this).data('remessa'), function(){
			$thisContext.attr('disabled', 'true');
			$thisContext.html('Liberando...');
		}, function(response){
			$thisContext.attr('disabled', 'true');
			$thisContext.removeClass('wm-btn-green');
			$thisContext.html('Liberado');
		});
	});
});

function liberar_remessa(remessa, beforeSendAction, successAction)
{
	$.ajax({
		url: '/financeiro/ajax-liberar',
		type: 'POST',
		dataType: 'json',
		data: {
			remessa: remessa
		},
		beforeSend: function(){
			if (typeof(beforeSendAction) == 'function') {
				beforeSendAction();
			}
		},
		success: function(response){
			if (!response.status) {
				new wmDialog('Problemas de conexão! Atualize a página e tente novamente.', {
                                height:230,
                                width: 330,
                                btnCancelEnabled: false
                            }).open();
			} else {
				if (typeof(successAction) == 'function') {
					successAction(response.message);
				}
			}
		},
		error: function(){
			new wmDialog('Problemas de conexão! Atualize a página e tente novamente.', {
                                height:230,
                                width: 330,
                                btnCancelEnabled: false
                            }).open();
		}
	});
}