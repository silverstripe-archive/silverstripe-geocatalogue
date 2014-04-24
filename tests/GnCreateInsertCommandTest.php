<?php

/**
 * @package geocatalog
 * @subpackage tests
 */
class GnCreateInsertCommandTest extends SapphireTest {

	/**
	 * Also uses SimpleNzctFixture in setUp()
	 */
	static $fixture_file = 'geocatalogue/tests/GetRecordsCommandTest.yml';

	static $MDMetaDataItem = array(
		'fileIdentifier' => '0587e442-eaee-470d-a0d1-3e3a54cc983b',
		'metadataStandardName' => '',
		'metadataStandardVersion' => '1.0',
		'MDTitle' => 'Hydrological Basins',
		'MDAbstract' => 'Major hydrological basins and their sub-basins.',
		'MDTopicCategory' => 'inlandWaters',
	);

	protected $controller = null;

	protected $page = null;

	/**
	 * Initiate the controller and page classes and configure GeoNetwork service
	 * to use the mockup-controller for testing.
	 */
	function setUp() {
		parent::setUp();
		
		$url = Director::absoluteBaseURL() . 'GetRecordByIdCommandTest_Controller';

		$page = $this->objFromFixture('CataloguePage', 'catalogue');
		$page->GeonetworkBaseURL  = $url;

		$this->page = $page;

		$this->controller = new CataloguePage_Controller($page);
		$this->controller->pushCurrent();
	}

	/**
	 * Remove test controller from global controller-stack.
	 */
	function tearDown() {
		$this->controller->popCurrent();
		parent::tearDown();
	}

	function testGeoNetworkGroupPopulatedCorrectly() {
		$metadata = new MDMetadata;
		$metadata->loadData(self::$MDMetaDataItem);
		$data = array('MDMetadata' => $metadata);

		// GeonetworkGroupID = 3
		$this->page->GeonetworkGroupID = 3;
		$cmd = $this->controller->getCommand("GnCreateInsert", $data);
		$result = $cmd->execute();

		$parameters = $this->DecodeRequestBody($result);
		$this->assertEquals(3, $parameters['group'],"Group parameter has not been populated into the request.");

		// GeonetworkGroupID = 10
		$this->page->GeonetworkGroupID = 10;
		$cmd = $this->controller->getCommand("GnCreateInsert", $data);
		$result = $cmd->execute();

		$parameters = $this->DecodeRequestBody($result);

		$this->assertEquals(10, $parameters['group'],"Group parameter has not been populated into the request.");
	}

	function testCreationWithoutGeonetworkGroupID() {
		$metadata = new MDMetadata;
		$metadata->loadData(self::$MDMetaDataItem);

		$data = array('MDMetadata' => $metadata);

		$cmd = $this->controller->getCommand("GnCreateInsert", $data);

		try {
			$cmd->execute();
		}
		catch(Exception $e) {
			$this->assertEquals($e->getMessage(),'Required GeoNetwork Group-ID for inserting new records is not defined.',"Exception was thrown but with wrong error message.");
			return;
		}
		$this->assertTrue(false,"Exception expected, but hasn't been thrown.");
	}

	function testCreationWithEmptyGeonetworkGroupID() {
		$metadata = new MDMetadata;
		$metadata->loadData(self::$MDMetaDataItem);

		$data = array('MDMetadata' => $metadata);

		// GeonetworkGroupID = ''
		$this->page->GeonetworkGroupID = '';
		$cmd = $this->controller->getCommand("GnCreateInsert", $data);

		try {
			$cmd->execute();
		}
		catch(Exception $e) {
			$this->assertEquals($e->getMessage(),'Required GeoNetwork Group-ID for inserting new records is not defined.',"Exception was thrown but with wrong error message.");
			return;
		}
		$this->assertTrue(false,"Exception expected, but hasn't been thrown.");
	}

	function testCreationWithNULLGeonetworkGroupID() {
		$metadata = new MDMetadata;
		$metadata->loadData(self::$MDMetaDataItem);

		$data = array('MDMetadata' => $metadata);

		// GeonetworkGroupID = null
		$this->page->GeonetworkGroupID = null;
		$cmd = $this->controller->getCommand("GnCreateInsert", $data);

		try {
			$cmd->execute();
		}
		catch(Exception $e) {
			$this->assertEquals($e->getMessage(),'Required GeoNetwork Group-ID for inserting new records is not defined.',"Exception was thrown but with wrong error message.");
			return;
		}
		$this->assertTrue(false,"Exception expected, but hasn't been thrown.");
	}

