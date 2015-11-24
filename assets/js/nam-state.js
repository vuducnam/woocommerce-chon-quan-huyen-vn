/*global wc_country_select_params */
/**
* Edit by Comfythemes
* @since 1.3
*/
jQuery( function( $ ) {

	// wc_country_select_params is required to continue, ensure the object exists
	if ( typeof nam_state_params === 'undefined' ) {
		return false;
	}
	var state = nam_state_params.state;

	$( document.body ).on( 'change', 'select.country_to_state, input.country_to_state', function() {

		var country     = $( this ).val(),
			$statebox   = $('#billing_state' );
			$('#billing_distric_vn_field').hide();
		
		if( $( this ).attr('id') == 'billing_country' && country == 'VN' ){

			var options = '';

			for( var index in state ) {
				if ( state.hasOwnProperty( index ) ) {
					options = options + '<option data-key="' + index + '" value="' + state[ index ].name + '">' + state[ index ].name + '</option>';
				}
			}
			$('#billing_state_field').show();
			if ( $('#billing_state').is( 'input' ) ) {
				// Change for select
				$('#billing_state').replaceWith( '<select name="billing_state" id="billing_state" class="state_select" placeholder="' + $('#billing_state_field label').text() + '"></select>' );
			}
			$('#billing_state').html( '<option value="">' + wc_country_select_params.i18n_select_state_text + '</option>' + options );
			$('#billing_state').val( country ).change();
			$( document.body ).trigger( 'country_to_state_changed', [ 'VN' ] );

		}

	});

	$( document.body ).on( 'change', 'select.state_select', function() {

		var value     	= $(this).find(':selected').data('key'),
			city 		= $(this).val(), 
			$statebox   = $('#billing_state' );
			if( $('#billing_distric_vn').length == 0 ){
				$('#billing_state_field').after( '<p class="form-row form-row address-field" id="billing_distric_vn_field" ><label for="billing_distric_vn" class="">District</label><select name="billing_distric_vn" id="billing_distric_vn" class="state_select" placeholder="Select district"></select></p>' );
			}
		
		if( $( this ).attr('id') == 'billing_state' ){

			if( city != null ){

				var districts = state[value].districts,
				options = '';

				for( var index in districts ) {
					if ( districts.hasOwnProperty( index ) ) {
						options = options + '<option data-key="' + index + '" value="' + districts[ index ] + '">' + districts[ index ] + '</option>';
					}
				}

				$('#billing_distric_vn').html( '<option value="">Select district</option>' + options );
				$('#billing_distric_vn_field').show();
			}

		}

	});

	$(function() {
		$( ':input.country_to_state' ).change();
	});

});
