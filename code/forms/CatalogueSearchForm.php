<?php
/**
 * @author Rainer Spittel (rainer at silverstripe dot com)
 * @package geocatalog
 * @subpackage forms
 */

/**
 * Standard CatalogueSearchForm
 *
 * This class implements the standard search form, which contains a search
 * text field and an option-set to select the metadata standard (ISO/DublinCore).
 *
 */
class CatalogueSearchForm extends Form {

	/**
	 * Initiate the standard Metadata catalogue search form. The 
	 * additional parameter $defaults defines the default values for the form.
	 * 
	 * @param Controller $controller The parent controller, necessary to create the appropriate form action tag.
	 * @param String $name The method on the controller that will return this form object.
	 * @param FieldSet $fields All of the fields in the form - a {@link FieldSet} of {@link FormField} objects.
	 * @param FieldSet $actions All of the action buttons in the form - a {@link FieldSet} of {@link FormAction} objects
	 * @param Validator $validator Override the default validator instance (Default: {@link RequiredFields})
	 * @param Array $defaults Override the default values of the form.		 
	 */
	function __construct($controller, $name, FieldSet $fields = null, FieldSet $actions = null, $validator = null, $defaults = null) {

		$format     = $defaults['format'];
		$searchTerm = $defaults['searchTerm'];

		$bboxUpper = $bboxLower = null;
		if (isset($defaults['bboxUpper']) && isset($defaults['bboxLower'])) {
			$bboxUpper = $defaults['bboxUpper'];
			$bboxLower = $defaults['bboxLower'];
		}
		
		$values = CataloguePage_Controller::get_standard_names();
		
		$upperField = new HiddenField('bboxUpper', _t('SearchForm.SEARCH', 'bboxUpper'), $bboxUpper);
		$upperField->addExtraClass('upper');

		$lowerField = new HiddenField('bboxLower', _t('SearchForm.SEARCH', 'bboxLower'), $bboxLower);
		$lowerField->addExtraClass('lower');
		
		if(!$fields) {
			$fields = new FieldSet(
				new TextField('searchTerm', _t('SearchForm.SEARCH', 'Search'), $searchTerm),
				$upperField, $lowerField,
				new OptionsetField('format', _t('SearchForm.MetadataStandard', 'Metadata Standard'), $values, $format)			
			);
		}

		if(singleton('SiteTree')->hasExtension('Translatable')) {
			$fields->push(new HiddenField('locale', 'locale', Translatable::get_current_locale()));
		}
		
		if(!$actions) {
			$actions = new FieldSet(
				new FormAction("submit", _t('SearchForm.Search', 'Search'))
			);
		}
		
		parent::__construct($controller, $name, $fields, $actions);
		$this->setFormMethod('get');
	}
	
	/**
	 * Enable this form to support custom forms/templates. This form
	 * loads the template CatalogueSearchForm.ss if available.
	 *
	 * @see CatalogueSearchForm.ss
	 *
	 * @return string rendered template
	 */
	function forTemplate() {
	      return $this->renderWith(array(
	         $this->class,
	         'Form'
	      ));
	   }
		
	
}