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
class GnAuthenticationCommand extends ControllerCommand
{

    private $username = '';
    
    private $password = '';

    /**
     * Sets username for GeoNetwork authentication.
     *
     * @return void
     * @author Rainer Spittel
     **/
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * Returns username for GeoNetwork authentication.
     *
     * @return string
     * @author Rainer Spittel
     **/
    public function getUsername()
    {
        return $this->username;
    }
    
    /**
     * Sets password for GeoNetwork authentication.
     *
     * @return void
     * @author Rainer Spittel
     **/
    public function setPassword($password)
    {
        $this->password = $password;
    }
    
    /**
     * Returns password for GeoNetwork authentication.
     *
     * @return string
     * @author Rainer Spittel
     **/
    public function getPassword()
    {
        return $this->password;
    }
    
    public function execute()
    {
    }
} // END class 
