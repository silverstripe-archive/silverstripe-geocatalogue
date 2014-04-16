<?php
/**
 * @author Rainer Spittel (rainer at silverstripe dot com)
 * @package geocatalog
 * @subpackage commands
 *
 * Implementation of the GeoNetwork insert request.
 */

/**
 * Insert ISO19139 Metadata into GeoNetwork
 *
 * This command creates an XML GeoNetwork request to insert new ISO19139 metadata
 * into the GeoNetwork. Similar to {@see CreateInsertCommand} but utilize 
 * GeoNetwork specific API.
 */
class GnCreateInsertCommand extends ControllerCommand {

	static $schema_name = 'GenerateISO19139XML';
	
	static $gn_group = "3";

	/**
	 * Command execute
	 *
	 * Perform the request command. Creates a XML request based on the given
	 * XML request parameter and the metadata data. This request is used to insert new
	 * Metadata records into GeoNetwork.
	 */
	public function execute() {
		$data = $this->getParameters();

		$xml = '';

		// If xml field is not set, generate xml file out of the MD Metadata object.
		if (!isset($data['xml'])) {
			$cmd = $this->getController()->getCommand(self::$schema_name, $data);
			$xml = $cmd->execute();
		} else {
			$xml = $data['xml'];
		}

		$data = array();
		$data['data']       = $xml;
		$data['group']      = self::$gn_group;
		$data['template']   = "n";
		$data['title']      = "";
		$data['category']   = "_none_";
		$data['styleSheet'] = "_none_";
		$data['validation'] = "off";
		$data = $this->implode_with_keys($data);
		
		return $data;		
	}

	/**
	 * @param $data
	 *
	 * @return string
	 */
	static function implode_with_keys($data) {
	    $first = true;
	    $output = '';
	    foreach($data as $key => $value) {
	        if ($first) {
	            $output = ''.$key.'='.urlencode($value);
	            $first = false;
	        } else {
	            $output .= '&'.$key.'='.$value;
	        }
	    }
	    return $output;
	}
}