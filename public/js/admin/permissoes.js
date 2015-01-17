$(function(){
	var $permissoesSearch = $('#permissao-search');
	var $boxSearchResult = $('.box-search-action');

	var timeout;
	$permissoesSearch.on('keyup', function(){
		clearTimeout(timeout);
		var thisContext = $(this);
		timeout = setTimeout(function(){
			$.ajax({
				url: '/admin/search-permission',
				type: 'GET',
				dataType: 'json',
				data: {
					search: thisContext.val()
				},
				beforeSend: function()
				{
					$boxSearchResult.fadeIn();
				},
				success: function(response)
				{
					var html = '';
					for (i in response) {
						html += "<div class='item' data-id='" + response[i].id + "'>" + response[i].name + " - " + response[i].action + "</div>";
					}
					$boxSearchResult.html(html);
					var $itemSearchResult = $('.box-search-action .item');
					$itemSearchResult.on('click', function(){
						$boxSearchResult.fadeOut();
						thisContext.val($(this).html());
						$('#permissao-id').val($(this).data('id'));
					});
				},
				error: function()
				{
					console.error('Problemas na conexão!');
				}
			});
		}, 500);
	});
});