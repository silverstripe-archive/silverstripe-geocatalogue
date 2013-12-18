<?php
/**
 * @author Rainer Spittel (rainer at silverstripe dot com)
 * @package geocatalog
 * @subpackage model
 */

/**
 * MDEmail implements the ISO19139 structure for multiple email addresses. It will 
 * be stored alongside with MDMetadata class.
 */
class MDEmail extends MDDataObject {

	/**
	 * Data structure for MDEmail
	 */	
	static $db = array(
		"Value" => "Varchar",
	);
	
	/**
	 * Data relationships for MDEmail
	 */
	static $has_one = array(
		"MDContact" => "MDContact",
	);
	
}
