<?php
/**
 * @author Rainer Spittel (rainer at silverstripe dot com)
 * @package geocatalog
 * @subpackage commands
 *
 * Implementation of an OGC insert request command.
 */

/**
 * Insert ISO19139 Metadata into GeoNetwork
 *
 * This command creates an XML OGC request to insert new ISO19139 metadata
 * into the an OGC CSW 2.0.1 service.
 */
class CreateInsertCommand extends ControllerCommand {
	
	static $schema_name = 'GenerateISO19139XML';

	static $templatename = 'cswInsertISO_xml';

	static function get_template_name() {
		return self::$templatename;
	}

	/**
	 * Command execute
	 *
	 * Perform the request command. Creates a XML request based on the given
	 * XML request parameter and the metadata data. This request is used to insert new
	 * Metadata records into GeoNetwork.
	 */
	public function execute() {
		$data       = $this->getParameters();
		
		// generate xml file
		$cmd = $this->getController()->getCommand(self::$schema_name, $data);
		$xml = $cmd->execute();
		
		$requestXML    = self::get_template_name();
		$MDMetadataXML = $xml;

		$obj = new ViewableData();
		$obj->customise( array("MDMetadataXML" => $MDMetadataXML) );
		$data = $obj->renderWith($requestXML);
		return $data;		
	}
	
}