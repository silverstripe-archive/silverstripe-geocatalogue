<?php
/**
 * @author Rainer Spittel (rainer at silverstripe dot com)
 * @package geocatalog
 * @subpackage commands
 */

/**
 * Perform a insert request to a GeoNetwork node.
 * 
 */
class GnInsertCommand extends GnAuthenticationCommand {

	private $gnID = null;

	private $uuid = null;

	/**
	 * Returns the UUID of the new metadata record.
	 * @return string
	 */
	public function get_uuid() {
		return $this->uuid;
	}
	
	/**
	 * Returns the GeoNetwork internal ID of the new metadata record.
	 * @return int
	 */
	public function get_gnid() {
		return $this->gnID;
	}
	
	public function get_api_url() {
		$config = Config::inst()->get('Catalogue', 'geonetwork');
		$version = $config['api_version'];
		return $config[$version]['geonetwork_url'].'.insert';
	}

	public function get_automatic_publishing() {
		return Config::inst()->get('Catalogue', 'automatic_publishing');
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
	 * @throws GeonetworkInsertCommand_Exception
	 *
	 * @return int GeoNetwork internal ID
	 */
	public function execute() {
		
		$controller = Controller::curr();

		// get GeoNetwork Page type
		$page = $controller->data();

		$data = $this->getParameters();
		$params = $data['RequestParameter'];

		$this->restfulService = new RestfulService($this->getController()->getGeoNetworkBaseURL(),0);
		if ($this->getUsername() ) {
			$this->restfulService->basicAuth($this->getUsername(), $this->getPassword());
		}

		// insert metadata into GeoNetwork
		$headers = array('Content-Type: application/x-www-form-urlencoded');

		$response    = $this->restfulService->request($this->get_api_url(),'POST',$params, $headers);
		$responseXML = $response->getBody();

		// We expect a status code of 200 for the insert/getrecords and getrecordsbyid requests.
		if ($response->getStatusCode() != 200) {
			throw new GeonetworkInsertCommand_Exception('HTTP request return following response code:'.$response->getStatusCode());
		}

		// because we use the Geonetwork API, the error message are returned as HTML page.
		if ( strpos($responseXML, "<html>" ) === 0 ) {
			if ( strpos($responseXML, "Duplicate entry" ) != false ) throw new GeonetworkInsertCommand_Exception('GeoNetwork responded with an invalid HTML string.',101);
			throw new GeonetworkInsertCommand_Exception('GeoNetwork responded with an invalid HTML string.',100);
		}

        // read GeoNetwork ID from the response-XML document
        $doc  = new DOMDocument();
        $doc->loadXML($responseXML);
		$xpath = new DOMXPath($doc);

        $idList = $xpath->query('/response/id');
		$gnID = null;
		if ($idList->length > 0) {
			$gnID = $idList->item(0)->nodeValue;
		}

		if (!isset($gnID)) {
			throw new GeonetworkInsertCommand_Exception('GeoNetwork ID for the new dataset has not been created.');
		}

		// update metadata record and send an update to add the UUID to the record.
		$data = $this->getParameters();
		$data['gnID'] = $gnID;
		
		$cmd = $this->getController()->getCommand("GnGetUUIDOfRecordByID", $data);		
		$cmd->setUsername($page->Username);
		$cmd->setPassword($page->Password);	
		
		$uuid = $cmd->execute();

		if (!isset($uuid)) {
			throw new GeonetworkInsertCommand_Exception("New metadata record has been created, but GeoNetwork can not provide the UUID for the new record."); 
		}

		// generate update GeoNetwork HTTP request (query metadata).
		if ($this->get_automatic_publishing()) {
			$cmd = $this->getController()->getCommand("GnPublishmetadata", $data);
			$cmd->setUsername($page->Username);
			$cmd->setPassword($page->Password);	
			$cmd->execute();
		}
		$this->gnID = $gnID;
		$this->uuid = $uuid;
		
		// return the geonetwork id of the new entry.
		return $gnID;		
	}
}

/**
 * Customised Exception class.
 */
class GeonetworkInsertCommand_Exception extends Exception {}

