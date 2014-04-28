<?php
/**
 * @author Rainer Spittel (rainer at silverstripe dot com)
 * @package geocatalog
 * @subpackage model
 *
 * The MDMetadata class implements the core Metadata dataobject
 * to store ISO19139 metadata. This dataobject is used to render the search 
 * results onto the CataloguePage (@link CataloguePage) and for processing
 * a metadata registration (@link RegisterDataPage).
 *
 * ISO19139 is a OGC metadata standard and 'replaces/derives' from ISO19115. 
 * Other standards are:
 *  - ISO19119
 *  - DublinCore
 *  - MCP (derived version of ISO19115)
 */
class MDMetadata extends MDDataObject {
	
	/**
	 * Data Structure for ISO19139 mandatory core data fields
	 * @var array
	 */
	static $db = array(
		"gnID"			=> "Varchar",			// internal GeoNetwork ID
		"fileIdentifier" => "Varchar",						// mandatory
		"dateStamp" => "SSDatetime",						// mandatory

		"metadataStandardName" => "Varchar",
		"metadataStandardVersion" => "Varchar",
		
		"MDTitle" => "Varchar",								// mandatory
		"MDAbstract" => "Varchar",							// mandatory
		"MDPurpose" => "Varchar",
		"MDLanguage" => "Varchar",							// mandatory    
		"MDEdition" => "Varchar",
		"MDPresentationForm" => "Varchar",

		"MDSpatialRepresentationType" => "Varchar",			// enhanced
		"MDGeographicDiscription" => "Varchar",				// mandatory

		"MDWestBound" => "Double",							// mandatory
		"MDEastBound" => "Double",							// mandatory
		"MDSouthBound" => "Double",							// mandatory
		"MDNorthBound" => "Double",							// mandatory

		"MDParentIdentifier" => "Varchar", // ANZLIC
	);

	static $summary_fields = array(
		"fileIdentifier" => "ID",
		"MDTitle" => "Title",
		"MDAbstract" => "Abstract",
	    "MDGeographicDiscription" => "GeographicDiscription",
	);
	
	/**
	 * Relationship to other data-objects. Implement a semi ISO19139 
	 * implementation recommondation.
	 * @var array
	 */ 
	static $has_many = array(
		"MDContacts" => "MDContact",
		"PointOfContacts" => "MDContact",
		"MDCitationDates" => "MDCitationDate",
		"MDKeywords" => "MDKeyword",
		"MDTopicCategory" => "MDTopicCategory",						// mandatory
		"MDResourceConstraints" => "MDResourceConstraint",
		"MDResourceFormats" => "MDResourceFormat",
		"CIOnlineResources" => "CIOnlineResource",
		"MCPMDCreativeCommons" => "MCPMDCreativeCommons",
		"MDHierarchyLevel" => "MDHierarchyLevel", // ANZLIC
		"MDHierarchyLevelName" => "MDHierarchyLevelName" // ANZLIC
	);
	
	/**
	 * Being able to configure what is shown as 'online urls' in templates.
	 */
	static $online_resource_web_url_filter = array(
		'WWW:LINK-1.0-http--link'
	);

	/**
	 * Sets the whiltelist array for online web urls protocol. 
	 * This array will be used to remove online-resources 
	 * from the metadata-detail page.
	 *
	 * @param $value Array of protocol values, such as array('WWW:LINK-1.0-http--downloaddata', 'WWW:LINK-1.0-http--link')
	 */
	static function set_online_resource_web_url_filter($value) {
		self::$online_resource_web_url_filter = $value;
	}

	static function get_online_resource_web_url_filter() {
		return self::$online_resource_web_url_filter;
	}	

	public function CIOnlineResources_WebAddresses() {
		return $this->getFilteredCIOnlineResources( self::get_online_resource_web_url_filter() );
	}
	
	public function MetadataCIOnlineResources() {
		return $this->getFilteredCIOnlineResources( array('WWW:LINK-1.0-http--metadata-URL'));
	}

	public function getMDAbstractJson() {
		return Convert::raw2json($this->MDAbstract);
	}

	/**
	 * This method is a work around as the $Top method in SSViewer does break in templates Included into
	 * a layout template.
	 *
	 * @param null $action
	 *
	 * @return string
	 */
	public function Link($action = null) {
		$controller = Controller::curr();
		return $controller->Link($action);
	}

