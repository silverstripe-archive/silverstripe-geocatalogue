<?php
/**
 * @author Rainer Spittel (rainer at silverstripe dot com)
 * @package geocatalog
 * @subpackage code
 */

/**
 * Main Catalogue Page
 *
 * This page type is used to handle a GeoNetwork search and search-result
 * visualization. The @see CataloguePage.ss template is used to visualise the
 * search form and the search result.
 */
class CataloguePage extends Page {

    public static $db = array('ResultsPerSearchPage' => "Int",
                              'GeonetworkBaseURL' => "Varchar",
                              'GeonetworkUsername' => "Varchar",
                              'GeonetworkPassword' => "Varchar",);
    static $defaults = array('ResultsPerSearchPage' => 10);
    /**
     * This variable defines the status of the website. If it is set to
     * 'setup', the GoeNetwork configuration fields are editable. Otherwise
     * it will be set to read-only. Just a security precaution to prevent
     * users to change the GeoNetwork configuration by accident.
     * @var string
     */
    protected static $siteStatus = 'live';

    static function set_site_status($value) {
        CataloguePage::$siteStatus = $value;
    }

    /**
     *
     * @return FieldList
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();

        Requirements::javascript('geocatalogue/javascript/GeonetworkUrlValidator.js');

        $fields->addFieldsToTab('Root.Catalog', array(new TextField('GeonetworkBaseURL', 'The base URL of the GeoNetwork-Server you want to connect to:'),
                                                      new TextField('GeonetworkUsername', 'GeoNetwork username'),
                                                      new PasswordField('GeonetworkPassword', 'Geonetwork password'),
                                                      new TextField('ResultsPerSearchPage', 'How many results per page (1 .. 99):')));

        if (CataloguePage::get_site_status() != 'setup') {
            $fields->makeFieldReadonly('GeonetworkBaseURL');
            $fields->makeFieldReadonly('GeonetworkUsername');
            $fields->makeFieldReadonly('ResultsPerSearchPage');

            $fields->removeByName('GeonetworkPassword');
        }
        // return the modified fieldset.
        return $fields;
    }

    static function get_site_status() {
        return CataloguePage::$siteStatus;
    }

    /**
     * Make sure Geonetwork url ends with an /.
     */
    function onBeforeWrite() {
        parent::onBeforeWrite();
        $this->GeonetworkBaseURL = $this->addEndingSlash($this->GeonetworkBaseURL);
    }

    /**
     * Adds an ending slash to a string
     * For url, ensures that the URL always has one ending '/'. No
     * pattern matching are performed, i.e. testing syntax or protocol.
     *
     * @param $url
     * @return string $url with ending '/'
     */
    private function addEndingSlash($url) {
        if (strlen($url) > 1) {
            $length = strlen($url) - 1;

            if ($url[$length] != '/') $url .= '/';
        }
        return $url;
    }


    public function getMaxRecordsPerPage() {
        $result = (int)$this->ResultsPerSearchPage;

        if (!isset($result) || $result == 0) {
            $result = $this->initdefaults['pageresults'];
        }
        return $result;
    }
}

/**
 * Controller Class for Main Catalogue Page
 *
 * Page controller class for Catalogue-Page (@link CataloguePage). The controller
 * class handles the requests and delegates the requests to the page instance
 * as well as to the available GeoNetwork node.
 */
class CataloguePage_Controller extends Page_Controller {

    private static $allowed_actions = array('dogetrecordbyid', 'dogetrecords');

    protected $maxRecords = 10;

    public static function get_search_form_name() {
        return Config::inst()->get('Catalogue', 'search_form');
    }

    public function getGeoNetworkBaseURL() {
        $url = $this->data()->GeonetworkBaseURL;
        if (!isset($url) || $url == '') {
	        throw new CataloguePage_Exception('URL to Metadata Catalogue not defined.');
        }
        return $url;
    }

    public function init() {
        parent::init();
        Requirements::css(Config::inst()->get('Catalogue', 'cssfile'));
    }

