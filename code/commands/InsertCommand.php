<?php
/**
 * @author Rainer Spittel (rainer at silverstripe dot com)
 * @package geocatalogue
 * @subpackage commands
 */

/**
 * Perform a OGC insert requrest to a GeoNetwork node.
 * 
 * A OGC CSW Transaction allows the client to insert/register new metadata and
 * add those to the metadata repository.
 */
class InsertCommand extends ControllerCommand {

	
	/**
	 * Command execute
	 *
	 * Performs the command to insert/add new metadata. This command creates a 
	 * request (initiates a sub-command) and uses this to send of the 
	 * OGC request to GeoNetwork.
	 *
	 * @see CreateInsertCommand
	 *
	 * @return string OGC CSW uuid
	 */
	public function execute() {
		$data       = $this->getParameters();
		
		// generate GeoNetwork HTTP request (query metadata).
		$cmd = null;

		$cmd = $this->getController()->getCommand("CreateInsert", $data);
		$xml = $cmd->execute();

		// insert metadata into GeoNetwork
		$headers = array('Content-Type: application/xml');
		$this->restfulService = new GeoNetworkRestfulService(
			$this->getController()->getGeoNetworkBaseURL(),0
		);

		$response    = $this->restfulService->request('srv/en/csw','POST',$xml, $headers);	

		// @todo better error handling
		$responseXML = $response->getBody();
		
		// parse catalogue response
		$data = array(
			'xml' => $responseXML,
			'xsl' => '../geocatalogue/xslt/insertResponse.xsl',
		);

		$cmd = $this->getController()->getCommand("TranslateXML", $data);
		$xml = $cmd->execute();
		
		// toDo: bad! use JSON
		eval(trim($xml));
		
		if (!isset($uuid)) {
			throw new InsertCommand_Exception('Global ID for the new dataset has not been created.');
		}

		if (!isset($totalInserted) || $totalInserted != 1) {
			throw new InsertCommand_Exception('Insert operation failed. Total number of inserted entries: '.$totalInserted );
		}
	 	
		return $uuid;		
	}

}

/**
 * Customised Exception class.
 */
class InsertCommand_Exception extends Exception {}

