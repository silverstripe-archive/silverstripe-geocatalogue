<?php
/**
 * @author Rainer Spittel (rainer at silverstripe dot com)
 * @package geocatalogue
 * @subpackage model
 */

/**
 * MDResourceFormat implements the ISO19139 structure for metadata resource constraints. It will 
 * be stored alongside with MDMetadata class.
 */
class MDResourceFormat extends MDDataObject {
	
	/**
	 * Data Structure for ISO19139 MDResourceFormat
	 * @var array
	 */
	static $db = array(
		"Name" => "Varchar",
		"Version" => "Varchar"
	);

	/**
	 * Data relationships for MDContact
	 */
	static $has_one = array(
		"MDMetadata" => "MDMetadata",
	);
	
}