	function testCreationWithDomainObject() {
		$this->page->GeonetworkGroupID = 3;

		$metadata = new MDMetadata;
		$metadata->loadData(self::$MDMetaDataItem);
		$data = array('MDMetadata' => $metadata);
		$cmd = $this->controller->getCommand("GnCreateInsert", $data);

		$result = $cmd->execute();

		$parameters = $this->DecodeRequestBody($result);

		$this->assertEquals(7,sizeof($parameters),"Expected 7 parameters in post body");

		$this->assertTrue(($parameters['data'] != ''),"Data parameter has not been populated into the request.");
		$this->assertEquals(3, $parameters['group'],"Group parameter has not been populated into the request.");
		$this->assertEquals('n', $parameters['template'],"Template parameter has not been populated into the request.");
		$this->assertEquals('', $parameters['title'],"Title parameter has not been populated into the request.");
		$this->assertEquals('_none_', $parameters['category'],"Category parameter has not been populated into the request.");
		$this->assertEquals('_none_', $parameters['styleSheet'],"StyleSheet parameter has not been populated into the request.");
		$this->assertEquals('off', $parameters['validation'],"Validation parameter has not been populated into the request.");
	}
	
	function testCreationWithXMLString() {
		$this->page->GeonetworkGroupID = 3;
		$xmlString = '<xml>This is a XML document</xml>';

		$data = array('xml' => $xmlString);
		$cmd = $this->controller->getCommand("GnCreateInsert", $data);

		$result = $cmd->execute();

		$parameters = $this->DecodeRequestBody($result);

		$this->assertEquals(7,sizeof($parameters),"Expected 7 parameters in post body");

		$this->assertTrue(($parameters['data'] != ''),"Data parameter has not been populated into the request.");
		$this->assertEquals($xmlString,urldecode($parameters['data']),"Expected an XML string");
	}

	/**
	 * testGnCreateInsertCommandWithEmptyData
	 *
	 * Using an empty object for generating the xml
	 *
	 */
	function testGnCreateInsertCommandWithEmptyData() {
		$this->page->GeonetworkGroupID = 3;

		$metadata = new MDMetadata;
		$data = array(
			'MDMetadata' => $metadata
		);
		$cmd = $this->controller->getCommand("GnCreateInsert", $data);
		$result = '&' . $cmd->execute(); // the & is neccessary for the stringpos below to match the first key
		
		// now $result should be a string of the type key=valye&otherkey=othervalue...
		if (strpos($result, '&data=') === false) $this->assertEquals(1,0,"Created command should include the data=");
		if (strpos($result, '&group=') === false) $this->assertEquals(1,0,"Created command should include the group=");
		if (strpos($result, '&template=') === false) $this->assertEquals(1,0,"Created command should include the template=");
		if (strpos($result, '&title=') === false) $this->assertEquals(1,0,"Created command should include the title=");
		if (strpos($result, '&category=') === false) $this->assertEquals(1,0,"Created command should include the category=");
		if (strpos($result, '&styleSheet=') === false) $this->assertEquals(1,0,"Created command should include the styleSheet=");
		if (strpos($result, '&validation=') === false) $this->assertEquals(1,0,"Created command should include the validation=");
	}

	/**
	 * testGnCreateInsertCommandWithoutDataObject
	 *
	 * Using no object for generating the xml
	 *
	 */
	function testGnCreateInsertCommandWithoutDataObject() {
		$this->page->GeonetworkGroupID = 3;

		$data = array();
		$cmd = $this->controller->getCommand("GnCreateInsert", $data);

		try {
			$cmd->execute();
		}
		catch(GenerateISO19139XMLCommand_Exception $e) {
			return;
		}
		$this->assertTrue(false,'GenerateISO19139XML should throw an error without data-object');
	}

	/**
	 * testGnCreateInsertCommandWithWrongDataObjectType
	 *
	 * Using an empty ViewableData object for generating the xml
	 *
	 */
	function testGnCreateInsertCommandWithWrongDataObjectType() {
		$this->page->GeonetworkGroupID = 3;

		$data = array(
			'MDMetadata' => new ViewableData
		);
		$cmd = $this->controller->getCommand("GnCreateInsert", $data);

		try {
			$cmd->execute();
		}
		catch(GenerateISO19139XMLCommand_Exception $e) {
			return;
		}
		$this->assertTrue(false,'GenerateISO19139XML should throw an error without a MDMetadata data-object');
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
}

