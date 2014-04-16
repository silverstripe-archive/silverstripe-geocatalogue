<?php
/**
 * @author Robin Marshall (robin at mdigital dot co dot nz)
 * @package geocatalog
 * @subpackage model
 * 
 * The MDCitationDate Data object is used to model the ISO19139 MD-Date XML
 * structure. 
 * This is not a full ISO19139 implementation, just a bare minimum to meet the 
 * business requirements.
 */
class MDCitationDate extends MDDataObject {
	
	/**
	 * Data structure for MDContact
	 */
	static $db = array(		
		"MDDateTime" => "SSDatetime",						// mandatory
		"MDDateType" => "Varchar",							// mandatory
	);

  /*
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
	public function getDateTypeNice() {
		$index = $this->MDDateType;
		$codeTypes = MDCodeTypes::get_date_types();
		return isset($codeTypes[$index]) ? $codeTypes[$index] : MDCodeTypes::$default_for_null_value;
	}	

	/**
	 * Returns the MDDateTime in RFC3339 string format (incl. timezone).
	 * This is required for the atom feed support.
	 */
	public function getMDDateTimeInRFC3339() {
		$date = $this->MDDateTime;
		$result = '';
		
		if ($date == NULL) {
			$result =  '';
		} else {	
			// strtotime doesn't like british dates so we reverse it first
			$tempDate= $date;
			// expected format by jQuery-UI datepicker: '2014-02-04 00:00:00'
			$dateParts=explode(' ',$tempDate);
			$dateParts=explode('-',$dateParts[0]);
			$date = $dateParts[2] . '-' . $dateParts[1] . '-' . $dateParts[0]; 
			$result = date('c', strtotime($date));
		}
		return $result;
	}

	/**
	 * Returns the nice, human readable string for MDDateTime. Without the
	 * validation (if the date instance is a SSDateTime object), you will get
	 * a default 01/01/1970 date when the user hasn't entered a date.
	 *
	 * @return string
	 */
	public function getDateTimeNice() {
		$date = $this->MDDateTime;
		$result = '';
		
		if ($date == NULL) {
			$result =  MDCodeTypes::$default_for_null_value;
		} elseif (is_string($date)) {
			
			if($date == '') {
				return MDCodeTypes::$default_for_null_value;
			}
			//should be YYYY-MM-DD hh:mm:ss
			//Take the date-part, explode YYYY MM DD, reverse them, and out them togater with slashes
			//And retrun the result
			$result = implode("/",array_reverse(explode("-",substr($date,0,10))));
		} elseif (is_a($date, "SS_DateTime") || is_a($date, "SSDateTime")) {
			// This if-clause ensures that the geonetwork catalogue works with
			// Silverstripe CMS V2.3 and V2.4.
			$result = $date->Nice();
		}
		return $result;
	}
	
	/**
	 * Overwrites a specific behaviour while loaded the array data into the object.
	 * date data can come from two sources in the XML schema: MDDatetime and MDDate.
	 */
	public function loadData($data) {
		if (isset($data['MDDate']) && $data['MDDate']) {
			$data['MDDateTime'] = $data['MDDate'];
		}
		return parent::loadData($data);
	}
}
