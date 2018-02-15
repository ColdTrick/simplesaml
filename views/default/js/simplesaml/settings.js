define(function(require) {
	
	var $ = require('jquery');
	
	$(document).on('change', '#simplesaml-settings-sources input[type="checkbox"][name$="force_authentication]"]', function() {
		
		if ($(this).is(':checked')) {
			// uncheck all others
			$('#simplesaml-settings-sources input[type="checkbox"][name$="force_authentication]"]').not($(this)).prop('checked', false);
		}
	});
	
});
