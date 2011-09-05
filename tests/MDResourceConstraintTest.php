<?php

/**
 * @package geocatalog
 * @subpackage tests
 */
class MDResourceConstraintTest extends SapphireTest {


	/**
	 * Test getAccessConstraintsNice of MDResourceConstraint.
	 */
	function testGetAccessConstraintsNice() {
		
		$mdResourceConstraint = array();
		$mdResourceConstraint['accessConstraints']  = trim('copyright'); 
		$mdResourceConstraint['useConstraints'] = trim('license');
		$mdResourceConstraint['otherConstraints'] = trim('otherRestrictions');
		
		// load the sub-array into the object
		$item = new MDResourceConstraint();
		$item->loadData($mdResourceConstraint);
		$this->assertEquals($item->getField('accessConstraints'), 'copyright', 'Problem creating the MDResourceConstraint');
		//Checking standardprotocol
		$this->assertEquals($item->getAccessConstraintsNice(),'Copyright','initial otherConstraints failed for getAccessConstraintsNice(). Value in MDCodeTypes might have changed');
		
		//other protocol
		$mdResourceConstraint['accessConstraints'] = trim('otherRestrictions');
		$item = new MDResourceConstraint();
		$item->loadData($mdResourceConstraint);
		$this->assertEquals($item->getAccessConstraintsNice(),'Other restrictions','other accessConstraints failed  for getAccessConstraintsNice(). Value in MDCodeTypes might have changed');
		
		// invalid protocol
		$mdResourceConstraint['accessConstraints'] = trim('Some invalid access constraints');
		$item = new MDResourceConstraint();
		$item->loadData($mdResourceConstraint);
		$this->assertEquals($item->getAccessConstraintsNice(),MDCodeTypes::$default_for_null_value,'invalid accessConstraints falied for getAccessConstraintsNice()');
		
		//empty protocol
		$mdResourceConstraint['accessConstraints'] = '';
		$item = new MDResourceConstraint();
		$item->loadData($mdResourceConstraint);
		$this->assertEquals($item->getAccessConstraintsNice(),MDCodeTypes::$default_for_null_value,'empty accessConstraints failed for getAccessConstraintsNice()');
		
		// null protocol
		$mdResourceConstraint['accessConstraints'] = null;
		$item = new MDResourceConstraint();
		$item->loadData($mdResourceConstraint);
		$this->assertEquals($item->getAccessConstraintsNice(),MDCodeTypes::$default_for_null_value,'null accessConstraints failed for getAccessConstraintsNice()');
	}


	/**
	 * Test getAccessConstraintsNice of MDResourceConstraint.
	 */
	function testGetUseConstraintsNice() {
		
		$mdResourceConstraint = array();
		$mdResourceConstraint['accessConstraints']  = trim('copyright'); 
		$mdResourceConstraint['useConstraints'] = trim('license');
		$mdResourceConstraint['otherConstraints'] = trim('otherRestrictions');
		
		// load the sub-array into the object
		$item = new MDResourceConstraint();
		$item->loadData($mdResourceConstraint);
		$this->assertEquals($item->getField('useConstraints'), 'license', 'Problem creating the MDResourceConstraint');
		//Checking standardprotocol
		$this->assertEquals($item->getUseConstraintsNice(),'License','initial otherConstraints failed for getUseConstraintsNice(). Value in MDCodeTypes might have changed');
		
		//other protocol
		$mdResourceConstraint['useConstraints'] = trim('otherRestrictions');
		$item = new MDResourceConstraint();
		$item->loadData($mdResourceConstraint);
		$this->assertEquals($item->getUseConstraintsNice(),'Other restrictions','other useConstraints failed  for getUseConstraintsNice(). Value in MDCodeTypes might have changed');
		
		// invalid protocol
		$mdResourceConstraint['useConstraints'] = trim('Some invalid access constraints');
		$item = new MDResourceConstraint();
		$item->loadData($mdResourceConstraint);
		$this->assertEquals($item->getUseConstraintsNice(),MDCodeTypes::$default_for_null_value,'invalid useConstraints falied for getUseConstraintsNice()');
		
		//empty protocol
		$mdResourceConstraint['useConstraints'] = '';
		$item = new MDResourceConstraint();
		$item->loadData($mdResourceConstraint);
		$this->assertEquals($item->getUseConstraintsNice(),MDCodeTypes::$default_for_null_value,'empty useConstraints failed for getUseConstraintsNice()');
		
		// null protocol
		$mdResourceConstraint['useConstraints'] = null;
		$item = new MDResourceConstraint();
		$item->loadData($mdResourceConstraint);
		$this->assertEquals($item->getUseConstraintsNice(),MDCodeTypes::$default_for_null_value,'null useConstraints failed for getUseConstraintsNice()');
	}


}
