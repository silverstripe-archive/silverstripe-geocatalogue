<?php
/**
 * Created by PhpStorm.
 * User: rspittel
 * Date: 28/04/14
 * Time: 12:41 PM
 */

class GnInsert_v2_10CommandTest extends SapphireTest {

	static $fixture_file = 'geocatalogue/tests/GetRecordsCommandTest.yml';

	protected $controller = null;

	protected $page = null;

	/**
	 * Initiate the controller and page classes and configure GeoNetwork service
	 * to use the mockup-controller for testing.
	 */
	function setUp() {
		parent::setUp();

		$page = $this->objFromFixture('CataloguePage', 'catalogue');
		$page->GeonetworkBaseURL  = '###';

		$this->page = $page;

		$this->controller = new CataloguePage_Controller($page);
		$this->controller->pushCurrent();

		$this->updateUrlConfiguration('url_gninsert','/checkrequest');
	}

	/**
	 * Remove test controller from global controller-stack.
	 */
	function tearDown() {
		$this->controller->popCurrent();
		parent::tearDown();
	}

	function testRequestAndIDExtraction() {
		$this->updateUrlConfiguration('url_gnupdate','/gnupdate');

		$data = array();
		$data['data'] = "<xml><text>This is a test XML document</text><?xml>";
		$data = GnCreateInsertCommand::implode_with_keys($data);

		Config::inst()->update('Catalogue', 'disable_geonetwork_error_handling_for_update',true);
		$this->page->AutoPublish = false;


		$cmd = $this->controller->getCommand("GnInsert_v2_10", array('RequestParameter' => $data));
		$cmd->setRestfulService(new RestfulServiceTest_MockRestfulService('GnInsertV210CommandTest_Controller',0));
		$cmd->setDOMetadata(new MDMetadata());

		$result = $cmd->execute();
		$this->assertEquals(1963, $result,"Invalid GeoNetwork ID has been returned.");

		$this->assertEquals(1963, $cmd->get_gnid(),"GeoNetwork ID has not been verified.");
		$this->assertEquals('1234-5678-9876-21', $cmd->get_uuid(),"UUID (File Identifier) has not been verified.");
		$this->assertFalse($cmd->get_published(),"New record supposed to be published.");
	}

	function testRequestAndPublish() {
		$this->updateUrlConfiguration('url_gnupdate','/gnupdate');
		$this->updateUrlConfiguration('url_publish','/gnpublish');

		$data = array();
		$data['data'] = "<xml><text>This is a test XML document</text><?xml>";
		$data = GnCreateInsertCommand::implode_with_keys($data);

		Config::inst()->update('Catalogue', 'disable_geonetwork_error_handling_for_update',true);
		$this->page->AutoPublish = true;
		$this->page->Privilege = '1,2,3';
		$this->page->GeonetworkGroupID = '3';


		$cmd = $this->controller->getCommand("GnInsert_v2_10", array('RequestParameter' => $data));
		$cmd->setRestfulService(new RestfulServiceTest_MockRestfulService('GnInsertV210CommandTest_Controller',0));
		$cmd->setDOMetadata(new MDMetadata());

		$result = $cmd->execute();
		$this->assertEquals(1963, $result,"Invalid GeoNetwork ID has been returned.");
		$this->assertTrue($cmd->get_published(),"New record supposed to be published.");
	}

	function testRequestAndStrinctValidation() {
		$this->updateUrlConfiguration('url_gnupdate','/gnupdate');
		$this->updateUrlConfiguration('url_publish','/gnpublish');

		$data = array();
		$data['data'] = "<xml><text>This is a test XML document</text><?xml>";
		$data = GnCreateInsertCommand::implode_with_keys($data);

		Config::inst()->update('Catalogue', 'disable_geonetwork_error_handling_for_update',false);
		$this->page->AutoPublish = false;

		$cmd = $this->controller->getCommand("GnInsert_v2_10", array('RequestParameter' => $data));
		$cmd->setRestfulService(new RestfulServiceTest_MockRestfulService('GnInsertV210CommandTest_Controller',0));
		$cmd->setDOMetadata(new MDMetadata());

		$result = $cmd->execute();
		$this->assertEquals(1963, $result,"Invalid GeoNetwork ID has been returned.");
	}

	function testRequestAndStrinctValidationFailed_PermissionError() {
		$this->updateUrlConfiguration('url_gnupdate','/gnupdate_forbidden_error');
		$this->updateUrlConfiguration('url_publish','/gnpublish');

		$data = array();
		$data['data'] = "<xml><text>This is a test XML document</text><?xml>";
		$data = GnCreateInsertCommand::implode_with_keys($data);

		Config::inst()->update('Catalogue', 'disable_geonetwork_error_handling_for_update',false);
		$this->page->AutoPublish = false;

		$cmd = $this->controller->getCommand("GnInsert_v2_10", array('RequestParameter' => $data));
		$cmd->setRestfulService(new RestfulServiceTest_MockRestfulService('GnInsertV210CommandTest_Controller',0));
		$cmd->setDOMetadata(new MDMetadata());

		try {
			$cmd->execute();
		}
		catch(GeonetworkInsertCommand_Exception $e) {
			$this->assertEquals('HTTP request return following response code: 403 - Forbidden',$e->getMessage(),"Exception expected which detected a wrong UUID.");
			return;
		}
		$this->assertTrue(false,"An Exception has been expected but has not been thrown.");
	}

