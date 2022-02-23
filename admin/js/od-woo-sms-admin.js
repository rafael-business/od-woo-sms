(function( $ ) {
	'use strict';

	$(function() {
		
		$(document).on('change', '#select-to', function(e) {

			let val = e.target.options[e.target.selectedIndex].value;

			if (val == 'customized') {

				$('#od_woo_sms_to').show();
			} else if (val == '') {

				$('#od_woo_sms_to').val('');
				$('#od_woo_sms_to').hide();
			} else {

				$('#od_woo_sms_to').val(val);
				$('#od_woo_sms_to').hide();
			}
		})
	});

})( jQuery );
