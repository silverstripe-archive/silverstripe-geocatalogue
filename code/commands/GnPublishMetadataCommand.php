<?php
/**
 * @author Rainer Spittel (rainer at silverstripe dot com)
 * @package geocatalog
 * @subpackage commands
 */

/**
 * Perform a insert request to a GeoNetwork node.
 */
class GnPublishMetadataCommand extends GnAuthenticationCommand {

	public function get_api_url() {
		$config = Config::inst()->get('Catalogue', 'geonetwork');
		$version = $config['api_version'];
		return $config[$version]['geonetwork_url'].'.privileges';
	}

	/**
	 * Command execute
	 *
	 * Performs the command to insert/add new metadata. This command creates a 
	 * request (initiates a sub-command) and uses this to send of the 
	 * OGC request to GeoNetwork.
	 *
	 * @see CreateInsertCommand
	 *
	 * @return string OGC CSW response
	 */
	public function execute() {
		$data       = $this->getParameters();
		$gnID 		= $data['gnID'];

		$this->restfulService = new RestfulService($this->getController()->getGeoNetworkBaseURL(),0);
		if ($this->getUsername() ) {
			$this->restfulService->basicAuth($this->getUsername(), $this->getPassword());
		}

		// build the parameters for the publish request. It is a structure of
		// a geonetwork form to publish the data to non-registered users and
		// allow the download of assigned data sources.
		$data = array();
		$data['id']       = $gnID;
		$data['_1_0']      = "on";
		$data['_1_1']      = "on";
		$params = GnCreateInsertCommand::implode_with_keys($data);

		$headers     = array('Content-Type: application/x-www-form-urlencoded');
		$response    = $this->restfulService->request($this->get_api_url(),'POST',$params, $headers);
		// @todo better error handling
		$responseXML = $response->getBody();

        // read GeoNetwork ID from the response-XML document
        $doc  = new DOMDocument();
        $doc->loadXML($responseXML);
		$xpath = new DOMXPath($doc);

        $idList = $xpath->query('/response/id');
		$response_gnID = null;
		if ($idList->length > 0) {
			$response_gnID = $idList->item(0)->nodeValue;
		}

		if (!isset($response_gnID)) {
			throw new GnPublishMetadataCommand_Exception('GeoNetwork ID for the new dataset has not been created.');
		}
		if ($gnID != $response_gnID) {
			throw new GnPublishMetadataCommand_Exception('GeoNetwork publication has failed.');
		}
		return $gnID;
	}

}

/**
 * Customised Exception class.
 */
class GnPublishMetadataCommand_Exception extends Exception {}

