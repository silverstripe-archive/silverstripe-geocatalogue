<?php
/**
 * 
 * @author Rainer Spittel
 * @version $Id$
 * @package geocatalogue
 **/

/**
 * GnAuthenticationCommand class - implements the API to manage user credentials for GeoNetwork API calls.
 **/
class GnAuthenticationCommand extends ControllerCommand {

	public $restfulService = null;

	private $username = '';
	
	private $password = '';

	/**
	 * Sets username for GeoNetwork authentication.
	 *
	 * @param $username
	 * @return void
	 **/
	function setUsername($username) {
		$this->username = $username;
	}

	/**
	 * Returns username for GeoNetwork authentication.
	 *
	 * @return string
	 **/
	function getUsername() {
		return $this->username;
	}
	
	/**
	 * Sets password for GeoNetwork authentication.
	 *
	 * @param $password
	 * @return void
	 **/
	function setPassword($password) {
		$this->password = $password;
	}
	
	/**
	 * Returns password for GeoNetwork authentication.
	 *
	 * @return string
	 **/
	function getPassword() {
		return $this->password;
	}

	public function execute() {
	}
} // END class 