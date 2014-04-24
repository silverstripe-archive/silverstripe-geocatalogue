<?php

/**
 * @package geocatalog
 * @subpackage tests
 */
class GetRecordsCommandTest extends SapphireTest {

	static $fixture_file = 'geocatalogue/tests/GetRecordsCommandTest.yml';

	protected $controller = null;

	/**
	 * Initiate the controller and page classes and configure GeoNetwork service
	 * to use the mockup-controller for testing.
	 */
	function setUp() {
		parent::setUp();
		
		$url = Director::absoluteBaseURL() . 'GetRecordsCommandTest_Controller';

		$page = $this->objFromFixture('CataloguePage', 'catalogue');
		$page->GeonetworkBaseURL  = $url;

		$this->controller = new CataloguePage_Controller($page);
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

	function testRequestAttributes() {
		$data = array(
			'requestxml' => 'cswGetRecordsSummaryISO_xml',
			'searchterm'   => 'testGetRecordsCommand',
			'startPosition'   => '5',
			'maxRecords'     => '15'
		);

		$cmd = $this->controller->getCommand("GetRecords", $data);
		$cmd->setRestfulService(new RestfulServiceTest_MockRestfulService('GetRecordsCommandTest_Controller',0));

		$xml = $cmd->execute();

		// parse response document
		$doc  = new DOMDocument();
		$doc->loadXML($xml);
		$xpath = new DOMXPath($doc);

		$xpath->registerNamespace("csw", "http://www.opengis.net/cat/csw/2.0.2");
		$rootList = $xpath->query('/csw:GetRecords');

		$this->assertEquals(1,$rootList->length,"Root element has not been created correctly.");

		$rootnode = $rootList->item(0);
		$this->assertEquals($rootnode->attributes->getNamedItem('service')->nodeValue,'CSW',"Attribute 'service' has not been set correctly.");
		$this->assertEquals($rootnode->attributes->getNamedItem('version')->nodeValue,'2.0.2',"Attribute 'version' for CSW has not been set correctly.");
		$this->assertEquals($rootnode->attributes->getNamedItem('resultType')->nodeValue,'results',"Attribute 'results' for CSW has not been set correctly.");
		$this->assertEquals($rootnode->attributes->getNamedItem('outputSchema')->nodeValue,'csw:IsoRecord',"Attribute 'OutputSchema' for CSW has not been set correctly.");
		$this->assertEquals($rootnode->attributes->getNamedItem('maxRecords')->nodeValue,15,"Attribute 'maxRecords' for CSW has not been set correctly.");
		$this->assertEquals($rootnode->attributes->getNamedItem('startPosition')->nodeValue,5,"Attribute 'startPosition' for CSW has not been set correctly.");
	}

	function testRequestDefaultAttributes() {
		$data = array(
			'requestxml' => 'cswGetRecordsSummaryISO_xml',
			'searchterm'   => 'testGetRecordsCommand'
		);

		$cmd = $this->controller->getCommand("GetRecords", $data);
		$cmd->setRestfulService(new RestfulServiceTest_MockRestfulService('GetRecordsCommandTest_Controller',0));

		$xml = $cmd->execute();

		// parse response document
		$doc  = new DOMDocument();
		$doc->loadXML($xml);
		$xpath = new DOMXPath($doc);

		$xpath->registerNamespace("csw", "http://www.opengis.net/cat/csw/2.0.2");
		$rootList = $xpath->query('/csw:GetRecords');

		$this->assertEquals(1,$rootList->length,"Root element has not been created correctly.");

		$rootnode = $rootList->item(0);
		$this->assertEquals($rootnode->attributes->getNamedItem('maxRecords')->nodeValue,10,"Default value of attribute 'maxRecords' for CSW has not been set correctly.");
		$this->assertEquals($rootnode->attributes->getNamedItem('startPosition')->nodeValue,0,"Default value of attribute 'startPosition' for CSW has not been set correctly.");
	}

	function testRequestWithOneSearchTerm() {
		$data = array(
			'requestxml' => 'cswGetRecordsSummaryISO_xml',
			'searchterm' => 'testSearchQuery'
		);

		$cmd = $this->controller->getCommand("GetRecords", $data);
		$cmd->setRestfulService(new RestfulServiceTest_MockRestfulService('GetRecordsCommandTest_Controller',0));

		$xml = $cmd->execute();

		// parse response document
		$doc  = new DOMDocument();
		$doc->loadXML($xml);
		$xpath = new DOMXPath($doc);

		$xpath->registerNamespace("csw", "http://www.opengis.net/cat/csw/2.0.2");
		$xpath->registerNamespace("ogc", "http://www.opengis.net/ogc");

		$nodelist = $xpath->query('/csw:GetRecords/csw:Query/csw:Constraint/ogc:Filter/ogc:And/ogc:PropertyIsLike/ogc:Literal');
		$this->assertEquals(1,$nodelist->length,"Filter element has not been created correctly.");

		$node = $nodelist->item(0);
		$this->assertEquals($node->nodeValue,"%testSearchQuery%","Search term has not been populated correctly.");
	}

	function testRequestWithMultipleSearchTerms() {
		$data = array(
			'requestxml' => 'cswGetRecordsSummaryISO_xml',
			'searchterm' => 'This is a Search Term'
		);

		$cmd = $this->controller->getCommand("GetRecords", $data);
		$cmd->setRestfulService(new RestfulServiceTest_MockRestfulService('GetRecordsCommandTest_Controller',0));

		$xml = $cmd->execute();

		// parse response document
		$doc  = new DOMDocument();
		$doc->loadXML($xml);
		$xpath = new DOMXPath($doc);

		$xpath->registerNamespace("csw", "http://www.opengis.net/cat/csw/2.0.2");
		$xpath->registerNamespace("ogc", "http://www.opengis.net/ogc");

		$nodelist = $xpath->query('/csw:GetRecords/csw:Query/csw:Constraint/ogc:Filter/ogc:And/ogc:PropertyIsLike/ogc:Literal');
		$this->assertEquals(5,$nodelist->length,"Filter element has not been created correctly.");

		$node = $nodelist->item(0);
		$this->assertEquals($node->nodeValue,"%This%","1st search term has not been populated correctly.");

		$node = $nodelist->item(1);
		$this->assertEquals($node->nodeValue,"%is%","2nd search term has not been populated correctly.");

		$node = $nodelist->item(2);
		$this->assertEquals($node->nodeValue,"%a%","3rd search term has not been populated correctly.");

		$node = $nodelist->item(3);
		$this->assertEquals($node->nodeValue,"%Search%","4th search term has not been populated correctly.");

		$node = $nodelist->item(4);
		$this->assertEquals($node->nodeValue,"%Term%","5th search term has not been populated correctly.");
	}

	function testRequestWithNoSearchTerm() {
		$data = array(
			'requestxml' => 'cswGetRecordsSummaryISO_xml'
		);

		$cmd = $this->controller->getCommand("GetRecords", $data);
		$cmd->setRestfulService(new RestfulServiceTest_MockRestfulService('GetRecordsCommandTest_Controller',0));

		try {
			$cmd->execute();
		}
		catch(CreateRequestCommand_Exception $e) {
			$this->assertEquals($e->getMessage(),'Exception: Undefined searchTerm',"Exception was thrown but with wrong error message.");
			return;
		}
		$this->assertTrue(false,"Exception expected, but hasn't been thrown.");
	}

	function testRequestWithNoRequestXMLParameter() {
		$data = array(
			'searchterm' => 'testGetRecordsCommand'
		);

		$cmd = $this->controller->getCommand("GetRecords", $data);
		$cmd->setRestfulService(new RestfulServiceTest_MockRestfulService('GetRecordsCommandTest_Controller',0));

		try {
			$cmd->execute();
		}
		catch(CreateRequestCommand_Exception $e) {
			$this->assertEquals($e->getMessage(),'Exception: Undefined requestxml',"Exception was thrown but with wrong error message.");
			return;
		}
		$this->assertTrue(false,"Exception expected, but hasn't been thrown.");
	}

	function testRequestWithEmptySearchTerm() {
		$data = array(
			'requestxml' => 'cswGetRecordsSummaryISO_xml',
			'searchterm' => ''
		);

		$cmd = $this->controller->getCommand("GetRecords", $data);
		$cmd->setRestfulService(new RestfulServiceTest_MockRestfulService('GetRecordsCommandTest_Controller',0));

		$xml = $cmd->execute();

		// parse response document
		$doc  = new DOMDocument();
		$doc->loadXML($xml);
		$xpath = new DOMXPath($doc);

		$xpath->registerNamespace("csw", "http://www.opengis.net/cat/csw/2.0.2");
		$xpath->registerNamespace("ogc", "http://www.opengis.net/ogc");

		$nodelist = $xpath->query('/csw:GetRecords/csw:Query/csw:Constraint/ogc:Filter');
		$this->assertEquals(1,$nodelist->length,"Filter element has not been created.");

		$nodelist = $nodelist->item(0)->childNodes;
		$this->assertEquals(1,$nodelist->length,"Filter element has not been created correctly.");

		$node = $nodelist->item(0);
		$this->assertEquals("#text",$node->nodeName,"Empty filter element has not been created correctly.");
	}

	function testApostrophyInSearchTerm() {
		$data = array(
			'requestxml' => 'cswGetRecordsSummaryISO_xml',
			'searchterm' => "test'test"
		);

		$cmd = $this->controller->getCommand("GetRecords", $data);
		$cmd->setRestfulService(new RestfulServiceTest_MockRestfulService('GetRecordsCommandTest_Controller',0));

		$xml = $cmd->execute();

		// parse response document
		$doc  = new DOMDocument();
		$doc->loadXML($xml);
		$xpath = new DOMXPath($doc);

		$xpath->registerNamespace("csw", "http://www.opengis.net/cat/csw/2.0.2");
		$xpath->registerNamespace("ogc", "http://www.opengis.net/ogc");

		$nodelist = $xpath->query('/csw:GetRecords/csw:Query/csw:Constraint/ogc:Filter/ogc:And/ogc:PropertyIsLike/ogc:Literal');
		$this->assertEquals(1,$nodelist->length,"Filter element has not been created correctly.");

		$node = $nodelist->item(0);
		$this->assertEquals($node->nodeValue,"%test'test%","Search term has not been populated correctly.");
	}

	function testLessThanInSearchTerm() {
		$data = array(
			'requestxml' => 'cswGetRecordsSummaryISO_xml',
			'searchterm' => "test<test"
		);

		$cmd = $this->controller->getCommand("GetRecords", $data);
		$cmd->setRestfulService(new RestfulServiceTest_MockRestfulService('GetRecordsCommandTest_Controller',0));

		$xml = $cmd->execute();

		// parse response document
		$doc  = new DOMDocument();
		$doc->loadXML($xml);
		$xpath = new DOMXPath($doc);

		$xpath->registerNamespace("csw", "http://www.opengis.net/cat/csw/2.0.2");
		$xpath->registerNamespace("ogc", "http://www.opengis.net/ogc");

		$nodelist = $xpath->query('/csw:GetRecords/csw:Query/csw:Constraint/ogc:Filter/ogc:And/ogc:PropertyIsLike/ogc:Literal');
		$this->assertEquals(1,$nodelist->length,"Filter element has not been created correctly.");

		$node = $nodelist->item(0);
		$this->assertEquals($node->nodeValue,"%test<test%","Search term has not been populated correctly.");
	}
}


/**
 * @package geocatalog
 *
 * Mockup controller class to simulate the GeoNetwork side in this test.
 */
class GetRecordsCommandTest_Controller extends Controller implements TestOnly {

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
