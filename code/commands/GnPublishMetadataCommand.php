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

	/**
	 * @var string $api_url refers the the GeoNetwork action to perform the publish process.
	 */
	static $api_url = 'srv/en/metadata.admin';
	static $RequireAuth = true;

//	static $xsl_path = '../geocatalogue/xslt/gnInsertResponse.xsl';

	static function get_api_url() {
		return self::$api_url;
	}
	
	static function set_api_url( $value ) {
		self::$api_url = $value;
	}

//	static function get_xsl_path() {
//		return self::$xsl_path;
//	}
//
//	static function set_xsl_path( $xsl_path ) {
//		self::$xsl_path = $xsl_path;
//	}
	
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
		try {
			$this->restfulService = new GeoNetworkRestfulService($this->getController()->getGeoNetworkBaseURL(),0);
			if ($this->getUsername() ) {
				$this->restfulService->setUsername($this->getUsername());
				$this->restfulService->setPassword($this->getPassword());
				$this->restfulService->setRequireAuthentication(true);
			}			
		}
		catch (CataloguePage_Exception $e) {
			throw new GnPublishMetadataCommand_Exception($e->getMessage());
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

