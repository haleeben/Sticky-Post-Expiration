(function($){

    /**
     * The code in the file handles the implementation of the Sticky Expiration input datepicker.
     *
     * Restores the original value if the user clicks the cancel button.
     * Clears the input value if the user clicks the clear button
     * Clears the input value if the user clears the datepicker value and clicks OK
     *
     */

    var $speExpiration = $( '#spe-expiration' );
    var $speLabel = $( '#spe-expiration-label' );
    var $speWrap = $( '#spe-expiration-wrap' );

    /**store the current value to use later if the user clicks the cancel button **/
    var previous = $speExpiration.val();


    /**
     * Set the datepicker format e.g 01 Jan, 2000
     */
    $speExpiration.datepicker({
        dateFormat: 'dd M, yy'
    });


    /**
     * Set the initial visibility of the Sticky Expiration area
     */
    if ( $('#sticky').is( ':checked' ) ){

        $speWrap.addClass( 'active' );
        $speWrap.slideDown();

    } else {

        $speWrap.removeClass('active');
        $speWrap.hide();
    }


    /**
     * Toggle the Sticky Expires area if the Sticky checkbox is changed
     */
    $('#sticky').change( function(){

        $speWrap.toggleClass( 'active' );

        if( $speWrap.hasClass( 'active' ) === false ) {

            $speWrap.slideUp();
            $speExpiration.val('');
            $speLabel.text( "Never" );

        } else if( $speWrap.hasClass( 'active' ) === true ) {

            $speWrap.slideDown();
        }
    });


    /**
     * If the user clicks the clear button, then clear the values including the previous
     */
    $('.spe-hide-expiration.clear').click( function( e ) {

        $speExpiration.val('');
        $speLabel.text( "Never" );
        previous = $speExpiration.val();

    });


    /**
     * If the Edit or Cancel or OK buttons are clicked do this
     **/
    $('#spe-edit-expiration, .spe-hide-expiration').click( function( e ) {

        e.preventDefault();

        // Set the current input value to a variable
        var date = $speExpiration.val();

        // If the user clicks the cancel button restore the previous value
        if( $( this ).hasClass( 'cancel' ) ) {

            $speExpiration.val( previous );
            $speLabel.text( $speExpiration.val() );

        } else if( date ) { // if there is a value, set the label and previous values

            $speLabel.text( $speExpiration.val() );
            previous = $speExpiration.val();

        } else { // if there isn't a value and the user didn't the cancel button

            $speExpiration.val('');
            $speLabel.text( "Never" );
        }

        $('#spe-expiration-field').slideToggle(); // Show the input field
    });

})(jQuery);