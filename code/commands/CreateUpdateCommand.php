<?php
/**
 * @author Rainer Spittel (rainer at silverstripe dot com)
 * @package geocatalog
 * @subpackage commands
 *
 * Implementation of an OGC update request command.
 */

/**
 * Updates a ISO19139 Metadata entry in GeoNetwork
 *
 * This command creates an XML OGC request to update an existing ISO19139 metadata
 * entry in an OGC CSW 2.0.1 service.
 */
class CreateUpdateCommand extends ControllerCommand {

	/**
	 * @var string $templatename template to generate a valid OGC update request.
	 */
	static $templatename = 'cswUpdateISO_xml';
	
	/**
	 * @var string $schema_name command name to generate a valid ISO19139 metadata XML.
	 */
	static $schema_command_name = 'GenerateISO19139XML';
	
	/**
	 * Command execute
	 *
	 * Perform the request command. Creates a XML request based on the given
	 * XML request parameter and the metadata data. This request is used to insert new
	 * Metadata records into GeoNetwork.
	 */
	public function execute() {
		
		$data       = $this->getParameters();

		$fileIdentifier = $data['MDMetadata']->fileIdentifier;
		
		$cmd = $this->getController()->getCommand(self::$schema_command_name, $data);
		$xml = $cmd->execute();

		$requestXML = self::$templatename;
		$MDMetadataXML = $xml;

		$obj = new ViewableData();

		$obj->customise( array(
			"MDMetadataXML" => $MDMetadataXML, 
			"fileIdentifier" => $fileIdentifier
		));
			
		$data = $obj->renderWith($requestXML);
		return $data;		
	}
	
}