<?php
/**
 * @author Rainer Spittel (rainer at silverstripe dot com)
 * @package geocatalog
 * @subpackage commands
 */


/**
 * This class is used to model the register metadata form and it's behaviour.
 * The form can be customised and overwritten in the controller, but the
 * page stores the information of the registered GeoNetwork Catalogue.
 * This enables us to use multiple search pages, running queries on different
 * Geonetwork servers.
 */
class RegisterDataPage extends Page
{
	public static $db = array('GeonetworkBaseURL' => "Varchar",
	                          'RedirectOnSuccess' => "Varchar",
	                          'Username' => "Varchar",
	                          'Password' => "Varchar",
	                          'SendConfitmationsTo' => "Varchar",
	                          'EmailName' => "Varchar",
							  'GeonetworkGroupID' => 'Int',
							  'GeonetworkName' => 'Varchar',
							  'AutoPublish' => 'Boolean',
							  'Privilege' => 'Varchar');

	/**
	 * Return the email address of the sender.
	 */
	public static function get_email_sender() {
		return Config::inst()->get('Catalogue', 'email_sender');
	}

	/**
	 * Sets the static sender-email address. Applies a regular expression
	 * validation to check if the email format is valid.
	 *
	 * @return string return the old email address which has been replaced.
	 */
	public function set_email_sender($value) {
		Config::inst()->update('Catalogue', 'email_sender',$value);
		return $value;
	}

	/**
	 * Overwrites SiteTree.getCMSFields to change the CMS form behaviour,
	 *  i.e. by adding form fields for the additional attributes defined in
	 * {@link RegisterDataPage::$db}.
	 */
	function getCMSFields() {
		$fields = parent::getCMSFields();

		Requirements::javascript('geocatalogue/javascript/GeonetworkUrlValidator.js');
		$pagesSearch = CatalogueHomePage::get_page_subclasses('CataloguePage');

		$groupArray = array($this->GeonetworkGroupID => $this->GeonetworkName);

		// customise form fields
		$fields->addFieldsToTab('Root.Catalogue',
				array(
					$gnfields = new CompositeField(array(
						$url = new TextField('GeonetworkBaseURL', 'URL'),
						$user = new TextField('Username','Username'),
						$pass = new PasswordField('Password','Password')
					)),
					$grpfields = new CompositeField(array(
						new HiddenField('GeonetworkGroupID','GeonetworkGroupID'),
						new HiddenField('GeonetworkName','GeonetworkName'),
						$gnGroupDropdown = new DropdownField('GeonetworkGroupID_dp','Geonetwork-Group', $groupArray ,$this->GeonetworkGroupID),
						new LiteralField('groupsbutton1',"<div class='field'><div class='middleColumn'><a href='#' data-icon='add' data-selected='".$this->GeonetworkGroupID."' class='ss-ui-button geonetwork_load_groups' data-url='geonetwork_info/dogetgroups/".$this->ID."'>Load and update list of groups</a></div></div>"),
					)),
					$pubfields = new CompositeField(array(
						$autopublish = new CheckboxField('AutoPublish','Automatic Publishing',$this->AutoPublish),
						$checkboxset = new CheckboxSetField('Privilege','Privilege',array(
							'0' => 'View',
							'1' => 'Download',
							'2' => 'Editing',
							'3' => 'Notify',
							'4' => 'Dynamic',
							'5' => 'Features'
						),$this->Privilege)
					)),
					$emailfields = new CompositeField(array(
						$name = new TextField('EmailName', 'Name'),
						$email = new EmailField('SendConfitmationsTo', 'EMail')
					)),
					$redirect = new DropdownField('RedirectOnSuccess','Redirect to',$pagesSearch)
				));

		$autopublish->setDescription('Set this option to enable automatic record publishing for new records. If enabled, privileges must be set.');
		$gnGroupDropdown->setDescription('The GeoNetwork group defines the user group who owns new created records. It is a mandatory field.');
		$checkboxset->setDescription('Once a record has been added to the catalog, define how the permissions to this records shall be set for public users.');

		$grpfields->setTag('fieldset');
		$grpfields->setLegend('<h3>Dataset Permissions</h3>');

		$pubfields->setTag('fieldset');
		$pubfields->setLegend('<h3>Dataset Publication</h3>');

		$gnfields->setTag('fieldset');
		$gnfields->setLegend('<h3>GeoNetwork Configurations</h3>');

		$url->setDescription('The base URL of the GeoNetwork-Server this page shall connect with, i.e. http://localhost:8080/geonetwork/');
		$user->setDescription('Geonetwork user name.<br>'.'The user must be defined in GeoNetwork. All new records created via this page will be owned by this user in Geonetwork.');
		$pass->setDescription('Geonetwork password.');

		$emailfields->setTag('fieldset');
		$emailfields->setLegend('<h3>EMail Settings</h3>');

		$name->setDescription('Name of the person who gets notified when new records are submitted.');
		$email->setDescription('Email address of that person who receives the notifications.');

		$redirect->setDescription('Redirect the user to this page after a metadata record has been submitted, i.e. to show the details of the new record.');

		// return the modified fieldset.
		return $fields;
	}

