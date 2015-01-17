function wmDialog(text, options)
{
    options = options || {};
    var $modal = $('#wm-alert').closest('.wm-modal').clone();
    var $body = $modal.find('.wm-modal-body');
    var $container = $modal.find('#wm-alert');
    var $cancel = $modal.find('.wm-modal-btn-cancel');
    var $confirm = $modal.find('.wm-modal-btn-confirm');
    var $close = $modal.find('.wm-modal-close');

    this.height = null;
    this.width = null;
    this.isHTML = false;
    this.btnCancelEnabled = true;
    this.btnOkEnabled = true;
    this.title = 'Alerta';

    this.btnCancel;
    this.btnConfirm;


    $.extend(this, options)

    $container.find('.wm-modal-title').html(this.title);
    $modal.find('.wm-modal-box').draggable({
        handle: '.wm-modal-title'
    });

    $confirm.click(function(){
        if ($.isFunction(options.onConfirm)) {
            options.onConfirm($modal);
        } else {
            $modal.fadeOut();
        }
        
    });

    $cancel.add($close).click(function(){
        if ($.isFunction(this.onCancel)) {
            this.onCancel(this);
        } else {
            $modal.fadeOut();
        }
    });


    this.open = function(callback) {

        if (!this.btnCancelEnabled) {
            $cancel.remove();
        }

        if (!this.btnOkEnabled) {
            $confirm.remove();
        }

        if (!this.isHTML) {
            $body.text(text);
        } else {
            $body.html(text);
        }

        $body.fadeIn(500, callback);


        $container.height(this.height).width(this.width);

        if ($.isFunction(this.btnConfirm)) {
            options.onConfirm = this.btnConfirm;
        }

        if ($.isFunction(this.btnCancel)) {
            this.btnCancel($cancel);
        }

        $modal.appendTo($('body')).fadeIn();
    }

    this.close = function(){
        $modal.fadeOut(500);
    }

}