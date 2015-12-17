<?php
/**
 * @author Rainer Spittel (rainer at silverstripe dot com)
 * @package geocatalog
 * @subpackage model
 */

/**
 * 
 */
class MetadataAdmin extends ModelAdmin
{
    public static $menu_title = "Metadata";
    public static $url_segment = "metadata";
    
    public function init()
    {
        parent::init();
    }
    
    public static $managed_models = array(
        "MDMetadata",
        "MDContact",
    );
    
    public static $allowed_actions = array(
        "MDMetadata",
        "MDContact",
    );
}
