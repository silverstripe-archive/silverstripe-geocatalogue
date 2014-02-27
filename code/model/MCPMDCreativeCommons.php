<?php
/**
 * @author Rainer Spittel (rainer at silverstripe dot com)
 * @package geocatalogue
 * @subpackage model
 */

/**
 * MCPMDCreativeCommons implements the MCP structure for metadata resource constraints. It will 
 * be stored alongside with MDMetadata class.
 */
class MCPMDCreativeCommons extends MDDataObject {
	
	/**
	 * Data Structure for ISO19139 MDResourceConstraint
	 * @var array
	 */
	static $db = array(
		"useLimitation" => "Varchar(250)",
		"jurisdictionLink" => "Varchar(250)",
		"licenseLink" => "Varchar(250)",
		"imageLink" => "Varchar(250)",
		"licenseName" => "Varchar(250)"
	);

	/**
	 * Data relationships for MDContact
	 */
	static $has_one = array(
		"MDMetadata" => "MDMetadata",
	);

}