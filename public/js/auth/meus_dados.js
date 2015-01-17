$(function(){

	$('#input-cep').mask('99999-999');

	$(".btn-verificar-cep").click(function(event)
	{
		event.preventDefault();

		var cep = $('#input-cep').val().replace(/[^0-9]/g, '');

		$.ajax({
			url: 'http://cep.correiocontrol.com.br/' + cep + '.json',
			type: 'get',
			dataType: 'json',
			success: function (response) {

				console.warn(response)

				$("#form-meus-dados").objectForm({
					uf		: response.uf,
					rua		: response.logradouro,
					cidade 	: response.localidade
				});
			}
		});
		
	});
});