    /**
     * This action retrieves a set of records from the GeoNetwork catalog and uses the SSViewer template
     * to render the list of results.
     *
     * @param SS_HTTPRequest $request
     * @return string
     *
     * @throws CataloguePage_Exception
     */
    public function dogetrecords(SS_HTTPRequest $request) {

        $format = Config::inst()->get('Catalogue', 'metadata_standard');

        if (!$this->validateRequestFormat($format)) {
            throw new CataloguePage_Exception('Invalid configuration: metadata format not supported.');
        }

        $params = $this->processRequestParameters($request);
        $query = $this->getQueryClass($params);

        try {
            $query->validate();
        } catch (CataloguePage_Exception $exception) {
            $this->redirectBack();
            return;
        }

        $standards = Config::inst()->get('Catalogue', 'standard_definitions');

        $data = array('searchterm' => Convert::raw2xml($query->get('searchTerm')),
                      'requestxml' => $standards[$format]['request'],
                      'startPosition' => $query->get('startPosition') > 0 ? $query->get('startPosition') : 1,
                      'maxRecords' => $this->data()->getMaxRecordsPerPage() > 0 ? $this->data()->getMaxRecordsPerPage() : 1,
                      'sortBy' => $query->get('sortBy'),
                      'sortOrder' => $query->get('sortOrder'),
                      'bboxUpper' => $query->get('bboxUpper'),
                      'bboxLower' => $query->get('bboxLower')
        );

        try {
            $cmd = $this->getCommand("GetRecords", $data);
            $cmd->setUsername($this->owner->GeonetworkUsername);
            $cmd->setPassword($this->owner->GeonetworkPassword);
            $responseXML = $cmd->execute();
        } catch (GeoNetworkRestfulService_Exception $exception) {
            $prefix = $this->prefixx();
            $mess = 'Unfortunately the query process failed due to a technical problem. Please retry later.';
            Session::set($prefix . ".errors.message", $mess);
            Session::set($prefix . ".errors.messageType", 'Error');
            return $this->render();
        }

        // parse response
        try {
            $resultSet = $this->parseResponse($format, $responseXML, $query->get('searchTerm'));
        } catch (Exception $exception) {
            $prefix = $this->prefixx();
            $mess = 'Unfortunately the query process failed due to a technical problem. Please retry later.';
            Session::set($prefix . ".errors.message", $mess);
            Session::set($prefix . ".errors.messageType", 'Error');

            // @todo better error handling
            $this->redirectBack();
            return;
        }

        $this->data()->result_items = $resultSet->__get('Items');
        $this->data()->search_term = $query->get('searchTerm');

	    $this->data()->sortBy = $query->get('sortBy');
	    $this->data()->sortOrder = $query->get('sortOrder');
	    $this->data()->bboxUpper = $query->get('bboxUpper');
	    $this->data()->bboxLower = $query->get('bboxLower');

        // calculate pagination values
        $this->calculatePaginationValues($resultSet, $query);
        return $this->render();
    }

