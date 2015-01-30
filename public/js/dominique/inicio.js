$(function(){
	if (annyang) {
		var commands = {
			'*term': captureVoice
		};
		annyang.addCommands(commands);
		annyang.debug();
    	annyang.setLanguage('pt-BR');
		annyang.start();
	} else {
		alert('Não estou escutando!');
	}
	talk();
});
function captureVoice(term) {
	$('.frase').val(term);
	$('#talk').submit();
}
function talk()
{
	var text = $('#text').html();
	
	var textParts = str_delimit(text, 90);
	requestTalk(textParts, 0);

}
function requestTalk(text, handle)
{
	if (typeof(text[handle]) != 'undefined') {
		$.ajax({
			url: '/dominique/speak-new',
			type: 'POST',
			dataType: 'json',
			data: {
				text: text[handle]
			},
			success: function(response)
			{
				if ($('#speak').length) $('#speak').remove();
				$('body').append('<audio id="speak" autoplay><source src="'+response.url+'"></audio>');
				document.getElementById('speak').onended = function(){
					requestTalk(text, handle + 1);
				};
			},
			error: function()
			{
				console.error('Problemas na conexão!');
			}
		});
	} else {
		console.log('não tem');
	}
}
function str_delimit(text, limitParts)
{
	var arrayText = [];
		
	var splited = text.split(' ');

	if (text.length <= limitParts) {
		arrayText.push(text);
	} else {
		var tempText = '';
		for (key in splited) {
			if (tempText.length <= limitParts) {
				tempText += splited[key] + ' ';
			} else {
				handleNumber = 0;
				arrayText.push(tempText);
				tempText = splited[key] + ' ';

				if (splited.length <= parseInt(key) + 1) {
					arrayText.push(tempText);
				}
			}
		}
	}

	return arrayText;
}