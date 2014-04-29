<?php
/**
 * @author Rainer Spittel (rainer at silverstripe dot com)
 * @package geocatalog
 * @subpackage code
 */

/**
 * General catalogue page
 */
class CatalogueHomePage extends Page {

	public static $db = array(
		'SearchPageName' => "Varchar", // Name of the searchpage (Type CataloguePage) to redirect the search to
		'BrowsePageName' => "Varchar", // Name of the searchpage (Type BrowsePage) to redirect the search to
		'SubmitDataPageName' => "Varchar", // Name of the searchpage (Type CataloguePage) to redirect the search to
	);
	
	/**
	 * Provide an array of instances of subclasses for a given classname. The
	 * array can be used to populate values into drop down lists to select
	 * redirect pages.
	 *
	 * @param string $classname Name of the parent class
	 *
	 * @return array
	 */
	static function get_page_subclasses($classname) {
		
		$classes = ClassInfo::subclassesFor($classname);
		$pages = array();
		foreach($classes as $class) {
			$dataObjectSet = DataObject::get($class);
			
			if ($dataObjectSet) {
				foreach($dataObjectSet as $item) {
					$pages[$item->URLSegment] = $item->Title ." (url: ".$item->URLSegment.")";
				}
			}
		}
		return $pages;
	}
	
	/**
	 * This method returns the standard form for the metadata search.
	 *
	 * To enable the submission into a different controller, I choose the 
	 * work around to define the Form Object Link method in this controller
	 * to point to the url-segment of the selected catalogue page {@link FormObjectLink). 
	 * 
	 * @return Form
	 */
	function Form() {
		// create the search form
		$searchForm = CataloguePage_Controller::get_search_form_name();
		$form = new $searchForm($this,'dogetrecords');		
		return $form;
	}
	
	/**
	 * Overwrites the controller - link handling for form submissions. 
	 * This method defines the form submission action for the search form, which
	 * sends its request to the catalogue page controller class.
	 *
	 * @return string URL segment and action name for the search form.
	 */
	function FormObjectLink() {
		return $this->SearchPageName."/dogetrecords";
	}
	
	/** 
	 * Populate the additional field.
	 * @return Fieldset 
	 */
	public function getCMSFields() {
		$fields = parent::getCMSFields();
		
		$pagesSearch = CatalogueHomePage::get_page_subclasses('CataloguePage');
		$pagesBrowse = CatalogueHomePage::get_page_subclasses('BrowsePage');
		$pagesRegister = CatalogueHomePage::get_page_subclasses('RegisterDataPage');

		$fields->addFieldsToTab('Root.Catalog',
			array( 
				$search = new DropdownField('SearchPageName','Search Page',$pagesSearch),				// drop down
				$browse = new DropdownField('BrowsePageName','Browse Page',$pagesBrowse),				// drop down
				$submit = new DropdownField('SubmitDataPageName','Submit Page',$pagesRegister),	        // drop down
			)
		);

		$config = SiteConfig::current_site_config();
		if ($config->CatalogueSettingReadonly) {
			$fields->makeFieldReadonly('SearchPageName');
			$fields->makeFieldReadonly('BrowsePageName');
			$fields->makeFieldReadonly('SubmitDataPageName');
		} else {
			$search->setDescription('Use this page as search page (must be of type "Catalogue Page")');
			$browse->setDescription('Use this page as browse page (must be of type "Browse Page")');
			$submit->setDescription('Use this page as submit data page (must be of type "Register Data Page")');
		}
		
		return $fields;
	}	
}

/**
 * Customised controller class
 */
class CatalogueHomePage_Controller extends Page_Controller {

	/**
	 * Initialises the controller for any action on this controller.
	 */
	public function init() {
		parent::init();
		$this->extend('extendInit');
	}

	/**
	 * Action to render the index-page.
	 * @param $data
	 *
	 * @return string
	 */
	public function index($data) {
		$html = $this->render();
        return $html;
	}
}