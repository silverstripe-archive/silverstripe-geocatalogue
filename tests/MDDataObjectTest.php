<?php

/**
 * @package geocatalog
 * @subpackage tests
 */
class MDDataObjectTest extends SapphireTest
{

    public function testLoadData()
    {
        $obj = new MDDataObject();
        
        $array = array();
        $array['test1'] = "TestValue1";
        $array['test2'] = "TestValue2";
        $array['test3'] = "TestValue3";
        
        $obj->loadData($array);
        
        $this->assertEquals($obj->test1, "TestValue1");
        $this->assertEquals($obj->test2, "TestValue2");
        $this->assertEquals($obj->test3, "TestValue3");
    }

    public function testLoadDataWithNullArray()
    {
        $obj = new MDDataObject();
        
        $array = null;
        $obj->loadData($array);

        $fields = $obj->db();
        
        $this->assertEquals(sizeof($fields), 0);
    }


    public function testLoadDataWithInvalidArray()
    {
        $obj = new MDDataObject();
        
        $array = "test";
        $obj->loadData($array);

        $fields = $obj->db();
        
        $this->assertEquals(sizeof($fields), 0);
    }
}
