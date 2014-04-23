<?php

/**
 * @package geocatalog
 * @subpackage tests
 */
class GetRecordByIdCommandTest extends FunctionalTest {

	static $fixture_file = 'geocatalogue/tests/GetRecordByIdCommandTest.yml';

	protected $controller = null;

	/**
	 * Initiate the controller and page classes and configure GeoNetwork service
	 * to use the mockup-controller for testing.
	 */
	function setUp() {
		parent::setUp();
		
		$url = Director::absoluteBaseURL() . 'GetRecordByIdCommandTest_Controller';

		$page = $this->objFromFixture('BrowsePage', 'catalogbrowsepage');
		$page->GeonetworkBaseURL  = $url;

		$this->controller = new BrowsePage_Controller($page);
		$this->controller->pushCurrent();

		$config = Config::inst()->get('Catalogue', 'geonetwork');
		$version = $config['api_version'];

		$array = $config[$version];
		$array['csw_url'] = "/getrecords";
		$config[$version] = $array;

		Config::inst()->update('Catalogue', 'geonetwork', $config);
	}

	/**
	 * Remove test controller from global controller-stack.
	 */
	function tearDown() {
		$this->controller->popCurrent();
		parent::tearDown();
	}

	/**
	 * Test the GetRecordById attributes of the XML request document
	 */
	function testRequestAttributes() {
		$data = array(
			'fileIdentifier' => Convert::raw2xml(''),
			'outputFormat'   => Convert::raw2xml('html'),
			'requestxml'     => 'cswGetRecordByID_xml'
		);
		
		$cmd = $this->controller->getCommand("GetRecordById", $data);
		$cmd->setRestfulService(new RestfulServiceTest_MockRestfulService('GetRecordByIdCommandTest_Controller',0));

		$xml = $cmd->execute();

		// parse response document
		$doc  = new DOMDocument();
		$doc->loadXML($xml);
		$xpath = new DOMXPath($doc);

		$xpath->registerNamespace("csw", "http://www.opengis.net/cat/csw/2.0.2");
		$rootList = $xpath->query('/csw:GetRecordById');

		$this->assertEquals(1,$rootList->length,"Root element has not been created correctly.");

		$rootnode = $rootList->item(0);
		$this->assertEquals($rootnode->attributes->getNamedItem('service')->nodeValue,'CSW',"Attribute 'service' has not been set correctly.");
		$this->assertEquals($rootnode->attributes->getNamedItem('version')->nodeValue,'2.0.2',"Attribute 'version' for CSW has not been set correctly.");
		$this->assertEquals($rootnode->attributes->getNamedItem('outputSchema')->nodeValue,'csw:IsoRecord',"Attribute 'OutputSchema' for CSW has not been set correctly.");
	}

	/**
	 * Test that id and ElementSetName is set correctly.
	 */
	function testRequestDocument() {
		$id = '7f1db956-b017-427c-866d-25c7a8af7384';
		$outputFormat = 'html';

		$data = array(
			'fileIdentifier' => Convert::raw2xml($id),
			'outputFormat'   => Convert::raw2xml($outputFormat),
			'requestxml'     => 'cswGetRecordByID_xml'
		);

		$cmd = $this->controller->getCommand("GetRecordById", $data);
		$cmd->setRestfulService(new RestfulServiceTest_MockRestfulService('GetRecordByIdCommandTest_Controller',0));

		$xml = $cmd->execute();

		// parse response document
		$doc  = new DOMDocument();
		$doc->loadXML($xml);
		$xpath = new DOMXPath($doc);

		$xpath->registerNamespace("csw", "http://www.opengis.net/cat/csw/2.0.2");
		$rootList = $xpath->query('/csw:GetRecordById');

		$this->assertEquals(1,$rootList->length,"Root element has not been created correctly.");

		$rootnode = $rootList->item(0);

		$list = $xpath->query('csw:ElementSetName',$rootnode);
		$this->assertEquals(1,$list->length,"Number of ElementSetName elements supposed to be one item only.");

		$node = $list->item(0);
		$this->assertEquals('full',$node->nodeValue,"ElementSetName node has not been set correctly.");

		$list = $xpath->query('csw:Id',$rootnode);
		$this->assertEquals(1,$list->length,"Number of Id elements supposed to be one item only.");

		$node = $list->item(0);
		$this->assertEquals($id,$node->nodeValue,"ID node has not been set correctly.");
	}