	/**
	 * Make sure Geonetwork url ends with an /.
	 */
	function onBeforeWrite() {
		parent::onBeforeWrite();
		$geoUrl = $this->GeonetworkBaseURL;
		if(strlen($geoUrl) > 1) {
			$geoUrlLen = strlen($geoUrl) - 1;
			if($geoUrl[$geoUrlLen] != '/') {
				$this->GeonetworkBaseURL .= '/';
			}
		}
	}
}


/**
 * Controller Class for Register Data Page
 *
 * This controller class implements the registration process, performed by the
 * RegisterDataPage.
 */
class RegisterDataPage_Controller extends Page_Controller
{

	/**
	 * Variable to store the classname of the form class.
	 *
	 * @var String
	 */
	public static $registrationFormName = "RegisterMetadataForm";

	private static $allowed_actions = array('getTLAfor','doRegisterMetadata', 'RegisterMetadataForm','MetadataEntryForm');

	/**
	 * Set static variable for the registration-form.
	 *
	 * @see searchFormName
	 *
	 * @param string $value New form-class name.
	 */
	public static function set_registration_form_name($value) {
		self::$registrationFormName = $value;
	}

	/**
	 * Get GeoNetwork base url.
	 *
	 * This method returns the base url to the OGC CSW catalogue (GeoNetwork).
	 * The url is stored in the page class {@see CataloguePage} because we
	 * might want to support multiple geonetwork nodes in one site, but each
	 * accesses a different GeoNetwork node.
	 *
	 * @throws CataloguePage_Exception
	 *
	 * @return string URL to the geonetwork node, i.e. "http://localhost:8080/geonetwork"
	 */
	public function getGeoNetworkBaseURL() {

		// get CataloguePage instance
		$page = $this->data();
		if(!isset($page)) throw new CataloguePage_Exception('Metadata Catalogue Page is not defined correctly.');

		// get GeoNetwork URL of that page.
		$url = $page->GeonetworkBaseURL;
		if(!isset($url) || $url == '') {
			throw new CataloguePage_Exception('URL to Metadata Catalogue not defined.');
		}
		// return base-url to GeoNetwork node.
		return $url;
	}

	/**
	 * Initialisation function that is run before any action on the controller is called.
	 */
	public function init() {
		Requirements::javascript(THIRDPARTY_DIR . '/jquery/jquery.js');
		Requirements::javascript(THIRDPARTY_DIR . '/jquery-ui/jquery-ui.js');
		parent::init();
		Requirements::javascript("geocatalogue/javascript/metadata_form.js");

		Requirements::themedCSS('layout');
		Requirements::themedCSS('typography');
		Requirements::themedCSS('form');
		Requirements::css(THIRDPARTY_DIR . '/jquery-ui-themes/smoothness/jquery-ui.css');
	}

	public function index($data) {
		$html = $this->render();
		// return $html;
		$options = array("indent" => true,
		                 "indent-spaces" => "2",
		                 "wrap" => "90",
		                 "output-html" => true,
		                 "hide-comments" => true);
		$tidy = tidy_parse_string($html, $options, 'utf8');
		tidy_clean_repair($tidy);
		return tidy_get_output($tidy);
	}

