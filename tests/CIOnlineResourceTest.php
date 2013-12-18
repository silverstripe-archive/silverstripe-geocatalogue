<?php

/**
 * @package geocatalog
 * @subpackage tests
 */
class CIOnlineResourceTest extends SapphireTest {


	/**
	 * Test getCIOnlineProtocolNice of CIOnlineResource.
	 */
	function testGetCIOnlineProtocolNice() {
		
		$ciOnlineResource = array();
		$ciOnlineResource['CIOnlineLinkage']  = trim('http://www.mysite.com'); 
		$ciOnlineResource['CIOnlineProtocol'] = trim('WWW:LINK-1.0-http--link');
		$ciOnlineResource['CIOnlineFunction'] = trim('download');
		$ciOnlineResource['CIOnlineDescription'] = trim('Valid Description');
		$ciOnlineResource['CIOnlineName'] = trim('Valid Name');
		
		// load the sub-array into the object
		$item = new CIOnlineResource();
		$item->loadData($ciOnlineResource);
		$this->assertEquals($item->getField('CIOnlineName'), 'Valid Name', 'Problem creating the CIOnlineResource');
		//Checking standardprotocol
		$this->assertEquals($item->getCIOnlineProtocolNice(),'Web address (URL)','initial protocol failed for getCIOnlineProtocolNice(). Value in MDCodeTypes might have changed');
		
		//other protocol
		$ciOnlineResource['CIOnlineProtocol'] = trim('WWW:LINK-1.0-http--rss');
		$item = new CIOnlineResource();
		$item->loadData($ciOnlineResource);
		$this->assertEquals($item->getCIOnlineProtocolNice(),'RSS News feed (URL)','other protocol failed  for getCIOnlineProtocolNice(). Value in MDCodeTypes might have changed');
		
		// invalid protocol
		$ciOnlineResource['CIOnlineProtocol'] = trim('WWW:LINK-1.0-INVALID-PROTOCOL');
		$item = new CIOnlineResource();
		$item->loadData($ciOnlineResource);
		$this->assertEquals($item->getCIOnlineProtocolNice(),MDCodeTypes::$default_for_null_value,'invalid protocol falied for getCIOnlineProtocolNice()');
		
		//empty protocol
		$ciOnlineResource['CIOnlineProtocol'] = '';
		$item = new CIOnlineResource();
		$item->loadData($ciOnlineResource);
		$this->assertEquals($item->getCIOnlineProtocolNice(),MDCodeTypes::$default_for_null_value,'empty protocol failed for getCIOnlineProtocolNice()');
		
		// null protocol
		$ciOnlineResource['CIOnlineProtocol'] = null;
		$item = new CIOnlineResource();
		$item->loadData($ciOnlineResource);
		$this->assertEquals($item->getCIOnlineProtocolNice(),MDCodeTypes::$default_for_null_value,'null protocol failed for getCIOnlineProtocolNice()');
	}


