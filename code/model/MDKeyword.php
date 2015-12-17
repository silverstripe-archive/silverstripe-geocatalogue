<?php
/**
 * @author Rainer Spittel (rainer at silverstripe dot com)
 * @package geocatalog
 * @subpackage model
 */

/**
 * MDKeyword implements the ISO19139 structure for metadata keywords. It will 
 * be stored alongside with MDMetadata class.
 */
class MDKeyword extends MDDataObject
{

    /**
     * Data structure for MDKeyword
     */
    public static $db = array(
        "Value" => "Varchar",
    );
    
    /**
     * Data relationships for MDKeyword
     */
    public static $has_one = array(
        "MDMetadata" => "MDMetadata",
    );
}
