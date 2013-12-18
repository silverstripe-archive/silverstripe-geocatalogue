<?php

/**
 * @package geocatalog
 * @subpackage tests
 */
class MDMetadataTest extends SapphireTest {

	/**
	 * Creates an array structure which is similar the the structure created
	 * by the ParseXML-command.
	 *
	 * @return array
	 */
	protected function getTestItem() {

		$mdItem = array();
		$mdItem['fileIdentifier']          	= trim('11111111-1111-1111-1111-11111111111');
		$mdItem['metadataStandardName']    	= trim('ISO 19115:2003/19139');
		$mdItem['metadataStandardVersion'] 	= trim('1.0');
		$mdItem['MDDateTime']				= '2009-11-01 00:00:00';
		$mdItem['MDDateType']				= 'publication';

		// create contact array
		$mdContact = array();
		$mdContact['MDIndividualName']   = trim('Joe Tester'); 
		$mdContact['MDOrganisationName'] = trim('SilverStripe Ltd');
		$mdContact['MDPositionName']     = trim('Developer');    
		
		$mdItem['MDContacts:MDContact'] = $mdContact;
		
		// create online resource array
		$ciOnlineResources = array();
		$ciOnlineResource = array();
		$ciOnlineResource['CIOnlineLinkage']  = trim('http://www.mysite.com'); 
		$ciOnlineResource['CIOnlineProtocol'] = trim('WWW:LINK-1.0-http--link');
		$ciOnlineResources[] = $ciOnlineResource;

		$ciOnlineResource = array();
		$ciOnlineResource['CIOnlineLinkage']  = trim('http://www.mysite.com/index.html');
		$ciOnlineResource['CIOnlineProtocol'] = trim('WWW:DOWNLOAD-1.0-http--download');
		$ciOnlineResources[] = $ciOnlineResource;
		
		$mdItem['CIOnlineResources:CIOnlineResource'] = $ciOnlineResources;

		$mdItem['MDAbstract'] = trim('Lorem ipsum dolor sit amet, consectetur adipiscing elit.');
		$mdItem['MDTitle']    = trim('Sample Dataset');

	//	$mdResourceFormat = array();
	//	$mdItem['MDResourceFormats:MDResourceFormat'] = $mdResourceFormat;

		$mdResourceConstraints = array();
		$mdResourceConstraint = array();
		$mdResourceConstraint['accessConstraints'] = trim('copyright');
		$mdResourceConstraint['useConstraints']    = trim('license');
		$mdResourceConstraint['otherConstraints']  = trim('Lorem ipsum dolor sit amet.');
		
		$mdResourceConstraints[] = $mdResourceConstraint;
		$mdItem['MDResourceConstraints:MDResourceConstraint'] = $mdResourceConstraints;
		
		//
		$mdItem['MDTopicCategory'] = 'biota';
		$mdItem['MDSpatialRepresentationType'] = 'stereoModel';
		// Places 	"-180;180;-90;90" => "World",
		$mdItem['MDWestBound'] = -180;
		$mdItem['MDEastBound'] = 180;
		$mdItem['MDSouthBound'] = -90;
		$mdItem['MDNorthBound'] = 90;
		
		return $mdItem;
	}

	/**
	 * Test the data load method of MDMetadata.
	 */
	function testLoadDataWithValidContent() {
		
		$metadata  = new MDMetadata();
		$item      = $this->getTestItem();
		$metadata->loadData($item);

		$this->assertEquals($metadata->getField('fileIdentifier'), '11111111-1111-1111-1111-11111111111', 'fileIdentifier failed');
		$this->assertEquals($metadata->getField('metadataStandardName'), 'ISO 19115:2003/19139', 'metadataStandardName failed');
		$this->assertEquals($metadata->getField('metadataStandardVersion'), '1.0', 'metadataStandardVersion failed');
		$this->assertEquals($metadata->getField('MDAbstract'), 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 'MDAbstract failed');
		$this->assertEquals($metadata->getField('MDTitle'), 'Sample Dataset', 'MDTitle failed');
	
	}
	
	/**
	 * Test data load for MDContacts via MDMetadata.
	 */
	function testLoadMDContactWithValidContent() {
		
		$metadata  = new MDMetadata();
		$item      = $this->getTestItem();
		$metadata->loadData($item);

		$items = $metadata->getComponents('MDContacts');
		
		$this->assertTrue(is_a($items,"ComponentSet"));
		$this->assertEquals($items->TotalItems(),1);

		foreach($items as $item) {
			$this->assertTrue(is_a($item,"MDContact"));
			$this->assertEquals($item->getField('MDIndividualName'),'Joe Tester');
			$this->assertEquals($item->getField('MDOrganisationName'),'SilverStripe Ltd');
			$this->assertEquals($item->getField('MDPositionName'),'Developer');
		}
	}
	
