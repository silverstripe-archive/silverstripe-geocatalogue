<?php

class GnCreateUpdateCommand extends ControllerCommand {

	static $schema_name = 'GenerateISO19139XML';


	/**
	 * Command execute
	 *
	 * Perform the request command. Creates a XML request based on the given
	 * XML request parameter and the metadata data. This request is used to insert new
	 * Metadata records into GeoNetwork.
	 */
	public function execute() {
		DEBUG::log('GnCreateInsertCommand executed');

		$requestData = $this->getParameters();

		$xml = '';

		// If xml field is not set, generate xml file out of the MD Metadata object.
		if (!isset($requestData['xml'])) {
			$cmd = $this->getController()->getCommand(self::$schema_name, $requestData);
			$xml = $cmd->execute();
		} else {
			$xml = $requestData['xml'];
		}

		$data = array();
		$data['data']    = $xml;
		$data['id']      = $requestData['id'];
		$data['version'] = $requestData['version'];

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