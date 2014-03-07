<?php

/**
 * @package geocatalog
 * @subpackage tests
 */
class TranslateXMLCommandTest extends SapphireTest {

	/**
	 * Also uses SimpleNzctFixture in setUp()
	 */
	static $fixture_file = 'geocatalogue/tests/GetRecordsCommandTest.yml';

	static $ISO19139response='<?xml version="1.0" encoding="UTF-8"?>
				<csw:GetRecordsResponse xmlns:csw="http://www.opengis.net/cat/csw/2.0.2" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.opengis.net/cat/csw/2.0.2 http://schemas.opengis.net/csw/2.0.2/CSW-discovery.xsd">
				  <csw:SearchStatus timestamp="2009-08-27T08:37:38" />
				  <csw:SearchResults numberOfRecordsMatched="1" numberOfRecordsReturned="1" elementSet="summary" nextRecord="0">
				    <gmd:MD_Metadata xmlns:gmd="http://www.isotc211.org/2005/gmd" xmlns:gml="http://www.opengis.net/gml" xmlns:gts="http://www.isotc211.org/2005/gts" xmlns:gco="http://www.isotc211.org/2005/gco" xmlns:geonet="http://www.fao.org/geonetwork">
				      <gmd:fileIdentifier>
				        <gco:CharacterString xmlns:srv="http://www.isotc211.org/2005/srv" xmlns:gmx="http://www.isotc211.org/2005/gmx">0587e442-eaee-470d-a0d1-3e3a54cc983b</gco:CharacterString>
				      </gmd:fileIdentifier>
				      <gmd:language>

				        <gco:CharacterString>eng</gco:CharacterString>
				      </gmd:language>
				      <gmd:characterSet>
				        <gmd:MD_CharacterSetCode codeListValue="utf8" codeList="http://www.isotc211.org/2005/resources/codeList.xml#MD_CharacterSetCode" />
				      </gmd:characterSet>
				      <gmd:dateStamp>
				        <gco:DateTime xmlns:srv="http://www.isotc211.org/2005/srv" xmlns:gmx="http://www.isotc211.org/2005/gmx">2007-07-19T14:45:07</gco:DateTime>
				      </gmd:dateStamp>

				      <gmd:metadataStandardName>
				        <gco:CharacterString xmlns:srv="http://www.isotc211.org/2005/srv" xmlns:gmx="http://www.isotc211.org/2005/gmx">ISO 19115:2003/19139</gco:CharacterString>
				      </gmd:metadataStandardName>
				      <gmd:metadataStandardVersion>
				        <gco:CharacterString xmlns:srv="http://www.isotc211.org/2005/srv" xmlns:gmx="http://www.isotc211.org/2005/gmx">1.0</gco:CharacterString>
				      </gmd:metadataStandardVersion>
				      <gmd:referenceSystemInfo>
				        <gmd:MD_ReferenceSystem>

				          <gmd:referenceSystemIdentifier>
				            <gmd:RS_Identifier>
				              <gmd:code>
				                <gco:CharacterString>WGS 1984</gco:CharacterString>
				              </gmd:code>
				            </gmd:RS_Identifier>
				          </gmd:referenceSystemIdentifier>
				        </gmd:MD_ReferenceSystem>

				      </gmd:referenceSystemInfo>
				      <gmd:identificationInfo>
				        <gmd:MD_DataIdentification>
				          <gmd:citation>
				            <gmd:CI_Citation>
				              <gmd:title>
				                <gco:CharacterString>Hydrological Basins in Africa (Sample record, please remove!)</gco:CharacterString>
				              </gmd:title>

				            </gmd:CI_Citation>
				          </gmd:citation>
				          <gmd:abstract>
				            <gco:CharacterString>Major hydrological basins and their sub-basins. This dataset divides the African continent according to its hydrological characteristics.
				The dataset consists of the following information:- numerical code and name of the major basin (MAJ_BAS and MAJ_NAME); - area of the major basin in square km (MAJ_AREA); - numerical code and name of the sub-basin (SUB_BAS and SUB_NAME); - area of the sub-basin in square km (SUB_AREA); - numerical code of the sub-basin towards which the sub-basin flows (TO_SUBBAS) (the codes -888 and -999 have been assigned respectively to internal sub-basins and to sub-basins draining into the sea)</gco:CharacterString>
				          </gmd:abstract>
				          <gmd:graphicOverview>
				            <gmd:MD_BrowseGraphic>
				              <gmd:fileName>