	/**
	 * Test data load for CIOnlineResources via MDMetadata.
	 */
	function testLoadCIOnlineResourcesWithValidContent() {
		
		$metadata  = new MDMetadata();
		$item      = $this->getTestItem();
		$metadata->loadData($item);

		$items = $metadata->getComponents('CIOnlineResources');
		
		$this->assertTrue(is_a($items,"ComponentSet"));
		
		// 2 items expected
		$this->assertEquals($items->TotalItems(),2);
		
		// iterate through items and test individually
		$iter = $items->getIterator();
		
		$item = $iter->current();
		$this->assertTrue(is_a($item,"CIOnlineResource"));
		$this->assertEquals($item->getField('CIOnlineLinkage'),'http://www.mysite.com');
		$this->assertEquals($item->getField('CIOnlineProtocol'),'WWW:LINK-1.0-http--link');

		$item = $iter->next();
		$this->assertTrue(is_a($item,"CIOnlineResource"));
		$this->assertEquals($item->getField('CIOnlineLinkage'),'http://www.mysite.com/index.html');
		$this->assertEquals($item->getField('CIOnlineProtocol'),'WWW:DOWNLOAD-1.0-http--download');
	}

	/**
	 * Test data load for MDResourceFormat via MDMetadata.
	 */
	function testLoadMDResourceFormatsWithValidContent() {
		
		$metadata  = new MDMetadata();
		$item      = $this->getTestItem();
		$metadata->loadData($item);

		$items = $metadata->getComponents('MDResourceFormats');
				
		$this->assertTrue(is_a($items,"ComponentSet"));
		
		// 2 items expected
		$this->assertEquals($items->TotalItems(),0);
		
	}
		
	/**
	 * Test data load for MDResourceConstraints via MDMetadata.
	 */
	function testLoadMDResourceConstraintsWithValidContent() {
		
		$metadata  = new MDMetadata();
		$item      = $this->getTestItem();
		
		$metadata->loadData($item);
		$items = $metadata->getComponents('MDResourceConstraints');
				
		$this->assertTrue(is_a($items,"ComponentSet"));

		// 1 item expected
		$this->assertEquals($items->TotalItems(),1);
		
		// iterate through items and test individually
		$iter = $items->getIterator();
		
		$item = $iter->current();

		$this->assertTrue(is_a($item,"MDResourceConstraint"));
		$this->assertEquals($item->getField('accessConstraints'),'copyright');
		$this->assertEquals($item->getField('useConstraints'),'license');
		$this->assertEquals($item->getField('otherConstraints'),'Lorem ipsum dolor sit amet.');
	}	
	
	/**
	*
	* checking CIOnlineResources_WebAddresses,
	* CIOnlineResources_HasFirstWebAddress and CIOnlineResources_FirstWebAddress
	*
	*/
	function testCIOnlineResources_WebAddresses(){
		
		MDMetadata::set_online_resource_web_url_filter(
			array(
				'WWW:LINK-1.0-http--link'
			)
		);
		
		$metadata  = new MDMetadata();
		$item      = $this->getTestItem();
		$metadata->loadData($item);
		//check the initial values
		$adresses = $metadata->CIOnlineResources_WebAddresses();

		//We should have 1 entry with the correct protocol 'WWW:LINK-1.0-http--link'
		$this->assertEquals($adresses->Count(),1,' one initial value');
		$address=$adresses->First();
		$this->assertEquals($address->CIOnlineLinkage,'http://www.mysite.com' , 'initial linkage value'); 
		$this->assertEquals($address->CIOnlineProtocol,'WWW:LINK-1.0-http--link','initial protocol value');
		// getting the first entry via the methods
		$this->assertTrue($metadata->CIOnlineResources_HasFirstWebAddress(),'checking initial value with CIOnlineResources_HasFirstWebAddress');
		$address=$metadata->CIOnlineResources_FirstWebAddress();
		$this->assertEquals($address->CIOnlineLinkage,'http://www.mysite.com','getting initial url via CIOnlineResources_FirstWebAddress'); 
		
		// now the same with invalid adresses;
		$address->CIOnlineLinkage='about:config';
		$address->CIOnlineProtocol='SomeInvalidProtocol';

		$adresses = $metadata->CIOnlineResources_WebAddresses();
		//We should have an empty DataObjectSet
		$this->assertTrue(is_a($adresses,"DataObjectSet"),'get a DataObjectSet on invalid data');
		//We should have 0 entry with the correct protocol 'WWW:LINK-1.0-http--link'
		$this->assertEquals($adresses->Count(),0,'Empty DataObjectSet due invalid data');
		// testing the methods
		$this->assertFalse($metadata->CIOnlineResources_HasFirstWebAddress(),'CIOnlineResources_HasFirstWebAddress on empty DataObjectSet');
		$address=$metadata->CIOnlineResources_FirstWebAddress();
		$this->assertEquals($address,null,'CIOnlineResources_FirstWebAddress on empty DataObjectSet'); 
		
		// doing the same without CIOnlineResources
		$metadata->CIOnlineResources=null;
		$adresses = $metadata->CIOnlineResources_WebAddresses();
		//We should have an empty DataObjectSet
		$this->assertTrue(is_a($adresses,"DataObjectSet"),'get a DataObjectSet on no data');
		//We should have 0 entry with the correct protocol 'WWW:LINK-1.0-http--link'
		$this->assertEquals($adresses->Count(),0);
		// testing the methods
		$this->assertFalse($metadata->CIOnlineResources_HasFirstWebAddress(),'CIOnlineResources_HasFirstWebAddress on no data');
		$address=$metadata->CIOnlineResources_FirstWebAddress();
		$this->assertEquals($address,null,'CIOnlineResources_FirstWebAddress on no data'); 
	}
	
