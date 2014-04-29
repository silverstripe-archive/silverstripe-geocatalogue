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

	private static $menu_icon = 'geocatalogue/images/16x16/catalogue.png';

	static $menu_title = "Metadata";

	static $url_segment = "metadata";

	public $showImportForm = false;

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