	/**
	 * Action: Register a new dataset.
	 *
	 * This action performs a geonetwork insert request to register a new
	 * metadata entry. It uses the GeoNetwork restful service API to pass
	 * on the metadata details to the registered Metadata catalogue.
	 *
	 * @param array $data request data
	 * @param form $form Form instance (registration form)
	 *
	 * @return void
	 *
	 * @todo add error message when geonetwork is down
	 */
	function doRegisterMetadata($data, $form) {
		$page = $this->data();

		// process form submission and send request to GeoNetwork.
		foreach($data as $key => $value) {
			if($key == "MDTopicCategory") continue; // Topic Category is an array of predefined values
			$invalidText = '//]]>';

			if(!(strpos($value, $invalidText) === false)) {
				$message = "Please ensure that the information you have entered does not contain the following<br /> text: '" . $invalidText . "'.<br />";
				$message .= "Unfortunately, this text segment can not be stored in the catalogue. ";
				$form->sessionMessage($message, 'bad');

				$emailValues = $this->generateEmailTemplateValues($message, $exception->getMessage());
				$this->sendEmail($emailValues, 'ErrorEMail');

				$this->redirectBack();
				return;
			}
		}

		$metadata = $this->LoadMetadataObject($data);

		// generate GeoNetwork HTTP request (query metadata).
		$cmd = $this->getCommand("GnCreateInsert", array('MDMetadata' => $metadata));
		$parameters = $cmd->execute();

		// Create record in GeoNetwork
		$config = Config::inst()->get('Catalogue', 'geonetwork');
		if ($config['api_version'] == 'geonetwork_v2_10') {
			$cmd = $this->getCommand("GnInsert_v2_10", array('RequestParameter' => $parameters));
			$cmd->setDOMetadata($metadata);
		} else {
			$cmd = $this->getCommand("GnInsert", array('RequestParameter' => $parameters));
		}

		$cmd->setUsername($page->Username);
		$cmd->setPassword($page->Password);

		$gnID = null;
		try {
			$gnID = $cmd->execute();
		}
		catch(GeonetworkInsertCommand_Exception $exception) {
			// add error message
			$message = 'Unfortunately the registration process failed due to a technical problem. Please retry later. ';
			$message .= $exception->getMessage();
			$form->sessionMessage($message, 'bad');

			$emailValues = $this->generateEmailTemplateValues('', $exception->getMessage());
			$this->sendEmail($emailValues, 'ErrorEMail');
			$this->redirectBack();
			return;
		}

		// update ID fields in local Metadata record
		$metadata->gnID = $gnID;
		$metadata->fileIdentifier = $cmd->get_uuid();
		$metadata->write();

		// send confirmation email to website-editor
		$this->sendConfirmationEmail($metadata);

		// create message for users, rendered for the next page
		$jsReference = $page->URLSegment;
		if($jsReference == '') {
			$jsReference = 'javascript:history.back(2)';
		}
		$messageObj = new ViewableData();
		$messageObj->customise(array('href_back' => $jsReference));
		$messageHTML = $messageObj->renderWith('ConfirmationMessage');

		$prefix = "FormInfo." . $page->RedirectOnSuccess;

		Session::set($prefix . ".info.message", $messageHTML);
		Session::set($prefix . ".info.messageType", 'ThankYou');

		$this->redirect($page->RedirectOnSuccess . "/dogetrecordbyid/".$metadata->fileIdentifier);
		return;
	}

	/**
	 * Generate variables for exceptions email template
	 * @param string $exceptionMsg
	 *
	 * @return array
	 */
	function generateEmailTemplateValues($text = '', $exceptionMsg = 'Something went wrong'){
		if ($text == '') {
			$text = 'While doing a "GnInsert" we caught the following "Exception" exception:';
		}

		return $emailValues = array(
			"SendEmailFrom" 	=> $this->data()->get_email_sender(),
			"SendEmailTo" 		=> Config::inst()->get('Email', 'admin_email'),
			"SendEmailSubject"	=> 'We encountered an exception' ,
			"DetailsText" 		=> $text,
			"ExceptionText" 	=> $exceptionMsg
		);
	}


	/**
	 *  prefixx()
	 *
	 * Returns the first prefix to be used with the Session::get and ::set
	 * It is either FormInfo.{FormName} or FormInfo.{url_segment} depending if we have a form or not
	 *
	 * @return string The prefix
	 */

