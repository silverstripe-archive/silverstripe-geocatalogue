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

		$mdResourceConstraints = array();
		$mdResourceConstraint = array();
		$mdResourceConstraint['accessConstraints'] = trim('copyright');
		$mdResourceConstraint['useConstraints']    = trim('license');
		$mdResourceConstraint['otherConstraints']  = trim('Lorem ipsum dolor sit amet.');
		
		$mdResourceConstraints[] = $mdResourceConstraint;
		$mdItem['MDResourceConstraints:MDResourceConstraint'] = $mdResourceConstraints;
		

		return $mdItem;
	}

	/**
	 * Test the data load method of MDMetadata.
	 */
	function testLoadDataWithValidContent() {
		$metadata  = new MDMetadata();

		$mdItem = array();
		$mdItem['fileIdentifier']          	= trim('11111111-1111-1111');
		$mdItem['metadataStandardName']    	= trim('ISO 19115:2003/19139');
		$mdItem['metadataStandardVersion'] 	= trim('1.0');

		$mdItem['MDTitle']    = trim('Sample Dataset');
		$mdItem['MDAbstract'] = trim('Lorem ipsum dolor sit amet, consectetur adipiscing elit.');

		$mdItem['MDSpatialRepresentationType'] = 'stereoModel';
		$mdItem['MDWestBound'] = -180;
		$mdItem['MDEastBound'] = 180;
		$mdItem['MDSouthBound'] = -90;
		$mdItem['MDNorthBound'] = 90;

		$metadata->loadData($mdItem);

		$this->assertEquals('11111111-1111-1111', $metadata->getField('fileIdentifier'), 'fileIdentifier failed');
		$this->assertEquals('ISO 19115:2003/19139', $metadata->getField('metadataStandardName'), 'metadataStandardName failed');
		$this->assertEquals('1.0', $metadata->getField('metadataStandardVersion'), 'metadataStandardVersion failed');

		$this->assertEquals('Lorem ipsum dolor sit amet, consectetur adipiscing elit.', $metadata->getField('MDAbstract'), 'MDAbstract failed');
		$this->assertEquals('Sample Dataset', $metadata->getField('MDTitle'), 'MDTitle failed');
	
		$this->assertEquals('stereoModel', $metadata->getField('MDSpatialRepresentationType'), 'MDSpatialRepresentationType failed');
		$this->assertEquals('Sample Dataset', $metadata->getField('MDTitle'), 'MDTitle failed');
		$this->assertEquals(-180, $metadata->getField('MDWestBound'), 'MDWestBound failed');
		$this->assertEquals(180, $metadata->getField('MDEastBound'), 'MDEastBound failed');
		$this->assertEquals(-90, $metadata->getField('MDSouthBound'), 'MDSouthBound failed');
		$this->assertEquals(90, $metadata->getField('MDNorthBound'), 'MDNorthBound failed');
	}
	
	/**
	 * Test data load for MDContacts via MDMetadata.
	 */
	function testLoadMDContactWithValidContent() {
		$metadata = new MDMetadata();
		$item = $this->getTestItem();
		$metadata->loadData($item);

		$items = $metadata->getComponents('MDContacts');
		$this->assertTrue(is_a($items,"ArrayList"));
		$this->assertEquals($items->count(),1);

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
		
		$this->assertTrue(is_a($items,"ArrayList"));
		$this->assertEquals($items->count(),2);
		
		// iterate through items and test individually
		$list = $items->toArray();
		
		$item = $list[0];
		$this->assertTrue(is_a($item,"CIOnlineResource"));
		$this->assertEquals($item->getField('CIOnlineLinkage'),'http://www.mysite.com');
		$this->assertEquals($item->getField('CIOnlineProtocol'),'WWW:LINK-1.0-http--link');

		$item = $list[1];
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
				
		$this->assertTrue(is_a($items,"ArrayList"));
		$this->assertEquals($items->count(),0);
	}
		
	/**
	 * Test data load for MDResourceConstraints via MDMetadata.
	 */
	function testLoadMDResourceConstraintsWithValidContent() {
		
		$metadata  = new MDMetadata();
		$item      = $this->getTestItem();
		
		$metadata->loadData($item);
		$items = $metadata->getComponents('MDResourceConstraints');
				
		$this->assertTrue(is_a($items,"ArrayList"));
		$this->assertEquals($items->count(),1);
		
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
	function testCIOnlineResources_WebAddresses() {
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
		$this->assertTrue(is_a($adresses,"ArrayList"),'get a DataObjectSet on invalid data');

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
		$this->assertTrue(is_a($adresses,"ArrayList"),'get a DataObjectSet on no data');
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
	function testSpatialRepresentationTypeNice() {
		$metadata  = new MDMetadata();
		$item      = $this->getTestItem();
		$item['MDSpatialRepresentationType'] = 'stereoModel';
		$metadata->loadData($item);

		// make it nice
		$topic=$metadata->getSpatialRepresentationTypeNice();
		$this->assertEquals($topic,'Stereo model','on initial value');
		
		// checking empty value
		$metadata->MDSpatialRepresentationType='';
		$topic=$metadata->getSpatialRepresentationTypeNice();
		$this->assertEquals(MDCodeTypes::$default_for_null_value,$topic,'on empty value');
		
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
	function testgetPlaceName() {
		$metadata  = new MDMetadata();
		$item      = $this->getTestItem();
		$item['MDWestBound'] = -180;
		$item['MDEastBound'] = 180;
		$item['MDSouthBound'] = -90;
		$item['MDNorthBound'] = 90;

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
