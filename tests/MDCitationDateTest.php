<?php

class MDCitationDateTest extends SapphireTest {
	
	function testInitialDatetime()
	{
		$date  = new MDCitationDate();
		$this->assertEquals(null, $date->MDDateTime, 'initial value');
	}

	function testDatetimeInitialisation() {
		$date  = new MDCitationDate();
		$date->MDDateTime = '2009-11-01 00:00:00';
		$this->assertEquals('2009-11-01 00:00:00', $date->MDDateTime, 'DateTime has not been set correctly.');
	}

	function testDatetimeNiceInitialValue() {
		$date  = new MDCitationDate();
		$dateNice = $date->getDateTimeNice();
		$this->assertEquals(MDCodeTypes::$default_for_null_value, $dateNice, 'Initial value expected to be default value.');
	}

	function testDateTimeNice() {
		$date  = new MDCitationDate();
		$date->MDDateTime = '1960-01-01 23:24:25';
		$dateNice = $date->getDateTimeNice();
		$this->assertEquals('01/01/1960', $dateNice, 'Date to be expected 01/01/1960');

		$date->MDDateTime = '2014-12-31 00:00:00';
		$dateNice = $date->getDateTimeNice();
		$this->assertEquals('31/12/2014', $dateNice, 'Date to be expected 31/12/2014');
	}

	function testLeapYearForDateTimeNice() {
		$date  = new MDCitationDate();
		$date->MDDateTime = '2012-02-29 23:24:25';
		$dateNice = $date->getDateTimeNice();
		$this->assertEquals('29/02/2012', $dateNice, 'Date to be expected 01/01/1960');
	}

	function testFalseLeapYearForDateTimeNice() {
		$date  = new MDCitationDate();
		$date->MDDateTime = '2013-02-29 23:24:25';
		$dateNice = $date->getDateTimeNice();
		$this->assertEquals('29/02/2013', $dateNice, 'At this stage, SSDateTime and DateTime do not validate date-entries.');

	function testInvalidDateForDateTimeNice() {
		$date  = new MDCitationDate();
		$date->MDDateTime = '2013-31-40 25:24:25';
		$dateNice = $date->getDateTimeNice();
		$this->assertEquals('40/31/2013', $dateNice, 'At this stage, SSDateTime and DateTime do not validate date-entries.');
	}

	function testEntryDateTimeNice() {
		$date  = new MDCitationDate();
		$date->MDDateTime = '';
		$dateNice = $date->getDateTimeNice();
		$this->assertEquals(MDCodeTypes::$default_for_null_value, $dateNice, 'empty date value');
	}

	function testNULLDateTimeNice() {
		$date  = new MDCitationDate();
		$date->MDDateTime = null;
		$dateNice = $date->getDateTimeNice();
		$this->assertEquals(MDCodeTypes::$default_for_null_value, $dateNice, 'null date value');
	}

	function testInvalidDateTypeNice() {
		$date  = new MDCitationDate();
		$date->MDDateType='INVALID_TYPE';
		$type=$date->getDateTypeNice();
		$this->assertEquals($type,MDCodeTypes::$default_for_null_value,'invalid value');		
	}
}}