	protected function prefixx() {
		$form = $this->MetadataEntryForm();
		$formname = $form->FormName();
		if(!isset($formname)) {
			$formname = $this->URLSegment;
		}
		return 'FormInfo.' . $formname;
	}

	/**
	 * Initiate and return the metadata entry form.
	 *
	 * @return Form new instance for the metadata registration
	 */
	function MetadataEntryForm() {
		// create a registerForm (uses the static searchForm value)
		$registerForm = self::get_registration_form_name();

		$form = new $registerForm($this, 'MetadataEntryForm');

		return $form;
	}

	/**
	 * Return the classname for the registration-form.
	 *
	 * @see $searchFormName
	 *
	 * @return string classname.
	 */
	public static function get_registration_form_name() {
		return self::$registrationFormName;
	}

	/**
	 * @param $customFields array with data used to be populated into the template
	 * @param string $templateName template to be used for rendering the email
	 *
	 * @return bool
	 */
	function sendEmail($customFields, $templateName = 'ConfirmationEMail') {

		if(!isset($customFields['SendEmailSubject']) || $customFields['SendEmailSubject'] == "") {
			$customFields['SendEmailSubject'] = $templateName;
		}

		$emailObj = new ViewableData();
		$emailObj->customise($customFields);

		// Render the text
		$emailText = $emailObj->renderWith($templateName);

		// Send an email to the administrator
		$email = new Email($customFields['SendEmailFrom'], $customFields['SendEmailTo'], $customFields['SendEmailSubject'], $emailText);
		return $email->send();
	}

	/**
	 *  getTLAfor()
	 *
	 * Helperfunction to create Dropdown-boxes on the register-data-form
	 * You append /getTLAfor/{territorial local authority}/ to get the assigned values and names as options like:
	 * <option value="177.0659658;178.6739575;-39.00195114;-37.51638332">Gisborne District</option>
	 *
	 * If you use an invalid TLA you just get the anywhere option
	 * <option value=";;;">(anywhere)</option>
	 *
	 * @return string options
	 */

	public function getTLAfor($httpRequest) {
		$params = $httpRequest->allParams();
		if(!isset($params['ID'])) {
			$params['ID'] = '';
		}
		else {
			// Illegal characters have to go
			$params['ID'] = $params['ID'];
		}
		$TLAs = NewZealandPlaces::get_nztla($params['ID']);

		$output = '';
		//<option value="172.6298219;174.3497666;-35.67495409;-34.38541495">Far North District</option>
		while(list($key, $val) = each($TLAs)) {
			$output .= "<option value=\"$key\">$val</option>\n";
		}
		print_r($output);
		return;
	}

