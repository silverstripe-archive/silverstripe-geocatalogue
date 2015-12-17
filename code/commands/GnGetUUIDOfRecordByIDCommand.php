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
class  GnGetUUIDOfRecordByIDCommand extends GnAuthenticationCommand
{

    /**
     * @var string $api_url refers the the GeoNetwork action to perform the publish process.
     */
    public static $api_url = 'srv/en/xml.metadata.get?';
    
    public static $RequireAuth = true;

    public static $xsl_path = '../geocatalogue/xslt/gnParseUUID.xsl';

    private $username = '';
    
    private $password = '';

    public static function get_api_url()
    {
        return self::$api_url;
    }
    
    public static function set_api_url($value)
    {
        self::$api_url = $value;
    }

    public static function get_xsl_path()
    {
        return self::$xsl_path;
    }
    
    public static function set_xsl_path($xsl_path)
    {
        self::$xsl_path = $xsl_path;
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
    public function execute()
    {
        $data       = $this->getParameters();
        
        $id = $data['gnID'];
        
        try {
            $this->restfulService = new GeoNetworkRestfulService($this->getController()->getGeoNetworkBaseURL(), 0);
            
            if ($this->getUsername()) {
                $this->restfulService->setUsername($this->getUsername());
                $this->restfulService->setPassword($this->getPassword());
                $this->restfulService->setRequireAuthentication(true);
            }
        } catch (CataloguePage_Exception $e) {
            throw new GnGetUUIDOfRecordByIDCommand_Exception($e->getMessage());
        }
        
        // generate GeoNetwork HTTP request (query metadata).
        $cmd = null;

        // insert metadata into GeoNetwork
        $headers = array('Content-Type: application/x-www-form-urlencoded');
        $response = $this->restfulService->request($this->get_api_url()."id=".$id, 'GET', null, $headers);

        // @todo better error handling
        $responseXML = $response->getBody();

        // parse catalogue response
        $data = array(
            'xml' => $responseXML,
            'xsl' => self::get_xsl_path()
        );

        $cmd = $this->getController()->getCommand("ParseXML", $data);
        $result = $cmd->execute();

        // render metadata data-structure
        $SearchRecord = $result->__get('Items');
        
        if ($SearchRecord->TotalItems() != 1) {
            throw new GnGetUUIDOfRecordByIDCommand_Exception('Unexpected GeoNetwork response. Can not locate or identify the UUID of the provided dataset.');
        }
        $metadata = $SearchRecord->First();
        
        $uuid = $metadata->fileIdentifier;
        return $uuid;
    }
}

/**
 * Customised excpetion class
 */
class GnGetUUIDOfRecordByIDCommand_Exception extends Exception
{
}