				                <gco:CharacterString>thumbnail_s.gif</gco:CharacterString>
				              </gmd:fileName>
				            </gmd:MD_BrowseGraphic>
				          </gmd:graphicOverview>
				          <gmd:graphicOverview>
				            <gmd:MD_BrowseGraphic>
				              <gmd:fileName>
				                <gco:CharacterString>thumbnail.gif</gco:CharacterString>

				              </gmd:fileName>
				            </gmd:MD_BrowseGraphic>
				          </gmd:graphicOverview>
				          <gmd:resourceConstraints>
				            <gmd:MD_Constraints>
				              <gmd:useLimitation gco:nilReason="missing">
				                <gco:CharacterString />
				              </gmd:useLimitation>
				            </gmd:MD_Constraints>

				          </gmd:resourceConstraints>
				          <gmd:spatialRepresentationType>
				            <gmd:MD_SpatialRepresentationTypeCode codeList="http://www.isotc211.org/2005/resources/codeList.xml#MD_SpatialRepresentationTypeCode" codeListValue="vector" />
				          </gmd:spatialRepresentationType>
				          <gmd:spatialResolution>
				            <gmd:MD_Resolution>
				              <gmd:equivalentScale>
				                <gmd:MD_RepresentativeFraction>
				                  <gmd:denominator>

				                    <gco:Integer>5000000</gco:Integer>
				                  </gmd:denominator>
				                </gmd:MD_RepresentativeFraction>
				              </gmd:equivalentScale>
				            </gmd:MD_Resolution>
				          </gmd:spatialResolution>
				          <gmd:language>
				            <gco:CharacterString>eng</gco:CharacterString>

				          </gmd:language>
				          <gmd:characterSet>
				            <gmd:MD_CharacterSetCode codeList="http://www.isotc211.org/2005/resources/codeList.xml#MD_CharacterSetCode" codeListValue="utf8" />
				          </gmd:characterSet>
				          <gmd:topicCategory>
				            <gmd:MD_TopicCategoryCode>inlandWaters</gmd:MD_TopicCategoryCode>
				          </gmd:topicCategory>
				          <gmd:extent>

				            <gmd:EX_Extent>
				              <gmd:geographicElement>
				                <gmd:EX_GeographicBoundingBox>
				                  <gmd:westBoundLongitude>
				                    <gco:Decimal>-17.3</gco:Decimal>
				                  </gmd:westBoundLongitude>
				                  <gmd:southBoundLatitude>
				                    <gco:Decimal>-34.6</gco:Decimal>

				                  </gmd:southBoundLatitude>
				                  <gmd:eastBoundLongitude>
				                    <gco:Decimal>51.1</gco:Decimal>
				                  </gmd:eastBoundLongitude>
				                  <gmd:northBoundLatitude>
				                    <gco:Decimal>38.2</gco:Decimal>
				                  </gmd:northBoundLatitude>
				                </gmd:EX_GeographicBoundingBox>

				              </gmd:geographicElement>
				            </gmd:EX_Extent>
				          </gmd:extent>
				        </gmd:MD_DataIdentification>
				      </gmd:identificationInfo>
				      <gmd:distributionInfo>
				        <gmd:MD_Distribution>
				          <gmd:distributionFormat>
				            <gmd:MD_Format>

				              <gmd:name>
				                <gco:CharacterString>ShapeFile</gco:CharacterString>
				              </gmd:name>
				              <gmd:version>
				                <gco:CharacterString>Grass Version 6.1</gco:CharacterString>
				              </gmd:version>
				            </gmd:MD_Format>
				          </gmd:distributionFormat>

				          <gmd:transferOptions>
				            <gmd:MD_DigitalTransferOptions>
				              <gmd:onLine>
				                <gmd:CI_OnlineResource>
				                  <gmd:linkage>
				                    <gmd:URL>http://www.fao.org/ag/AGL/aglw/aquastat/watresafrica/index.stm</gmd:URL>
				                  </gmd:linkage>
				                </gmd:CI_OnlineResource>