	/**
	 * @param $data
	 *
	 * @return MDMetadata
	 */
	public function LoadMetadataObject($data) {
		$metadata = new MDMetadata();

		// process MDMetadata attributes
		$metadata->MDLanguage = 'English';
		$metadata->MDTitle = $data['MDTitle'];
		$metadata->MDAbstract = $data['MDAbstract'];

		// process MDDateTime list
		for($i = 1; $i < 4; $i++) {
			if(!empty($data['MDDateTime' . $i])) {
				$item = new MDCitationDate();
				$item->MDDateTime = $data['MDDateTime' . $i];
				$item->MDDateType = $data['MDDateType' . $i];
				$item->write();
				$metadata->MDCitationDates()->add($item);
			}
		}

		// process MDTopicCategory
		foreach($data['MDTopicCategory'] as $category) {
			$item = new MDTopicCategory();
			$item->Value = $category;
			$item->write();
			$metadata->MDTopicCategory()->add($item);
		}

		// process CIOnlineLinkage/CIOnlineLinkageData
		$urls = explode('||', $data['CIOnlineLinkageData']);
		foreach($urls as $url) {
			$item = new CIOnlineResource();
			$item->CIOnlineLinkage = $url;
			$item->CIOnlineName = $url;
			$item->CIOnlineProtocol = 'WWW:LINK-1.0-http--link';
			$item->write();
			$metadata->CIOnlineResources()->add($item);
		}

		// process MDResourceConstraint useLimitation only
		$item = new MDResourceConstraint();
		$item->useLimitation = $data['useLimitation'];
		$item->write();
		$metadata->MDResourceConstraints()->add($item);

		// process MDResourceFormatName list
		for($i = 1; $i < 6; $i++) {
			if(!empty($data['MDResourceFormatName' . $i])) {
				$item = new MDResourceFormat();
				$item->Name = $data['MDResourceFormatName' . $i];
				$item->Version = $data['MDResourceFormatVersion' . $i];
				$item->write();
				$metadata->MDResourceFormats()->add($item);
			}
		}

		// process MDHierarchyLevel list
		$scopeCodes = explode('||', $data['MDHierarchyLevelData']);
		foreach($scopeCodes as $code) {
			$item = new MDHierarchyLevel();
			$item->Value = $code;
			$item->write();
			$metadata->MDHierarchyLevel()->add($item);
		}

		// process MDHierarchyLevelName list
		$scopeTypes = explode('||', $data['MDHierarchyLevelNameData']);
		foreach($scopeTypes as $type) {
			$item = new MDHierarchyLevelName();
			$item->Value = $type;
			$item->write();
			$metadata->MDHierarchyLevelName()->add($item);
		}

		// process parent Identifier
		$metadata->MDParentIdentifier = $data['MDParentIdentifier'];

		// process location data
		$metadata->MDGeographicDiscription = $data['MDGeographicDiscription'];
		$metadata->MDWestBound = $data['MDWestBound'];
		$metadata->MDEastBound = $data['MDEastBound'];
		$metadata->MDSouthBound = $data['MDSouthBound'];
		$metadata->MDNorthBound = $data['MDNorthBound'];

		// process Contact
		$contact = new MDContact();
		$contact->MDIndividualName = $data['MDIndividualName'];
		$contact->MDOrganisationName = $data['MDOrganisationName'];
		$contact->MDPositionName = $data['MDPositionName'];
		$contact->write();

		// process email address list
		$emails = explode('||', $data['MDElectronicMailAddressData']);
		foreach($emails as $email) {
			$item = new MDEmail();
			$item->Value = $email;
			$item->write();
			$contact->MDElectronicMailAddress()->add($item);
		}

		// process phone number list
		$phonenumbers = explode('||', $data['MDVoiceData']);
		foreach($phonenumbers as $phone) {
			$item = new MDPhoneNumber();
			$item->Value = $phone;
			$item->write();
			$contact->MDVoice()->add($item);
		}
		$contact->write();

		$metadata->MDContacts()->add($contact);
		$metadata->write();

		return $metadata;
	}

	/**
	 * @param $uuid
	 * @param $metadata
	 */
	public function sendConfirmationEmail($metadata) {

		// get GeoNetwork URL of that page.
		$url = $this->data()->RedirectOnSuccess;
		$uuid = $metadata->fileIdentifier;

		$baseURLParts = explode('/', $this->AbsoluteLink());
		if(array_pop($baseURLParts) == '') {
			//if it's empty, we had a slash at the end and have to remove the controllername
			//again ;0)
			array_pop($baseURLParts);
		}

		$absoluteURLToDetails = implode('/', $baseURLParts) . "/" . $url . "/dogetrecordbyid/" . $uuid;

		$name = $this->data()->EmailName;
		if($name == '') {
			$name = 'Administrator';
		}

		// get email from contact/submitter
		$contactList = $metadata->MDContacts();
		$contact = $contactList->First();
		$emailList = $contact->MDElectronicMailAddress();
		$email = $emailList->First();
		$emailAddress = $email->Value;

		// prepare email sending
		$emailValues = array("emailName" => $name,
		                     "SendEmailTo" => $this->data()->SendConfitmationsTo,
		                     "SendEmailFrom" => $this->data()->get_email_sender(),
		                     "SendEmailSubject" => 'Metadata Catalogue: New submission from ' . $emailAddress,
		                     "detailsURL" => $absoluteURLToDetails,
		                     "submittedEmail" => $emailAddress,
		                     "submittedTitle" => $metadata->MDTitle,
		                     "submittedAbstract" => substr($metadata->MDAbstract, 0, 500));

		// send email to registered maintainer of the site to trigger a entry review process manually
		$this->sendEmail($emailValues, 'ConfirmationEMail');
	}
}