	function testRequestAndStrinctValidationFailed_InvalidUUID() {
		$this->updateUrlConfiguration('url_gnupdate','/gnupdate_invalid_uuid');
		$this->updateUrlConfiguration('url_publish','/gnpublish');

		$data = array();
		$data['data'] = "<xml><text>This is a test XML document</text><?xml>";
		$data = GnCreateInsertCommand::implode_with_keys($data);

		Config::inst()->update('Catalogue', 'disable_geonetwork_error_handling_for_update',false);
		$this->page->AutoPublish = false;

		$cmd = $this->controller->getCommand("GnInsert_v2_10", array('RequestParameter' => $data));
		$cmd->setRestfulService(new RestfulServiceTest_MockRestfulService('GnInsertV210CommandTest_Controller',0));
		$cmd->setDOMetadata(new MDMetadata());

		try {
			$cmd->execute();
		}
		catch(GeonetworkInsertCommand_Exception $e) {
			$this->assertEquals('The global file identifier has not been created. Please contact your system administrator.',$e->getMessage(),"Exception expected which detected a wrong UUID.");
			return;
		}
		$this->assertTrue(false,"An Exception has been expected but has not been thrown.");
	}

	function testRequestAndStrinctValidationFailed_InvalidXML() {
		$this->updateUrlConfiguration('url_gnupdate','/gnupdate_invalid_xml');
		$this->updateUrlConfiguration('url_publish','/gnpublish');

		$data = array();
		$data['data'] = "<xml><text>This is a test XML document</text><?xml>";
		$data = GnCreateInsertCommand::implode_with_keys($data);

		Config::inst()->update('Catalogue', 'disable_geonetwork_error_handling_for_update',false);
		$this->page->AutoPublish = false;

		$cmd = $this->controller->getCommand("GnInsert_v2_10", array('RequestParameter' => $data));
		$cmd->setRestfulService(new RestfulServiceTest_MockRestfulService('GnInsertV210CommandTest_Controller',0));
		$cmd->setDOMetadata(new MDMetadata());

		try {
			$cmd->execute();
		}
		catch(GeonetworkInsertCommand_Exception $e) {
			$this->assertEquals("The global file identifier has not been created. Please contact your system administrator.",$e->getMessage(),"Exception expected which detected a wrong UUID.");
			return;
		}
		$this->assertTrue(false,"An Exception has been expected but has not been thrown.");
	}

	/**
	 * @param $result
	 *
	 * @return array
	 */
	protected function DecodeRequestBody($result) {
		$list = explode("&", $result);
		$parameters = array();
		foreach($list as $item) {
			$element = explode("=", $item);

			$this->assertEquals(2, sizeof($element), "Expected key-value decoding for all parameters");
			$parameters[$element[0]] = $element[1];
		}
		return $parameters;
	}

	/**
	 * @param $url_segment
	 */
	public function updateUrlConfiguration($key, $url_segment) {
		$config = Config::inst()->get('Catalogue', 'geonetwork');
		$config['api_version'] = 'geonetwork_v2_10';
		$version = $config['api_version'];

		$urlList = $config[$version];
		$urlList[$key] = $url_segment;
		$config[$version] = $urlList;

		Config::inst()->update('Catalogue', 'geonetwork', $config);
	}
}



class GnInsertV210CommandTest_Controller extends Controller implements TestOnly {

	private static $allowed_actions = array(
		'checkrequest','gnupdate', 'gnpublish', 'gnupdate_invalid_uuid', 'gnupdate_forbidden_error', 'gnupdate_invalid_xml'
	);

	function checkrequest($request) {
		$vars = $request->postVars();
		$parameterString = $vars[0];

		$resp=$this->getResponse();
		$resp->addHeader("Content-Type","text/xml");
		if ($parameterString === "data=%3Cxml%3E%3Ctext%3EThis+is+a+test+XML+document%3C%2Ftext%3E%3C%3Fxml%3E") {
			return "<?xml version=\"1.0\" encoding=\"UTF-8\"?><response><id>1963</id><uuid>1234-5678-9876-21</uuid></response>";
		}
		return '<?xml version=\"1.0\" encoding=\"UTF-8\"?><response><message>Parameter String not created properly.</message></response>';
	}

	function gnupdate($request) {
		return "gmd:fileIdentifier|gmd:MD_Metadata|gmd:MD_Metadata/gmd:<uuid>1234-5678-9876-21</uuid>";
	}

	function gnupdate_invalid_uuid($request) {
		return "gmd:fileIdentifier|gmd:MD_Metadata|gmd:MD_Metadata/gmd:<uuid>1234-5678-9876-22</uuid>";
	}

	function gnupdate_forbidden_error($request) {
		$response = $this->getResponse();
		$response->setStatusCode(403, "Forbidden");
		return $response;
	}

	function gnupdate_invalid_xml($request) {
		return "gmd:fileIdentifier|gmd:MD_Metadata|gmd:MD_Meta<uuid>1234-5678-9876-21</uuid>";
	}

	function gnpublish($request) {
		return "<?xml version=\"1.0\" encoding=\"UTF-8\"?><response><id>1963</id><uuid>1234-5678-9876-21</uuid></response>";
	}
}