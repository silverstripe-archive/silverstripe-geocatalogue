<?php
/**
 *
 * @author Rainer Spittel (rainer at silverstripe dot com)
 * @package geocatalogue
 * @subpackage model
 */
class CIOnlineResource extends MDDataObject {
	
	/**
	 * Data Structure for ISO19139 MDDistributionInfo
	 * @var array
	 */
	static $db = array(
		"CIOnlineLinkage" => "Varchar",
		"CIOnlineProtocol" => "Varchar",
		"CIOnlineName" => "Varchar",
		"CIOnlineDescription" => "Varchar",
		"CIOnlineFunction" => "Varchar"
	);

	/**
	 * Data relationships for MDContact
	 */
	static $has_one = array(
		"MDMetadata" => "MDMetadata",
	);
	
	static $public_protocols = array(
		'WWW:LINK-1.0-http--downloaddata',
		'WWW:LINK-1.0-http--link'
	);


	/**
	 * Sets the whiltelist array for support protocols. This array will be used
	 * to remove online-resources from the metadata-detail page.
	 *
	 * @param $value Array of protocol values, such as array('WWW:LINK-1.0-http--downloaddata', 'WWW:LINK-1.0-http--link')
	 */
	static function set_public_protocols($value) {
		self::$public_protocols = $value;
	}
	
	static function get_public_protocols() {
		return self::$public_protocols;
	}	

	/**
	 * Returns the nice, human readable string for the codetype (defined by
	 * the OGC ISO standard).
	 *
	 * @return string
	 */
	public function getCIOnlineProtocolNice() {
		$index = $this->CIOnlineProtocol;
		$codeTypes = MDCodeTypes::get_online_resource_protocol();
		return isset($codeTypes[$index]) ? $codeTypes[$index] : MDCodeTypes::$default_for_null_value;
	}
	
	/**
	 * Returns the nice, human readable string for the online name, truncates
	 * after 70 characters.
	 *
	 * @return string
	 */
	public function getCIOnlineNameNice() {
		$value = $this->CIOnlineName;

		$varchar = new Varchar(1024);
		$varchar->setValue($value);
		
		return htmlentities($varchar->LimitCharacters(70));
	}

	/**
	 * Returns the nice, human readable string for the codetype (defined by
	 * the OGC ISO standard).
	 *
	 * @return string
	 */
	public function getCIOnlineFunctionNice() {
		$index = $this->CIOnlineFunction;
		$codeTypes = MDCodeTypes::get_online_resource_function();
		return isset($codeTypes[$index]) ? $codeTypes[$index] : MDCodeTypes::$default_for_null_value;
	}

	/**
	 * Returns the nice, human readable string for CIOnlineLinkage.
	 *
	 * @return string
	 */
	public function getCIOnlineLinkageNice() {
		$value = $this->CIOnlineLinkage;
		if(isset($value) && $value != ''){
			if (strpos($value, "://") === false) {
				$value = "http://".$value;
			}
		}
		else {
			$value = MDCodeTypes::$default_for_null_value;
		}
		$varchar = new Varchar(1024);
		$varchar->setValue($value);
		
		return htmlentities($varchar->LimitCharacters(70));
	}
	
	
	
}