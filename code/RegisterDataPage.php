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
class RegisterDataPage extends Page {

	/**
	 * Static variable to store data alongside with the page instance.
	 * @var array
	 */
	public static $db = array(
		'GeonetworkBaseURL' => "Varchar",
		'RedirectOnSuccess' => "Varchar",
		'Username' => "Varchar",
		'Password' => "Varchar",
		'SendConfitmationsTo' => "Varchar",
		'EmailName'	=> "Varchar",
	);

	/**
	 * This email address is used as the sender for all emails we send off. 
 	 * @var string
	 */
	static $email_sender ='testemails@silverstripe.com';

	/**
	 * Return the email address of the sender.
	 */
	public static function get_email_sender(){
		return self::$email_sender;
	}
	
	/**
	 * Sets the static sender-email address. Applies a regular expression 
	 * validation to check if the email format is valid.
	 *
	 * @return string return the old email address which has been replaced.
	 */
	public function set_email_sender($newEmail){
		$oldEmail = self::$email_sender;
		if(
			ereg('^([a-zA-Z0-9_+\.\-]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$',
			 		$newEmail
			)
		){
			self::$email_sender=$newEmail;
			return $oldEmail;
		}
		else{
			return '';
		}
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
	
		// customise form fields
		$fields->addFieldsToTab('Root.Content.Catalogue',
			array( 
				new TextField('GeonetworkBaseURL', 'The base URL of the GeoNetwork-Server you want to connect to:'),
				new EmailField('SendConfitmationsTo', 'Notify this email address of new submissions'),
				new TextField('EmailName', 'The name of the person who receives the notification'),
				new TextField('Username','GeoNetwork username'),
				new PasswordField('Password','Geonetwork password'),
				new DropdownField('RedirectOnSuccess','page (url-segment) to redirect the user after a successful submission")',$pagesSearch),				// drop down				
			));
			
		if (CataloguePage::get_site_status() != 'setup') {
			$fields->makeFieldReadonly('GeonetworkBaseURL');
			$fields->makeFieldReadonly('SendConfitmationsTo');
			$fields->makeFieldReadonly('EmailName');
			$fields->makeFieldReadonly('Username');
			$fields->makeFieldReadonly('RedirectOnSuccess');

			$fields->removeByName('Password');
		}			

		// return the modified fieldset.
		return $fields;
	}
	
