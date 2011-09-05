<?php
/**
 * @author Rainer Spittel (rainer at silverstripe dot com)
 * @package geocatalog
 * @subpackage model
 */

/**
 * 
 */
class MetadataAdmin extends ModelAdmin {
	static $menu_title = "Metadata";
	static $url_segment = "metadata";
	
	function init(){
		parent::init();
	}
	
	static $managed_models = array(
		"MDMetadata",
		"MDContact",
	);
	
	static $allowed_actions = array(
		"MDMetadata",
		"MDContact",
	);

}