<?php
/**
 * @author Rainer Spittel (rainer at silverstripe dot com)
 * @package geocatalog
 * @subpackage commands
 */

/**
 * Parse the OGC catalogue response and create SS data-object structure.
 *
 * ParseXMLCommand implements the parsing command to read an OGC CSW XML response
 * and to create a SilverStripe Data-Object array structure. This command creates
 * a single DataObjectSet which contains viewable data-objects. 
 */
class ParseXMLCommand extends ControllerCommand {
	
	/** 
	 * Flag for more strict XML validation, used for validating the XML 
	 * Declaration flag is set correctly.
	 */
	private $strict_xml = true;
	
	public function setStrictXMLValidation($value) {
		$this->strict_xml = $value;
	}

	public function getStrictXMLValidation() {
		return $this->strict_xml;
	}

	/**
	 * This method parses a given XML string and returns a DataObjectSet.
	 * This implementation parses a xml schema (i.e. dublin core, iso19139)
	 * and retrieves just the title and all subjects of each result entry.
	 *
	 * The dublin-core metadata schema is embedded into a CSW-metadata
	 * envelope (@link http://www.opengis.net/cat/csw/2.0.2).
	 *
	 * @param string $responseXML valid OGC XML response string
	 * @param string $xsl SilverStripe XSLT to transform the XML response into the internal data structure.
	 *
	 * @return ViewableData
	 */
	public function parseXML($responseXML, $xsl) {
/*
		$cmd = $this->getController()->getCommand("TranslateXML", $data);
		$resul = $cmd->execute();
*/
		$keyword = array();

		# LOAD XML FILE
		$XML = new DOMDocument();
		
		$responseXML = str_replace("'","\'",$responseXML);
		$XML->loadXML( $responseXML );
		// var_dump($responseXML);die();	
		
		# START XSLT
		$xslt = new XSLTProcessor();
		$XSL  = new DOMDocument();

		$XSL->load( $xsl, LIBXML_NOCDATA);
		$xslt->importStylesheet( $XSL );
		
		# Transform XML into php structure
		$eval = $xslt->transformToXML( $XML );
		$eval = str_replace('<?xml version="1.0"?>',"",$eval);

		$mdArray = array();

		// toDo: bad! use JSON
  		// echo('<pre>');print_r($eval);echo('</pre>');die();
		eval(trim($eval));
		// var_dump($mdArray);die();
		
		$result      = new ViewableData();
		$resultItems = new DataObjectSet();
		foreach($mdArray as $item) {
			$metadata = new MDMetadata();

			if (isset($item['dateTimeStamp']) && $item['dateTimeStamp']) {
				$item['dateStamp'] = $item['dateTimeStamp'];
			}

			$metadata->update($item);
			$metadata->loadData($item);
			
			// print_r($metadata);die();
			$resultItems->push($metadata);
		}

		//To avoid unset variables due the xslt
		if(! isset($nextRecord)) $nextRecord = 1;
		if(! isset($timestamp)) $timestamp = null;
		if(! isset($numberOfRecordsMatched)) $numberOfRecordsMatched = 1;
		if(! isset($numberOfRecordsReturned)) $numberOfRecordsReturned = 1;
		
		$result = $result->customise(array(	'Items' => $resultItems,
											'timestamp' => $timestamp,
											'nextRecord' => $nextRecord,
											'numberOfRecordsMatched' => $numberOfRecordsMatched,
											'numberOfRecordsReturned' => $numberOfRecordsReturned
											)
                    );
		return $result;
	}
	
	/**
	 * Command execute
	 *
	 * This method performs the action to parse an OGC CSW XML response and
	 * create the MDMetadata structure.
	 *
	 * @throws ParseXMLCommand_Exception
	 *
	 * @return DataObjectSet
	 */
	public function execute() {
		$result = new ViewableData();
		$data   = $this->getParameters();

		// Throw exception on null-xml
		if(!isset($data['xml'])){
			throw new ParseXMLCommand_Exception("Expected an XML string, but there is nothing given.");
		}
		// Return an empty DatasetObject with the defaults for paging if xml is empty
		if($data['xml'] == ''){
			$result      = new ViewableData();
			$resultItems = new DataObjectSet();
			return $result->customise(array(	'Items' => $resultItems,
												'nextRecord' => 0,
												'numberOfRecordsMatched' => 0,
												'numberOfRecordsReturned' => 0
												)
										);
			
		}

		// Throw exception on null or empty xsl
		if(!isset($data['xsl']) or $data['xsl'] == '' ){
			throw new ParseXMLCommand_Exception("Expected an XSL file name, but there is none given.");
		}
		
		$xml    = $data['xml'];
		$xsl    = $data['xsl'];

		if(! file_exists($xsl)){
			throw new ParseXMLCommand_Exception("There is something wrong with stylesheet $xsl!");
		}
		
		if ($this->getStrictXMLValidation() && !( strpos($xml, "<?xml " ) === 0 )) {
			throw new ParseXMLCommand_Exception("Invalid response. Expected an XML string, but received something else instead.");
		}

		$result = $this->parseXML($xml, $xsl);
		// 
		// foreach($result->getField('Items') as $item){
		// 	var_dump($item);
		// }
		// die();
		
		return $result;		
	}
	
}

/**
 * Customised Exception class
 */
class ParseXMLCommand_Exception extends Exception {}
