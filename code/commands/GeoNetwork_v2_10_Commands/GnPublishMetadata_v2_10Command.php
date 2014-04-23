<?php
/**
 * @author Rainer Spittel (rainer at silverstripe dot com)
 * @package geocatalog
 * @subpackage commands
 */

/**
 * Perform a insert request to a GeoNetwork node.
 */
class GnPublishMetadata_v2_10Command extends GnAuthenticationCommand {

	public function get_api_url() {
		return 'srv/eng/metadata.admin';
	}

	/**
	 * @throws GnPublishMetadataCommand_Exception
	 */
	public function execute() {
		$data       = $this->getParameters();
		$gnID 		= $data['gnID'];

		$this->restfulService = new RestfulService($this->getController()->getGeoNetworkBaseURL(),0);
		if ($this->getUsername() ) {
			$this->restfulService->basicAuth($this->getUsername(), $this->getPassword());
		}

		$data = array();
		$data['id']       = $gnID;
		$data['_1_0']      = "on";
		$data['_1_1']      = "on";
		$data['_1_3']      = "on";
		$data['_3_0']      = "on";
		$data['_3_1']      = "on";
		$data['_3_3']      = "on";
		$params = GnCreateInsertCommand::implode_with_keys($data);

		$response    = $this->restfulService->request($this->get_api_url()."?".$params,'GET');
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