				              </gmd:onLine>
				              <gmd:onLine>
				                <gmd:CI_OnlineResource>
				                  <gmd:linkage xmlns:srv="http://www.isotc211.org/2005/srv" xmlns:gmx="http://www.isotc211.org/2005/gmx">
				                    <gmd:URL>http://localhost:8080/geonetwork/srv/en/resources.get?id=11&amp;fname=basins.zip&amp;access=private</gmd:URL>
				                  </gmd:linkage>
				                </gmd:CI_OnlineResource>

				              </gmd:onLine>
				              <gmd:onLine>
				                <gmd:CI_OnlineResource>
				                  <gmd:linkage>
				                    <gmd:URL>http://geonetwork3.fao.org/ows/296</gmd:URL>
				                  </gmd:linkage>
				                </gmd:CI_OnlineResource>
				              </gmd:onLine>

				            </gmd:MD_DigitalTransferOptions>
				          </gmd:transferOptions>
				        </gmd:MD_Distribution>
				      </gmd:distributionInfo>
				      <gmd:dataQualityInfo>
				        <gmd:DQ_DataQuality>
				          <gmd:lineage>
				            <gmd:LI_Lineage>
				              <gmd:statement>

				                <gco:CharacterString>The linework of the map is obtained by delineating drainage basin boundaries from an hydrologically corrected digital elevation model with a resolution of 1 * 1 km.</gco:CharacterString>
				              </gmd:statement>
				            </gmd:LI_Lineage>
				          </gmd:lineage>
				        </gmd:DQ_DataQuality>
				      </gmd:dataQualityInfo>
				    </gmd:MD_Metadata>
				  </csw:SearchResults>
				</csw:GetRecordsResponse>
';	

	static $HTMLResponseNoDocType='<html>
	  <head>
		<base href="http://www.silverstripe.com/" >
		<title>SilverStripe.com - Open Source CMS / Framework</title>
	</head>
		<body>
			<h1>SilverStripe</h1>
		</body>
	</html>';

	static $EmptyXMLResponse='<?xml version="1.0" encoding="UTF-8"?><nothing></nothing>';
	
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
	 * testTranslateXMLCommand
	 *
	 * Using the standard Response translating it into PHP-Code to set up the 
	 * the structure for MDMetadataObject 
	 *
	 */
	function testTranslateXMLCommand() {

		$baseURL = Director::baseFolder();
		
		//seting up the environment
		$data = array(
			'xml' => self::$ISO19139response,
			'xsl' => $baseURL.'/geocatalogue/xslt/ISO19139/iso19139_to_silverstripe.xsl',
		);
		$cmd = $this->controller->getCommand("TranslateXML", $data);
		$result = $cmd->execute();
				
		$position = strpos($result, '0587e442-eaee-470d-a0d1-3e3a54cc983b');

		if ($position === false) {
			$this->assertEquals(1,0,'UUID not found in translation.>>>'. $result . '<<<');
		}
	}
	
	/**
	 * testTranslateXMLCommandWithHTMLResponseNoDoctype
	 *
	 * Using a valid HTML-Response without a DocType is the way of the geonetwork server to
	 * indicate that an error has occured
	 *
	 */
	function testTranslateXMLCommandWithHTMLResponseNoDoctype() {

		$baseURL = Director::baseFolder();

		//seting up the environment
		$data = array(
			'xml' => self::$HTMLResponseNoDocType ,
			'xsl' => $baseURL.'/geocatalogue/xslt/iso19139_to_silverstripe.xsl',
		);
		$cmd = $this->controller->getCommand("TranslateXML", $data);
		//execute has to throw an error
		try {
			$result = $cmd->execute();
		}
		catch(TranslateXMLCommand_Exception $e) {
			return;
		}
		$this->assertTrue(false,'TranslateXML should throw an error on html-content starting with <html>');
	}

	/**
	 * testTranslateXMLCommandWithEmptyXMLResponse
	 *
	 * Using a valid XML-Response with just an empty
	 * should result in an empty string 
	 *
	 */
	function testTranslateXMLCommandWithEmptyXMLResponse() {

		//seting up the environment
		$baseURL = Director::baseFolder();

		$data = array(
			'xml' => self::$EmptyXMLResponse ,
			'xsl' => $baseURL.'/geocatalogue/xslt/ISO19139/iso19139_to_silverstripe.xsl',
		);
		$cmd = $this->controller->getCommand("TranslateXML", $data);
		//execute has not to throw an error
		try {
			$result = $cmd->execute();
		}
		catch(TranslateXMLCommand_Exception $e) {
			$this->assertTrue(false,'TranslateXML should NOT throw an error on valid xml-content');
		}
		$this->assertTrue(isset($result), 'results has not been set');
		$this->assertTrue($result == '' , 'results must be an empty string');
	}

