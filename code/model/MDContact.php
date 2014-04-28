<?php
/**
 * @author Rainer Spittel (rainer at silverstripe dot com)
 * @package geocatalog
 * @subpackage model
 * 
 * The MDContact Data object is used to model the ISO19139 MD-Contact XML
 * structure. 
 * This is not a full ISO19139 implementation, just a bare minimum to meet the 
 * business requirements.
 */
class MDContact extends MDDataObject {
	
	/**
	 * Data structure for MDContact
	 */
	static $db = array(		
		// CI_ResponsibleParty
		"MDIndividualName" => "Varchar",			// mandatory
		"MDOrganisationName" => "Varchar",			// mandatory
		"MDPositionName" => "Varchar",				// mandatory
	
		// CI_Telephone
		"MDFacsimile" => "Varchar", 

		// CI_Address
		"MDDeliveryPoint" => "Varchar",
		"MDCity" => "Varchar",
		"MDAdministrativeArea" => "Varchar",
		"MDPostalCode" => "Varchar",
		"MDCountry" => "Varchar",
	);

	/**
	 * Data relationships for MDContact
	 */
	static $has_one = array(
		"MDMetadata" => "MDMetadata",
	);

	static $has_many = array(
		"MDVoice" => "MDPhoneNumber",
		"MDElectronicMailAddress" => "MDEmail"
	);
	
	public function getFirstVoicePhoneNumber() {
		$dos = $this->MDVoice();
		
		if ($dos->First()) {
			return $dos->First();
		} 
		return null;
	}

	public function getFirstElectronicMailAddress() {

		$dos = $this->MDElectronicMailAddress();
		if ($dos->First()) {
			return $dos->First();
		} 
		return null;
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
					if ($relation == 'MDVoice') {
					
						// load the sub-array into the MDContact object
						foreach($v as $mdVoiceEntry) {
							$item = new MDPhoneNumber();
							$item->loadData($mdVoiceEntry);
							
							// add object to list
							$relObj->MDVoice()->add($item);
						}
					}
				}

				foreach($relations as $relation) {
					if ($relation == 'MDElectronicMailAddress') {
					
						// load the sub-array into the MDContact object
						foreach($v as $mdEmail) {
							$item = new MDEmail();
							$item->loadData($mdEmail);
							
							// add object to list
							$relObj->MDElectronicMailAddress()->add($item);
						}
					}
				}

			}
		}
	}
}