	/**
	*  check getSpatialRepresentationTypeNice
	*/
	function testgetSpatialRepresentationTypeNice()
	{
		$metadata  = new MDMetadata();
		$item      = $this->getTestItem();
		$metadata->loadData($item);
		//check initial value
		$this->assertEquals($metadata->MDSpatialRepresentationType,'stereoModel','initial value');
		
		// make it nice
		$topic=$metadata->getSpatialRepresentationTypeNice();
		$this->assertEquals($topic,'Stereo model','on initial value');
		
		// checking empty value
		$metadata->MDSpatialRepresentationType='';
		$topic=$metadata->getSpatialRepresentationTypeNice();
		$this->assertEquals($topic,MDCodeTypes::$default_for_null_value,'on empty value');
		
		//null-Value should return the default for null
		$metadata->MDSpatialRepresentationType=null;
		$topic=$metadata->getSpatialRepresentationTypeNice();
		$this->assertEquals($topic,MDCodeTypes::$default_for_null_value, 'on null value');
		
		//checking an invalid value
		$metadata->MDSpatialRepresentationType='INVALID_TOPIC';
		$topic=$metadata->getSpatialRepresentationTypeNice();
		$this->assertEquals($topic,MDCodeTypes::$default_for_null_value,'on invalid value');
	}
	
	/**
	*  check getPlaceName
	*/
	function testgetPlaceName()
	{
		$metadata  = new MDMetadata();
		$item      = $this->getTestItem();
		$metadata->loadData($item);

		//check initial value
		$this->assertEquals($metadata->MDWestBound,-180,'initial value MDWestBound');
		$this->assertEquals($metadata->MDEastBound,180,'initial value MDEastBound');
		$this->assertEquals($metadata->MDSouthBound,-90,'initial valueMDSouthBound');
		$this->assertEquals($metadata->MDNorthBound,90,'initial valueMDNorthBound');
		
		// What is it
		$it=$metadata->getPlaceName();
		$this->assertEquals($it,'World','on initial values');
		
		// checking other value
		$metadata->MDWestBound=0;
		$metadata->MDEastBound=0;
		$metadata->MDSouthBound=0;
		$metadata->MDNorthBound=0;
		$it=$metadata->getPlaceName();
		$this->assertEquals($it,'Custom Location', 'on 0;0;0;0');
		
		//null-Value should return '' 
		$metadata->MDWestBound=null;
		$metadata->MDEastBound=null;
		$metadata->MDSouthBound=null;
		$metadata->MDNorthBound=null;
		$it=$metadata->getPlaceName();
		$this->assertEquals($it,'','on null;null;null;null');
		
		//checking an invalid value
		$metadata->MDWestBound='what';
		$metadata->MDEastBound='is';
		$metadata->MDSouthBound='wrong';
		$metadata->MDNorthBound='here';
		$it=$metadata->getPlaceName();
		$this->assertEquals($it,'', 'on invalid value');
		
	}
}
