<?php

/**
 * @package geocatalog
 * @subpackage tests
 */
class GetRecordByIdCommandTest extends SapphireTest {

	/**
	 * Also uses SimpleNzctFixture in setUp()
	 */
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
		
		//GetRecordsCommand::set_catalogue_url("/dogetrecordbyid/7f1db956-b017-427c-866d-25c7a8af7384/?usetestmanifest=1&flush=1");
		GetRecordByIdCommand::set_catalogue_url("/getrecords?usetestmanifest=1&flush=1");
	}

	/**
	 * Remove test controller from global controller-stack.
	 */
	function tearDown() {
		
		$this->controller->popCurrent();
		
		parent::tearDown();
	}


	/**
	 * Test the testGetRecordByIdCommand
	 *
	 * Test the testGetRecordByIdCommand. The test controller GetRecordByIdCommandTest_Controller
	 * expects a certain request and returns ok or failed.
	 *
	 * This test tests the cswGetRecordById_xml template only.
	 *
	 * @see GetRecordByIdCommand
	 * @see GetRecordByIdCommandTest_Controller
	 */
	function testGetRecordByIdCommand() {

		$id           = '7f1db956-b017-427c-866d-25c7a8af7384';
		$outputFormat = 'html';
		$format       = 'iso';

		$data = array(
			'fileIdentifier' => Convert::raw2xml($id),
			'outputFormat'   => Convert::raw2xml($outputFormat),
			'requestxml'     => 'cswGetRecordByID_xml'
		);
		
		$cmd    = $this->controller->getCommand("GetRecordById", $data);
		$result = $cmd->execute();
		
		$position = strpos($result, '<csw:GetRecordById xmlns:csw="http://www.opengis.net/cat/csw/2.0.2" service="CSW" version="2.0.2" outputSchema="csw:IsoRecord">');

		if ($position === false) {
			$this->assertEquals(1,0,"Invalid CSW GetRecordById XML request");
		}

		$position = strpos($result, "<csw:Id>". $id ."</csw:Id>");
		if ($position === false) {
			$this->assertEquals(1,0,"Invalid csw:Id or not found");
		}
	}
	

	/**
	 * 
	 * This test tests the cswGetRecordById_xml template only.
	 *  Without ID
	 *
	 * @see GetRecordByIdCommand
	 * @see GetRecordByIdCommandTest_Controller
	 */
	function testGetRecordByIdCommandWithoutID() {

		$id           = '';
		$outputFormat = 'html';
		$format       = 'iso';

		$data = array(
			'fileIdentifier' => Convert::raw2xml($id),
			'outputFormat'   => Convert::raw2xml($outputFormat),
			'requestxml'     => 'cswGetRecordByID_xml'
		);
		
		$cmd    = $this->controller->getCommand("GetRecordById", $data);
		$result = $cmd->execute();
		
		$position = strpos($result, '<csw:GetRecordById xmlns:csw="http://www.opengis.net/cat/csw/2.0.2" service="CSW" version="2.0.2" outputSchema="csw:IsoRecord">');

		if ($position === false) {
			$this->assertEquals(1,0,"Invalid CSW GetRecordById XML request");
		}

		$position = strpos($result, "<csw:Id>". $id ."</csw:Id>");
		if ($position === false) {
			$this->assertEquals(1,0,"Failed empty id");
		}
	}
	
	/**
	 * 
	 * This test tests the cswGetRecordById_xml template only.
	 *  With  > in ID
	 *
	 * @see GetRecordByIdCommand
	 * @see GetRecordByIdCommandTest_Controller
	 */
	function testGetRecordByIdCommandWithLessThenInID() {

		$id           = 'whatever < this means';
		$outputFormat = 'html';
		$format       = 'iso';

		$data = array(
			'fileIdentifier' => Convert::raw2xml($id),
			'outputFormat'   => Convert::raw2xml($outputFormat),
			'requestxml'     => 'cswGetRecordByID_xml'
		);
		
		$cmd    = $this->controller->getCommand("GetRecordById", $data);
		$result = $cmd->execute();
		
		$position = strpos($result, '<csw:GetRecordById xmlns:csw="http://www.opengis.net/cat/csw/2.0.2" service="CSW" version="2.0.2" outputSchema="csw:IsoRecord">');

		if ($position === false) {
			$this->assertEquals(1,0,"Invalid CSW GetRecordById XML request");
		}

		$position = strpos($result, "<csw:Id>whatever &lt; this means</csw:Id>");
		if ($position === false) {
			$this->assertEquals(1,0,"failed id with < \n" . $result);
		}
	}

	/**
	 * 
	 * This test tests the cswGetRecordById_xml template only.
	 *  With  ' in ID
	 *
	 * @see GetRecordByIdCommand
	 * @see GetRecordByIdCommandTest_Controller
	 */
	function testGetRecordByIdCommandWithQuoteInID() {

		$id           = 'with \' here';
		$outputFormat = 'html';
		$format       = 'iso';

		$data = array(
			'fileIdentifier' => Convert::raw2xml($id),
			'outputFormat'   => Convert::raw2xml($outputFormat),
			'requestxml'     => 'cswGetRecordByID_xml'
		);
		
		$cmd    = $this->controller->getCommand("GetRecordById", $data);
		$result = $cmd->execute();
		
		$position = strpos($result, '<csw:GetRecordById xmlns:csw="http://www.opengis.net/cat/csw/2.0.2" service="CSW" version="2.0.2" outputSchema="csw:IsoRecord">');
		if ($position === false) {
			$this->assertEquals(1,0,"Invalid CSW GetRecordById XML request");
		}

		// NOTE: 
		// This is not an interesting change. When converting a ' into xml
		// we got a plain ' back, later this behaviour changed and returned
		// &#39;  instead of '.
		// Again a change, now we get a ' back again. 
		// Need ot investigate side effects of this new behaviour
		$position = (strpos($result, "<csw:Id>with &#39; here</csw:Id>")) ? (strpos($result, "<csw:Id>with &#39; here</csw:Id>")) : strpos($result, "<csw:Id>with ' here</csw:Id>");
		if ($position === false) {
			$this->assertEquals(1,0,"failed id with ' \n");
		}
	}
	
}







/**
 * @package geocatalog
 * @subpackage tests
 *
 * Mockup controller class to simulate the GeoNetwork side in this test.
 */
class GetRecordByIdCommandTest_Controller extends Controller {

	/**
	 * Standard method, not in use.
	 */
	function index() {
		BasicAuth::disable();
		return "failed";
	}

	/**
	 * Returns the request body so that the calling unit test can perform the validation.
	 *
	 * @return string request body
	 */
	function getrecords($data) {

		$request = $data->getBody();
		return $request;

	}
}
