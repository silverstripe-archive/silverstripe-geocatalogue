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
class GetRecordByIdCommand extends GnAuthenticationCommand {

	public function get_catalogue_url() {
		$config = Config::inst()->get('Catalogue', 'geonetwork');
		$version = $config['api_version'];
		return $config[$version]['csw_url'];
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

		// generate GeoNetwork HTTP request (query metadata).
		$cmd = $this->getController()->getCommand("CreateRecordByIdRequest", $data);
		$xml = $cmd->execute();

		// send request to GeoNetwork
		$restfulService = $this->getRestfulService();

		$headers     = array('Content-Type: application/xml');
		$response    = $restfulService->request($this->get_catalogue_url(),'POST',$xml, $headers);
		return $response->getBody();
	}
	
}