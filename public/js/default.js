$(document).ready(function(){
    var $btnMenuHeader = $('#btn-menu-header');
    var $rightTopMenu = $('#right-top-menu');
    $btnMenuHeader.on('click', function(){
        if ($rightTopMenu.data('open')) {
            $rightTopMenu.animate({
                width: '0px'
            }, function(){
                $(this).hide().data('open', false);
            });
        } else {
            $rightTopMenu.animate({
                width: '180px'
            }).show().data('open', true);
        }
    });
});