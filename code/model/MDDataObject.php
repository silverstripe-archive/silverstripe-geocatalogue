<?php
/**
 *
 * @author Rainer Spittel (rainer at silverstripe dot com)
 * @package geocatalog
 * @subpackage model
 */
class MDDataObject extends DataObject
{

    /**
     * This method loads the content of the given array $data into the current
     * dataobject. Similar to the DataObject-merge function, but 
     * without iterating through the relationships.
     * 
     * @param $data array of attributes (keys should match to the $db fields).
     */
    public function loadData($data)
    {
        if ($data == null) {
            return;
        }
        
        if (!is_array($data)) {
            return;
        }

        foreach ($data as $k => $v) {
            $this->$k =  Convert::xml2raw($v);
        }
    }
}