	/**
	 * Returns all CIOnlineResources objects of this metadata record which
	 * are web-addresses.
	 *
	 * @param $filter Array of protocol types you like to retrieve.
	 *
	 * @return DataObjectSet The components of the one-to-many relationship.
	 */
	public function getFilteredCIOnlineResources($filter = null) {
		// important: don't pass in the filter into the get-component 
		// method because it would try to get the data from the database
		// and would not use the memory/cached version.
		$resources = $this->getComponents('CIOnlineResources');
		$result = new ArrayList();

		$protocols = $filter;
		
		// if $filter is null, then get the default list, stored in 
		// CIOnlineResource.
		if ($filter == null) {
			$protocols = CIOnlineResource::get_public_protocols();
		}

		// if filter is an empty array, then return all online resource records.
		if(is_array($filter) && empty($filter)) {
			$result = $resources;
		} else
		if($resources) {
			foreach($resources as $resource) {
				if ( in_array($resource->CIOnlineProtocol,$protocols) ) {
					if (isset($resource->CIOnlineLinkage) && $resource->CIOnlineLinkage != '') {
						$result->add($resource);
					}
				}
			}
		}		
		return $result;
	}

	/**
	 * Returns all CIOnlineResources objects of this metadata record which
	 * are web-addresses.
	 *
	 * @see RecordFull.ss
	 *
	 * @return DataObjectSet The components of the one-to-many relationship.
	 */
	public function CIOnlineResources_FirstWebAddress() {
		$result = $this->CIOnlineResources_WebAddresses();
		return $result->First();
	}

	/**
	 * Returns true if a web-address for this metadata exists. It is used by
	 * the RecordFull.ss template to determine if the web-address block need
	 * to be rendered.
	 *
	 * @see CIOnlineResources_FirstWebAddress
	 * @see RecordFull.ss
	 * @return boolean true if the metadata record has a web-address reference.
	 */
	public function CIOnlineResources_HasFirstWebAddress() {
		$result = $this->CIOnlineResources_WebAddresses();
		$item = $result->First();
		$retValue = true;

		if ($item == null) {
			$retValue = false;
		}
		return $retValue;
	}

	/**
	 * Returns the MDDateTime in RFC3339 string format (incl. timezone).
	 * This is required for the atom feed support.
	 */
	public function getDateTimeInRFC3339($dateType = 'creation') {
		$result = '';
		foreach ($this->MDCitationDates() as $date) {
			if ($date != NULL) {
				// expected format by jQuery-UI datepicker: '2014-02-04 00:00:00'
				$dateParts=explode('T',$date->MDDateTime);
				if (sizeof($dateParts) != 2) {
					$dateParts=explode(' ',$date->MDDateTime);
				}
				$dateParts=explode('-',$dateParts[0]);
				$temp = $dateParts[2] . '-' . $dateParts[1] . '-' . $dateParts[0];
				$result = date('c', strtotime($temp));

				if ($date->MDDateType == $dateType) {
					return $result;
				}
			}
		}
		return $result;
	}
	
  	/**
	 * Returns the MDDateTime in RFC3339 string format (incl. timezone).
	 * This is required for the atom feed support.
	 */
	public function getCreatedInRFC3339() {
		return $this->getDateTimeInRFC3339('creation');
	}	
	
  	/**
	 * Returns the MDDateTime in RFC3339 string format (incl. timezone).
	 * This is required for the atom feed support.
	 */
	public function getPublicationInRFC3339() {
		return $this->getDateTimeInRFC3339('publication');
	}	
	
	/**
	 * Returns the nice, human readable string for the codetype (defined by
	 * the OGC ISO standard).
	 *
	 * @return string
	 */
	public function getTopicCategoryNice() {
		$dos = new ArrayList();
		foreach ($this->MDTopicCategory() as $item) {
			$codeTypes = MDCodeTypes::get_categories();

			if (isset($codeTypes[$item->Value])) {
				if ($codeTypes[$item->Value] != "(please select a category)") {
					$dos->push( new ArrayData(array('Value' => $codeTypes[$item->Value])) );
				}
			}
		}
		return $dos;
	}

	/**
	 * Returns the nice, human readable string for the codetype (defined by
	 * the OGC ISO standard).
	 *
	 * @return string
	 */
	public function getSpatialRepresentationTypeNice() {
		$index = $this->MDSpatialRepresentationType;
		$codeTypes = MDCodeTypes::get_spatial_representation_type();
		
		return isset($codeTypes[$index]) ? $codeTypes[$index] : MDCodeTypes::$default_for_null_value;
	}

	/**
	 * Returns the nice, human readable string for the codetype (defined by
	 * the OGC ISO standard).
	 *
	 * @return string
	 */
	public function getPlaceName() {
		$index = $this->MDWestBound.";".$this->MDEastBound.";".$this->MDSouthBound.";".$this->MDNorthBound;
		
		$result = '';

		$codeTypes = MDCodeTypes::get_places();
		if (isset($codeTypes[$index])) $result = $codeTypes[$index];

		return $result;
	}

