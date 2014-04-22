<?php
/**
 * @author Rainer Spittel (rainer at silverstripe dot com)
 * @package geocatalog
 * @subpackage commands
 */

/**
 * Perform a OGC search on the GeoNetwork node.
 * 
 * This command sends a request to the GeoNetwork node to search for Metadata 
 * records. It returns a XML string which is the plain XML catalogue response.
 */
class GetRecordsCommand extends GnAuthenticationCommand {

	public function get_catalogue_url() {
		$config = Config::inst()->get('Catalogue', 'geonetwork');
		$version = $config['api_version'];
		return $config[$version]['csw_url'];
	}
	
	/**
	 * Command execute
	 *
	 * Performs the command to search for metadata. This command creates a 
	 * request (initiates a sub-command) and uses this to send of the 
	 * OGC request to GeoNetwork.
	 *
	 * @see CreateRequestCommand
	 *
	 * @return string OGC CSW response
	 */
	public function execute() {
		
		$data = $this->getParameters();

		// generate GeoNetwork HTTP request (query metadata).
		$cmd = $this->getController()->getCommand("CreateRequest", $data);
		$xml = $cmd->execute();

		// send requrest to GeoNetwork
		$this->restfulService = new RestfulService($this->getController()->getGeoNetworkBaseURL(),0);
		if ($this->getUsername() ) {
			$this->restfulService->basicAuth($this->getUsername(), $this->getPassword());
		}

		$headers     = array('Content-Type: application/xml');
		$response    = $this->restfulService->request($this->get_catalogue_url(),'POST',$xml, $headers);
		
		$responseXML = $response->getBody();
		return $responseXML;
	}
}
