(function ($) {
	$(document).ready(function () {

		/* ------------
		 * Metadata Catalog Stuff
		 * ----------- */

		/* --------------------------------------------------------------------------------------------
		 * Smart Fields
		 * ------------------------------------------------------------------------------------------- */
		//expand/colapse extra location fields
		$('#coordinates').hide();

		// initiate jquery-ui datepicker (manually)
		$('#RegisterMetadataForm_MetadataEntryForm_MDDateTime1').datepicker();
		$('#RegisterMetadataForm_MetadataEntryForm_MDDateTime1').datepicker("option","dateFormat", "dd/mm/yy");
		$('#RegisterMetadataForm_MetadataEntryForm_MDDateTime2').datepicker();
		$('#RegisterMetadataForm_MetadataEntryForm_MDDateTime2').datepicker("option","dateFormat", "dd/mm/yy");
		$('#RegisterMetadataForm_MetadataEntryForm_MDDateTime3').datepicker();
		$('#RegisterMetadataForm_MetadataEntryForm_MDDateTime3').datepicker("option","dateFormat", "dd/mm/yy");

		//if you type something into the coordinates field changes the combo to "custom location"
		$('#RegisterMetadataForm_MetadataEntryForm_MDWestBound,\n' +
		  '#RegisterMetadataForm_MetadataEntryForm_MDEastBound,\n' +
		  '#RegisterMetadataForm_MetadataEntryForm_MDSouthBound,\n' +
		  '#RegisterMetadataForm_MetadataEntryForm_MDNorthBound').keypress(function (e) {
			$("#RegisterMetadataForm_MetadataEntryForm_Places").val("0;0;0;0");
			var charCode = (e.which) ? e.which : e.keyCode;
			if (charCode > 31 && (charCode < 45 || charCode > 57)) {
				return false;
			}
		})

		//when you select a plece fade in the fields to tell the user the data has changed and populates the description field
		$("#RegisterMetadataForm_MetadataEntryForm_Places").change(function () {
			var str = $("option:selected", this).val();

			var txt = jQuery.trim($("option:selected", this).text());

			if (str == "custom") {
				$('#coordinates').slideDown('fast');
				$("#Regions, #TAs, #OffshoreIslands, #Dependencies").slideUp('fast');

			} else if (str != "0;0;0;0") {

				if (str == "166.0000;-175.5000;-47.8333;-34.0000") {
					fillRegions();
					$("#Regions, #TAs").slideDown('fast');
					$("#Regions select, #TAs select").css('visibility', 'visible');
					$("#OffshoreIslands, #Dependencies, #coordinates").slideUp('fast');
				} else if (str == "166.4833;-177.8333;-55.9500;-29.2167") {
					fillRegions();
					$("#OffshoreIslands").slideDown('fast');
					$("#OffshoreIslands select").css('visibility', 'visible');
					$("#Regions, #TAs, #Dependencies, #coordinates").slideUp('fast');
				} else if (str == "165.8333;-177.8333;-29.2166;-5.9500") {
					fillRegions();
					$("#Dependencies").slideDown('fast');
					$("#Dependencies select").css('visibility', 'visible');
					$("#Regions, #TAs, #OffshoreIslands, #coordinates").slideUp('fast');
				} else {
					$("#Regions, #TAs, #OffshoreIslands, #Dependencies, #coordinates").slideUp('fast');
					// $('#RegisterMetadataForm_MetadataEntryForm_MDGeographicDiscription').val(txt);
				}
				// this and above removed because we don't allow modification of standard locations anymore
				// populateCoordinates(str);
			}
		})

		function fillRegions() {
			var tx1 = jQuery.trim($("#RegisterMetadataForm_MetadataEntryForm_Places option:selected").text());
			var tx2 = jQuery.trim($("#RegisterMetadataForm_MetadataEntryForm_Regions option:selected").text());
			var tx3 = jQuery.trim($("#RegisterMetadataForm_MetadataEntryForm_TAs option:selected").text());
			var tx4 = jQuery.trim($("#RegisterMetadataForm_MetadataEntryForm_OffshoreIslands option:selected").text());
			var tx5 = jQuery.trim($("#RegisterMetadataForm_MetadataEntryForm_Dependencies option:selected").text());
			if (tx1 == "New Zealand Land") {
				if (tx2 == "(anywhere)") {
					$('#RegisterMetadataForm_MetadataEntryForm_MDGeographicDiscription').val(tx1);
				} else if (tx3 == "(anywhere)") {
					$('#RegisterMetadataForm_MetadataEntryForm_MDGeographicDiscription').val(tx1 + ", " + tx2);
				} else {
					$('#RegisterMetadataForm_MetadataEntryForm_MDGeographicDiscription').val(tx1 + ", " + tx2 + ", " + tx3);
				}
			} else if (tx1 == "New Zealand's offshore islands") {
				$('#RegisterMetadataForm_MetadataEntryForm_MDGeographicDiscription').val(tx1 + ", " + tx4);
			} else if (tx1 == "New Zealand Dependencies in the South West Pacific") {
				$('#RegisterMetadataForm_MetadataEntryForm_MDGeographicDiscription').val(tx1 + ", " + tx5);
			}
		}

		function populateCoordinates(str) {
			var coord = str.split(";");
			$('#RegisterMetadataForm_MetadataEntryForm_MDWestBound').val(coord[0]).hide().fadeIn();
			$('#RegisterMetadataForm_MetadataEntryForm_MDEastBound').val(coord[1]).hide().fadeIn();
			$('#RegisterMetadataForm_MetadataEntryForm_MDSouthBound').val(coord[2]).hide().fadeIn();
			$('#RegisterMetadataForm_MetadataEntryForm_MDNorthBound').val(coord[3]).hide().fadeIn();
		}

		// add location details to the description field if in new
		$("#RegisterMetadataForm_MetadataEntryForm_TAs, #RegisterMetadataForm_MetadataEntryForm_OffshoreIslands, #RegisterMetadataForm_MetadataEntryForm_Dependencies").change(function () {
			var coordinates = $("option:selected", this).val();
			populateCoordinates(coordinates);
			fillRegions();
		})


		// Populates TAs using ajax
		$("#RegisterMetadataForm_MetadataEntryForm_Regions").change(function () {
			var coordinates = $("#RegisterMetadataForm_MetadataEntryForm_Regions option:selected").val();
			var regionName = jQuery.trim($("#RegisterMetadataForm_MetadataEntryForm_Regions option:selected").text());

			if (coordinates == ";;;") {
				coordinates = $("#RegisterMetadataForm_MetadataEntryForm_Places option:selected").val();
			}
			var theAddress = location.href;
			if (theAddress.substr(-1, 1) != "/") {
				$("#RegisterMetadataForm_MetadataEntryForm_TAs").empty().load(location.href + "/getTLAfor/" + escape(regionName) + "/", null, fillRegions);
			} else {
				$("#RegisterMetadataForm_MetadataEntryForm_TAs").empty().load(location.href + "getTLAfor/" + escape(regionName) + "/", null, fillRegions);
			}

			populateCoordinates(coordinates);
		})


		// Fill the description field with TA's
		$("#RegisterMetadataForm_MetadataEntryForm_TAs").change(function () {
			var coordinates = $("#RegisterMetadataForm_MetadataEntryForm_TAs option:selected").val();

			if (coordinates == ";;;") {
				coordinates = $("#RegisterMetadataForm_MetadataEntryForm_Regions option:selected").val();

			}
			populateCoordinates(coordinates);
			fillRegions();
		})


		// Hide File format and add more
		$('.fileFormat').hide();
		// $('.addScope').hide();
		$('.addScopeType').hide();


		$(".ResourceFormatsList select").change(function () {
			var strFile = $("option:selected", this).val();
			var fileParts = strFile.split("|");
			if (fileParts[0] == 'Other') {
				$(this).parent().find('.fileFormat input').val('');
				$(this).parent().find('.fileFormat').show('fast');
			} else {
				$(this).parent().find('.fileFormat input').val(fileParts[0]);
				$(this).parent().find('.fileFormat').hide('fast');
			}

		});

		$('#RegisterMetadataForm_MetadataEntryForm_Places').append('<option value="custom">Custom location - enter coordinates below</option>');

		$("#RegisterMetadataForm_MetadataEntryForm_Places").trigger('change');

		// Scope type
		$('#scopeType').toggle();

		$('#showScope').click(function () {
			$('#scopeType').toggle('fast', function () {
				$('#scopeType select').css('visibility', 'visible'); // IE 8 fix
				if ($('#scopeType').is(':visible')) {
					$('#showScope').removeClass('expand').addClass('collapse').text("I don't know these details");
				} else {
					$('#showScope').removeClass('collapse').addClass('expand').text("If you're describing a geospatial resource, please add these details to be ANZLIC compliant");
				}
			});
		});


		/* --------------------------------------------------------------------------------------------
		 * Validation
		 * ------------------------------------------------------------------------------------------- */

		function isValidEmail(str) {
			var is_email = /^([a-zA-Z0-9_+\.\-]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
			return is_email.test(str);
		}

		function isDate(str) {
			if (str.length > 0) {
				var is_Date = /^[0-9]{2}[\/ ]?[0-9]{2}[\/ ]?[0-9]{4}$/;
				return is_Date.test(str);
			} else {
				return false;
			}
		}

		function isPhone(str) {
			if (str.length > 0) {
				var is_Phone = /^[\d\-\(\)]{6,20}$/;
				return is_Phone.test(str);
			} else {
				return true;
			}
		}

		function invalidateForm() {
			if (!$('#RegisterMetadataForm_MetadataEntryForm').hasClass('validationError')) {
				$('#RegisterMetadataForm_MetadataEntryForm').addClass('invalid');
			}
		}

		function validateForm() {
			if ($('#RegisterMetadataForm_MetadataEntryForm').hasClass('validationError')) {
				$('#RegisterMetadataForm_MetadataEntryForm').removeClass('invalid');
			}
		}

		function verifyRequired(field) {
			if (field.val() == "") {
				if (!field.parent().hasClass('validationError')) {
					field.parent().append("<span class='error'>This field can't be empty</span>").addClass('validationError');
					invalidateForm();
				}
			} else {
				field.parent().removeClass('validationError');
				field.parent().find('.error').remove();
				validateForm();
				cleanErrors();
			}
		}

		// we need an special function for contact details, since they are not mandatory, but at least 1 of them has to be entered
		function verifyContactRequired() {
			if ($('#RegisterMetadataForm_MetadataEntryForm_MDIndividualName').val() == '' && $('#RegisterMetadataForm_MetadataEntryForm_MDOrganisationName').val() == '' && $('#RegisterMetadataForm_MetadataEntryForm_MDPositionName').val() == '') {
				if (!$('#contactRequiredFields').hasClass('error')) {
					$('#contactRequiredFields').addClass('error');
					$('#contactRequiredFields').parent().addClass('validationError');
					invalidateForm();
				}
			} else {
				$('#contactRequiredFields').removeClass('error');
				$('#contactRequiredFields').parent().removeClass('validationError');
				validateForm();
				cleanErrors();
			}
		}

		$("#RegisterMetadataForm_MetadataEntryForm_MDIndividualName, #RegisterMetadataForm_MetadataEntryForm_MDOrganisationName, #RegisterMetadataForm_MetadataEntryForm_MDPositionName").blur(function () {
			verifyContactRequired();
		});

		function verifyEmail(field) {
			var thisText = field.val();
			var is_email = isValidEmail(thisText);
			if (!is_email) {
				if (field.parent().hasClass('validationError')) {
					field.parent().removeClass('validationError');
					field.parent().find('.error').remove();
				}
				field.parent().append("<span class='error'>Email in an incorrect format</span>").addClass('validationError');
				invalidateForm();
			} else {
				field.parent().removeClass('validationError');
				field.parent().find('.error').remove();
				validateForm();
				cleanErrors();
			}
		}

		// because we have 3 possible dates of different types, we need to be sure there is only one of each
		function verifyDates() {
			var dateItems = $('div.date select:visible');
			var selectedTypes = [];
			if ($('#dateUniqueMessage').parent().hasClass('validationError')) {
				$('#dateUniqueMessage').parent().removeClass('validationError');
				$('#dateUniqueMessage').removeClass('.error');
			}

			dateItems.each(function () {
				var type = $(this).val();
				if ($.inArray(type, selectedTypes) !== -1) {
					if (!$('#dateUniqueMessage').hasClass('error')) {
						$('#dateUniqueMessage').addClass('error');
						$('#dateUniqueMessage').parent().addClass('validationError');
						invalidateForm();
					}
				} else {
					selectedTypes.push(type);
				}

			});
		}

		function verifyDate(field) {
			var thisText = field.val();
			var is_date = isDate(thisText);
			if (!is_date) {
				if (field.parent().parent().hasClass('validationError')) {
					field.parent().parent().removeClass('validationError');
					field.parent().parent().find('.error').remove();
				}
				field.parent().parent().append("<span class='error'>Date in an incorrect format</span>").addClass('validationError');
				invalidateForm();
			} else {
				field.parent().parent().removeClass('validationError');
				field.parent().parent().find('.error').remove();
				validateForm();
				cleanErrors();
			}
		}

		/*function verifyPhone(field){
		 var thisText = field.val();
		 var is_phone = isPhone(thisText);
		 if(!is_phone){
		 if(field.parent().hasClass('validationError')){
		 field.parent().removeClass('validationError');
		 field.parent().find('.error').remove();
		 }
		 field.parent().append("<span class='error'>Phone in an incorrect format</span>").addClass('validationError');
		 invalidateForm();
		 }else{
		 field.parent().removeClass('validationError');
		 field.parent().find('.error').remove();
		 validateForm();
		 cleanErrors();
		 }
		 }*/
		function cleanErrors() {
			if ($('.validationError').size() < 1) {
				$('.formInvalid').remove();
			}
		}

		//required fields
		$("#RegisterMetadataForm_MetadataEntryForm_MDAbstract, #RegisterMetadataForm_MetadataEntryForm_MDTitle,  #RegisterMetadataForm_MetadataEntryForm_MDDateTime1").keyup(function () {
			if ($('.formInvalid').size() > 0) verifyRequired($(this));
			cleanErrors();
		});

		// email validation
		// $("#RegisterMetadataForm_MetadataEntryForm_MDElectronicMailAddress").keyup(function () {
		// 	if($('.formInvalid').size() > 0) verifyEmail($(this));
		// 	cleanErrors();
		// });
		//
		$("#RegisterMetadataForm_MetadataEntryForm_MDElectronicMailAddress").blur(function () {
			if ($('.formInvalid').size() > 0) verifyEmail($(this));
			cleanErrors();
		});

		$("#RegisterMetadataForm_MetadataEntryForm_MDDateTime1,#RegisterMetadataForm_MetadataEntryForm_MDDateTime2, #RegisterMetadataForm_MetadataEntryForm_MDDateTime3 ").keyup(function () {
			if ($('.formInvalid').size() > 0) verifyDate($(this));
			cleanErrors();
		});

		// $("#RegisterMetadataForm_MetadataEntryForm_MDVoice").keyup(function () {
		// 	if($('.formInvalid').size() > 0) verifyPhone($(this));
		// 	cleanErrors();
		// });

		$("#RegisterMetadataForm_MetadataEntryForm").submit(function () {

			serializeMultiples();
			verifyRequired($("#RegisterMetadataForm_MetadataEntryForm_MDTitle"));
			verifyRequired($("#RegisterMetadataForm_MetadataEntryForm_MDAbstract"));
//		verifyRequired($("#RegisterMetadataForm_MetadataEntryForm_MDTopicCategory"));
			verifyDate($("#RegisterMetadataForm_MetadataEntryForm_MDDateTime1"));
			verifyEmail($("#RegisterMetadataForm_MetadataEntryForm_MDElectronicMailAddress"));
			verifyContactRequired();
			verifyDates();
			//verifyPhone($("#RegisterMetadataForm_MetadataEntryForm_MDVoice"));
			if ($('.validationError').size() > 0) {
				if ($('.formInvalid').size() < 1)
					$('.Actions').append("<span class='formInvalid'>Some fields need your attention before you can register your data.</span>");
				return false;

			} else {
				$('select,input').removeAttr('disabled');
				return true;
			}
		});

		/**
		 *  Serialise the form (serialises the dynamic added elements into a dedicated text field.
		 */
		function serializeMultiples() {
			var phnums = $('input[name=MDVoice]').map(function () {
				return this.value;
			}).get().join('||');

			var emails = $('input[name=MDElectronicMailAddress]').map(function () {
				return this.value;
			}).get().join('||');

			var URLs = $('input[name=CIOnlineLinkage]').map(function () {
				return this.value;
			}).get().join('||');

			var scopes = $('select[name=MDHierarchyLevel]').map(function () {
				return this.value;
			}).get().join('||');

			var scopetypes = $('input[name=MDHierarchyLevelName]').map(function () {
				return this.value;
			}).get().join('||');

			$('input[name=MDVoiceData]').val(phnums);
			$('input[name=MDElectronicMailAddressData]').val(emails);
			$('input[name=CIOnlineLinkageData]').val(URLs);
			$('input[name=MDHierarchyLevelData]').val(scopes);
			$('input[name=MDHierarchyLevelNameData]').val(scopetypes);
		}

		function elementEnable(el, className, type) {
			if (!type) type = "select";
			if ($(el).hasClass(className)) {
				$('div.' + className + ':visible:last button').show();
				$('div.' + className + ':visible:last img').show();
				$('div.' + className + ':visible:last ' + type).removeAttr('disabled');
			}
		}

		function removeFields(e) {
			e.preventDefault();
			if ($(this).parent().attr('id').match('MDResourceFormatVersion')) {
				var parentDiv = $(this).parent().parent();

				parentDiv.hide('fast');
				parentDiv.find('input').val('');
				parentDiv.find("select option[value='']").attr('selected', 'selected');

				$('a.addFormat').show();
			} else {
				$(this).parent().find('input').val('');
				$(this).parent().hide('fast', function () {
					if ($('div.date:hidden').length > 0) {
						$('a.addDate').show();
					}
					elementEnable(this, 'date');
					elementEnable(this, 'date', 'img');
					elementEnable(this, 'date', 'input');
					elementEnable(this, 'MDHierarchyLevel');
					elementEnable(this, 'MDHierarchyLevelName', 'input');
				});
			}
		}

		// show additional date
		$('a.addDate').click(function (e) {
			e.preventDefault();
			// validate the existing date first
			if (!isDate($('div.date:visible:last input').val())) {
				$('div.date:visible:last').find('.error').remove();
				$('div.date:visible:last').append("<span class='error'>Date in an incorrect format</span>").addClass('validationError');
				invalidateForm();
				return;

			} else {

				//if ok, stop people from selecting a used date type
				$('div.date:visible:last').removeClass('validationError');
				$('div.date:visible:last').find('.error').remove();
				validateForm();
				cleanErrors();

				// disable current date control and add new date-control
				$('div.date:visible:last select').attr('disabled', 'true');
				$('div.date:visible:last input').attr('disabled', 'true');
				$('div.date:visible:last img').hide();
				// $('div.date:visible:last button').hide();

				var usedDateTypes = $('div.date:visible select').map(function () {
					return this.value;
				});

				$('div.date:hidden:first select').children().each(function () {
					if (jQuery.inArray(this.value, usedDateTypes) != -1) {
						$(this).remove();
					}
				});


				$('div.date:hidden:first').show('fast', function () {
					$('div.date select').css('visibility', 'visible');
				});
				if ($('div.date:hidden').length == 0) $('a.addDate').hide();
			}
		});

		// show additional format
		$('a.addFormat').click(function (e) {
			e.preventDefault();
			$('div.ResourceFormatsList:hidden:first').show('fast', function () {
				$('div.ResourceFormatsList select').css('visibility', 'visible');
			});
			if ($('div.ResourceFormatsList:hidden:hidden').length == 0) {
				$('a.addFormat').hide();
			}
		});

		$('button.remove').live('click', removeFields);


		/* duplicate fields */

		function cloneField(field) {
			var newField = field.clone();
			newField.find('input').each(function () {
				this.id = "";
				$(this).removeAttr('disabled');
				this.value = "";
			});
			newField.find('select').each(function () {
				this.id = "";
				$(this).removeAttr('disabled');
			});
			newField.find('span').remove();
			newField.find('label').html('');
			newField.find('input,select').after('<button class="remove">X</button>');
			newField.hide();

			var input = newField.find('input, select');
			$('input[name=' + input.attr('name') + ']:last, select[name=' + input.attr('name') + ']:last').parent().after(newField);
			newField.show('fast');
		}

		function bindMDHierarchyChange() {
			$('.MDHierarchyLevel select').change(function (e) {
				// if ($(this).val()!="") {
				// $('a.addScope').show('fast');
				// } else {
				// 	$('a.addScope').hide('fast');
				// }
			});
		}

		bindMDHierarchyChange();

		$('.MDHierarchyLevelName input').live('keyup', function (e) {
			if ($(this).val() != "") {
				$('a.addScopeType').show('fast');
			} else {
				$('a.addScopeType').hide('fast');
			}
		});

		/**
		 * Handle Add-elements events, such as, add scope-code, add url etc.
		 */
		$('a.addScope').click(function (e) {
			e.preventDefault();

			$('div.MDHierarchyLevel:visible:last select').attr('disabled', 'true');
			$('div.MDHierarchyLevel:visible:last button').hide();

			var usedOptions = $('div.MDHierarchyLevel:visible select').map(function () {
				return this.value;
			});

			cloneField($('#MDHierarchyLevel'));
			$('div.MDHierarchyLevel:last select').children().each(function () {
				if (jQuery.inArray(this.value, usedOptions) != -1) $(this).remove();
			});
			// $('a.addScope').hide();
			bindMDHierarchyChange();
		});

		$('a.addScopeType').click(function (e) {
			e.preventDefault();

			$('div.MDHierarchyLevelName:visible:last input').attr('disabled', 'true');
			$('div.MDHierarchyLevelName:visible:last button').hide();

			cloneField($('#MDHierarchyLevelName'));

			$('a.addScopeType').hide();
		});

		$('a.addURL').click(function (e) {
			e.preventDefault();
			cloneField($('#CIOnlineLinkage'));
		});

		$('a.addVoice').click(function (e) {
			e.preventDefault();
			cloneField($('#MDVoice'));
		});

		$('a.addEmail').click(function (e) {
			e.preventDefault();
			cloneField($('#MDElectronicMailAddress'));
		});

	});
})(jQuery);
