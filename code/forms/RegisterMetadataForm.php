<?php
/**
 * @author Rainer Spittel (rainer at silverstripe dot com)
 * @package geocatalog
 * @subpackage forms
 */

/**
 * Standard RegisterMetadataForm
 *
 * This class implements the standard registration form, which contains a mandatory
 * text fields to register metadata in MCP format (derived ISO19139 format).
 */
class RegisterMetadataForm extends Form {

	function getRecaptchaField() {
		$field = null;

		if (Config::inst()->get('Catalogue', 'spamprotection_enabled') !== false) {
			$field = new RecaptchaField("Recaptcha","Please enter text");
			$field->jsOptions = array('theme' => 'clean');
			$field->addExtraClass("required");
		}

		return $field;
	}

	/**
	 * Initiate the standard Metadata catalogue search form. The 
	 * additional parameter $defaults defines the default values for the form.
	 * 
	 * @param Controller $controller The parent controller, necessary to create the appropriate form action tag.
	 * @param String $name The method on the controller that will return this form object.
	 * @param FieldSet $fields All of the fields in the form - a {@link FieldSet} of {@link FormField} objects.
	 * @param FieldSet $actions All of the action buttons in the form - a {@link FieldSet} of {@link FormAction} objects
	 * @param Validator $validator Override the default validator instance (Default: {@link RequiredFields})
	 */
	function __construct($controller, $name, FieldList $fields = null, FieldList $actions = null, $validator = null) {
		$recaptchaField = $this->getRecaptchaField();

		if(!$fields) {
			// Create fields
			
			//adding extra class for custom validation
			$title = new TextField('MDTitle',"TITLE"); 
			$title->addExtraClass("required");

			$fields = new FieldList(
				new CompositeField (
					$title,
					new TextareaField('MDAbstract'),
					new DateField('MDDateTime1','MDDateTime1',$this->MDDateTime1),
					new DropdownField('MDDateType1','DateType',MDCodeTypes::get_date_types(),""),	// drop down
					new DateField('MDDateTime2','MDDateTime2',$this->MDDateTime2),
					new DropdownField('MDDateType2','DateType',MDCodeTypes::get_date_types(),""),	// drop down
					new DateField('MDDateTime3','MDDateTime3',$this->MDDateTime3),
					new DropdownField('MDDateType3','DateType',MDCodeTypes::get_date_types(),""),	// drop down
					new ListboxField('MDTopicCategory','Category',MDCodeTypes::get_categories(),"",8,true)	// drop down
				),
				new CompositeField (
					new DropdownField('MDSpatialRepresentationType','Spatial Representation Type',MDCodeTypes::get_spatial_representation_type(),""),	// drop down				
					new TextField('MDGeographicDiscription','Geographic Description'),	// drop down				

					new TextField('MDWestBound'),  // double
					new TextField('MDEastBound'),  // double
					new TextField('MDSouthBound'), // double
					new TextField('MDNorthBound'), // double

					new DropdownField('ISOPlaces','ISOPlaces',MDCodeTypes::get_places(),"170;180;-52.57806;-32.41472"),	// drop down				
					new DropdownField('Places','Places',NewZealandPlaces::get_nzplaces(),"-141;160;-7;-90"),	// drop down				

					new DropdownField('OffshoreIslands','NZ Offshore islands',NewZealandPlaces::get_nzoffshoreislands(),""),					// drop down

					new DropdownField('Dependencies','NZ Dependencies in the South West Pacific',NewZealandPlaces::get_nzdependencies(),""),					// drop down

					new DropdownField('Regions','Regions',NewZealandPlaces::get_nzregions(),""),					// drop down
					new DropdownField('TAs','TAs',NewZealandPlaces::get_nzta(),"")									// drop down				
				),
				new CompositeField (
					new TextField('MDIndividualName'),
					new TextField('MDOrganisationName'),
					new TextField('MDPositionName'),
					new TextField('MDVoice'),
					new HiddenField('MDVoiceData'),
/*
			         new TextField('MDFacsimile'),
			         new TextField('MDDeliveryPoint'),
			         new TextField('MDCity'),
			         new TextField('MDAdministrativeArea'),
			         new TextField('MDPostalCode'),
			         new TextField('MDCountry'),
*/
					new EmailField('MDElectronicMailAddress'),
					new HiddenField('MDElectronicMailAddressData')
				),
				new CompositeField (
					new DropdownField('ResourceFormatsList1','ResourceFormatsList1',MDCodeTypes::get_resource_formats(),""),	// drop down				
					new TextField('MDResourceFormatName1','MDResourceFormatName1',""),	// drop down				
					new TextField('MDResourceFormatVersion1','MDResourceFormatVersion1',"")	// drop down				
        ),
				new CompositeField (
					new DropdownField('ResourceFormatsList2','ResourceFormatsList2',MDCodeTypes::get_resource_formats(),""),	// drop down				
					new TextField('MDResourceFormatName2','MDResourceFormatName2',""),	// drop down				
					new TextField('MDResourceFormatVersion2','MDResourceFormatVersion2',"")	// drop down				
        ),
				new CompositeField (
					new DropdownField('ResourceFormatsList3','ResourceFormatsList3',MDCodeTypes::get_resource_formats(),""),	// drop down				
					new TextField('MDResourceFormatName3','MDResourceFormatName3',""),	// drop down				
					new TextField('MDResourceFormatVersion3','MDResourceFormatVersion3',"")	// drop down				
				),
				new CompositeField (
					new DropdownField('ResourceFormatsList4','ResourceFormatsList4',MDCodeTypes::get_resource_formats(),""),	// drop down				
					new TextField('MDResourceFormatName4','MDResourceFormatName4',""),	// drop down				
					new TextField('MDResourceFormatVersion4','MDResourceFormatVersion4',"")	// drop down				
				),
				new CompositeField (
					new DropdownField('ResourceFormatsList5','ResourceFormatsList5',MDCodeTypes::get_resource_formats(),""),	// drop down				
					new TextField('MDResourceFormatName5','MDResourceFormatName5',""),	// drop down				
					new TextField('MDResourceFormatVersion5','MDResourceFormatVersion5',"")	// drop down				
				),

				new CompositeField (
					new DropDownField('MDHierarchyLevel','MDHierarchyLevel',MDCodeTypes::get_scope_codes_keys(),""), // drop down
					new HiddenField('MDHierarchyLevelData','MDHierarchyLevelData',""),
					new TextField('MDHierarchyLevelName','MDHierarchyLevelName',""),
					new HiddenField('MDHierarchyLevelNameData','MDHierarchyLevelNameData',""),
					new TextField('MDParentIdentifier','MDParentIdentifier',"")
				),
				new CompositeField (	
					new TextField('CIOnlineLinkage','CIOnlineLinkage',""),				
					new HiddenField('CIOnlineLinkageData','CIOnlineLinkageData',""),				
					new DropdownField('CIOnlineProtocol','CIOnlineProtocol',MDCodeTypes::get_online_resource_protocol(),""),	// drop down				
					new TextField('CIOnlineName','CIOnlineName',""),		
					new TextField('CIOnlineDescription','CIOnlineDescription',""),				
					new DropdownField('CIOnlineFunction','CIOnlineFunction',MDCodeTypes::get_online_resource_function(),"")	// drop down				
				),
				new CompositeField (
					new DropdownField('useLimitation','License',MDCodeTypes::get_use_limitation(),"")	// drop down				
				)
			);
		}
		if ($recaptchaField) {
			$fields->push($recaptchaField);
		}
		
		if(!$actions) {
	      $actions = new FieldList(
				FormAction::create("doRegisterMetadata")->setTitle("Submit")
	      );
		}
		
		if(!$validator){
			$validator = new RequiredFields( 
						//make sure that MDTitle and MDAbstract is filled and MDElectronicMailAddress is a valid email-address
			            'MDTitle', 'MDAbstract', 'MDElectronicMailAddress', 'MDDateTime', 'MDTopicCategory'
			        );
		}

		parent::__construct($controller, $name, $fields, $actions, $validator);
	}
	
	function forTemplate() {
		return $this->renderWith(array(
			$this->class,
			'Form'
		));
	}
}