	/**
	 * Make sure Geonetwork url ends with an /.
	 */
	function onBeforeWrite(){
		parent::onBeforeWrite();
		$geoUrl = $this->GeonetworkBaseURL;
		if(strlen($geoUrl) > 1){
			$geoUrlLen = strlen($geoUrl)-1;
			if($geoUrl[$geoUrlLen] != '/'){
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
class RegisterDataPage_Controller extends Page_Controller {

	/**
	 * Variable to store the classname of the form class.
	 * @var String
	 */ 
	public static $registrationFormName = "RegisterMetadataForm";

	/**
	 * Return the classname for the registration-form.
	 * @see $searchFormName
	 *
	 * @return string classname. 
	 */
	public static function get_registration_form_name() {
		return self::$registrationFormName;
	}

	/**
	 * Set static variable for the registration-form.
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
		if (!isset($page)) throw new CataloguePage_Exception('Metadata Catalogue Page is not defined correctly.');

		// get GeoNetwork URL of that page.
		$url = $page->GeonetworkBaseURL;
		if (!isset($url) || $url == '') {
			
			// if this is a dev environment, use a local geonetwork (standard 
			// development environment setup).
			if (Director::isDev()) {
				$url = 'http://192.168.1.136:8080/geonetwork';
			} else {
				throw new CataloguePage_Exception('URL to Metadata Catalogue not defined.');
			}
		}
		// return base-url to GeoNetwork node.
		return $url;
	}
		
	/**
	 * Initialisation function that is run before any action on the controller is called.
	 */
	public function init() {
		parent::init();
		Requirements::javascript("geocatalogue/javascript/metadata_form.js");
		Requirements::themedCSS('layout');
		Requirements::themedCSS('typography');
		Requirements::themedCSS('form');
	}	

	public function index($data) {
		$html = $this->render();
		// return $html;
		$options = array(
			"indent" => true,
			"indent-spaces" => "2",
			"wrap" => "90",
			"output-html" => true,
			"hide-comments" => true
		);
		$tidy = tidy_parse_string($html, $options, 'utf8');		
		tidy_clean_repair($tidy); 
		return tidy_get_output($tidy);
	}

	/**
	 * Initiate and return the metadata entry form.
	 *
	 * @return Form new instance for the metadata registration
	 */
	function MetadataEntryForm() {
		// create a registerForm (uses the static searchForm value)
		$registerForm = self::get_registration_form_name();
		
		$form = new $registerForm($this,'MetadataEntryForm');		
		
		// 
		
		//SpamProtectorManager::update_form($form, null, array('Title', 'Content', 'Name', 'Website', 'Email'));
		///SpamProtectorManager::update_form($form);
		
		return $form;
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
		// process form submission and send request to GeoNetwork.

		// for Session-Messages
		$prefix=$this->prefixx();
	
		foreach($data as $key => $value) {
			if ($key=="MDTopicCategory") continue; // Topic Category is an array of predefined values
			$invalidText = '//]]>';
      
			if (!(strpos($value,$invalidText) === false)) {
				$mess  = "Please ensure that the information you have entered does not contain the following<br /> text: '".$invalidText."'.<br />";
				$mess .= "Unfortunately, this text segment can not be stored in the catalogue. ";

				Session::set($prefix . ".errors.message", $mess);
				Session::set($prefix . ".errors.messageType", 'Error');
      

				// optional: send email to admin?
				$emailValues = array(
					"SendEmailFrom" 	=> $this->data()->get_email_sender(),
					"SendEmailTo" 		=> Email::getAdminEmail(),
					"SendEmailSubject"	=> 'We encountered an exception' , 
					"DetailsText" 		=> $mess,
					"ExceptionText" 	=> '',
				);
			
				$this->doSendEmailToAdministrator($emailValues,'ErrorEMail');
        
				return Director::redirectBack();
			}
		}
		
		$metadata = new MDMetadata();
		$form->saveInto($metadata);
		$metadata->MDLanguage = 'English';
		$metadata->write();
		
		$scopeCodes=explode('||',$data['MDHierarchyLevelData']);
		$scopeTypes=explode('||',$data['MDHierarchyLevelNameData']);

		foreach ($scopeCodes as $code) {
			$item=new MDHierarchyLevel();
			$item->Value=$code;
			$item->write();
			$metadata->MDHierarchyLevel()->add($item);
		}
    
		foreach ($scopeTypes as $type) {
			$item=new MDHierarchyLevelName();
			$item->Value=$type;
			$item->write();
			$metadata->MDHierarchyLevelName()->add($item);
		}
 		
		for ($i=1;$i<4;$i++) {
			if (!empty($data['MDDateTime'.$i])) {
				$item=new MDCitationDate();
				$item->MDDateTime = $data['MDDateTime'.$i];
				$item->MDDateType = $data['MDDateType'.$i];
				$item->write();
				$metadata->MDCitationDates()->add($item);
			}	
		}	
		
		for ($i=1;$i<6;$i++) {
			if (!empty($data['MDResourceFormatName'.$i])) {
				$item=new MDResourceFormat();
				$item->Name = $data['MDResourceFormatName'.$i];
				$item->Version = $data['MDResourceFormatVersion'.$i];
				$item->write();
				$metadata->MDResourceFormats()->add($item);
			}	
		}	

		$urls = explode('||',$data['CIOnlineLinkageData']);

		foreach ($urls as $url) {
			$item = new CIOnlineResource();
			$item->CIOnlineLinkage=$url;
			$item->CIOnlineName = $item->CIOnlineLinkage;
			$item->CIOnlineProtocol = 'WWW:LINK-1.0-http--link';
			$item->write();
			$metadata->CIOnlineResources()->add($item);
		}	
   
		foreach ($data['MDTopicCategory'] as $category) {
			$item=new MDTopicCategory();
			$item->Value=$category;
			$metadata->MDTopicCategory()->add($item);
		}
    

		$contact = new MDContact();
		$contact->write();
		$form->saveInto($contact);
		
		$emails=explode('||',$data['MDElectronicMailAddressData']);
		$phonenumbers=explode('||',$data['MDVoiceData']);

		foreach ($emails as $email) {
			$item=new MDEmail();
			$item->Value=$email;
			$item->write();
			$contact->MDElectronicMailAddress()->add($item);
		}
		foreach ($phonenumbers as $phone) {
			$item=new MDPhoneNumber();
			$item->Value=$phone;
			$item->write();
			$contact->MDVoice()->add($item);
		}

		// foreach($contact->MDElectronicMailAddress() as $item) {
		// 	Debug::show($item);
		// }
		// 
		$contact->write();
		$metadata->MDContacts()->add($contact);

		$item = new MDResourceConstraint();
		$form->saveInto($item);
		$item->write();
		$metadata->MDResourceConstraints()->add($item);

		//Debug::show($metadata); exit();

		$data = array(
			'MDMetadata' => $metadata
		);
		// generate GeoNetwork HTTP request (query metadata).
		$cmd = $this->getCommand("GnCreateInsert", $data);
		$request_params = $cmd->execute();
			
		$data = array(
			'RequestParameter' => $request_params
		);
		$page = $this->data();
    
		$cmd = $this->getCommand("GnInsert", $data);
		$cmd->setUsername($page->Username);
		$cmd->setPassword($page->Password);
		
		$gnID = null;
		
		try {
			$gnID = $cmd->execute();		
		}
		catch(GeoNetworkRestfulService_Exception $exception) {
			// add error message
			$mess= 'Unfortunately the registration process failed due to a technical problem. Please retry later. ';
			$mess.=$exception->getMessage();
			
			Session::set($prefix . ".errors.message", $mess);
			Session::set($prefix . ".errors.messageType", 'Error');
			
			// optional: send email to admin?
			$emailValues = array(
				"SendEmailFrom" 	=> $this->data()->get_email_sender(),
				"SendEmailTo" 		=> Email::getAdminEmail(),
				"SendEmailSubject"	=> 'We encountered an exception' , 
				"DetailsText" 		=> 'While doing a "GnInsert" we caught the following "GeoNetworkRestfulService_Exception" exception:',
				"ExceptionText" 	=> $exception->getMessage(),
			);
			$this->doSendEmailToAdministrator($emailValues,'ErrorEMail');
			return Director::redirectBack();
		}
		catch(GeonetworkInsertCommand_Exception $exception) {
			// add error message
			$mess= 'Unfortunately the registration process failed due to a technical problem. Please retry later. ';
			$mess.=$exception->getMessage();

			//Session::setFormMessage($prefix, $mess, 'Error');	// Info , Notice, Warning, Error or something like that
			Session::set($prefix . ".errors.message", $mess);
			Session::set($prefix . ".errors.messageType", 'Error');
			
			// optional: send email to admin?
			$emailValues = array(
				"SendEmailFrom" 	=> $this->data()->get_email_sender(),
				"SendEmailTo" 		=> Email::getAdminEmail(),
				"SendEmailSubject"	=> 'We encountered an exception' , 
				"DetailsText" 		=> 'While doing a "GnInsert" we caught the following "GeonetworkInsertCommand_Exception" exception:',
				"ExceptionText" 	=> $exception->getMessage(),
			);
			$this->doSendEmailToAdministrator($emailValues,'ErrorEMail');
			return Director::redirectBack();
		}
		
		$uuid = $cmd->get_uuid();

		$metadata->gnID           = $gnID;
		$metadata->fileIdentifier = $uuid;
		$metadata->write();
				
		$page = $this->data();
		if (!isset($page)) {
			throw new CataloguePage_Exception('Metadata Catalogue Page is not defined correctly.');
		}

		// get GeoNetwork URL of that page.
		$url = $page->RedirectOnSuccess;
		
		$baseURLParts = explode('/' , $this->AbsoluteLink());
		if(array_pop($baseURLParts) == ''){
			//if it's empty, we had a slash at the end and have to remove the controllername
			//again ;0)
			array_pop($baseURLParts);
		}
		
		$absoluteURLToDetails = implode('/',$baseURLParts)."/".$url."/dogetrecordbyid/".$uuid ; 
		
		$nameWhoGetsTheEmail = $page->EmailName;
		if($nameWhoGetsTheEmail == '') $nameWhoGetsTheEmail='Administrator';
		// prepare email sending
		$emailValues = array(
			"emailName" 		=> $nameWhoGetsTheEmail,
			"SendEmailFrom" 	=> $page->get_email_sender(),
			"SendEmailTo" 		=> $page->SendConfitmationsTo, 
			"SendEmailSubject"	=> 'Metadata Catalogue: New submission from '. $contact->MDElectronicMailAddress, 
			"detailsURL" 		=> $absoluteURLToDetails,
			"submittedEmail" 	=> $contact->MDElectronicMailAddress,
			"submittedTitle"   	=> $metadata->MDTitle,
			"submittedAbstract"	=> substr($metadata->MDAbstract,0,500),
		);
		
				
		$successfulySentEmail=$this->doSendEmailToAdministrator($emailValues,'ConfirmationEMail');
		
		$refe = $page->URLSegment ;
		if($refe == ''){
			$refe='javascript:history.back(2)';
		}
		$messageObj = new ViewableData();
		$customFields = array('TakeMeBackTo'=> $refe);
		
		$messageObj->customise($customFields);

		$mess = $messageObj->renderWith('ConfirmationMessage');		
		
		$prefix="FormInfo." . $page->RedirectOnSuccess;
		Session::set($prefix . ".info.message", $mess);
		Session::set($prefix . ".info.messageType", 'ThankYou');
		Director::redirect($url."/dogetrecordbyid/".$uuid);
   }

	/**
	 *	doSendEmailToAdministrator(array $values, string $template)
	 *
	 * @param 	array $values is used for rendering the email via $template
	 *			and some fields for the email itself, which are:
	 *			'SendEmailSubject' is the subject of the mail and defaults to $template
	 *			'SendEmailFrom' is the email address shown in the FROM field of the email defaults to the 
	 *							admin email-address of the silverstripe installation
	 *  		'SendEmailTo' is the email-address where the mail is sended to (mandatory)
	 * @param	string $template is the name of the template to be used for rendering the email
	 *			
	 * @return 	boolean true on success, otherwise false 
	 */
	function doSendEmailToAdministrator($customFields, $templateName='ConfirmationEMail'){
		//checks
		if(! isset($customFields['SendEmailSubject']) || $customFields['SendEmailSubject'] == ""){
			$customFields['SendEmailSubject'] = $templateName;
		}
		
		$emailObj = new ViewableData();
		$emailObj->customise($customFields);

		// Render the text
		$emailText = $emailObj->renderWith($templateName);

		// Send an email to the administrator
		$email = new Email(
			$customFields['SendEmailFrom'],
			$customFields['SendEmailTo'], 
			$customFields['SendEmailSubject'], 
			$emailText
		);
		return $email->send();
			
	}
	
	/**
	*  getTheSessionMessage()
	*
	* Returns the first Session-Error-Message  for the given page
	*
	* @return string The Session-Message
	*				or empty string if there's none
	*/
	
	public function getTheSessionMessage(){
		$formerrors=Session::get($this->prefixx() . '.errors');
		$message='';
		if(isset($formerrors) && is_array($formerrors)) {
			if(isset($formerrors[0])){
				$message= $formerrors[0]['message'];
			}
			else{
				$message= $formerrors['message'];
			}
		}
		return $message;
	}


	/**
	*  getTheSessionMessageType()
	*
	* Returns the first Session-Error-MessageType  for the given page 
	* The Session-Message-Type can be 'Info','Warning','Error' or 'Validation'
	*
	* @return string The Session-Message-Type
	*				or empty string if there's none
	*/
	
	public function getTheSessionMessageType(){
		$formerrors=Session::get($this->prefixx() . '.errors');
		$type='';
		if(isset($formerrors) && is_array($formerrors)) {
			if(isset($formerrors[0])){
				$type= $formerrors[0]['messageType'];
			}
			else{
				$type= $formerrors['messageType'];
			}
		}
		return $type;
	}
	
	/**
	*  clearTheSessionMessage()
	*
	* Clears all the Session-Errors for the given page 
	*
	*/

	public function clearTheSessionMessage(){
		$prefix=$this->prefixx();
		Session::clear($prefix .'.errors');
	}
	
	/**
	*  prefixx()
	*
	* Returns the first prefix to be used with the Session::get and ::set
	* It is either FormInfo.{FormName} or FormInfo.{url_segment} depending if we have a form or not
	*
	* @return string The prefix
	*/
	
	protected function prefixx(){
		$form=$this->MetadataEntryForm();
		$formname=$form->FormName();
		if(! isset($formname)){
			$formname = $this->URLSegment;
		}
		return 'FormInfo.' . $formname;
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
	
	public function getTLAfor($httpRequest){
		$params = $httpRequest->allParams();
		if (!isset($params['ID'])) {
			$params['ID']='';
		}else{
			// Illegal characters have to go
			$params['ID']=$params['ID'];
		}
		$TLAs=NewZealandPlaces::get_nztla($params['ID']);
		$output='';
		//<option value="172.6298219;174.3497666;-35.67495409;-34.38541495">Far North District</option>
		while (list($key, $val) = each($TLAs)) {
			    $output .= "<option value=\"$key\">$val</option>\n";
		}
		print_r($output);
		return ;
	}
	
}
