<?php
/**
 * @author Rainer Spittel (rainer at silverstripe dot com)
 * @package geocatalog
 * @subpackage model
 */

/**
 * MDResourceConstraint implements the ISO19139 structure for metadata resource constraints. It will 
 * be stored alongside with MDMetadata class.
 */
class MDResourceConstraint extends MDDataObject {
	
	/**
	 * Data Structure for ISO19139 MDResourceConstraint
	 * @var array
	 */
	static $db = array(
		"accessConstraints" => "Varchar",
		"useConstraints" => "Varchar",
		"otherConstraints" => "Varchar"			// mandatory
	);

	/**
	 * Data relationships for MDContact
	 */
	static $has_one = array(
		"MDMetadata" => "MDMetadata",
	);
	
	/**
	 * Returns the nice, human readable string for the codetype (defined by
	 * the OGC ISO standard).
	 *
	 * @return string
	 */
	public function getAccessConstraintsNice() {
		$index = $this->accessConstraints;
		$codeTypes = MDCodeTypes::get_resource_constraints();
		return isset($codeTypes[$index]) ? $codeTypes[$index] : MDCodeTypes::$default_for_null_value;
	}

	/**
	 * Returns the nice, human readable string for the codetype (defined by
	 * the OGC ISO standard).
	 *
	 * @return string
	 */
	public function getUseConstraintsNice() {
		$index = $this->useConstraints;
		$codeTypes = MDCodeTypes::get_resource_constraints();
		return isset($codeTypes[$index]) ? $codeTypes[$index] : MDCodeTypes::$default_for_null_value;
	}
	
}