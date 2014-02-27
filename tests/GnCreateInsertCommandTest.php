<?php

/**
 * @package geocatalogue
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
		'MDTitle' => 'Hydrological Basins in Africa (Sample record, please remove!)',
		'MDAbstract' => 'Major hydrological basins and their sub-basins. This dataset divides the African continent according to its hydrological characteristics.
	The dataset consists of the following information:- numerical code and name of the major basin (MAJ_BAS and MAJ_NAME); - area of the major basin in square km (MAJ_AREA); - numerical code and name of the sub-basin (SUB_BAS and SUB_NAME); - area of the sub-basin in square km (SUB_AREA); - numerical code of the sub-basin towards which the sub-basin flows (TO_SUBBAS) (the codes -888 and -999 have been assigned respectively to internal sub-basins and to sub-basins draining into the sea)',
		'MDTopicCategory' => 'inlandWaters',
	);
	

	
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
		
		GetRecordsCommand::set_catalogue_url("/getrecords?usetestmanifest=1&flush=1");
	}

	/**
	 * Remove test controller from global controller-stack.
	 */
	function tearDown() {
		
		$this->controller->popCurrent();
		
		parent::tearDown();
	}


	/**
	 * testGnCreateInsertCommand
	 *
	 * Using the standard Response translating it into PHP-Code to set up the 
	 * the structure for MDMetadataObject 
	 *
	 */
	function testGnCreateInsertCommand() {
		//seting up the environment
		$metadata = new MDMetadata;
		$metadata->loadData(self::$MDMetaDataItem);
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
	 * testGnCreateInsertCommandWithEmptyData
	 *
	 * Using an empty object for generating the xml
	 *
	 */
	function testGnCreateInsertCommandWithEmptyData() {
		//seting up the environment
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
		//seting up the environment
		$data = array();
		$cmd = $this->controller->getCommand("GnCreateInsert", $data);

		try {
			$result = $cmd->execute();
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
		//seting up the environment
		$metadata = new ViewableData;
		$data = array(
			'MDMetadata' => $metadata
		);
		$cmd = $this->controller->getCommand("GnCreateInsert", $data);

		try {
			$result = $cmd->execute();
		}
		catch(GenerateISO19139XMLCommand_Exception $e) {
			return;
		}
		$this->assertTrue(false,'GenerateISO19139XML should throw an error without a MDMetadata data-object');
	}


	
}

