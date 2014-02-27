<?php
/**
 * @author Rainer Spittel (rainer at silverstripe dot com)
 * @package geocatalogue
 * @subpackage commands
 */

/**
 * Create a ISO19139 Metadata XML file
 *
 * This command generates a ISO19139 metadata XML file.
 */
class GenerateISO19139XMLCommand extends ControllerCommand {
	
	/**
	 * @var string $templatename template to generate a valid OGC update request.
	 */
	static $templatename = 'cswISO19139_xml';
	
	/**
	 * Command execute
	 *
	 * Generate the ISO19139 metadata XML and return it.
	 */
	public function execute() {
		
		$data       = $this->getParameters();
		if(! isset($data['MDMetadata'])){
			throw new GenerateISO19139XMLCommand_Exception("No data-object given");
		}
		
		$MDMetadata = $data['MDMetadata'];
		
		if(! is_a($MDMetadata,'MDMetadata')){
			throw new GenerateISO19139XMLCommand_Exception("data-object is not a MDMetadata");
		}
		
		$requestXML = self::$templatename;

		$obj = new ViewableData();

		$obj->customise($MDMetadata);
		$data = $obj->renderWith($requestXML);

		return $data;		
	}	
}

/**
 * Customised exception class
 */
class GenerateISO19139XMLCommand_Exception extends Exception {}
