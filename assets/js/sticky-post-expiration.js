(function($){

    var previous = $('#spe-expiration').val();

    $('#spe-expiration').datepicker({
        dateFormat: 'yy-mm-dd'
    });

    $('#spe-edit-expiration, .spe-hide-expiration').click(function(e) {

        e.preventDefault();

        var date = $('#spe-expiration').val();

        if( $(this).hasClass('cancel') ) {

            $('#spe-expiration').val( previous );

        } else if( date ) {

            $('#spe-expiration-label').text( $('#spe-expiration').val() );

        }

        $('#spe-expiration-field').slideToggle();

    });

})(jQuery);