<?php
/**
 * @author Rainer Spittel (rainer at silverstripe dot com)
 * @package geocatalog
 * @subpackage commands
 */

/**
 * Retrieve a metadata XML file from the catalogue
 *
 * This command creates an XML OGC request to retrieve the OGC XML metadata
 * file for a specific metadata entry. Each metadata entry has a unique
 * ID  (fileIdentifier).
 */
class CreateRecordByIdRequestCommand extends ControllerCommand {
	
	/**
	 * Command execute
	 *
	 * Perform the request command. Creates a XML request based on the given
	 * XML request parameter and fileIdentifier. This request is used to create
	 * OGC compliant search requests and can be extended.
	 *
	 * @return string - XML CSW requests which can be used to access an OGC CSW 2.0.1 web service.
	 */
	public function execute() {
		
		$data       = $this->getParameters();
		$requestXML = $data['requestxml'];
		$fileIdentifier = $data['fileIdentifier'];
		
		// retrieve as Dublin-Core metadata schema
		$obj = new ViewableData();
		$fields = array(
			"fileIdentifier" => $fileIdentifier
		);
		
		$obj->customise($fields);
		$data = $obj->renderWith($requestXML);

		return $data;		
	}
	
}