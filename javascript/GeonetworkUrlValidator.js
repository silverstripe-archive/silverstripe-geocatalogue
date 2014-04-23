(function($) {
	$.entwine('ss', function($){

		$('input[name=\"GeonetworkBaseURL\"]').entwine({

			onchange: function() {
				if($('input[name=\"GeonetworkBaseURL\"]').val().length > 1 && $('input[name=\"GeonetworkBaseURL\"]').val().substr(-1,1) != '/'){
					$('input[name=\"GeonetworkBaseURL\"]').val(jQuery.trim($('input[name=\"GeonetworkBaseURL\"]').val()) + '/');
				}
			}
		});

		$('.geonetwork_load_groups').entwine({

			onclick: function() {

				$('.geonetwork_load_groups .ui-button-text')[0].innerHTML = 'Refreshing list of groups...';

				var url = this.data('url');
				var selectedID = this.data('selected');

				$.getJSON(url, function(data) {
					var items = [];
					$.each( data, function( key, val ) {
						if (key == selectedID) {
							items.push( "<option value='" + key + "' selected>" + val + "</option>" );

						} else {
							items.push( "<option value='" + key + "'>" + val + "</option>" );
						}
					});
					list = items.join( "" );

					$('#Form_EditForm_GeonetworkGroupID_dp option').remove();
					$('#Form_EditForm_GeonetworkGroupID_dp').append(list);
					$('#Form_EditForm_GeonetworkGroupID_dp').trigger('liszt:updated');

					$('.geonetwork_load_groups .ui-button-text')[0].innerHTML = 'Load and update list of groups';

				}).fail(function(data) {
					if (data.status == 401) {

					} else
					if (data.status == 404) {

					}
					$('.geonetwork_load_groups .ui-button-text')[0].innerHTML = 'Load and update list of groups';
				});
				return false;
			}
		});

		$('#Form_EditForm_GeonetworkGroupID_dp').entwine({
			onchange: function() {
				var index = $('#Form_EditForm_GeonetworkGroupID_dp').chosen().val();
				$('#Form_EditForm_GeonetworkGroupID')[0].value = index;
				$('#Form_EditForm_GeonetworkName')[0].value = $('#Form_EditForm_GeonetworkGroupID_dp option[value='+index+']').text();

			}
		});

		$('#Form_EditForm_AutoPublish').entwine({
			onmatch: function() {
				var checked = $(this).prop('checked');
				this.updatePrivilegeFieldSet(checked);
			},

			onclick: function() {
				var checked = $(this).prop('checked');
				this.updatePrivilegeFieldSet(checked);
			},

			updatePrivilegeFieldSet: function(status) {
				if (status === true) {
					$('#Form_EditForm_Privilege .checkbox').prop('disabled',0);
				} else {
					$('#Form_EditForm_Privilege .checkbox').prop('disabled',1);
				}
			}
		});

		$('#Form_EditForm_Username.changed').entwine({

			onmatch: function() {
				$('.geonetwork_load_groups').button("disable");
			},

			onunmatch: function() {
				if ($('#Form_EditForm_Password.changed').length == 0) {
					$('.geonetwork_load_groups').button("enable");
				}
			}
		});

		$('#Form_EditForm_Password.changed').entwine({

			onmatch: function() {
				$('.geonetwork_load_groups').button("disable");
			},

			onunmatch: function() {
				if ($('#Form_EditForm_Username.changed').length == 0) {
					$('.geonetwork_load_groups').button("enable");
				}
			}
		});

	});

})(jQuery);