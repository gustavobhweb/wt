$(document).ready(function(){

    $('.minha-conta').click(function(){
        var $linkLogout = $('.sair-link');
        if ($linkLogout.is(':visible')) {
            $linkLogout.fadeOut();
        } else {
            $linkLogout.fadeIn();
        }
    });

    // # Modal Flash # //

    $('#modal-flash').find('.cancel-flash-modal').click(function(e){
    	e.preventDefault();
    	$(this).closest('#modal-flash').fadeOut();
    });


    if ($.fn.draggable) {
    	$(".wm-modal-box").draggable({
    		handle: '.wm-modal-title'
    	});
    }
});