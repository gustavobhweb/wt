/*! 
* DevExpress jQuery Object Form
* Version: 1.0
* Build date: Nov 25, 2014
*
* Copyright (c) Wallace Maxters
*/
(function ($) {

    $.fn.objectForm = function (object) {
        
        var object = object || {};    

        if ($.isEmptyObject(object)) {

            
            this.serializeArray().forEach(function (data) {
                object[data.name] = data.value;
            });

            return object;

        } else {

            var self = this;

            $.each(object, function (key, value) {
                self.find('[name='+key+']').val(value);
            });

            delete self;
            
            return this;
        }
        
    }

})( jQuery );