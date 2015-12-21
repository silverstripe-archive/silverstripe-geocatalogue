<?php
/**
 * @author Rainer Spittel (rainer at silverstripe dot com)
 * @package geocatalog
 * @subpackage commands
 */

/**
 * Perform a search on the catalogues metadata repository.
 *
 * Base class to create OGC CSW requests to retrieve Metadata from a GeoNetwork
 * or OGC compliant Metadata catalogue.
 * This command uses the xml-templates to generate the requests. The command
 * expect a request-xml parameter which contains the name of the xml file to 
 * create the OGC getRecord request.
 */
class CreateRequestCommand extends ControllerCommand
{
    
    /**
     * Command execute
     *
     * Perform the request command. Creates a XML request based on the given
     * XML request parameter and the search term. This request is used to create
     * OGC compliant search requests and can be extended.
     *
     * @return string - XML CSW requests which can be used to access an OGC CSW 2.0.1 web service.
     */
    public function execute()
    {
        $data = $this->getParameters();
        
        if (!isset($data['startPosition'])) {
            $data['startPosition'] = 0;
        }

        if (!isset($data['maxRecords'])) {
            $data['maxRecords'] = 10;
        }

        if (!isset($data['searchterm'])) {
            throw new CreateRequestCommand_Exception('Exception: Undefined searchTerm');
        }

        if (!isset($data['requestxml'])) {
            throw new CreateRequestCommand_Exception('Exception: Undefined requestxml');
        }
        
        if (!isset($data['sortBy'])) {
            $data['sortBy'] = 'title';
        }
        
        if (!isset($data['sortOrder'])) {
            $data['sortOrder'] = 'asc';
        }

        if (!isset($data['bboxUpper'])) {
            $data['bboxUpper'] = '';
        }

        if (!isset($data['bboxLower'])) {
            $data['bboxUpper'] = '';
            $data['bboxLower'] = '';
        }
        
        $requestXML    = $data['requestxml'];
        $searchTerm    = $data['searchterm'];
        $startPosition    = $data['startPosition'];
        $maxRecords    = $data['maxRecords'];
        $sortBy    = $data['sortBy'];
        $sortOrder    = $data['sortOrder'];
        $bboxUpper    = $data['bboxUpper'];
        $bboxLower    = $data['bboxLower'];
        
        $WordsToSearchFor = array();
        if ($searchTerm) {
            // if we have a searchterm
            // split it by any number of commas or space characters,
            // which include " ", \r, \t, \n and \f
            $WordsToSearchFor = preg_split("/[\s,]+/", $searchTerm, -1, PREG_SPLIT_NO_EMPTY);
        }
        
        $DOB=new ArrayList();
        foreach ($WordsToSearchFor as $word) {
            $DOB->push(new ArrayData(array("word" => $word)));
        }
        // retrieve as Dublin-Core/ISO 19139 metadata schema
        $obj = new ViewableData();
        $fields = array(
            "searchTerm"    => $searchTerm,
            "startPosition" => $startPosition,
            "maxRecords"    => $maxRecords,
            "WordsToSearchFor" => $DOB,
            "sortBy" => $sortBy,
            "sortOrder" => $sortOrder,
            "bboxUpper" => $bboxUpper,
            "bboxLower" => $bboxLower
        );
        
        $obj->customise($fields);

        // render XML request
        $requestXML = $obj->renderWith($requestXML);
        return $requestXML->value;
    }
}

/**
 * Customised Exception class
 */
class CreateRequestCommand_Exception extends Exception
{
}
