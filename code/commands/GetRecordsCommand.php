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
 *
 * @throws GeoNetworkRestfulService_Exception
 */
class GetRecordsCommand extends GnAuthenticationCommand
{
    
    public static $catalogue_url = 'srv/en/csw';
    
    public static function get_catalogue_url()
    {
        return self::$catalogue_url;
    }
    
    public static function set_catalogue_url($value)
    {
        self::$catalogue_url = $value;
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
    public function execute()
    {
        $data = $this->getParameters();

        // generate GeoNetwork HTTP request (query metadata).
        $cmd = null;
        
        $cmd = $this->getController()->getCommand("CreateRequest", $data);
        $xml = $cmd->execute();
        
        // send requrest to GeoNetwork
        $this->restfulService = new GeoNetworkRestfulService($this->getController()->getGeoNetworkBaseURL(), 0);

        if ($this->getUsername() != '') {
            $this->restfulService->setUsername($this->getUsername());
            $this->restfulService->setPassword($this->getPassword());
            $this->restfulService->setRequireAuthentication(true);
        }

        $headers     = array('Content-Type: application/xml');
        $response    = $this->restfulService->request(self::get_catalogue_url(), 'POST', $xml, $headers);
        
        // @todo better error handling
        $responseXML = $response->getBody();
        return $responseXML;
    }
}
