<?php
/**
 * @author Rainer Spittel (rainer at silverstripe dot com)
 * @package geocatalogue
 * @subpackage commands
 */

/**
 * Perform a general xml-xslt transformation and returns the created string.
 */
class TranslateXMLCommand extends ControllerCommand {

	/**
	 * Command execute
	 *
	 * This method performs the action to parse a XML string and returns the 
	 * translated string.
	 *
	 * @return string
	 */
	public function execute() {
				
		$result = new ViewableData();
		$data   = $this->getParameters();
		
		// Throw exception on null-xml
		if(!isset($data['xml'])){
			throw new TranslateXMLCommand_Exception("Expected an XML string, but there is nothing given.");
		}

		// Throw exception on null or empty xsl
		if(!isset($data['xsl']) or $data['xsl'] == '' ){
			throw new TranslateXMLCommand_Exception("Expected an XSL file name, but there is none given.");
		}

		// return empty string on empty-xml
		if($data['xml'] == ''){
			return '';
		}
		
		$xml    = $data['xml'];
		$xsl    = $data['xsl'];

		if(! file_exists($xsl)){
			throw new TranslateXMLCommand_Exception("There is something wrong with stylesheet $xsl!");
		}

		
		// if (!( strpos($xml, "<?xml " ) === 0 )) {
		// 	throw new TranslateXMLCommand_Exception("Invalid response. Expected an XML string, but received something else instead.");
		// }

		# LOAD XML FILE
		$XML = new DOMDocument();
		$XML->loadXML( $xml );

		# START XSLT
		$xslt = new XSLTProcessor();
		$XSL  = new DOMDocument();

		$XSL->load( $xsl, LIBXML_NOCDATA);
		$xslt->importStylesheet( $XSL );
		# Transform XML into php structure
		$result = $xslt->transformToXML( $XML );
		$result = str_replace('<?xml version="1.0"?>',"",$result);

		return $result;		
	}
}

/**
 * Customised exception class
 */
class TranslateXMLCommand_Exception extends Exception {}
