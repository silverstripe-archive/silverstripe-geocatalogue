<?php

/**
 * @package geocatalog
 * @subpackage tests
 */
class GetRecordsCommandTest extends SapphireTest
{

    /**
     * Also uses SimpleNzctFixture in setUp()
     */
  public static $fixture_file = 'geocatalogue/tests/GetRecordsCommandTest.yml';

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
    public function testGetRecordsInISOCommand()
    {
        $data = array();
        
        $data['requestxml']    = "cswGetRecordsSummaryISO_xml";
        $data['searchterm']    = "testGetRecordsCommand";
        $data['startPosition'] = "5";
        $data['maxRecords']    = "15";

        $cmd    = $this->controller->getCommand("GetRecords", $data);
        $result = $cmd->execute();
        
        $position = strpos($result, '<csw:GetRecords xmlns:csw="http://www.opengis.net/cat/csw/2.0.2" xmlns:ogc="http://www.opengis.net/ogc" xmlns:gml="http://www.opengis.net/gml" service="CSW" version="2.0.2" resultType="results" outputSchema="csw:IsoRecord" maxRecords="15" startPosition="5">');

        if ($position === false) {
            $this->assertEquals(1, 0, 'Invalid CSW GetRecord XML request.');
        }

        $position = strpos($result, '<Literal>%testGetRecordsCommand%</Literal>');
        if ($position === false) {
            $this->assertEquals(1, 0, 'Invalid literal search term.');
        }
    }
    
    /**
     * Test the testDefaultValues
     *
     * Test the testDefaultValues. The test controller GetRecordsCommandTest_Controller
     * expects a certain request and returns ok or failed.
     *
     * This test tests the cswGetRecordsSummaryISO_xml template only.
     *
     * @see GetRecordsCommand
     * @see GetRecordsCommandTest_Controller
     */
    public function testDefaultValuesForPaging()
    {
        $data = array();
        
        $data['requestxml']    = "cswGetRecordsSummaryISO_xml";
        $data['searchterm']    = "testGetRecordsCommand";

        $cmd = $this->controller->getCommand("GetRecords", $data);
        $result = $cmd->execute();
        
        $position = strpos($result, '<csw:GetRecords xmlns:csw="http://www.opengis.net/cat/csw/2.0.2" xmlns:ogc="http://www.opengis.net/ogc" xmlns:gml="http://www.opengis.net/gml" service="CSW" version="2.0.2" resultType="results" outputSchema="csw:IsoRecord" maxRecords="10" startPosition="0">');

        if ($position === false) {
            $this->assertEquals(1, 0, 'Invalid CSW GetRecord XML request.');
        }
    }

    /**
     * Test the testDefaultValuesForSearchTerm
     */
    public function testDefaultValuesForSearchTerm()
    {
        $data = array();

        $data['requestxml']    = "cswGetRecordsSummaryISO_xml";
        $data['startPosition'] = "0";
        $data['maxRecords']    = "10";

        $cmd = $this->controller->getCommand("GetRecords", $data);
        
        try {
            $result = $cmd->execute();
        } catch (CreateRequestCommand_Exception $e) {
            return;
        }
        $this->assertTrue(false, "Exception expected, but hasn't been thrown.");
    }

    /**
     * Test the testDefaultValuesForSearchTerm
     */
    public function testDefaultValuesForRequestXML()
    {
        $data = array();

        $data['searchterm']    = "testGetRecordsCommand";
        $data['startPosition'] = "0";
        $data['maxRecords']    = "10";

        $cmd = $this->controller->getCommand("GetRecords", $data);
        try {
            $result = $cmd->execute();
        } catch (CreateRequestCommand_Exception $e) {
            return;
        }
        $this->assertTrue(false, "Exception expected, but hasn't been thrown.");
    }
        
    /**
     * Test the testApostrophySearchTerm
     *
     *
     * This test tests the cswGetRecordsSummaryISO_xml template only.
     *
     * @see GetRecordsCommand
     * @see GetRecordsCommandTest_Controller
     */
    public function testApostrophySearchTerm()
    {
        $data = array();
        
        $data['requestxml']    = "cswGetRecordsSummaryISO_xml";
        $data['searchterm']    = "test'test";
        $data['startPosition'] = "0";
        $data['maxRecords']    = "0";

        $cmd = $this->controller->getCommand("GetRecords", $data);
        $result = $cmd->execute();
        
        $position = strpos($result, "<Literal>%test'test%</Literal>");
        if ($position === false) {
            $this->assertEquals(1, 0, 'Invalid literal search term.');
        }
    }

    /**
     * Test the testLessThanSearchTerm
     *
     *
     * This test tests the cswGetRecordsSummaryISO_xml template only.
     *
     * @see GetRecordsCommand
     * @see GetRecordsCommandTest_Controller
     */
    public function testLessThanSearchTerm()
    {
        $data = array();
        
        $data['requestxml']    = "cswGetRecordsSummaryISO_xml";
        $data['searchterm']    = "test<test";
        $data['startPosition'] = "0";
        $data['maxRecords']    = "0";

        $cmd = $this->controller->getCommand("GetRecords", $data);
        $result = $cmd->execute();
        
        $position = strpos($result, "<Literal>%test<test%</Literal>");
        if ($position === false) {
            $this->assertEquals(1, 0, 'Invalid literal search term.');
        }
    }
}


/**
 * @package geocatalog
 * @subpackage tests
 *
 * Mockup controller class to simulate the GeoNetwork side in this test.
 */
class GetRecordsCommandTest_Controller extends Controller
{

    /**
     * Standard method, not in use.
     */
    public function index()
    {
        BasicAuth::disable();
        return "failed";
    }

    /**
     * Returns the request body so that the calling unit test can perform the validation.
     *
     * @return string request body
     */
    public function getrecords($data)
    {
        $request = $data->getBody();
        return $request;
    }
}