	public function getHasBBox() {
		$value = ($this->MDWestBound & $this->MDEastBound & $this->MDSouthBound & $this->MDNorthBound);
		return $value;
	}

	/**
	 * This method loads a provided array into the data structure.
	 * It also creates dependencies, such as contact data objects
	 * and populate the values into those objects.
	 *
	 * @param $data array of db-values.
	 */
	public function loadData($data) {	
		
		if ($data == null) {
			return;
		}
		
		if (!is_array($data)) {
			return;
		}
				
		foreach($data as $k => $v) {
			// store data into this object (no ':" in the string)
			if(strpos($k,':') === false) {
				$this->$k =  Convert::xml2raw($v);
			} else {
				// A ':' is used as a namespace marker. It is used to 
				// create the related data objects, such as MDContacts.
				$relations = explode(':', $k);
				$fieldName = array_pop($relations);
				$relObj = $this;

				// iterate through the relationships. At the moment, this 
				// loading process just works for 1 level hierarchies. 
				foreach($relations as $relation) {
					
					if ($relation == 'PointOfContacts') {
						
						// load the sub-array into the MDContact object
						$item = new MDContact();
						$item->loadData($v);
						
						// add the new MDContect to the collection class of this
						// object.
						$relObj->PointOfContacts()->add($item);
					}

					if ($relation == 'MDContacts') {
						// load the sub-array into the MDContact object
						$item = new MDContact();
						$item->loadData($v);
						
						// add the new MDContect to the collection class of this
						// object.
						$relObj->MDContacts()->add($item);
					}
					if ($relation == 'MDResourceConstraints') {
						// load the sub-array into the MDResourceConstraints object
						if (is_array($v)) {
							foreach($v as $vitem) {
								$item = new MDResourceConstraint();
								$item->loadData($vitem);
						
								// add the new MDContect to the collection class of this
								// object.
								$relObj->MDResourceConstraints()->add($item);
							}
						}
					}
					if ($relation == 'MDResourceFormats') {
						
						if (is_array($v)) {
							foreach($v as $vitem) {
								// load the sub-array into the MDResourceFormats object
								$item = new MDResourceFormat();
								$item->loadData($vitem);
						
								// add the new MDContect to the collection class of this
								// object.
								$relObj->MDResourceFormats()->add($item);
							}
						}
					}	

					if ($relation == 'MDTopicCategory') {
						
						if (is_array($v)) {
							foreach($v as $vitem) {
								// load the sub-array into the MDResourceFormats object
								$item = new MDTopicCategory();
								$item->loadData($vitem);
						
								// add the new MDTopicCategory to the collection class of this
								// object.
								$relObj->MDTopicCategory()->add($item);
							}
						}
					}	

					if ($relation == 'MDCitationDates') {
						if (is_array($v)) {
							foreach($v as $vitem) {
								// load the sub-array into the MDResourceFormats object
								$item = new MDCitationDate();
								$item->loadData($vitem);

								// add the new MDContect to the collection class of this
								// object.
								$relObj->MDCitationDates()->add($item);
							}
						}
					}	      
					if ($relation == 'MCPMDCreativeCommons') {
						if (is_array($v)) {
							foreach($v as $vitem) {
								// load the sub-array into the MDContact object
								$item = new MCPMDCreativeCommons();
								$item->loadData($vitem);

								// add the new MCPMDCreativeCommons to the collection class of this
								// object.
								$relObj->MCPMDCreativeCommons()->add($item);
							}
						}	
					}
					if ($relation == 'CIOnlineResources') {
						if (is_array($v)) {
							foreach($v as $vitem) {
								// load the sub-array into the MDContact object
								$item = new CIOnlineResource();
								$item->loadData($vitem);

								// add the new MDContect to the collection class of this
								// object.
								$relObj->CIOnlineResources()->add($item);
							}
						}	
					}

					if ($relation == 'MDHierarchyLevel') {
						if (is_array($v)) {
							foreach($v as $vitem) {
								$codes = MDCodeTypes::get_scope_codes();
								if (isset($codes[$vitem['Value']])) {
									$item = new MDHierarchyLevel();
									$item->loadData($vitem);
									$relObj->MDHierarchyLevel()->add($item);
								}
							}
						}	
					}
					
					if ($relation == 'MDHierarchyLevelName') {
						if (is_array($v)) {
							foreach($v as $vitem) {
								$item = new MDHierarchyLevelName();
								$item->loadData($vitem);
								$relObj->MDHierarchyLevelName()->add($item);
							}
						}	
					}
				}
			}
		}	
	}
}
