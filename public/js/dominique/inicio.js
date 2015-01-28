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
		alert('Estou muda!');
	}
});
function captureVoice(term) {
	$('.frase').val(term);
	$('#talk').submit();
}