    /**
     * This action returns a rendered string of an individual metadata record (i.e. MCP metadata record). The optional
     * parameter OtherID can be used to retrieve the raw XML document.
     *
     * @param $httpRequest
     * @return mixed|string
     *
     * @throws CataloguePage_Exception
     */
    public function dogetrecordbyid($httpRequest) {
        $params = $httpRequest->allParams();

        if (!$params['ID']) {
            Director::redirectBack();
            return;
        }

        if (!$params['OtherID']) {
            $params['OtherID'] = "html";
        }

        $id = $params['ID'];

        $outputFormat = $params['OtherID'];

        $data = array('fileIdentifier' => Convert::raw2xml($id),
                      'outputFormat' => Convert::raw2xml($outputFormat),
                      'requestxml' => 'cswGetRecordByID_xml');

        // get XML document for requested metadata entry.

        try {
            $cmd = $this->getCommand("GetRecordById", $data);
            $cmd->setUsername($this->owner->GeonetworkUsername);
            $cmd->setPassword($this->owner->GeonetworkPassword);
            $responseXML = $cmd->execute();
        } catch (GeoNetworkRestfulService_Exception $exception) {
            $prefix = $this->prefixx();
            $mess = 'Unfortunately the query process failed due to a technical problem. Please retry later.';
            Session::set($prefix . ".errors.message", $mess);
            Session::set($prefix . ".errors.messageType", 'Error');

            // @TODO add error message
            return $this->render();
        }

        // If xml representation is requestet, forward the answer
        // using {$uuid}.xml as filename
        if ($outputFormat == 'xml') {

            // removing the geonetwork XML envelope from the document:
            // '<csw:GetRecordByIdResponse xmlns:csw="http://www.opengis.net/cat/csw/2.0.2">'
            $responseXML = preg_replace('/((\<csw\:GetRecordByIdResponse)|(<\/csw\:GetRecordByIdResponse)).*\>/', '', $responseXML);

            // return XML document
            $resp = $this->getResponse();
            $resp->addHeader("Content-Type", "text/xml");
            $resp->addHeader('Content-Disposition', "attachment; filename=\"" . $id . ".xml\";");
            return $responseXML;
        }

        // parse XML response and create the SilverStripe data-structure.
        $standards = Config::inst()->get('Catalogue', 'standard_definitions');
	    $format = $this->getFormat($responseXML);

		if (!$this->validateRequestFormat($format)) {
			throw new CataloguePage_Exception('Invalid configuration: metadata format not supported.');
		}

        $data = array('xml' => $responseXML);

        $classname = $standards[$format]['full_response'];
        $cmd = $this->getCommand($classname, $data);

        $result = $cmd->execute();
        // echo('<pre>'); print_r($result);echo('<pre>');

        // render metadata data-structure
        $this->data()->result_item = $result->__get('Items');
        return $this->render();
    }

    /**
     * Verifies that the requested metadata definition does exist in the configuration settings.
     *
     * @param $format
     * @return bool
     */
	protected function validateRequestFormat($format) {
        $standards = Config::inst()->get('Catalogue', 'standard_definitions');
        return isset($standards[$format]);
    }

    /**
     * Returns the standard query class, used to create the GeoNetwork requests.
     *
     * @param $params
     * @return Catalogue_QueryClass
     */
    protected function getQueryClass($params) {
        return new Catalogue_QueryClass($params);
    }


    public function prefixx() {
        return $this->URLSegment;
    }

    /**
     * @param $standard
     * @param $responseXML
     * @param $searchTerm
     * @return mixed
     */
    public function parseResponse($standard, $responseXML, $searchTerm) {

	    $standards = Config::inst()->get('Catalogue', 'standard_definitions');

	    $data = array('xml' => $responseXML);
        $cmd = $this->getCommand($standards[$standard]['response'], $data);

        $result = $cmd->execute();

        // add the search term to the response (to populate the default search
        // term into the search page.
        if ($result->customisedObj) {
            $result->customisedObj->customise(array('searchTerm' => $searchTerm));
        } else {
            $result->customise(array('searchTerm' => $searchTerm));
        }

        return $result;
    }

    /**
     * @param $resultSet
     * @param $query
     */
	protected function calculatePaginationValues($resultSet, $query) {

        $nextRecord = $resultSet->__get('nextRecord');
        $startPosition = $query->get('startPosition');

        $temp = floor($startPosition / $this->maxRecords) + 1;
        $this->data()->pagination_page_number = $temp;
        $this->data()->pagination_is_first_page = ($temp == 1) ? true : false;

        $matchedRecords = $resultSet->__get('numberOfRecordsMatched');

        $temp = ceil($matchedRecords / $this->maxRecords);

	    $this->data()->number_of_records_matched = $matchedRecords;
        $this->data()->pagination_number_of_pages = $temp;
        $this->data()->pagination_is_last_page = ($this->data()->pagination_page_number == $this->data()->pagination_number_of_pages) ? true : false;
	    $this->data()->pageindex_of_last_pages = (($this->data()->pagination_number_of_pages-1) * $this->maxRecords) + 1;

        $temp = $nextRecord;
        if ($nextRecord > $matchedRecords) {
            $temp = $startPosition;
        }
        $this->data()->pagination_next_index = $temp;

        $temp = $startPosition - $this->maxRecords;
        if ($temp < 1) $temp = 1;
        $this->data()->pagination_prev_index = $temp;

	    $numlinks = new ArrayList();

		$startNum = $this->data()->pagination_page_number < 3 ? 1 : ($this->data()->pagination_page_number - 2);
		$endNum = ($startNum + 10) > $this->data()->pagination_number_of_pages ? $this->data()->pagination_number_of_pages : ($startNum + 10);

		for ($i = $startNum; $i <= $endNum; $i++) {
			$linkData = array('label' => $i, 'linknum' => ($i - 1) * $this->maxRecords + 1);

			if ($i == $this->data()->pagination_page_number) {
				$linkData['current'] = true;
			}
			$numlinks->push(new ArrayData($linkData));
        }
		$this->data()->numlinks = $numlinks;
    }