	/**
	 * testTranslateXMLCommandWithEmptyResponse
	 *
	 * Using an empty Response should result in an empty string 
	 *
	 */
	function testTranslateXMLCommandWithEmptyResponse() {

		//seting up the environment
		$baseURL = Director::baseFolder();

		$data = array(
			'xml' => '' ,
			'xsl' => $baseURL.'/geocatalogue/xslt/ISO19139/iso19139_to_silverstripe.xsl',
		);
		$cmd = $this->controller->getCommand("TranslateXML", $data);
		//execute has not to throw an error
		try {
			$result = $cmd->execute();
		}
		catch(TranslateXMLCommand_Exception $e) {
			$this->assertTrue(false,'TranslateXML should NOT throw an error on empty Request');
		}
		$this->assertTrue(isset($result), 'results has been set');
		$this->assertTrue($result == '' , 'results must be an empty string');
	}

	/**
	 * testTranslateXMLCommandWithNullResponse
	 *
	 * Using a null Response should result in an empty string 
	 *
	 */
	function testTranslateXMLCommandWithNullResponse() {

		//seting up the environment
		$baseURL = Director::baseFolder();

		$data = array(
			'xml' => null,
			'xsl' => $baseURL.'/geocatalogue/xslt/ISO19139/iso19139_to_silverstripe.xsl',
		);
		$cmd = $this->controller->getCommand("TranslateXML", $data);
		//execute has not to throw an error
		try {
			$result = $cmd->execute();
		}
		catch(TranslateXMLCommand_Exception $e) {
			return;// ok, we got an exception
		}
		$this->assertTrue(false,'TranslateXML should throw an error on null Request');
	}

	/**
	 * testTranslateXMLCommandWithEmptyStylesheet
	 *
	 * Using an empty stylesheet should result in an exception 
	 *
	 */
	function testTranslateXMLCommandWithEmptyStylesheet() {

		//seting up the environment
		$data = array(
			'xml' => self::$EmptyXMLResponse ,
			'xsl' => '',
		);
		$cmd = $this->controller->getCommand("TranslateXML", $data);
		//execute has not to throw an error
		try {
			$result = $cmd->execute();
		}
		catch(TranslateXMLCommand_Exception $e) {
			return; //ok we got an excetion
		}
		$this->assertTrue(false,'TranslateXML should throw an exception on an empty stylesheet');
	}


	/**
	 * testTranslateXMLCommandWithNullStylesheet
	 *
	 * Using NO stylesheet should result in an exception 
	 *
	 */
	function testTranslateXMLCommandWithNullStylesheet() {

		//seting up the environment
		$data = array(
			'xml' => self::$EmptyXMLResponse ,
			'xsl' => null,
		);
		$cmd = $this->controller->getCommand("TranslateXML", $data);
		//execute has not to throw an error
		try {
			$result = $cmd->execute();
		}
		catch(TranslateXMLCommand_Exception $e) {
			return; //ok we got an excetion
		}
		$this->assertTrue(false,'TranslateXML should throw an exception if there is NO stylesheet');
	}

	/**
	 * testTranslateXMLCommandWithInvalidStylesheet
	 *
	 * Using an invalid stylesheet should result in an exception 
	 *
	 */
	function testTranslateXMLCommandWithInvalidStylesheet() {

		//seting up the environment
		$data = array(
			'xml' => self::$EmptyXMLResponse ,
			'xsl' => '../NoStylesheetFound',
		);
		$cmd = $this->controller->getCommand("TranslateXML", $data);
		//execute has not to throw an error
		try {
			$result = $cmd->execute();
		}
		catch(TranslateXMLCommand_Exception $e) {
			return; //ok we got an excetion
		}
		$this->assertTrue(false,'TranslateXML should throw an exception if the stylesheet could not be found');
	}


	
}
