<?php
/**
 * @author Rainer Spittel (rainer at silverstripe dot com)
 * @package geocatalogue
 */

/**
 * Create a ISO19139 Metadata XML document.
 *
 * This command generates a ISO19139 metadata XML document. It can be used
 * to insert/update records in a remote GeoNetwork server.
 */
class GenerateISO19139XMLCommand extends ControllerCommand {
	
	/**
	 * @var string $templatename path to template used to generate the XML document.
	 */
	static $templatename = 'cswISO19139_xml';
	
	/**
	 * Execute the command to generate the ISO19139 metadata XML.
	 *
	 * @return HTMLText
	 * @throws GenerateISO19139XMLCommand_Exception
	 */
	public function execute() {
		
		$data = $this->getParameters();
		if(! isset($data['MDMetadata'])){
			throw new GenerateISO19139XMLCommand_Exception("No data-object given");
		}
		
		$MDMetadata = $data['MDMetadata'];
		if(!is_a($MDMetadata,'MDMetadata')){
			throw new GenerateISO19139XMLCommand_Exception("data-object is not a MDMetadata");
		}
		
		$requestXML = self::$templatename;

		$obj = new ViewableData();
		$obj->customise($MDMetadata);

		return $obj->renderWith($requestXML);;
	}	
}

/**
 * Class GenerateISO19139XMLCommand_Exception
 * Customised exception class
 */
class GenerateISO19139XMLCommand_Exception extends Exception {}