	/**
	 * Test request XML with an empty id.
	 */
	function testRequestDocumentWithEmptyID() {
		$id = '';
		$data = array(
			'fileIdentifier' => Convert::raw2xml($id),
			'outputFormat'   => Convert::raw2xml('html'),
			'requestxml'     => 'cswGetRecordByID_xml'
		);
		
		$cmd    = $this->controller->getCommand("GetRecordById", $data);
		$cmd->setRestfulService(new RestfulServiceTest_MockRestfulService('GetRecordByIdCommandTest_Controller',0));

		$xml = $cmd->execute();

		// parse response document
		$doc  = new DOMDocument();
		$doc->loadXML($xml);
		$xpath = new DOMXPath($doc);

		$xpath->registerNamespace("csw", "http://www.opengis.net/cat/csw/2.0.2");

		$list = $xpath->query('/csw:GetRecordById/csw:Id');
		$this->assertEquals(1,$list->length,"Number of Id elements supposed to be one item only.");

		$node = $list->item(0);
		$this->assertEquals($id,$node->nodeValue,"ID node supposed to be empty.");
	}
	
	/**
	 * Test request XML with an id containing a less-than character.
	 */
	function testRequestDocumentWithLessThanID() {
		$id = 'whatever < this means';
		$data = array(
			'fileIdentifier' => Convert::raw2xml($id),
			'outputFormat'   => Convert::raw2xml('html'),
			'requestxml'     => 'cswGetRecordByID_xml'
		);
		
		$cmd    = $this->controller->getCommand("GetRecordById", $data);
		$cmd->setRestfulService(new RestfulServiceTest_MockRestfulService('GetRecordByIdCommandTest_Controller',0));

		$xml = $cmd->execute();

		// parse response document
		$doc  = new DOMDocument();
		$doc->loadXML($xml);
		$xpath = new DOMXPath($doc);

		$xpath->registerNamespace("csw", "http://www.opengis.net/cat/csw/2.0.2");

		$list = $xpath->query('/csw:GetRecordById/csw:Id');
		$this->assertEquals(1,$list->length,"Number of Id elements supposed to be one item only.");

		$node = $list->item(0);
		$this->assertEquals('whatever < this means',$node->nodeValue,"ID node supposed to be 'whatever < this means'.");
	}

	/**
	 * Test request XML with an id containing a ' character.
	 */
	function testGetRecordByIdCommandWithQuoteInID() {
		$id           = 'with \' here';
		$outputFormat = 'html';

		$data = array(
			'fileIdentifier' => Convert::raw2xml($id),
			'outputFormat'   => Convert::raw2xml($outputFormat),
			'requestxml'     => 'cswGetRecordByID_xml'
		);
		
		$cmd    = $this->controller->getCommand("GetRecordById", $data);
		$cmd->setRestfulService(new RestfulServiceTest_MockRestfulService('GetRecordByIdCommandTest_Controller',0));

		$xml = $cmd->execute();

		// parse response document
		$doc  = new DOMDocument();
		$doc->loadXML($xml);
		$xpath = new DOMXPath($doc);

		$xpath->registerNamespace("csw", "http://www.opengis.net/cat/csw/2.0.2");

		$list = $xpath->query('/csw:GetRecordById/csw:Id');
		$this->assertEquals(1,$list->length,"Number of Id elements supposed to be one item only.");

		$node = $list->item(0);
		$this->assertEquals("with ' here",$node->nodeValue,"ID node supposed to be 'with ' here'.");
	}
}

/**
 * @package geocatalog
 * @subpackage tests
 *
 * Mockup controller class to simulate the GeoNetwork side in this test.
 */
class GetRecordByIdCommandTest_Controller extends Controller implements TestOnly {

	private static $allowed_actions = array(
		'getrecords'
	);

	/**
	 * Standard method, not in use.
	 * @return string
	 */
	function index() {
		BasicAuth::disable();
		return "failed";
	}

	/**
	 * This action returns the post body as a response.
	 * This way the unit test is able to evaluate the request sent of for testing.
	 *
	 * @param $data
	 *
	 * @return mixed
	 */
	function getrecords($data) {
		$result = $data->postVars();
		return $result[0];
	}
}
