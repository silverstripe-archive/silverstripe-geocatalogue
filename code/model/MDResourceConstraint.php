<?php
/**
 * @author Rainer Spittel (rainer at silverstripe dot com)
 * @package geocatalog
 * @subpackage model
 */

/**
 * MDResourceConstraint implements the ISO19139 structure for metadata resource constraints. It will 
 * be stored alongside with MDMetadata class.
 */
class MDResourceConstraint extends MDDataObject {
	
	/**
	 * Data Structure for ISO19139 MDResourceConstraint
	 * @var array
	 */
	static $db = array(
		"accessConstraints" => "Varchar",	
		"useLimitation" => "Varchar",
		"useConstraints" => "Varchar",		// mandatory
		"otherConstraints" => "Varchar"	
	);

	static $restrictionCode = array(
		"copyright" => "exclusive right to the publication, production, or sale of the rights to a literary, dramatic, musical, or artistic work, or to the use of a commercial print or label, granted by law for a specified period of time to an author, composer, artist, distributor",
		"patent" => "government has granted exclusive right to make, sell, use or license an invention or discovery",
		"patentPending" => "produced or sold information awaiting a patent",
		"trademark" => "a name, symbol, or other device identifying a product, officially registered and legally restricted to the use of the owner or manufacturer",
		"license" => "formal permission to do something",
		"intellectualPropertyRights" => "rights to financial benefit from and control of distribution of non-tangible property that is a result of creativity",
		"restricted" => "withheld from general circulation or disclosure",
		"otherRestrictions" => "limitation not listed"
	);

	/**
	 * Data relationships for MDContact
	 */
	static $has_one = array(
		"MDMetadata" => "MDMetadata",
	);
	
	/**
	 * Returns the nice, human readable string for the codetype (defined by
	 * the OGC ISO standard).
	 *
	 * @return string
	 */
	public function getAccessConstraintsNice() {
		$index = $this->accessConstraints;
		$codeTypes = MDCodeTypes::get_resource_constraints();
		return isset($codeTypes[$index]) ? $codeTypes[$index] : MDCodeTypes::$default_for_null_value;
	}

	/**
	 * Returns the nice, human readable string for the codetype (defined by
	 * the OGC ISO standard).
	 *
	 * @return string
	 */
	public function getUseConstraintsNice() {
		$index = $this->useConstraints;
		$codeTypes = MDCodeTypes::get_resource_constraints();
		return isset($codeTypes[$index]) ? $codeTypes[$index] : MDCodeTypes::$default_for_null_value;
	}
	
	public function getUseConstraintsText() {
		$index = $this->useConstraints;
		return isset(self::$restrictionCode[$index]) ? self::$restrictionCode[$index] : "";
	}
	
}