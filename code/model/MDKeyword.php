<?php
/**
 * @author Rainer Spittel (rainer at silverstripe dot com)
 * @package geocatalogue
 * @subpackage model
 */

/**
 * MDKeyword implements the ISO19139 structure for metadata keywords. It will 
 * be stored alongside with MDMetadata class.
 */
class MDKeyword extends MDDataObject {

	/**
	 * Data structure for MDKeyword
	 */	
	static $db = array(
		"Value" => "Varchar",
	);
	
	/**
	 * Data relationships for MDKeyword
	 */
	static $has_one = array(
		"MDMetadata" => "MDMetadata",
	);
	
}