jQuery('document').ready( function(){
    jQuery('#addNewField').on('show.bs.modal', function (event) {
        var button = jQuery(event.relatedTarget);
        
        var label       = button.data('label');
        var field       = button.data('field');
        var type        = button.data('type');
        var required    = button.data('required');

        if( field ) {
            jQuery('#field_label').val(label);
            jQuery('#field_name').val(field);
        }
    });
});
