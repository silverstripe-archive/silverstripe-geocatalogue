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

	/**
	 * This variable defines the status of the website. If it is set to
	 * 'setup', the GoeNetwork configuration fields are editable. Otherwise
	 * it will be set to read-only. Just a security precaution to prevent 
	 * users to change the GeoNetwork configuration by accident.
	 * @var string
	 */
	protected static $siteStatus = 'live';

	/**
	 * Static variable to store data alongside with the page instance.
	 * @var array
	 */
	public static $db = array(
		'GeonetworkBaseURL' 	=> "Varchar", // The base URL for the GeoNetwork server
		'ResultsPerSearchPage'	=> "Int",     // Number of entries displayed on this search-result-page
		'GeonetworkUsername'	=> "Varchar", //Username for acccessing the GeoNwtwork server above
		'GeonetworkPassword'	=> "Varchar", //Password for acccessing the GeoNwtwork server above
	);
	
	static $defaults = array(
		'ResultsPerSearchPage' => 10
	);
	
/*	//Sanity-Check FOR CMS-Side has to be established 
	function setResultsPerSearchPage($maxresults){
		if($maxresults < 1) {$maxresults = 1;}	// keeping the lower limit
		if($maxresults > 99) {$maxresults = 99;} //keeping the upper limit
		$this->GetField('ResultsPerSearchPage') = $maxresults;
	}
*/

	/**
	 * Sets the CataloguePage::$siteStatus status, can be any string. In
	 * general, getCMSFields will check if the siteStatus is set to 'setup'
	 * to make the GeoNetwork fields editable.
	 *
	 * @see getCMSFields
	 * @see siteStatus
	 * @see get_site_status
	 *
	 * @param string $value any string, use 'setup' to make the fields editable.
	 */
	static function set_site_status($value) {
		CataloguePage::$siteStatus = $value;
	}

	/**
	 * Returns the status string of the site.
  	 *
	 * @see getCMSFields
	 * @see siteStatus
	 * @see set_site_status
	 *
	 * @return string any string, getCMSFields validate against the value 'setup'.
	 */
	static function get_site_status() {
		return CataloguePage::$siteStatus;
	}

	
	/**
	 * Overwrites SiteTree.getCMSFields to change the CMS form behaviour, 
	*  i.e. by adding form fields for the additional attributes defined in 
	 * {@link CataloguePage::$db}.
	 */ 
	function getCMSFields() {
		$nicelength = 80;
		$fields = parent::getCMSFields();
		$proxyUrl = $this->Link('csw');
		$proxyUrlNice = $proxyUrl;
		
		$capabilityUrl = $this->Link('csw?request=GetCapabilities&service=CSW&acceptVersions=2.0.2&acceptFormats=application%2Fxml');
		$capabilityUrlNice = $capabilityUrl;

		if (strlen($proxyUrlNice) > $nicelength) {
			$proxyUrlNice = substr($proxyUrlNice,0,$nicelength-3).'...';
		}

		
		if (strlen($capabilityUrlNice) > $nicelength) {
			$capabilityUrlNice = substr($capabilityUrlNice,0,$nicelength-3).'...';
		}
	
		Requirements::javascript('geocatalog/javascript/GeonetworkUrlValidator.js');
		$fields->addFieldsToTab('Root.Content.Catalogue',
			array( 
				new TextField('GeonetworkBaseURL',	'The base URL of the GeoNetwork-Server you want to connect to:'),
				new TextField('GeonetworkUsername','GeoNetwork username'),
				new PasswordField('GeonetworkPassword','Geonetwork password'),
				new TextField('ResultsPerSearchPage', 'How many results per page (1 .. 99):')
			));

		$fields->addFieldsToTab('Root.Content.Catalogue',
			array( 
				new LiteralField('CSWProxytitle','<h3>GeoNetwork CSW proxy</h3>'),
				new LiteralField('CSWProxydesc', '<p>To use the CSW proxy (with uses the same credentials as this catalog page), simply use the following URL:</p><p></p>'),
				new LiteralField('CSWProxylink', sprintf('<p>Proxy base URL: <a href="%s" target="csw">%s</a></p>',$proxyUrl,$proxyUrlNice)),

				new LiteralField('CSWCapabilitieslink', sprintf('<p>Example GetCapability request: <a href="%s" target="csw">%s</a></p>',$capabilityUrl, $capabilityUrlNice))
			));
		
		if (CataloguePage::get_site_status() != 'setup') {
			$fields->makeFieldReadonly('GeonetworkBaseURL');
			$fields->makeFieldReadonly('GeonetworkUsername');
			$fields->makeFieldReadonly('ResultsPerSearchPage');

			$fields->removeByName('GeonetworkPassword');
		}
		// return the modified fieldset.
		return $fields;
	}
	
	/**
	 * Make sure Geonetwork url ends with an /.
	 */
	function onBeforeWrite(){
		parent::onBeforeWrite();
		$geoUrl = $this->GeonetworkBaseURL;
		if(strlen($geoUrl) > 1){
			$geoUrlLen = strlen($geoUrl)-1;
			if($geoUrl[$geoUrlLen] != '/'){
				$this->GeonetworkBaseURL .= '/';
			}
		}
		
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

	static public $default_metadata_standard = 'iso';

	/**
	 * Structure to maintain standards with getrecord commands.
	 */
	static protected $xml_response = array (
		'iso' => '../geocatalog/xslt/ISO19139/iso19139_to_silverstripe.xsl',
		'anzlic' => '../geocatalog/xslt/ISO19139/iso19139_to_silverstripe.xsl',
		'dublin' => '../geocatalog/xslt/DublicCore/dubliccore_to_silverstripe.xsl',
		'mcp' => '../geocatalog/xslt/MCP/mcp_to_silverstripe.xsl',
	);

	/**
	 * Structure to maintain standards with getrecordbyid commands.
	 */
	static protected $xml_full_response = array (
		'iso' => '../geocatalog/xslt/ISO19139/iso19139full_to_silverstripe.xsl',
		'mcp' => '../geocatalog/xslt/MCP/mcpfull_to_silverstripe.xsl'
	);

	/**
	 * Structure to maintain standards with labels (pure for front-end).
	 */
	static protected $standard_names = array (
		'iso'  => 'ISO19139',
		'anzlic' => 'ANZLIC Metadata Profile',
		'dublin' => 'Dublin Core',
		'mcp' => 'Australian Marine Community Profile MCP'
	);
	
	/**
	 * Structure to maintain standards with OGC CSW requests
	 */
	static protected $xml_request = array (
		'iso' => 'cswGetRecordsSummaryISO_xml',
		'anzlic' => 'cswGetRecordsSummaryISO_xml',
		'dublin' => 'cswGetRecordsSummaryDC_xml',
		'mcp' => 'cswGetRecordsSummaryISO_xml',
	);
	
	static protected $initdefaults = array(
		'searchTerm' => 'africa',
		'pageresults' => 10
	);

	
	static function get_xml_full_response() {
		return self::$xml_full_response;
	}
	
	static function get_standard_names() {
		return self::$standard_names;
	}

	static function add_xml_response($key,$value) {
		self::$xml_response[$key] = $value;
	}

	static function get_all_xml_responses() {
		return self::$xml_response;
	}
	
	static function get_xml_response($key) {
		if (isset(self::$xml_response[$key])) {
			return self::$xml_response[$key];
		}
		return null;
	}
	
	static function add_xml_request($key,$value) {
		self::$xml_request[$key] = $value;
	}

	static function get_all_xml_requests() {		
		return self::$xml_request;
	}

	static function get_xml_request($key) {		
		if (isset(self::$xml_request[$key])) {
			return self::$xml_request[$key];
		}
		return null;
	}
		
	/**
	* These variables are used for paging the request
	*/
	// On Backend
	protected $maxRecords = 5;
	protected $numberOfRecordsMatched = 0;
	protected $nextRecord = 1;
	protected $numberOfRecordsReturned = 0;
	// on frontend
	protected $startPosition = 1;
	protected $onpage=1;
	protected $ofpages=1;
	protected $numlinks;
	protected $LastStart=0;
	protected $PrevStart=0;
	protected $NextStart=0;	
	protected $sortBy="title";	
	protected $sortOrder="ASC";	
	protected $bboxUpper;
	protected $bboxLower;

	/**
	 * Variable to store the classname of the form class.
	 * @var String
	 */ 
	public static $searchFormName= "CatalogueSearchForm";

	/**
	 * Variable to store the search results temporarily in the controller class.
	 * @var DataObjectSet
	 */
	protected $SearchResultItems = null;
	
	/**
	 * Return the classname for the default metadata standard.
	 * @see $default_metadata_standard
	 *
	 * @return string classname. 
	 */
	public static function get_default_metadata_standard() {
		return self::$default_metadata_standard;
	}

	/**
	 * Set static variable for the default metadata standard.
	 * @see default_metadata_standard
	 *
	 * @param string $value default metadata standard.
	 */
	public static function set_default_metadata_standard($value) {
		self::$default_metadata_standard = $value;
	}

	/**
	 * Return the classname for the search-form.
	 * @see $searchFormName
	 *
	 * @return string classname. 
	 */
	public static function get_search_form_name() {
		return self::$searchFormName;
	}

	/**
	 * Set static variable for the search-form.
	 * @see searchFormName
	 *
	 * @param string $value New form-class name.
	 */
	public static function set_search_form_name($value) {
		self::$searchFormName = $value;
	}
	
	/**
	 * Get configured ResultsPerSearchPage.
	 *
	 * This method returns the number of records fetched from the GeoNetwork server. 
	 * The value is stored in the page class {@see CataloguePage} because we 
	 * might want to support multiple geonetwork nodes in one site, but each 
	 * accesses a different GeoNetwork node.
	 *
	 * @throws CataloguePage_Exception
 	 * 
	 * @return int ResultsPerSearchPage
	 */
	public function getConfiguredResultsPerSearchPage() {
		
		// get CataloguePage instance
		$page = $this->data();
		if (!isset($page)) throw new CataloguePage_Exception('Metadata Catalogue Page is not defined correctly.');

		// get the ResultsPerSearchPage value of that page.
		$rpp = $page->ResultsPerSearchPage;
		if (!isset($rpp) || $rpp == 0) $rpp = $this->initdefaults['pageresults']; 
		// return value.
		return $rpp;
	}

	/**
	 * CSW proxy service
	 *
	 * This method passes through GET or POST data to the CSW avoiding the need
	 * to expose the Geonetwork username and password to the public
	 *
	 * @param request $request HTTP Request object
	 */
	public function csw( $request ) {

		$restfulService = new GeoNetworkRestfulService($this->getGeoNetworkBaseURL(),0);
		$restfulService->setUsername($this->data()->GeonetworkUsername);
		$restfulService->setPassword($this->data()->GeonetworkPassword);
		$restfulService->setRequireAuthentication(true);
		
		$responseXML = '';
		try {
			if ($request->isGet()) {
				$params = http_build_query($request->getVars());
				$headers = array('Content-Type: application/xml');
				$response = $restfulService->request('srv/en/csw?'.$params,'GET','', $headers);	
			} else {
				$xml = $request->getBody();
				$headers = array('Content-Type: application/xml');
				$response = $restfulService->request('srv/en/csw','POST',$xml, $headers);	
			}
			$responseXML = $response->getBody();
		}
		catch (GeoNetworkRestfulService_Exception $e) {
			$response = $this->getResponse(); 
			$response->setStatusCode(500,'Connection to backend catalog failed. Please try again.');
			return $response;
		}
		
		$this->getResponse()->addHeader('Content-Type','application/xml;charset=UTF-8');
		
		return $responseXML;
	}

	/**
	 * Get GeoNetwork base url.
	 *
	 * This method returns the base url to the OGC CSW catalogue (GeoNetwork). 
	 * The url is stored in the page class {@see CataloguePage} because we 
	 * might want to support multiple geonetwork nodes in one site, but each 
	 * accesses a different GeoNetwork node.
	 *
	 * @throws CataloguePage_Exception
 	 * 
	 * @return string URL to the geonetwork node, i.e. "http://localhost:8080/geonetwork"
	 */
	public function getGeoNetworkBaseURL() {
		// get CataloguePage instance
		$page = $this->data();
		if (!isset($page)) throw new CataloguePage_Exception('Metadata Catalogue Page is not defined correctly.');

		// get GeoNetwork URL of that page.
		$url = $page->GeonetworkBaseURL;
		if (!isset($url) || $url == '') {
			
			// if this is a dev environment, use a local geonetwork (standard 
			// development environment setup).
			if (Director::isDev()) {
				$url = 'http://192.168.1.136:8080/geonetwork';
			} else {
				throw new CataloguePage_Exception('URL to Metadata Catalogue not defined.');
			}
		}
		// return base-url to GeoNetwork node.
		return $url;
	}


	/**
	 * Initialisation function that is run before any action on the controller is called.
	 */
	public function init() {
		parent::init();

		Requirements::themedCSS('layout');
		Requirements::themedCSS('typography');
		Requirements::themedCSS('form');
		Requirements::themedCSS('cataloguepage');
		
		$this->extend('extendInit');
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
	function GeoNetworkSearchForm() {

		$controller = Controller::curr();
		
		$httpRequest = $controller->getRequest();
		$httpParams = $httpRequest->allParams();


		$params = $this->getRequest()->requestVars();

		// default values
		$defaults = array();
		$defaults['searchTerm'] = 'africa';
		
		$defaults['format'] = self::get_default_metadata_standard();
		if ($params) {
			if (isset($params['searchTerm'])) {
				$defaults['searchTerm'] = $params['searchTerm'];
			} else {
				$defaults['searchTerm'] = $httpParams['OtherID'];
			}
			if (isset($params['format'])) $defaults['format'] = self::validate_request_format($params['format']);
		}

		if (isset($params['bboxUpper']) && isset($params['bboxLower'])) {
			$defaults['bboxUpper'] = $params['bboxUpper'];
			$defaults['bboxLower'] = $params['bboxLower'];
		}

		// create a searchForm (uses the static searchForm value)
		$searchForm = self::get_search_form_name();
		$form = new $searchForm($this,'dogetrecords', null, null, null, $defaults);		
		return $form;
	}
	
	/**
	 * Validate HTTP-Request parameter.
	 *
	 * Depending on the implementation, some might want to allow just certain
	 * types of searches. This method is used to enable the catalogue to customise
	 * search behaviour, i.e. allow the 'empty search' which will result in a 
	 * browsing capability. CataloguePage does not allow this type of search.
	 * If validation fails, this method will throw a CataloguePage_Exception exception.
	 *
	 * @param array $params http-request parameter
	 *
	 * @throws CataloguePage_Exception
	 */
	protected function validateRequest($params) {
		
    // validate the content of the http request.
    //
    if (!$params['searchTerm']) {
     
      if (!($params['bboxUpper'] && $params['bboxLower'])) {
        throw new CataloguePage_Exception('Search term is missing. Please enter a query.');
      }
		}
	}
	
	/**
	 * Parses XML responses, received from GeoNetwork.
	 *
	 * This method parses a given XML string and returns a DataObjectSet.
	 * This implementation parses different xml schemas, such as  Dublin-Core 
	 * (@link http://purl.org/dc/elements/1.1/) and ISO19139. 
	 * The method parses a summary search response. The dublin-core metadata 
	 * schema is embedded into a CSW-metadata envelope (@link http://www.opengis.net/cat/csw/2.0.2).
	 *
	 * @param string $standard name of the XML standard, i.e.: iso,dublin
	 * @param string $responseXML xml-string 
	 * @param string $searchTerm current search term, used for template output only
	 *
	 * @return DataObjectSet
	 */
	public function parseResponse($standard, $responseXML, $searchTerm) {

		// create command parameters
		$data = array(
			'xml' => $responseXML,
			'xsl' => self::get_xml_response($standard),
		);

		// parse the XML document
		$cmd    = $this->getCommand("ParseXML", $data);
		
		$result = $cmd->execute();

		// add the search term to the response (to populate the default search
		// term into the search page.
		if ($result->customisedObj) {
			$result->customisedObj->customise( 
				array('searchTerm' => $searchTerm) 
			);
		} else {
			$result->customise( array('searchTerm' => $searchTerm) );
		}
		
		// return the DataObjectSet
		return $result;
	}
	
	/* checks if there is a request schema assigned to the given format
	 * 
	 * @param string: $format the requested format
	 * 
	 * @return string: the init-default-format if there's no request schema assigned
	 */
	
	static function validate_request_format($format){
		// sanity check the format:
		if(isset($format) && self::get_xml_request($format)) {
			return $format;
		}
		return self::get_default_metadata_standard();
	}
  

	/**
	 * Action: search for metadata
	 *
	 * Perform a keyword search on a geonetwork node.
	 * This method is used to delegate a search request to the geonetwork catalog 
	 * and receives the response, parse the xml and generate an output via a ss-template.
	 *
	 * Current request expects a dublin-core xml response. Other response types
	 * need to be added (i.e. MCP, ISO19139 and ISO19115)
	 *
	 * @param array $data request parameters. Requires $data['searchterm'] or $data['bboxUpper'] and $data['bboxLower']
	 *
	 * @return string HTML output
	 *
	 * @todo add error message when geonetwork is down
	 */
  public function dogetrecords( $params ) {

    // a searchTerm will be searched with %term% for fulltext matching
		if (!isset($params['searchTerm']) ) {
			$params['searchTerm'] = '';
		}

		if (!isset($params['startPosition']) ) {
			$params['startPosition'] = 1;
    }

    // sortBy may be a special word such as popularity, rating or changeDate
    // or it may be an attribute such as title
    if (!isset($params['sortBy']) ) {
			$params['sortBy'] = "title";
    }

    $this->sortBy=$params['sortBy'];

    // sortOrder may be DESC or ASC
    if (!isset($params['sortOrder']) ) {
			$params['sortOrder'] = "ASC";
    }

    $this->sortOrder=$params['sortOrder'];

    if (!isset($params['bboxUpper']) || !isset($params['bboxLower'])) {
      $params['bboxUpper']=false;
      $params['bboxLower']=false;
    }

    $this->bboxUpper=$params['bboxUpper'];
    $this->bboxLower=$params['bboxLower'];

		$format = self::get_default_metadata_standard();
		if (isset($params['format'])) {
			$format = self::validate_request_format($params['format']);
		}		

		// validate the content of the http request. Can be customised 
		// in subclasses.
		try {
			$this->validateRequest($params);
		}
		catch(CataloguePage_Exception $exception) {
			Director::redirectBack();
			return;
		}

		// continue search operation
		$searchTerm = $params['searchTerm'];
		$this->startPosition=$params['startPosition'];
		if($this->startPosition < 1){$this->startPosition = 1;}
		$this->maxRecords= $this->getConfiguredResultsPerSearchPage();
		//setting it to the hardcoded 10 if is set to 0 in the admin
		if($this->maxRecords < 1) {
			$this->maxRecords= 10;
		}
		
		$searchTerm = Convert::raw2xml($searchTerm);

		//don't know where this piece is committed in r83366 when CommandFactory is even not exist
		$data = array(
			'searchterm' => Convert::raw2xml($searchTerm),
			'requestxml' => self::get_xml_request($format),
			'startPosition' => $this->startPosition,
			'maxRecords' => $this->maxRecords,
			'sortBy' => $this->sortBy,
			'sortOrder' => $this->sortOrder,
			'bboxUpper' => $this->bboxUpper,
			'bboxLower' => $this->bboxLower
		);
		
		try {
			$cmd = $this->getCommand("GetRecords", $data);
			$cmd->setUsername($this->owner->GeonetworkUsername);
			$cmd->setPassword($this->owner->GeonetworkPassword);
			$responseXML = $cmd->execute();
		}
		catch(GeoNetworkRestfulService_Exception $exception) {

			$prefix=$this->prefixx();
			$mess= 'Unfortunately the query process failed due to a technical problem. Please retry later.';
			Session::set($prefix . ".errors.message", $mess);
			Session::set($prefix . ".errors.messageType", 'Error');
			
			return $this->render();			
		}

		// parse response
		try {
			$resultSet = $this->parseResponse($format, $responseXML, $searchTerm);
		}
		catch (Exception $exception) {
			$prefix=$this->prefixx();
			$mess= 'Unfortunately the query process failed due to a technical problem. Please retry later.';
			Session::set($prefix . ".errors.message", $mess);
			Session::set($prefix . ".errors.messageType", 'Error');
			// @todo better error handling
			//echo $exception->getMessage();
			Director::redirectBack();
			return;			
		}
		
		$this->searchTerm = $searchTerm;
		$this->SearchResultItems = $resultSet->__get('Items');
		$this->numberOfRecordsMatched = $resultSet->__get('numberOfRecordsMatched');
		$this->nextRecord = $resultSet->__get('nextRecord');
		$this->numberOfRecordsReturned = $resultSet->__get('numberOfRecordsReturned');
		
		// compute page of pages
		$this->onpage=floor($this->startPosition / $this->maxRecords) + 1;
		$this->ofpages= floor($this->numberOfRecordsMatched / $this->maxRecords);

		// see if there are some records on the other page
		if( ($this->numberOfRecordsMatched / $this->maxRecords ) > $this->ofpages){
			// Yes there are, so we have another page
			$this->ofpages++;
		}

		// Workaround Geonetwork error
		if($this->nextRecord > $this->numberOfRecordsMatched){
			$this->NextStart=$this->startPosition; 
		}
		else {
			$this->NextStart=$this->nextRecord;
		}
		
		// see if there are some records on the other page
		if( ($this->numberOfRecordsMatched / $this->maxRecords ) > $this->ofpages){
			// Yes there are, so we have another page
			$this->ofpages++;
		}

    $this->numlinks=new DataObjectSet();

    $startNum=$this->onpage<4?1:($this->onpage-2);
    $endNum=($startNum+5)>$this->ofpages?$this->ofpages:($startNum+5);

    for ($i=$startNum; $i<=$endNum; $i++) {
      $linkData=array('label'=>$i,'linknum'=>($i-1)*10+1,'Link'=>$this->Link(),'searchTerm'=>$searchTerm,'sortBy'=>$this->sortBy,'sortOrder'=>$this->sortOrder,'bboxUpper'=>$this->bboxUpper,'bboxLower'=>$this->bboxLower);
      if ($i==$this->onpage) $linkData['current']=true;
      $this->numlinks->push(new ArrayData($linkData));
    }

		// Workaround Geonetwork error
		if($this->nextRecord > $this->numberOfRecordsMatched){
			$this->NextStart=$this->startPosition; 
		}
		else {
			$this->NextStart=$this->nextRecord;
		}
		
		$this->PrevStart=$this->startPosition - $this->maxRecords;
		
		if($this->PrevStart < 1) {
			$this->PrevStart= 1;
    } 

    $this->LastStart=($this->ofpages-1)*10+1;

		return $this->render();
	}
	
	/*
	 * IsFirstResultPage
	 * 
	 * checks if the query-results are actually the first page or not 
	 * 
	 * @returns bool true if it is so 
	 * 
	 */
	public function IsFirstResultPage(){
		if($this->onpage == 1) return true;
		return false;
	}
	
	/*
	 * IsLastResultPage
	 * 
	 * checks if the query-results are actually the last page or not 
	 * 
	 * @returns bool true if it is so 
	 * 
	 */
	public function IsLastResultPage(){
		if($this->onpage == $this->ofpages) return true;
		return false;
	}
	
	// Make paging more restful
	// GiveResultPage/{StartAtRecord}/{SearchTerm}/

	public function giveresult($httpRequest){
		$params = $httpRequest->allParams();
		if (!isset($params['ID'])) {
			Director::redirectBack();
			return;
		}
		$params['startPosition']=$params['ID'] + 0;
		if($params['startPosition'] < 1 ) $params['startPosition'] = 1;
		
		if (!$params['OtherID']) {
			$params['searchTerm']='';
		}
		else{
			$params['searchTerm']=$params['OtherID'];
		}
		$params=array_merge($params,$httpRequest->getVars());

		return $this->dogetrecords($params);

	}
	
	
	/**
	 * Action: show details/XML download of a metadata entry.
	 *
	 * Controller API to handle 'doshowitem' requrests. Performed when a user
	 * clicks on a detail-link on the page-result page
	 *.
	 * @param array $httpRequest http-request parameter.
	 *
	 * @todo add error message when geonetwork is down
	 */
	public function dogetrecordbyid( $httpRequest ) {
		
		$params = $httpRequest->allParams();

		if (!$params['ID']) {
			Director::redirectBack();
			return;
		}

		// Pass in OtherID to implement the XML download capability. Currently
		// not supported.
		if (!$params['OtherID']) {
			$params['OtherID'] = "html";
		}

		$id           = $params['ID'];
		$outputFormat = $params['OtherID'];
		$format       = self::get_default_metadata_standard();

		$data = array(
			'fileIdentifier' => Convert::raw2xml($id),
			'outputFormat'   => Convert::raw2xml($outputFormat),
			'requestxml'     => 'cswGetRecordByID_xml'
		);

		// get XML document for requested metadata entry.
		
		try {
			$cmd = $this->getCommand("GetRecordById", $data);
			$cmd->setUsername($this->owner->GeonetworkUsername);
			$cmd->setPassword($this->owner->GeonetworkPassword);
			$responseXML = $cmd->execute();
		}
		catch(GeoNetworkRestfulService_Exception $exception) {
			$prefix=$this->prefixx();
			$mess= 'Unfortunately the query process failed due to a technical problem. Please retry later.';
			Session::set($prefix . ".errors.message", $mess);
			Session::set($prefix . ".errors.messageType", 'Error');

			// @TODO add error message
			return $this->render();			
		}
		// If xml representation is requestet, forward the answer
		// using {$uuid}.xml as filename
		if ($outputFormat == 'xml'){
			//ereasing the email-addresses from the XML
			$responseXML=preg_replace  ( '/\>([a-zA-Z0-9_+\.\-]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)\</'  ,
			 							'>email-address removed for security reason<', 
										$responseXML
									);
			// removing the geodata envelope
			//<csw:GetRecordByIdResponse xmlns:csw="http://www.opengis.net/cat/csw/2.0.2">
			$responseXML=preg_replace  ( '/((\<csw\:GetRecordByIdResponse)|(<\/csw\:GetRecordByIdResponse)).*\>/'  ,
			 							'', 
										$responseXML
									);
			
			$resp=$this->getResponse();
			$resp->addHeader("Content-Type","text/xml"); 
			$resp->addHeader('Content-Disposition' , "attachment; filename=\"".$id.".xml\";");
			return $responseXML;
		}

		// parse XML response and create the silverstripe data-structure.
		$formatList = self::get_xml_full_response();
		$xslFilename = $formatList[$format];
		$data = array(
			'xml' => $responseXML,
			'xsl' => $xslFilename
    	);


		$cmd    = $this->getCommand("ParseXML", $data);
		// @todo: error handling: redirect back
		// Director::redirectBack();
		//
		$result = $cmd->execute();
// echo('<pre>'); print_r($result);echo('<pre>'); 
		// render metadata data-structure
		$this->SearchRecord = $result->__get('Items');
		return $this->render();
	}
	
	/**
	*  getTheSessionMessage()
	*
	* Returns the Session-Message for the given page (of $this->URLSegment)
	*
	* @return string The Session-Message
	*/
	
	public function getTheSessionMessage(){
		return Session::get("FormInfo.". $this->URLSegment . ".info.message");
	}


	/**
	*  getTheSessionMessageType()
	*
	* Returns the Session-Message-Type for the given page (of $this->URLSegment)
	* The Session-Message-Type can be 'Info','Warning' or 'Error'
	*
	* @return string The Session-Message-Type
	*/
	
	public function getTheSessionMessageType(){
		return Session::get("FormInfo.". $this->URLSegment . ".info.messageType");
	}
	
	/**
	*  clearTheSessionMessage()
	*
	* Clears the Session-Message and the Session-Message-Type for the given page (of $this->URLSegment)
	*
	*/

	public function clearTheSessionMessage(){
		Session::clear("FormInfo.". $this->URLSegment . ".info.message");
		Session::clear("FormInfo.". $this->URLSegment . ".info.messageType");
	}
	
	public function prefixx(){
		return $this->URLSegment;
	}
}


/**
 * Catalogue-Page Exception Class
 */
class CataloguePage_Exception extends Exception { }
