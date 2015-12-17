<?php

/**
 * @package geocatalog
 * @subpackage tests
 */
class GenerateISO19139XMLCommandTest extends SapphireTest
{

    /**
     * Also uses SimpleNzctFixture in setUp()
     */
    public static $fixture_file = 'geocatalogue/tests/GetRecordsCommandTest.yml';

    public static $MDMetaDataItem = array(
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
    public function setUp()
    {
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
    public function tearDown()
    {
        $this->controller->popCurrent();
        
        parent::tearDown();
    }


    /**
     * testGenerateISO19139XMLCommand
     *
     * Using the standard Response translating it into PHP-Code to set up the 
     * the structure for MDMetadataObject 
     *
     */
    public function testGenerateISO19139XMLCommand()
    {
        //seting up the environment
        $metadata = new MDMetadata;
        $metadata->loadData(self::$MDMetaDataItem);
        $data = array(
            'MDMetadata' => $metadata
        );
        $cmd = $this->controller->getCommand("GenerateISO19139XML", $data);
        $result = $cmd->execute();
        
        // now we should have an xml in $result
        $result = preg_replace('/\<!\-\-.*\-\-\>\n/', '', $result); //remove any comment line from the file
        $position = strpos($result, '<gmd:MD_Metadata');

        if ($position === false) {
            $this->assertEquals(1, 0, "Invalid XML! No Starting tag '<gmd:MD_Metadata' found");
        }

        if ($position > 0) {
            $this->assertEquals(1, 0, "returned value should start with '<gmd:MD_Metadata' at the very first beginning not at $position");
        }

        $position = strpos($result, '>0587e442-eaee-470d-a0d1-3e3a54cc983b<');
        if ($position === false) {
            $this->assertEquals(1, 0, "Missing fileIdentifier '0587e442-eaee-470d-a0d1-3e3a54cc983b'! should be in there");
        }
    }
    
    /**
     * testGenerateISO19139XMLCommandWithEmptyData
     *
     * Using an empty object for generating the xml
     *
     */
    public function testGenerateISO19139XMLCommandWithEmptyData()
    {
        //seting up the environment
        $metadata = new MDMetadata;
        $data = array(
            'MDMetadata' => $metadata
        );
        $cmd = $this->controller->getCommand("GenerateISO19139XML", $data);
        $result = $cmd->execute();
        
        // now we should have an xml in $result
        $result = preg_replace('/\<!\-\-.*\-\-\>\n/', '', $result); //remove any comment line from the file
        $position = strpos($result, '<gmd:MD_Metadata');

        if ($position === false) {
            $this->assertEquals(1, 0, "Invalid XML! No Starting tag '<gmd:MD_Metadata' found");
        }

        if ($position > 0) {
            $this->assertEquals(1, 0, "returned value should start with '<gmd:MD_Metadata' at the very first beginning");
        }
    }

    /**
     * testGenerateISO19139XMLCommandWithoutDataObject
     *
     * Using no object for generating the xml
     *
     */
    public function testGenerateISO19139XMLCommandWithoutDataObject()
    {
        //seting up the environment
        $data = null;
        $cmd = $this->controller->getCommand("GenerateISO19139XML", $data);
        $metadata = new ViewableData;
        $data = array(
            'MDMetadata' => $metadata
        );

        try {
            $result = $cmd->execute();
        } catch (GenerateISO19139XMLCommand_Exception $e) {
            return;
        }
        $this->assertTrue(false, 'GenerateISO19139XML should throw an error without a MDMetadata data-object');
    }

    /**
     * testGenerateISO19139XMLCommandWithWrongDataObjectType
     *
     * Using a wrong object for generating the xml
     *
     */
    public function testGenerateISO19139XMLCommandWithWrongDataObjectType()
    {
        //seting up the environment
        $data = array();
        $cmd = $this->controller->getCommand("GenerateISO19139XML", $data);

        try {
            $result = $cmd->execute();
        } catch (GenerateISO19139XMLCommand_Exception $e) {
            return;
        }
        $this->assertTrue(false, 'GenerateISO19139XML should throw an error without a data-object');
    }
}
