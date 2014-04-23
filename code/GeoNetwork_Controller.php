<?php
/**
 * Created by PhpStorm.
 * User: rspittel
 * Date: 22/04/14
 * Time: 4:35 PM
 */

class GeoNetwork_Controller extends Controller {

	public static $url_handlers = array(
		'dogetgroups/$ID' => 'dogetgroups'
	);


	static $allowed_actions = array(
		'dogetgroups'
	);

	public function dogetgroups($request) {

		$parameters = $request->allParams();
		$id = $parameters['ID'];

		$obj = DataObject::get_by_id('SiteTree',$id);

		$member = Member::currentUserID();

		if (!$obj->canEdit($member)) {
			Security::permissionFailure($this);
			return;
		}

		$url = $user = $pass = null;
		if ($obj->hasField('GeonetworkBaseURL')) {
			$url = $obj->GeonetworkBaseURL;
			$user = $obj->Username;
			$pass = $obj->Password;
		}

		$restfulService = new RestfulService($url,0);
		if ($user) {
			$restfulService->basicAuth($user, $pass);
		}

		// insert metadata into GeoNetwork
		$headers = array('Content-Type: application/xml');
		$postbody = "<request><type>groups</type></request>";

		$response = $restfulService->request('srv/eng/xml.info','POST',$postbody, $headers);
		if ($response->getStatusCode() == 401) {
			$this->response->setStatusCode(401);
			$this->response->setStatusDescription('Permission denied');
			return "Permission Denied";
		} elseif ($response->getStatusCode() == 404) {
			$this->response->setStatusCode(404);
			$this->response->setStatusDescription('File not found');
			return 'File not found.';
		} elseif ($response->getStatusCode() != 200) {
			$this->response->setStatusCode(500);
			$this->response->setStatusDescription('Server Error');
			return 'Server Error';
		}

		$xml = $response->getBody();
		$doc  = new DOMDocument();
		$doc->loadXML($xml);
		$xpath = new DOMXPath($doc);

		$array_groups = array();
		$groupList = $xpath->query('/info/groups/group');

		foreach($groupList as $group) {
			$id = $group->attributes->getNamedItem('id')->nodeValue;

			$xName = $xpath->query('name', $group);
			if ($xName->length > 0) {
				$name = $xName->item(0)->nodeValue;
			}

			$array_groups[$id] = $name;
		}
		return json_encode($array_groups);
	}
}

/*
 *
 *

$xml = <<<xml
<?xml version="1.0" encoding="UTF-8"?>
<info>
  <groups>
    <group id="4">
      <name>API</name>
      <description />
      <email />
      <referrer />
      <label />
    </group>
    <group id="-1">
      <name>GUEST</name>
      <description>self-registered users</description>
      <email />
      <referrer />
      <label />
    </group>
    <group id="1">
      <name>all</name>
      <description />
      <email />
      <referrer />
      <label />
    </group>
    <group id="0">
      <name>intranet</name>
      <description />
      <email />
      <referrer />
      <label />
    </group>
    <group id="2">
      <name>sample</name>
      <description>sample</description>
      <email>s@g.d</email>
      <referrer />
      <label />
    </group>
    <group id="3">
      <name>test</name>
      <description>test</description>
      <email />
      <referrer />
      <label />
    </group>
  </groups>
</info>
xml;

 *
 *
 */