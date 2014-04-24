<?php
/**
 * @author Rainer Spittel (rainer at silverstripe dot com)
 * @package geocatalog
 * @subpackage commands
 */

/**
 * Retrieve the full metadata record of a specific record from the GeoNetwork node.
 *
 * This command sends a request to the GeoNetwork node to retrieve a single metadata 
 * record. It returns a XML string which is the plain XML catalogue response.
 */
class  GnGetUUIDOfRecordByIDCommand extends GnAuthenticationCommand {

//	public function get_api_url() {
//		$config = Config::inst()->get('Catalogue', 'geonetwork');
//		$version = $config['api_version'];
//		return $config[$version]['geonetwork_url'].'.get?';
//	}

	public function get_api_url() {
		$config = Config::inst()->get('Catalogue', 'geonetwork');
		return $config[$config['api_version']]['url_getuuid'];
	}
	/**
	 * Command execute
	 *
	 * Performs the command to retrieve a single metadata record. This command creates a 
	 * request (initiates a sub-command) and uses this to send of the 
	 * OGC request to GeoNetwork.
	 *
	 * @see CreateRecordByIdRequestCommand
	 *
	 * @return string OGC CSW response
	 */
	public function execute() {
		$data       = $this->getParameters();
		
		$id = $data['gnID'];

		$restfulService = $this->getRestfulService();

		// generate GeoNetwork HTTP request (query metadata).
		$cmd = null;

		// insert metadata into GeoNetwork
		$headers = array('Content-Type: application/x-www-form-urlencoded');
		$response = $restfulService->request($this->get_api_url()."?id=".$id,'GET', null, $headers);

		// @todo better error handling
		$responseXML = $response->getBody();

		$fileIdentifier = null;

		$doc  = new DOMDocument();
		$doc->loadXML($responseXML);

		$xpath = new DOMXPath($doc);
		$xpath->registerNamespace("gmd", "http://www.isotc211.org/2005/gmd");
		$xpath->registerNamespace("gco", "http://www.isotc211.org/2005/gco");
		$xpath->registerNamespace("csw", "http://www.opengis.net/cat/csw/2.0.2");

		$metadataList = $xpath->query('/gmd:MD_Metadata');
		foreach($metadataList as $metadata) {
			$list = $xpath->query('gmd:fileIdentifier/gco:CharacterString',$metadata);
			if ($list->length > 0) {
				$fileIdentifier = $list->item(0)->nodeValue;
			}
		}
		return $fileIdentifier;
	}
}
