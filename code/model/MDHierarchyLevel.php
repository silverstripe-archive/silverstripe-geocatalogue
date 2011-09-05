<?php
/**
 * @author Rainer Spittel (rainer at silverstripe dot com)
 * @package geocatalog
 * @subpackage model
 */

/**
 * MDHierarchyLevel implements the ISO19139 structure for multiple hierarchy levels. It will 
 * be stored alongside with MDMetadata class.
 */
class MDHierarchyLevel extends MDDataObject {

	/**
	 * Data structure for MDHierarchyLevel
	 */	
	static $db = array(
		"Value" => "Varchar",
	);
	
	/**
	 * Data relationships for MDHierarchyLevel
	 */
	static $has_one = array(
		"MDMetadata" => "MDMetadata",
	);
	
	
	public function getValueNice() {
		$retValue = $this->Value;
		$codes = MDCodeTypes::get_scope_codes();
		if (isset($codes[$this->Value])) {
			$retValue = $codes[$this->Value];
		}
		return $retValue;
	}
}
