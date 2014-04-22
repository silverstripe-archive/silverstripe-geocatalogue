<?php
/**
 * Created by PhpStorm.
 * User: rspittel
 * Date: 22/04/14
 * Time: 12:56 PM
 */
class GnInsert_v2_10Command extends GnAuthenticationCommand {

	private $gnID = null;

	private $uuid = null;

	private $doMetadata = null;

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

	public function setDOMetadata($metadata) {
		$this->doMetadata = $metadata;
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
		DEBUG::log('GnInsert_v2_10Command executed');

		$controller = Controller::curr();

		// get GeoNetwork Page type
		$page = $controller->data();

		$data = $this->getParameters();
		$params = $data['RequestParameter'];

		$this->restfulService = new RestfulService($this->getController()->getGeoNetworkBaseURL(),0);
		$this->restfulService->setConnectTimeout(10);
		if ($this->getUsername() ) {
			$this->restfulService->basicAuth($this->getUsername(), $this->getPassword());
		}

		// insert metadata into GeoNetwork
		$headers = array('Content-Type: application/x-www-form-urlencoded');

		$response    = $this->restfulService->request($this->get_api_url(),'POST',$params, $headers);
		$responseXML = $response->getBody();

		// We expect a status code of 200 for the insert/getrecords and getrecordsbyid requests.
		if ($response->getStatusCode() != 200) {
			throw new GeonetworkInsertCommand_Exception('HTTP request return following response code:'.$response->getStatusCode().' - '.$response->getStatusDescription());
		}

		// because we use the Geonetwork API, the error message are returned as HTML page.
		if (strpos($responseXML, "<html>") === 0 ) {
			if ( strpos($responseXML, "Duplicate entry" ) != false ) {
				throw new GeonetworkInsertCommand_Exception('GeoNetwork responded with an invalid HTML string.',101);
			}
			throw new GeonetworkInsertCommand_Exception('GeoNetwork responded with an invalid HTML string.',100);
		}

        // read GeoNetwork ID from the response-XML document
        $doc  = new DOMDocument();
        $doc->loadXML($responseXML);
		$xpath = new DOMXPath($doc);

		// get ID
		$gnID = null;
		$idList = $xpath->query('/response/id');
		if ($idList->length > 0) $gnID = $idList->item(0)->nodeValue;

		if (!isset($gnID)) {
			throw new GeonetworkInsertCommand_Exception('GeoNetwork ID for the new dataset has not been created.');
		}

		// get UUID based on GeoNetwork version used in the backend. The version 2.10+ provides a more effective API and
		// does not require the command to send of another request to retrieve the UUID of the new record.
		$uuid = null;
		$idList = $xpath->query('/response/uuid');
		if ($idList->length > 0) {
			$uuid = $idList->item(0)->nodeValue;
		}
		if (!isset($uuid)) {
			throw new GeonetworkInsertCommand_Exception("GeoNetwork UUID (fileIdentifier) for the new dataset has not been created.");
		}

		// update metadata record and send an update to add the UUID to the record.
		$data = $this->getParameters();
		$data['gnID'] = $gnID;
		$data['UUID'] = $uuid;

		// @2to resolve once GeoNetwork is fixed
		// BAD HACK TO WORK AROUND SEVERAL GEONETWORK 2.10 BUGS
		{
			// THE UUID is not populated into hte document be default as it should.
			// I make a FORM submission on the GeoNetwork User Interface with the
			// UUID data added to the XML and then parse if I receive a Form back to verify
			// the submission was successful.
			$metadata = $this->doMetadata;
			$metadata->fileIdentifier = $uuid;

			$cmd = $this->getController()->getCommand("GnCreateUpdate", array('MDMetadata' => $metadata, 'id' => $gnID, 'version' => 1));
			$result = $cmd->execute();

			$response    = $this->restfulService->request('srv/eng/metadata.update','POST',$result, $headers);

			if ($response->getStatusCode() != 200) {
				throw new GeonetworkInsertCommand_Exception('HTTP request return following response code: '.$response->getStatusCode().' - '.$response->getStatusDescription());
			}

			// Spot check for some data in the response. We do not parse the html page completely.
			// The check verifies if the form-label for field identifier exists and if the uuid appears.
			{
				$responseXML = $response->getBody();
				if (strpos($responseXML, "gmd:fileIdentifier|gmd:MD_Metadata|gmd:MD_Metadata/gmd:") === false ) {
					throw new GeonetworkInsertCommand_Exception('The global file identifier has not been created. Please contact your system administrator.');
				}

				if (strpos($responseXML, $uuid) === false ) {
					throw new GeonetworkInsertCommand_Exception('The global file identifier has not been created. Please contact your system administrator.');
				}
			}

			// At this stage we assume that GeoNetwork returned the form with the uuid fields populated which implies
			// that the update was successful.
			// The application can continue with the publication now.
		}

		// generate update GeoNetwork HTTP request (query metadata).
		if ($this->get_automatic_publishing()) {
			$cmd = $this->getController()->getCommand("GnPublishMetadata_v2_10", $data);
			$cmd->setUsername($page->Username);
			$cmd->setPassword($page->Password);
			$cmd->execute();
		}
		$this->gnID = $gnID;
		$this->uuid = $uuid;

		return $gnID;
	}
}
