(function($) {
	$(document).ready(function() {
		$('input[name=\"GeonetworkBaseURL\"]').livequery('change', function(){
			if($('input[name=\"GeonetworkBaseURL\"]').val().length > 1 && $('input[name=\"GeonetworkBaseURL\"]').val().substr(-1,1) != '/'){
				$('input[name=\"GeonetworkBaseURL\"]').val(jQuery.trim($('input[name=\"GeonetworkBaseURL\"]').val()) + '/');
			}
		});
	});
})(jQuery);