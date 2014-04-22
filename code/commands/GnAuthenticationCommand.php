<?php
/**
 * 
 *
 * @author Rainer Spittel
 * @version $Id$
 * @copyright SilverStripe Ltd., 25 May, 2011
 * @package default
 **/

/**
 * GnAuthenticationCommand class - implements the API to manage user credentials for GeoNetwork API calls.
 *
 * @package geocatalog
 * @author Rainer Spittel
 **/
class GnAuthenticationCommand extends ControllerCommand {

	public $restfulService = null;

	private $username = '';
	
	private $password = '';

	/**
	 * Sets username for GeoNetwork authentication.
	 *
	 * @return void
	 * @author Rainer Spittel
	 **/
	function setUsername($username)
	{
		$this->username = $username;
	}

	/**
	 * Returns username for GeoNetwork authentication.
	 *
	 * @return string
	 * @author Rainer Spittel
	 **/
	function getUsername()
	{
		return $this->username;
	}
	
	/**
	 * Sets password for GeoNetwork authentication.
	 *
	 * @return void
	 * @author Rainer Spittel
	 **/
	function setPassword($password)
	{
		$this->password = $password;
	}
	
	/**
	 * Returns password for GeoNetwork authentication.
	 *
	 * @return string
	 * @author Rainer Spittel
	 **/
	function getPassword()
	{
		return $this->password;
	}
	
	public function execute() {
	}
} // END class 