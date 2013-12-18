<?php
/**
 * This file defines the implementation of the Browse - Page type.
 *
 * @author Rainer Spittel (rainer at silverstripe dot com)
 * @package geocatalog
 */

/**
 * BrowsePage implements the page type to browse GeoNetwork records.
 *
 * This page type is used to browse GeoNetwork data and visualise the results.
 */
class BrowsePage extends CataloguePage {
	
	/**
	 * Overwrites {@link SiteTree::getCMSFields}.
	 * 
	 * Appends a GeoNetwork JavaScript validator the the CMS backend.
	 *
	 * @return FieldSet
	 */ 
	function getCMSFields() {
		Requirements::javascript('geocatalog/javascript/GeonetworkUrlValidator.js');	
		
		$fields = parent::getCMSFields();
		return $fields;
	} 
}

class Browser_QueryClass extends Catalogue_QueryClass {

    public function validate() {
    }
}

/**
 * Controller Class for BrowsePage
 *
 * Page controller class for Browse-Page {@link BrowsePage}. The controller
 * class handles the requests and delegates the requests to the page instance
 * as well as to the available GeoNetwork node.
 */
class BrowsePage_Controller extends CataloguePage_Controller {

    protected function getQueryClass($params) {
        return new Browser_QueryClass($params);
    }

    /**
	 * Validate HTTP-Request parameter.
	 *
	 * BrowsePage customise the search capabilities and allows the 'empty' search.
	 *
	 * @param array $params http-request parameter
	 */
	protected function validateRequest($params) {
	}
	
	/**
	 * Action: index 
	 * 
	 * Browse Page perform a empty search to populate the all results on the 
	 * Browse Page.
	 */
	public function index($data) {
		$html=  $this->dogetrecords($data);
        return $html;
	}
}
