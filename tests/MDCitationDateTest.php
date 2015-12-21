<?php

class MDCitationDateTest extends SapphireTest
{
    
    
    /**
     * Checks for method getDateTimeNice with different valid and invalid dates
     */
    public function testgetDateTimeNice()
    {
        $date  = new MDCitationDate();
        
        $date->MDDateTime = '2009-11-01 00:00:00';
        $this->assertEquals($date->MDDateTime, '2009-11-01 00:00:00', 'checking initial value');
        
        $dateNice = $date->getDateTimeNice();
        $this->assertEquals($dateNice, '01/11/2009', 'initial value');

        $date->MDDateTime = '1960-01-01 23:24:25';
        $dateNice = $date->getDateTimeNice();
        $this->assertEquals($dateNice, '01/01/1960', 'date of other epoc');

        $date->MDDateTime = '';
        $dateNice = $date->getDateTimeNice();
        $this->assertEquals($dateNice, MDCodeTypes::$default_for_null_value, 'empty date value');

        $date->MDDateTime = null;
        $dateNice = $date->getDateTimeNice();
        $this->assertEquals($dateNice, MDCodeTypes::$default_for_null_value, 'null date value');
        
        //checking an empty ssDateTime value. (should be 1.1.1970 00:00:00)  

        $date->MDDateTime = new SS_Datetime();
        $dateNice = $date->getDateTimeNice();
        $this->assertEquals($dateNice, '01/01/1970 12:00pm', 'empty ssDateTime');
    }
    
    /**
     * Checks for method getDateTypeNice with several values
     *
     */
    public function testgetDateTypeNice()
    {
        $date  = new MDCitationDate();
        $date->MDDateType = 'publication';
        $this->assertEquals($date->MDDateType, 'publication', 'checking initial value');

        $type=$date->getDateTypeNice();
        $this->assertEquals($type, 'Publication', 'initial value');
        
        // checking empty value
        $date->MDDateType='';
        $type=$date->getDateTypeNice();
        $this->assertEquals($type, MDCodeTypes::$default_for_null_value, 'empty value');
        
        
        //null-Value should return the default for null
        $date->MDDateType=null;
        $type=$date->getDateTypeNice();
        $this->assertEquals($type, MDCodeTypes::$default_for_null_value, 'null value');
            
        //checking an invalid value
        $date->MDDateType='INVALID_TYPE';
        $type=$date->getDateTypeNice();
        $this->assertEquals($type, MDCodeTypes::$default_for_null_value, 'invalid value');
    }
}
