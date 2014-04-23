<?php

/**
 * @package geocatalog
 * @subpackage tests
 */
class GenerateISO19139XMLCommandTest extends SapphireTest {

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

	function setUp() {
		parent::setUp();

		$page = $this->objFromFixture('CataloguePage', 'catalogue');

		$this->controller = new CataloguePage_Controller($page);
		$this->controller->pushCurrent();
	}

	function tearDown() {
		$this->controller->popCurrent();
		parent::tearDown();
	}

	/**
	 * Test partial generation of the Metadata XML document from a domain object.
	 */
	function testGenerateISO19139XMLCommand() {

		$metadata = new MDMetadata;
		$metadata->loadData(self::$MDMetaDataItem);
		$data = array(
			'MDMetadata' => $metadata
		);

		$cmd = $this->controller->getCommand("GenerateISO19139XML", $data);
		$xml = $cmd->execute();
		
		// parse response document
		$doc  = new DOMDocument();
		$doc->loadXML($xml);
		$xpath = new DOMXPath($doc);

		$xpath->registerNamespace("gmd","http://www.isotc211.org/2005/gmd");
		$xpath->registerNamespace("gco","http://www.isotc211.org/2005/gco");

		$rootList = $xpath->query('/gmd:MD_Metadata');
		$this->assertEquals(1,$rootList->length,"Root element has not been created correctly.");

		$rootnode = $rootList->item(0);

		$list = $xpath->query('gmd:fileIdentifier/gco:CharacterString',$rootnode);
		$this->assertEquals(1,$list->length,"Number of fileIdentifier elements supposed to be one item only.");

		$node = $list->item(0);
		$this->assertEquals('0587e442-eaee-470d-a0d1-3e3a54cc983b',$node->nodeValue,"File-Identifier node has not been set correctly.");
	}

	/**
	 * Test XML generation with an empty domain object.
	 */
	function testGenerateISO19139XMLCommandWithEmptyData() {
		$metadata = new MDMetadata;
		$data = array(
			'MDMetadata' => $metadata
		);

		$cmd = $this->controller->getCommand("GenerateISO19139XML", $data);
		$xml = $cmd->execute();

		// parse response document
		$doc  = new DOMDocument();
		$doc->loadXML($xml);
		$xpath = new DOMXPath($doc);

		$xpath->registerNamespace("gmd","http://www.isotc211.org/2005/gmd");
		$xpath->registerNamespace("gco","http://www.isotc211.org/2005/gco");

		$rootList = $xpath->query('/gmd:MD_Metadata');
		$this->assertEquals(1,$rootList->length,"Root element has not been created correctly.");

		$rootnode = $rootList->item(0);

		$list = $xpath->query('gmd:fileIdentifier/gco:CharacterString',$rootnode);
		$this->assertEquals(1,$list->length,"Number of fileIdentifier elements supposed to be one item only.");

		$node = $list->item(0);
		$this->assertEquals('',$node->nodeValue,"Empty file-Identifier node was expected.");
	}

	/**
	 * Test XML generation with a no data. Expected to catch an exception.
	 */
	function testGenerateISO19139XMLCommandWithoutDataParameter() {

		$data = null;
		$cmd = $this->controller->getCommand("GenerateISO19139XML", $data);

		try {
			$cmd->execute();
		}
		catch(GenerateISO19139XMLCommand_Exception $e) {
			return;
		}
		$this->assertTrue(false,'GenerateISO19139XML should throw an error without a MDMetadata data-object');
	}

	/**
	 * Test XML generation with a empty data object. Expected to catch an exception.
	 */
	function testGenerateISO19139XMLCommandWithWrongDataParameter() {

		$data = array();
		$cmd = $this->controller->getCommand("GenerateISO19139XML", $data);

		try {
			$cmd->execute();
		}
		catch(GenerateISO19139XMLCommand_Exception $e) {
			return;
		}
		$this->assertTrue(false,'GenerateISO19139XML should throw an error without a data-object');
	}

	/**
	 * Test XML generation with a null domain object. Expected to catch an exception.
	 */
	function testGenerateISO19139XMLCommandWithNullDomainObject() {

		$data = array(
			'MDMetadata' => null
		);
		$cmd = $this->controller->getCommand("GenerateISO19139XML", $data);

		try {
			$cmd->execute();
		}
		catch(GenerateISO19139XMLCommand_Exception $e) {
			return;
		}
		$this->assertTrue(false,'GenerateISO19139XML should throw an error without a data-object');
	}

	
}