	/**
	 * Test getCIOnlineFunctionNice of CIOnlineResource.
	 */
	function testGetCIOnlineFunctionNice() {
		
		$ciOnlineResource = array();
		$ciOnlineResource['CIOnlineLinkage']  = trim('http://www.mysite.com'); 
		$ciOnlineResource['CIOnlineProtocol'] = trim('WWW:LINK-1.0-http--link');
		$ciOnlineResource['CIOnlineFunction'] = trim('download');
		$ciOnlineResource['CIOnlineDescription'] = trim('Valid Description');
		$ciOnlineResource['CIOnlineName'] = trim('Valid Name');
		
		// load the sub-array into the object
		$item = new CIOnlineResource();
		$item->loadData($ciOnlineResource);
		$this->assertEquals($item->getField('CIOnlineFunction'), 'download', 'Problem creating the CIOnlineResource');
		//Checking standardprotocol
		$this->assertEquals($item->getCIOnlineFunctionNice(),'Download','initial CIOnlineFunction failed for getCIOnlineFunctionNice(). Value in MDCodeTypes might have changed');
		
		//other function
		$ciOnlineResource['CIOnlineFunction'] = trim('search');
		$item = new CIOnlineResource();
		$item->loadData($ciOnlineResource);
		$this->assertEquals($item->getCIOnlineFunctionNice(),'Search','other CIOnlineFunction failed  for getCIOnlineFunctionNice(). Value in MDCodeTypes might have changed');
		
		// invalid function
		$ciOnlineResource['CIOnlineFunction'] = trim('INVALID-FUNCTION');
		$item = new CIOnlineResource();
		$item->loadData($ciOnlineResource);
		$this->assertEquals($item->getCIOnlineFunctionNice(),MDCodeTypes::$default_for_null_value,'invalid CIOnlineFunction falied for getCIOnlineFunctionNice()');
		
		//empty function
		$ciOnlineResource['CIOnlineFunction'] = '';
		$item = new CIOnlineResource();
		$item->loadData($ciOnlineResource);
		$this->assertEquals($item->getCIOnlineFunctionNice(),MDCodeTypes::$default_for_null_value,'empty CIOnlineFunction failed for getCIOnlineFunctionNice()');
		
		// null function
		$ciOnlineResource['CIOnlineFunction'] = null;
		$item = new CIOnlineResource();
		$item->loadData($ciOnlineResource);
		$this->assertEquals($item->getCIOnlineFunctionNice(),MDCodeTypes::$default_for_null_value,'null CIOnlineFunction failed for getCIOnlineFunctionNice()');
	}

	/**
	 * Test getCIOnlineLinkageNice of CIOnlineResource.
	 */
	function testGetCIOnlineLinkageNice() {
		
		$ciOnlineResource = array();
		$ciOnlineResource['CIOnlineLinkage']  = trim('http://www.mysite.com'); 
		$ciOnlineResource['CIOnlineProtocol'] = trim('WWW:LINK-1.0-http--link');
		$ciOnlineResource['CIOnlineFunction'] = trim('download');
		$ciOnlineResource['CIOnlineDescription'] = trim('Valid Description');
		$ciOnlineResource['CIOnlineName'] = trim('Valid Name');
		
		// load the sub-array into the object
		$item = new CIOnlineResource();
		$item->loadData($ciOnlineResource);
		$this->assertEquals($item->getField('CIOnlineLinkage'), 'http://www.mysite.com', 'Problem creating the CIOnlineResource');
		//Checking standardprotocol
		$this->assertEquals($item->getCIOnlineLinkageNice(),'http://www.mysite.com','initial CIOnlineFunction failed for getCIOnlineLinkageNice(). Value in MDCodeTypes might have changed');
		
		//without protocol in th field http:// is added
		$ciOnlineResource['CIOnlineLinkage'] = trim('www.mysite.com');
		$item = new CIOnlineResource();
		$item->loadData($ciOnlineResource);
		$this->assertEquals($item->getCIOnlineLinkageNice(),'http://www.mysite.com','adding http:// to link failed  for getCIOnlineLinkageNice().');
		
		//other protocol in the field svn:// is ok
		$ciOnlineResource['CIOnlineLinkage'] = trim('svn://www.mysite.com');
		$item = new CIOnlineResource();
		$item->loadData($ciOnlineResource);
		$this->assertEquals($item->getCIOnlineLinkageNice(),'svn://www.mysite.com','other protocol failed for getCIOnlineLinkageNice().');
		
		//empty function
		$ciOnlineResource['CIOnlineLinkage'] = '';
		$item = new CIOnlineResource();
		$item->loadData($ciOnlineResource);
		$this->assertEquals($item->getCIOnlineLinkageNice(),MDCodeTypes::$default_for_null_value,'empty CIOnlineFunction failed for getCIOnlineLinkageNice()');
		
		// null function
		$ciOnlineResource['CIOnlineLinkage'] = null;
		$item = new CIOnlineResource();
		$item->loadData($ciOnlineResource);
		$this->assertEquals($item->getCIOnlineLinkageNice(),MDCodeTypes::$default_for_null_value,'null CIOnlineFunction failed for getCIOnlineLinkageNice()');
	}


}