    /**
     * @param SS_HTTPRequest $request
     * @return array
     */
	protected function processRequestParameters(SS_HTTPRequest $request) {
        $params = array();
        if ($request != null) {
            $params = $request->allParams();
            $variables = $request->getVars();

            $params['startPosition'] = $params['ID'];
            if ($params['startPosition'] < 1) $params['startPosition'] = 1;

            if ( isset($variables['searchTerm']) && $variables['searchTerm']) {
                $params['searchTerm'] = $variables['searchTerm'];
            }

            if ($params['OtherID']) {
                $params['searchTerm'] = $params['OtherID'];
            }

	        if ( isset($variables['sortBy']) && $variables['sortBy']) {
                $params['sortBy'] = $variables['sortBy'];
	        }

	        if ( isset($variables['sortOrder']) && $variables['sortOrder']) {
                $params['sortOrder'] = $variables['sortOrder'];
	        }

	        if ( isset($variables['bboxUpper']) && isset($variables['bboxLower']) && $variables['bboxUpper'] && $variables['bboxLower']) {
                $params['bboxUpper'] = $variables['bboxUpper'];
                $params['bboxLower'] = $variables['bboxLower'];
	        }

        }

        return $params;
    }

    /**
     * Return fieldset for the search form
     *
     * Create and return the search form for the metadata catalogue search.
     * This search form creates the simple search form.
     * @see get_search_form_name()
     *
     * @return Form Simple Search Form
     */
    public function GeoNetworkSearchForm() {

        $controller = Controller::curr();

        $httpRequest = $controller->getRequest();
        $httpParams = $httpRequest->allParams();

        $params = $this->getRequest()->requestVars();

        // default values
        $defaults = array();
        $defaults['searchTerm'] = '';
        $defaults['format'] = Config::inst()->get('Catalogue', 'metadata_standard');

        if ($params) {
            if (isset($params['searchTerm'])) {
                $defaults['searchTerm'] = $params['searchTerm'];
            } else {
                $defaults['searchTerm'] = $httpParams['OtherID'];
            }
        }

        if (isset($params['bboxUpper']) && isset($params['bboxLower'])) {
            $defaults['bboxUpper'] = $params['bboxUpper'];
            $defaults['bboxLower'] = $params['bboxLower'];
        }

        // create a searchForm (uses the static searchForm value)
        $searchForm = self::get_search_form_name();
        $form = new $searchForm($this, 'dogetrecords', null, null, null, $defaults);
        return $form;
    }

	/**
	 * This method parses the XML Record of the CSW server to determine which standard is used.
	 * Currently the system supports ANZLIC/ISO19139 and MCP only.
	 *
	 * @param $responseXML
	 *
	 * @return string
	 */
	protected function getFormat($responseXML) {
		$format = Config::inst()->get('Catalogue', 'metadata_standard');

		$doc = new DOMDocument();
		$doc->loadXML($responseXML);

		$xpath = new DOMXPath($doc);
		$xpath->registerNamespace("csw", "http://www.opengis.net/cat/csw/2.0.2");

		$responseDOM = $xpath->query('/csw:GetRecordByIdResponse');
		$searchResultItem = $responseDOM->item(0);

		foreach($searchResultItem->childNodes as $child) {
			if($child->nodeType == XML_ELEMENT_NODE) {
				if($child->nodeName == 'mcp:MD_Metadata') {
					$format = 'mcp';
				}
				else if($child->nodeName == 'gmd:MD_Metadata') {
					$format = 'anzlic';
				}
			}
		}
		return $format;
	}

}

/**
 * Catalogue-Page Exception Class
 */
class CataloguePage_Exception extends Exception {
}
