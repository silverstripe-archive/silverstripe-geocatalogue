<?php
/**
 * @author Rainer Spittel (rainer at silverstripe dot com)
 * @package geocatalogue
 * @subpackage model
 */

/**
 * MDHierarchyLevelName implements the ISO19139 structure for multiple hierarchy level names. It will 
 * be stored alongside with MDMetadata class.
 */
class MDHierarchyLevelName extends MDDataObject {

	/**
	 * Data structure for MDHierarchyLevelName
	 */	
	static $db = array(
		"Value" => "Varchar",
	);
	
	/**
	 * Data relationships for MDHierarchyLevelName
	 */
	static $has_one = array(
		"MDMetadata" => "MDMetadata",
	);
	
}
