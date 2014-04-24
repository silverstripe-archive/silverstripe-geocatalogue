<?php

/**
 * @package geocatalog
 * @subpackage tests
 */
class GnGetUUIDOfRecordByIDCommandTest extends SapphireTest {

	static $fixture_file = 'GnGetUUIDOfRecordByIDCommandTest.yml';

	protected $controller = null;

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
		$page->GeonetworkBaseURL  = "###";

		$this->controller = new RegisterDataPage_Controller($page);
		$this->controller->pushCurrent();

		$this->updateUrlConfiguration('url_getuuid','/getuuid');
	}

	/**
	 * Remove test controller from global controller-stack.
	 */
	function tearDown() {
		$this->controller->popCurrent();
		parent::tearDown();
	}

	/**
	 * Test the testGetRecordsInISOCommand
	 *
	 * Test the testGetRecordsInISOCommand. The test controller GetRecordsCommandTest_Controller
	 * expects a certain request and returns ok or failed.
	 *
	 * This test tests the cswGetRecordsSummaryISO_xml template only.
	 *
	 * @see GetRecordsCommand
	 * @see GetRecordsCommandTest_Controller
	 */
	function testGnGetUUIDOfRecordByIDCommand() {
		$data = array();
		$data['gnID'] = 1963;
		
		$cmd    = $this->controller->getCommand("GnGetUUIDOfRecordByID", $data);
		$cmd->setRestfulService(new RestfulServiceTest_MockRestfulService('GnGetUUIDOfRecordByIDCommandTest_Controller',0));

		$result = $cmd->execute();

		$this->assertEquals($result,'0587e442-eaee-470d-a0d1-3e3a54cc983b','Invalid UUID. ');
	}
	
}


/**
 * @package geocatalog
 * @subpackage tests
 *
 * Mockup controller class to simulate the GeoNetwork side in this test.
 */
class GnGetUUIDOfRecordByIDCommandTest_Controller extends Controller implements TestOnly {

	private static $allowed_actions = array(
		'getuuid'
	);

	static $ISO19139response='<?xml version="1.0" encoding="UTF-8"?>
<gmd:MD_Metadata xmlns:gmd="http://www.isotc211.org/2005/gmd" xmlns:gml="http://www.opengis.net/gml" xmlns:gts="http://www.isotc211.org/2005/gts" xmlns:gco="http://www.isotc211.org/2005/gco" xmlns:geonet="http://www.fao.org/geonetwork">
  <gmd:fileIdentifier>
    <gco:CharacterString xmlns:srv="http://www.isotc211.org/2005/srv" xmlns:gmx="http://www.isotc211.org/2005/gmx">0587e442-eaee-470d-a0d1-3e3a54cc983b</gco:CharacterString>
  </gmd:fileIdentifier>
</gmd:MD_Metadata>
';

	function getuuid($params) {
		$id = $params->getVar('id');
		if($id == 1963){
			$resp=$this->getResponse();
			$resp->addHeader("Content-Type","text/xml");
			return self::$ISO19139response;
		} else{
			return "Parameter id is(". $params['id'] .") should be 1963";
		}
	}
}
