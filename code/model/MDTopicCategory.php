<?php
/**
 * @author Rainer Spittel (rainer at silverstripe dot com)
 * @package geocatalogue
 * @subpackage model
 */

/**
 * MDTopicCategory implements the ISO19139 structure for topic categories. It will 
 * be stored alongside with MDMetadata class.
 */
class MDTopicCategory extends MDDataObject {

	/**
	 * Data structure for MDTopicCategory
	 */	
	static $db = array(
		"Value" => "Varchar",
	);
	
	/**
	 * Data relationships for MDTopicCategory
	 */
	static $has_one = array(
		"MDMetadata" => "MDMetadata",
	);

	public function getTopicCategoryNice() {
		$retValue = '';
		$codeTypes = MDCodeTypes::get_categories();
		if (isset($codeTypes[$this->Value])) {
			if ($codeTypes[$this->Value] != "(please select a category)") {
				$retValue = $codeTypes[$this->Value];
			}
		}
		return $retValue;
	}
	
}
