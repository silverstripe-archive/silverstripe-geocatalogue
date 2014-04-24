<?php

/**
 * @package geocatalog
 * @subpackage tests
 */
class GnPublishMetadataCommandTest extends SapphireTest {

	static $fixture_file = 'geocatalogue/tests/GnGetUUIDOfRecordByIDCommandTest.yml';

	protected $controller = null;

	protected $page = null;

	/**
	 * @param $url_segment
	 */
	public function updateUrlConfiguration($key, $url_segment) {
		$config = Config::inst()->get('Catalogue', 'geonetwork');
		$config['api_version'] = 'default';
		$version = $config['api_version'];

		$urlList = $config[$version];
		$urlList[$key] = $url_segment;
		$config[$version] = $urlList;

		Config::inst()->update('Catalogue', 'geonetwork', $config);
	}

	/**
	 * Initiate the controller and page classes and configure GeoNetwork service
	 * to use the mockup-controller for testing.
	 */
	function setUp() {
		parent::setUp();
		
		$page = $this->objFromFixture('RegisterDataPage', 'registerdatapage');
		$page->GeonetworkBaseURL  = '###';
		$this->page = $page;

		$this->controller = new RegisterDataPage_Controller($page);
		$this->controller->pushCurrent();

		$this->updateUrlConfiguration('url_publish','/checkrequest');
	}

	/**
	 * Remove test controller from global controller-stack.
	 */
	function tearDown() {
		$this->controller->popCurrent();
		parent::tearDown();
	}

	function testGeoNetworkGroupIsNull() {
		$this->page->GeonetworkGroupID = null;

		$data = array('gnID' => 1963);
		$cmd = $this->controller->getCommand("GnPublishMetadata", $data);
		try {
			$cmd->execute();
		}
		catch(Exception $e) {
			$this->assertEquals($e->getMessage(),'Group for record publishing not set correctly. Please contact the system administrator.',"Exception was thrown but with wrong error message.");
			return;
		}
		$this->assertTrue(false,"Exception expected, but hasn't been thrown.");
	}

	function testPrivilegeIsNull() {
		$this->page->GeonetworkGroupID = 2;
		$this->page->Privilege = null;

		$data = array('gnID' => 1963);
		$cmd = $this->controller->getCommand("GnPublishMetadata", $data);
		try {
			$cmd->execute();
		}
		catch(Exception $e) {
			$this->assertEquals($e->getMessage(),'Privileges for publishing not set correctly. Please contact the system administrator.',"Exception was thrown but with wrong error message.");
			return;
		}
		$this->assertTrue(false,"Exception expected, but hasn't been thrown.");
	}

	function testPrivilegeRequest() {
		$this->page->GeonetworkGroupID = 2;
		$this->page->Privilege = '0,1,5';

		$data = array('gnID' => 1963);
		$cmd = $this->controller->getCommand("GnPublishMetadata", $data);
		$cmd->setRestfulService(new RestfulServiceTest_MockRestfulService('GnPublishMetadataCommandTest_Controller',0));

		$response = $cmd->execute();
		try {
			$cmd->execute();
		}
		catch(Exception $e) {
			$this->assertTrue(false,"An unexpected exception has been thrown.");
			return;
		}
		$this->assertEquals($response,1963,'A different ID has been returned than expected.');
	}

	function testCommandCatchesUnequalIDs() {
		$this->updateUrlConfiguration('url_publish','/wrongid');

		$this->page->GeonetworkGroupID = 2;
		$this->page->Privilege = '0,1,5';

		$data = array('gnID' => 1963);
		$cmd = $this->controller->getCommand("GnPublishMetadata", $data);
		$cmd->setRestfulService(new RestfulServiceTest_MockRestfulService('GnPublishMetadataCommandTest_Controller',0));

		try {
			$cmd->execute();
		}
		catch(GnPublishMetadataCommand_Exception $e) {
			$this->assertEquals('GeoNetwork publication has failed.',$e->getMessage(),"The command did not throw the expected GnPublishMetadataCommand_Exception exception.");
			return;
		}
		$this->assertTrue(0,'An expected exception has not been thrown.');
	}

	function testCommandCatchesMissingIs() {
		$this->updateUrlConfiguration('url_publish','/noid');

		$this->page->GeonetworkGroupID = 2;
		$this->page->Privilege = '0,1,5';

		$data = array('gnID' => 1963);
		$cmd = $this->controller->getCommand("GnPublishMetadata", $data);
		$cmd->setRestfulService(new RestfulServiceTest_MockRestfulService('GnPublishMetadataCommandTest_Controller',0));

		try {
			$cmd->execute();
		}
		catch(GnPublishMetadataCommand_Exception $e) {
			$this->assertEquals('GeoNetwork ID for the new dataset has not been created.',$e->getMessage(),"The command did not throw the expected GnPublishMetadataCommand_Exception exception.");
			return;
		}
		$this->assert('An expected exception has not been thrown.');
	}
}


/**
 * @package geocatalog
 * @subpackage tests
 *
 * Mockup controller class to simulate the GeoNetwork side in this test.
 */
class GnPublishMetadataCommandTest_Controller extends Controller implements TestOnly {

	private static $allowed_actions = array(
		'checkrequest','wrongid','noid'
	);

	function checkrequest($request) {
		$vars = $request->postVars();
		$parameterString = $vars[0];

		$resp=$this->getResponse();
		$resp->addHeader("Content-Type","text/xml");
		if ($parameterString === '_1_0=on&_1_1=on&_1_5=on&_2_0=on&_2_1=on&_2_5=on&id=1963') {
			return "<?xml version=\"1.0\" encoding=\"UTF-8\"?><response><id>1963</id></response>";
		}
		return '<?xml version=\"1.0\" encoding=\"UTF-8\"?><response><message>Parameter String not created properly.</message></response>';
	}

	function wrongid($request) {
		return "<?xml version=\"1.0\" encoding=\"UTF-8\"?><response><id>1234</id></response>";
	}

	function noid($request) {
		return "<?xml version=\"1.0\" encoding=\"UTF-8\"?><response></response>";
	}
}
