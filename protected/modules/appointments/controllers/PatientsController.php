<?php
/**
 * Appointments controller
 * This controller intended to both Backend and Frontend modes
 *
 * PUBLIC:                      PRIVATE
 * -----------                  ------------------
 * __construct                  _checkAccountsAccess
 * indexAction                  _checkAppointmentAccess
 * manageAction                 _checkPatientAccess
 * addAction                    _checkDoctorAccess
 * editAction				    _prepareAccountFields
 * deleteAction                 _logout
 * loginAction                  _outputAjax
 * logoutAction                 _outputJson
 * registrationAction
 * restorePasswordAction
 * confirmRegistrationAction
 * dashboardAction
 * myAccountAction
 * editAccountAction
 * removeAccountAction
 * activeStatusAction
 * termsConditionsAction
 * myAppointmentsAction
 * editMyAppointmentAction
 * cancelMyAppointmentAction
 * ajaxGetPatientNamesAction
 */

namespace Modules\Appointments\Controllers;

// Module
use \Modules\Appointments\Components\AppointmentsComponent;
use \Modules\Appointments\Models\Patients;
use \Modules\Appointments\Models\Titles;
use \Modules\Appointments\Models\Degrees;
use \Modules\Appointments\Models\Appointments;
use \Modules\Appointments\Models\Doctors;
use \Modules\Appointments\Models\VisitReasons;
use \Modules\Appointments\Models\Specialties;

// Framework
use \A,
    \CAuth,
    \CArray,
    \CDatabase,
    \CFile,
    \CString,
    \CHtml,
    \CLocale,
    \CWidget,
    \CTime,
    \CHash,
    \CValidator,
    \CController,
    \CConfig;

// Application
use \Website,
    \Admins,
    \Accounts,
    \Bootstrap,
    \Languages,
    \ModulesSettings,
    \Countries,
    \Modules,
    \LocalTime,
    \SocialLogin,
    \States,
    \BanLists;



class PatientsController extends CController
{
    private $_checkBruteforce;
    private $_redirectDelay;
    private $_badLogins;
    /**
     * Class default constructor
     */
    public function __construct()
    {
        parent::__construct();
        // block access if the module is not installed
        if(!Modules::model()->isInstalled('appointments')){
            if(CAuth::isLoggedInAsAdmin()){
                $this->redirect('modules/index');
            }else{
                $this->redirect(Website::getDefaultPage());
            }
        }

        $this->_settings = Bootstrap::init()->getSettings();
        $this->_cSession = A::app()->getSession();

        $this->_view->actionMessage = '';
        $this->_view->errorField = '';

		$settings = Bootstrap::init()->getSettings();
		$this->_view->dateFormat     = $settings->date_format;
		$this->_view->timeFormat     = $settings->time_format;
		$this->_view->dateTimeFormat = $settings->datetime_format;
		$this->_view->numberFormat   = $settings->number_format;
		$this->_view->currencySymbol = A::app()->getCurrency('symbol');

        $this->_view->labelStatusAppointments = array(
            '0'=>'<span class="label-yellow label-square">'.A::t('appointments', 'Reserved').'</span>',
            '1'=>'<span class="label-green label-square">'.A::t('appointments', 'Verified').'</span>',
            '2'=>'<span class="label-red label-square">'.A::t('appointments', 'Canceled').'</span>',
        );

        if(CAuth::isLoggedInAsAdmin()){
            // set meta tags according to active patients
            Website::setMetaTags(array('title'=>A::t('appointments', 'Patients Management')));

            $this->_view->tabs = AppointmentsComponent::prepareTab('patients');
        }
    }

    /**
     * Controller default action handler
     * @return void
     */
    public function indexAction()
    {
        if(CAuth::isLoggedInAs('patient')){
            $this->redirect('patients/dashboard');    // !Not done
        }elseif(CAuth::isLoggedInAsAdmin()){
            $this->redirect('patients/manage');
        }else{
            $this->redirect(Website::getDefaultPage());
        }
    }

    /**
     * Manage action handler
     * @return void
     */
    public function manageAction()
    {
        // set backend mode
        Website::setBackend();
        Website::prepareBackendAction('manage', 'patient', 'modules/index');
        $this->_prepareAccountFields();

        $alert = A::app()->getSession()->getFlash('alert');
        $alertType = A::app()->getSession()->getFlash('alertType');

        if(!empty($alert)){
            $this->_view->actionMessage = CWidget::create('CMessage', array($alertType, $alert, array('button'=>true)));
        }

        $this->_view->render('patients/manage');
    }

    /**
     * Add new action handler
     * @return void
     */
    public function addAction()
    {
        // set backend mode
        Website::setBackend();
        Website::prepareBackendAction('add', 'patient', 'patients/manage');
        $this->_prepareAccountFields();

        $cRequest = A::app()->getRequest();
        if($cRequest->isPostRequest()){
            $this->_view->countryCode = $cRequest->getPost('country_code');
            $this->_view->stateCode = $cRequest->getPost('state');
        }else{
            $this->_view->countryCode = $this->_view->defaultCountryCode;
            $this->_view->stateCode = '';
        }

        // prepare salt
        $this->_view->salt = '';
        if(A::app()->getRequest()->getPost('password') != ''){
            $this->_view->salt = CConfig::get('password.encryptSalt') ? CHash::salt() : '';
        }

        $this->_view->render('patients/add');
    }

    /**
     * Edit patients action handler
     * @param int $id
     * @return void
     */
    public function editAction($id = 0)
    {
        // set backend mode
        Website::setBackend();
        Website::prepareBackendAction('edit', 'patient', 'patients/manage');
        $patient = $this->_checkPatientAccess($id);
        $this->_prepareAccountFields();

        $cRequest = A::app()->getRequest();
        if($cRequest->isPostRequest()){
            $this->_view->countryCode = $cRequest->getPost('country_code');
            $this->_view->stateCode = $cRequest->getPost('state');
        }else{
            $this->_view->countryCode = $patient->country_code;
            $this->_view->stateCode = $patient->state;
        }

        $this->_view->id = $patient->id;
        // fetch datetime format from settings table
        $this->_view->dateTimeFormat = Bootstrap::init()->getSettings('datetime_format');

        // prepare salt
        $this->_view->salt = '';
        if(A::app()->getRequest()->getPost('password') != ''){
            $this->_view->salt = CConfig::get('password.encryptSalt') ? CHash::salt() : '';
            A::app()->getRequest()->setPost('salt', $this->_view->salt);
        }

        //if(!empty($alert)){
        //    $this->_view->actionMessage = CWidget::create('CMessage', array($alertType, $alert, array('button'=>true)));
        //}

        $this->_view->render('patients/edit');
    }

    /**
     * Delete action handler
     * @param int $id
     * @return void
     */
    public function deleteAction($id = 0)
    {
        // set backend mode
        Website::setBackend();
        Website::prepareBackendAction('delete', 'patient', 'patients/manage');
        $patient = $this->_checkPatientAccess($id);
        $this->_prepareAccountFields();

        $alert = '';
        $alertType = '';

        if($patient->delete()){
            $alert = A::t('appointments', 'Patients deleted successfully');
            $alertType = 'success';
        }else{
            if(APPHP_MODE == 'demo'){
                $alert = CDatabase::init()->getErrorMessage();
                $alertType = 'warning';
            }else{
                $alert = A::t('appointments', 'Patients deleting error');
                $alertType = 'error';
            }
        }

        if(!empty($alert)){
            A::app()->getSession()->setFlash('alert', $alert);
            A::app()->getSession()->setFlash('alertType', $alertType);
        }

        $this->redirect('patients/manage');
    }

    /**
     * Patient login action handler
     * @param string $type
     * @return void
     */
    public function loginAction($type = '')
    {
        // Redirect logged in patients
        CAuth::handleLoggedIn('patients/dashboard', 'patient');

        // Check if login action is allowed
        if(!ModulesSettings::model()->param('appointments', 'patient_allow_login')){
            $this->redirect(Website::getDefaultPage());
        }

        // Social login
        if(!empty($type)){
            if(APPHP_MODE == 'demo'){
                A::app()->getSession()->setFlash(
                    'msgLoggedOut',
                    CWidget::create('CMessage', array('warning', A::t('core', 'This operation is blocked in Demo Mode!')))
                );
                $this->redirect('patients/login');
            }

            $config = array();
			$config['returnUrl'] = 'patients/login';
			$config['model'] = 'Modules\Appointments\Models\Patients';

            SocialLogin::config($config);
            SocialLogin::login(strtolower($type));
        }

        // Set frontend mode
        Website::setFrontend();

        $this->_view->allowRememberMe = ModulesSettings::model()->param('appointments', 'patient_allow_remember_me');
        $this->_view->allowRegistration = ModulesSettings::model()->param('appointments', 'patient_allow_registration');
        $this->_view->allowResetPassword = ModulesSettings::model()->param('appointments', 'patient_allow_restore_password');

        //#000
        $this->_checkBruteforce = CConfig::get('validation.bruteforce.enable');
        $this->_redirectDelay = (int)CConfig::get('validation.bruteforce.redirectDelay', 3);
        $this->_badLogins = (int)CConfig::get('validation.bruteforce.badLogins', 5);
        $alert = '';
        $alertType = '';
        $errors = array();
        $cRequest = A::app()->getRequest();

        $patient = new Accounts();

        // Check if access is blocked to this IP address
        $ipBanned = Website::checkBan('ip_address', $cRequest->getUserHostAddress(), $errors);
        if($ipBanned){
            // do nothing
            $this->_view->actionMessage = CWidget::create('CMessage', array($errors['alertType'], $errors['alert']));
        }else{
            // -------------------------------------------------
            // Perform auto-login "remember me"
            // --------------------------------------------------
            if(!CAuth::isLoggedIn()){
                if($this->_view->allowRememberMe){
					parse_str(A::app()->getCookie()->get('patientAuth'), $output);
					if(!empty($output['usr']) && !empty($output['hash'])){
						$username = CHash::decrypt($output['usr'], CConfig::get('password.hashKey'));
						$password = $output['hash'];

                        // Check if access is blocked to this username
                        $usernameBanned = Website::checkBan('username', $username);
                        if($usernameBanned){
                            // do nothing
                        }elseif($patient->login($username, $password, 'patient', true, true)){
                            // Save patient role ID
                            $cust = Patients::model()->find('account_id = :account_id', array(':account_id' => (int)$patient->id));
                            if($cust){
                                A::app()->getSession()->set('loggedRoleId', $cust->id);
                            }
                            $this->redirect('patients/dashboard');
                        }
                    }
                }
            }

            $this->_view->username = $cRequest->getPost('login_username');
            $this->_view->password = $cRequest->getPost('login_password');
            $this->_view->remember = $cRequest->getPost('remember');
            $alert = A::app()->getSession()->getFlash('alert');
            $alertType = A::app()->getSession()->getFlash('alertType');
            if(empty($alert)){
                $alert = A::t('appointments', 'Patient Login Message');
                $alertType = 'info';
            }

            // -------------------------------------------------
            // Handle form submission
            // --------------------------------------------------
            if($cRequest->getPost('act') == 'send'){

                // perform login form validation
                $fields = array();
                $fields['login_username'] = array('title'=>A::t('appointments', 'Username'), 'validation'=>array('required'=>true, 'type'=>'any', 'minLength'=>6, 'maxLength'=>32));
                $fields['login_password'] = array('title'=>A::t('appointments', 'Password'), 'validation'=>array('required'=>true, 'type'=>'any', 'minLength'=>6, 'maxLength'=>25));
                if($this->_view->allowRememberMe) $fields['remember'] = array('title'=>A::t('appointments', 'Remember Me'), 'validation'=>array('required'=>false, 'type'=>'set', 'source'=>array(0,1)));
                $result = CWidget::create('CFormValidation', array(
                    'fields' => $fields
                ));

                if($result['error']){
                    $alert = $result['errorMessage'];
                    $alertType = 'validation';
                    $this->_view->errorField = $result['errorField'];
                }else{
                    // Check if access is blocked to this username
                    $usernameBanned = Website::checkBan('username', $this->_view->username, $errors);
                    if($usernameBanned){
                        // do nothing
                        $alert = $errors['alert'];
                        $alertType = $errors['alertType'];
                    }else{
                        if($patient->login($this->_view->username, $this->_view->password, 'patient', false, ($this->_view->allowRememberMe && $this->_view->remember))){
                            if($this->_view->allowRememberMe && $this->_view->remember){
                                // Username may be decoded
                                $usernameHash = CHash::encrypt($this->_view->username, CConfig::get('password.hashKey'));
                                // Password cannot be decoded, so we save ID + username + salt + HTTP_USER_AGENT
                                $httpUserAgent = A::app()->getRequest()->getUserAgent();
                                $passwordHash = CHash::create(CConfig::get('password.encryptAlgorithm'), $patient->id.$patient->username.$patient->getPasswordSalt().$httpUserAgent);
                                A::app()->getCookie()->set('patientAuth', 'usr='.$usernameHash.'&hash='.$passwordHash, (time() + 3600 * 24 * 14));
                            }
                            //#001 clean login attempts counter
                            if($this->_checkBruteforce){
                                A::app()->getSession()->remove('patientLoginAttempts');
                                A::app()->getCookie()->remove('patientLoginAttemptsAuth');
                            }

                            // Save patient role ID
                            $cust = Patients::model()->find('account_id = :account_id', array(':account_id' => (int)$patient->id));
                            if($cust){
                                A::app()->getSession()->set('loggedRoleId', $cust->id);
                            }

                            $lastVisitedPage = Website::getLastVisitedPage();
                            if(!empty($lastVisitedPage) && !preg_match('/(login|registration|Home\/index|index\/index)/i', $lastVisitedPage)){
                                $this->redirect($lastVisitedPage, true);
                            }
                            $this->redirect('patients/dashboard');
                        }else{
                            $alert = $patient->getErrorDescription();
                            $alertType = 'error';
                            $this->_view->errorField = 'username';
                        }
                    }
                }

                if(!empty($alert)){
                    $this->_view->username = $cRequest->getPost('login_username');
                    $this->_view->password = $cRequest->getPost('login_password');
                    if($this->_view->allowRememberMe) $this->_view->remember = $cRequest->getPost('remember', 'string');
                    $this->_view->actionMessage = CWidget::create('CMessage', array($alertType, $alert));

                    //#002 increment login attempts counter
                    if($this->_checkBruteforce && $this->_view->username != '' && $this->_view->password != ''){
                        $logAttempts = A::app()->getSession()->get('patientLoginAttempts', 1);
                        if($logAttempts < $this->_badLogins){
                            A::app()->getSession()->set('patientLoginAttempts', $logAttempts+1);
                        }else{
                            A::app()->getCookie()->set('patientLoginAttemptsAuth', md5(uniqid()));
                            sleep($this->_redirectDelay);
                            $this->redirect('patients/login');
                        }
                    }
                }
            }else{
                //#003 validate login attempts coockie
                if($this->_checkBruteforce){
                    $logAttempts = A::app()->getSession()->get('patientLoginAttempts', 0);
                    $logAttemptsAuthCookie = A::app()->getCookie()->get('patientLoginAttemptsAuth');
                    $logAttemptsAuthPost = $cRequest->getPost('patientLoginAttemptsAuth');
                    if($logAttempts >= $this->_badLogins){
                        if($logAttemptsAuthCookie != '' && $logAttemptsAuthCookie == $logAttemptsAuthPost){
                            A::app()->getSession()->remove('patientLoginAttempts');
                            A::app()->getCookie()->remove('patientLoginAttemptsAuth');
                            $this->redirect('patients/login');
                        }
                    }elseif($logAttempts == 0 && !empty($logAttemptsAuthPost)){
                        // If the lifetime of the session ended, and confirm the button has not been pressed
                        A::app()->getCookie()->remove('patientLoginAttemptsAuth');
                        $this->redirect('patients/login');
                    }
                }
                $this->_view->actionMessage = CWidget::create('CMessage', array('info', $alert));
            }
        }

        $this->_view->buttons = SocialLogin::drawButtons(array(
            'facebook'=>'patients/login/type/facebook',
            'twitter'=>'patients/login/type/twitter',
            'google'=>'patients/login/type/google')
        );

		// Logged out messages
		if(A::app()->getSession()->hasFlash('msgLoggedOut')){
			$this->_view->actionMessage = A::app()->getSession()->getFlash('msgLoggedOut');	
		}
		
        $this->_view->render('patients/login');
    }

    /**
     * Patient logout action handler
     * @return void
     */
    public function logoutAction()
    {
        if(CAuth::isLoggedInAs('patient')){
            $this->_logout();
            $this->_cSession->startSession();
            $this->_cSession->setFlash('msgLoggedOut', CWidget::create('CMessage', array('info', A::t('appointments', 'You have been successfully logged out. We hope to see you again soon.'))));
        }
		
        $this->redirect('patients/login');
    }

    /**
     * Patient registration action handler
     * @return void
     */
    public function registrationAction()
    {
        // redirect logged in patients
        CAuth::handleLoggedIn('patients/dashboard', 'patient');

        // check if action allowed
        if(!ModulesSettings::model()->param('appointments', 'patient_allow_registration')){
            $this->redirect(Website::getDefaultPage());
        }

        // set frontend mode
        Website::setFrontend();

        $this->_prepareAccountFields();
        $cRequest = A::app()->getRequest();
        $approvalType = ModulesSettings::model()->param('appointments', 'patient_approval_type');
        $arr = array();
        $errors = array();

        if($cRequest->isPostRequest()){

            // Check if access is blocked to this IP address
            $ipBanned = Website::checkBan('ip_address', $cRequest->getUserHostAddress(), $errors);
            if($ipBanned){
                // Do nothing
                $arr[] = '"status": "0"';
                $arr[] = '"error": "'.$errors['alert'].'"';
            }else{
                if($cRequest->getPost('act') != 'send'){
                    $this->redirect(CConfig::get('defaultController').'/');
                }elseif(APPHP_MODE == 'demo'){
                    $arr[] = '"status": "0"';
                }else{
                    // Perform registration form validation
                    $fields = array();
                    $fields['first_name'] = array('title'=>A::t('appointments', 'First Name'), 'validation'=>array('required'=>true, 'type'=>'text', 'maxLength'=>32));
                    $fields['last_name'] = array('title'=>A::t('appointments', 'Last Name'), 'validation'=>array('required'=>true, 'type'=>'text', 'maxLength'=>32));
                    $fields['gender'] = array('title'=>A::t('appointments', 'Gender'), 'validation'=>array('required'=>true, 'type'=>'set', 'source'=>array_keys($this->_view->genders)));
                    //$fields['birth_date'] = array('title'=>A::t('appointments', 'Birth Date'), 'validation'=>array('required'=>false, 'type'=>'date', 'maxLength'=>10, 'minValue'=>'1900-00-00', 'maxValue'=>date('Y-m-d')));
                    $fields['phone'] = array('title'=>A::t('appointments', 'Phone'), 'validation'=>array('required'=>false, 'type'=>'phoneString', 'maxLength'=>32));
                    //$fields['fax'] = array('title'=>A::t('appointments', 'Fax'), 'validation'=>array('required'=>false, 'type'=>'phoneString', 'maxLength'=>32));
                    //$fields['address'] = array('title'=>A::t('appointments', 'Address'), 'validation'=>array('required'=>true, 'type'=>'text', 'maxLength'=>64));
                    //$fields['address_2'] = array('title'=>A::t('appointments', 'Address (line 2)'), 'validation'=>array('required'=>false, 'type'=>'text', 'maxLength'=>64));
                    //$fields['city'] = array('title'=>A::t('appointments', 'City'), 'validation'=>array('required'=>true, 'type'=>'text', 'maxLength'=>64));
                    //$fields['zip_code'] = array('title'=>A::t('appointments', 'Zip Code'), 'validation'=>array('required'=>true, 'type'=>'zipCode', 'maxLength'=>32));
                    //$fields['country_code'] = array('title'=>A::t('appointments', 'Country'), 'validation'=>array('required'=>false, 'type'=>'set', 'source'=>array_keys($this->_view->countries)));
                    //$fields['state'] = array('title'=>A::t('appointments', 'State/Province'), 'validation'=>array('required'=>false, 'type'=>'text', 'maxLength'=>64));
                    $fields['email'] = array('title'=>A::t('appointments', 'Email'), 'validation'=>array('required'=>true, 'type'=>'email', 'maxLength'=>100));
                    $fields['username'] = array('title'=>A::t('appointments', 'Username'), 'validation'=>array('required'=>true, 'type'=>'login', 'minLength'=>6, 'maxLength'=>32));
                    $fields['password'] = array('title'=>A::t('appointments', 'Password'), 'validation'=>array('required'=>true, 'type'=>'password', 'minLength'=>6, 'maxLength'=>25));
                    $fields['confirm_password'] = array('title'=>A::t('appointments', 'Confirm Password'), 'validation'=>array('required'=>true, 'type'=>'confirm', 'confirmField'=>'password', 'minLength'=>6, 'maxLength'=>25));
                    $fields['notifications'] = array('title'=>A::t('appointments', 'Notifications'), 'validation'=>array('required'=>false, 'type'=>'set', 'source'=>array('1')));
                    $fields['i_agree'] = array('title'=>A::t('appointments', 'Terms & Conditions'), 'validation'=>array('required'=>true, 'type'=>'set', 'source'=>array('1')));
                    $captcha = $cRequest->getPost('captcha');

                    $result = CWidget::create('CFormValidation', array(
                        'fields' => $fields
                    ));
                    if($result['error']){
                        $arr[] = '"status": "0"';
                        $arr[] = '"error": "'.$result['errorMessage'].'"';
                    }elseif($this->_view->verificationCaptcha && $captcha === '' && !in_array(CAuth::getLoggedRole(), array('owner', 'mainadmin', 'admin', 'doctor'))){
                        $arr[] = '"status": "0"';
                        $arr[] = '"error_field": "captcha_validation"';
                        $arr[] = '"error": "'.A::t('appointments', 'The field captcha cannot be empty!').'"';
                    }elseif($this->_view->verificationCaptcha && $captcha != A::app()->getSession()->get('captchaResult') && !in_array(CAuth::getLoggedRole(), array('owner', 'mainadmin', 'admin', 'doctor'))){
                        $arr[] = '"status": "0"';
                        $arr[] = '"error_field": "captcha_validation"';
                        $arr[] = '"error": "'.A::t('appointments', 'Sorry, the code you have entered is invalid! Please try again.').'"';
                    }else{
                        $username = $cRequest->getPost('username');
                        $password = $cRequest->getPost('password');

                        // Check if access is blocked to this username
                        $usernameBanned = Website::checkBan('username', $username, $errors);
                        if($usernameBanned){
                            // Do nothing
                            $arr[] = '"status": "0"';
                            $arr[] = '"error": "'.$errors['alert'].'"';
                        }else{
                            // Password encryption
                            if(CConfig::get('password.encryption')){
                                $encryptAlgorithm = CConfig::get('password.encryptAlgorithm');
                                $hashKey = CConfig::get('password.hashKey');
                                $passwordEncrypted = CHash::create($encryptAlgorithm, $password, $hashKey);
                            }else{
                                $passwordEncrypted = $password;
                            }

                            $patient = new Patients();
                            $patient->patient_first_name        = $cRequest->getPost('first_name');
                            $patient->patient_last_name         = $cRequest->getPost('last_name');
                            $patient->gender                    = $cRequest->getPost('gender');
                            //$patient->birth_date              = $cRequest->getPost('birth_date');
                            $patient->phone                   = $cRequest->getPost('phone');
                            //$patient->fax                     = $cRequest->getPost('fax');
                            //$patient->address                 = $cRequest->getPost('address');
                            //$patient->address_2               = $cRequest->getPost('address_2');
                            //$patient->city                    = $cRequest->getPost('city');
                            //$patient->zip_code                = $cRequest->getPost('zip_code');
                            //$patient->country_code            = $cRequest->getPost('country_code');
                            //$patient->state                   = $cRequest->getPost('state');

                            $accountCreated = false;
                            if($patient->save()){
                                $patient = $patient->refresh();

                                // update accounts table
                                $account = Accounts::model()->findByPk((int)$patient->account_id);
                                if($approvalType == 'by_admin'){
                                    $account->registration_code = CHash::getRandomString(20);
                                    $account->is_active = 0;
                                }elseif($approvalType == 'by_email'){
                                    $account->registration_code = CHash::getRandomString(20);
                                    $account->is_active = 0;
                                }else{
                                    $account->registration_code = '';
                                    $account->is_active = 1;
                                }
                                if($account->save()){
                                    $arr[] = '"patientId": "'.$patient->id.'"';
                                    $accountCreated = true;
                                }
                            }

                            if(!$accountCreated){
                                $arr[] = '"status": "0"';
                                if(APPHP_MODE == 'demo'){
                                    $arr[] = '"error": "'.A::t('appointments', 'This operation is blocked in Demo Mode!').'"';
                                }else{
                                    $arr[] = '"error": "'.(($patient->getError() != '') ? $patient->getError() : A::t('appointments', 'An error occurred while creating patient account! Please try again later.')).'"';
                                    $arr[] = '"error_field": "'.$patient->getErrorField().'"';
                                }
                            }else{
                                $firstName = $patient->patient_first_name;
                                $lastName = $patient->patient_last_name;
                                $patientEmail = $cRequest->getPost('email');
                                $emailResult = true;

                                // Send notification to admin about new registration
                                if(ModulesSettings::model()->param('appointments', 'patient_new_registration_alert')){
                                    $adminLang = '';
                                    if($defaultLang = Languages::model()->find('is_default = 1')){
                                        $adminLang = $defaultLang->code;
                                    }
                                    $emailResult = Website::sendEmailByTemplate(
                                        $this->_settings->general_email,
                                        'patients_account_created_notify_admin',
                                        $adminLang,
                                        array('{FIRST_NAME}' => $firstName, '{LAST_NAME}' => $lastName, '{CUSTOMER_EMAIL}' => $patientEmail, '{USERNAME}' => $username)
                                    );
                                }

                                // Send email to patient according to approval type
                                if(!empty($patientEmail)){
                                    if($approvalType == 'by_admin'){
                                        // approval by admin
                                        $emailResult = Website::sendEmailByTemplate(
                                            $patientEmail,
                                            'patients_account_created_admin_approval',
                                            A::app()->getLanguage(),
                                            array('{FIRST_NAME}' => $firstName, '{LAST_NAME}' => $lastName, '{USERNAME}' => $username, '{PASSWORD}' => $password)
                                        );
                                    }elseif($approvalType == 'by_email'){
                                        // confirmation by email
                                        $emailResult = Website::sendEmailByTemplate(
                                            $patientEmail,
                                            'patients_account_created_email_confirmation',
                                            A::app()->getLanguage(),
                                            array('{FIRST_NAME}' => $firstName, '{LAST_NAME}' => $lastName, '{USERNAME}' => $username, '{PASSWORD}' => $password, '{REGISTRATION_CODE}' => $account->registration_code)
                                        );
                                    }else{
                                        // auto approval
                                        $emailResult = Website::sendEmailByTemplate(
                                            $patientEmail,
                                            'patients_account_created_auto_approval',
                                            A::app()->getLanguage(),
                                            array('{FIRST_NAME}' => $firstName, '{LAST_NAME}' => $lastName, '{USERNAME}' => $username, '{PASSWORD}' => $password)
                                        );
                                    }
                                }
                                if(!$emailResult){
                                    $arr[] = '"status": "1"';
                                    $arr[] = '"error": "'.A::t('appointments', 'Your account has been successfully created, but email not sent! Please try again later.').'"';
                                }else{
                                    $arr[] = '"status": "1"';
                                }
                            }
                        }
                    }
                }
            }

            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');   // date in the past
            header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
            header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
            header('Pragma: no-cache'); // HTTP/1.0
            header('Content-Type: application/json');

            echo '{';
            echo implode(',', $arr);
            echo '}';

            exit;
        }else{
            if($approvalType == 'by_admin'){
                $messageSuccess = A::t('appointments', 'Account successfully created! Admin approval required.');
                $messageInfo    = A::t('appointments', 'Admin approve registration? Click <a href="{url}">here</a> to proceed.', array('{url}'=>'patients/login'));
            }elseif($approvalType == 'by_email'){
                $messageSuccess = A::t('appointments', 'Account successfully created! Email confirmation required.');
                $messageInfo    = A::t('appointments', 'Already confirmed your registration? Click <a href="{url}">here</a> to proceed.', array('{url}'=>'patients/login'));
            }else{
                $messageSuccess = A::t('appointments', 'Account successfully created!');
                $messageInfo    = A::t('appointments', 'Click <a href="{url}">here</a> to proceed.', array('{url}'=>'patients/login'));
            }
            $this->_view->messageSuccess = $messageSuccess;
            $this->_view->messageInfo    = $messageInfo;
        }

        $this->_view->showOnlyText = true;
        $this->_view->setLayout('no_columns');
        $this->_view->textTermsConditions = $this->_view->render('doctors/termsconditions', true, true);

        $this->_view->setLayout('default');
        $this->_view->render('patients/registration');
    }

    /**
     * Patient restore password action handler
     * @return void
     */
    public function restorePasswordAction()
    {
        // Redirect logged in patients
        CAuth::handleLoggedIn('patients/dashboard', 'patient');

        // Check if action allowed
        if(!ModulesSettings::model()->param('appointments', 'patient_allow_restore_password')){
            $this->redirect(Website::getDefaultPage());
        }

        // Set frontend mode
        Website::setFrontend();

        $errors = array();
        $cRequest = A::app()->getRequest();

        if($cRequest->getPost('act') == 'send'){

            // Check if access is blocked to this IP address
            $ipBanned = Website::checkBan('ip_address', $cRequest->getUserHostAddress(), $errors);
            if($ipBanned){
                $alert = $errors['alert'];
                $alertType = $errors['alertType'];
            }else{
                $email = $cRequest->getPost('email');
                $alertType = '';
                $alert = '';

                // Check if access is blocked to this email
                $emailBanned = Website::checkBan('email_address', $email, $errors);
                if($emailBanned){
                    // do nothing
                    $alert = $errors['alert'];
                    $alertType = $errors['alertType'];
                }else{
                    if(empty($email)){
                        $alertType = 'validation';
                        $alert = A::t('appointments', 'The field email cannot be empty!');
                    }elseif(!empty($email) && !CValidator::isEmail($email)){
                        $alertType = 'validation';
                        $alert = A::t('appointments', 'You must provide a valid email address!');
                    }elseif(APPHP_MODE == 'demo'){
                        $alertType = 'warning';
                        $alert = A::t('appointments', 'This operation is blocked in Demo Mode!');
                    }else{
                        $account = Accounts::model()->find('role = :role AND email = :email', array(':role'=>'patient', ':email'=>$email));
                        if(empty($account)){
                            $alertType = 'error';
                            $alert = A::t('appointments', 'Sorry, but we were not able to find a patient with that login information!');
                        }else{
                            $username = $account->username;
                            $preferedLang = $account->language_code;
                            // generate new password
                            if(CConfig::get('password.encryption')){
                                $password = CHash::getRandomString(8);
                                $account->password = CHash::create(CConfig::get('password.encryptAlgorithm'), $password, $account->salt);
                                if(!$account->save()){
                                    $alertType = 'error';
                                    if(APPHP_MODE == 'debug'){
                                        $alert = Accounts::model()->getErrorMessage();
                                    }else{
                                        $alert = A::t('appointments', 'An error occurred while password recovery process! Please try again later.');
                                    }
                                }
                            }else{
                                $password = $account->password;
                            }

                            if(!$alert){
                                $result = Website::sendEmailByTemplate(
                                    $email,
                                    'patients_password_forgotten',
                                    $preferedLang,
                                    array(
                                        '{USERNAME}' => $username,
                                        '{PASSWORD}' => $password
                                    )
                                );
                                if($result){
                                    $alertType = 'success';
                                    $alert = A::t('appointments', 'Check your e-mail address linked to the account for the confirmation link, including the spam or junk folder.');
                                }else{
                                    $alertType = 'error';
                                    $alert = A::t('appointments', 'An error occurred while password recovery process! Please try again later.');
                                }
                            }
                        }
                    }
                }
            }

            if(!empty($alert)){
                $this->_view->actionMessage = CWidget::create('CMessage', array($alertType, $alert));
            }
        }

        $this->_view->render('patients/restorePassword');
    }

    /**
     * Patient confirm registration action handler
     * @param string $code
     * @return void
     */
    public function confirmRegistrationAction($code)
    {
        // redirect logged in directory
        CAuth::handleLoggedIn('patients/dashboard', 'patient');

        // set frontend mode
        Website::setFrontend();

        if($patient = Accounts::model()->find('is_active = 0 AND registration_code = :code', array(':code'=>$code))){
            $patient->is_active = 1;
            $patient->registration_code = '';
            if($patient->save()){
                $alertType = 'success';
                $alert = A::t('appointments', 'Account registration confirmed');
            }else{
                if(APPHP_MODE == 'demo'){
                    $alertType = 'warning';
                    $alert = CDatabase::init()->getErrorMessage();
                }else{
                    $alertType = 'error';
                    $alert = A::t('appointments', 'Account registration error');
                }
            }
        }else{
            $alertType = 'warning';
            $alert = A::t('appointments', 'Account registration wrong code');
        }

        if(!empty($alert)){
            $this->_view->actionMessage = CWidget::create('CMessage', array($alertType, $alert));
        }
        $this->_view->render('patients/confirmRegistration');
    }

    /**
     * Dashboard action handler
     * @return void
     */
    public function dashboardAction()
    {
        // block access to this controller for not-logged patients
        CAuth::handleLogin('patients/login', 'patient');
        // set meta tags according to active language
        Website::setMetaTags(array('title'=>A::t('appointments', 'Dashboard')));
        // set frontend settings
        Website::setFrontend();

        $this->_view->render('patients/dashboard');
    }

    /**
     * Patient Account action handler
     * @return void
     */
    public function myAccountAction()
    {
        // block access to this controller for not-logged patients
        CAuth::handleLogin('patients/login', 'patient');
        // set meta tags according to active language
        Website::setMetaTags(array('title'=>A::t('appointments', 'My Account')));
        // set frontend settings
        Website::setFrontend();

        $alert = A::app()->getSession()->getFlash('alert');
        $alertType = A::app()->getSession()->getFlash('alertType');
        $this->_view->actionMessage = !empty($alertType) ? CWidget::create('CMessage', array($alertType, $alert)) : '';

        $patient = $this->_checkAccountsAccess(A::app()->getSession()->get('loggedId'));
        $this->_prepareAccountFields();
        // fetch datetime format from settings table
        $dateTimeFormat = Bootstrap::init()->getSettings('datetime_format');
        $dateFormat = Bootstrap::init()->getSettings('date_format');

        $this->_view->patient = $patient;
        // prepare some fields
        if($patient->patient_first_name || $patient->patient_last_name){
            $this->_view->fullName = $patient->patient_first_name.($patient->patient_last_name != '' ? ' '.$patient->patient_last_name : '');
        }else{
            $this->_view->fullName = '';
        }
        $this->_view->countryName = $patient->country_code;
        $this->_view->stateName = $patient->state;
        $this->_view->langName = $patient->language_code;
        $this->_view->notifications = ($patient->notifications) ? A::t('appointments', 'Yes') : A::t('appointments', 'No');
        $this->_view->birthDate = ($patient->birth_date && ! CTime::isEmptyDate($patient->birth_date)) ? date($dateFormat, strtotime($patient->birth_date)) : '';
        $this->_view->createdAt = ($patient->created_at && ! CTime::isEmptyDateTime($patient->created_at)) ? date($dateTimeFormat, strtotime($patient->created_at)) : '- '.A::t('appointments', 'Unknown').' -';
        $this->_view->lastVisitedAt = ($patient->last_visited_at && ! CTime::isEmptyDateTime($patient->last_visited_at)) ? date($dateTimeFormat, strtotime($patient->last_visited_at)) : '- '.A::t('appointments', 'Unknown').' -';

        if($country = Countries::model()->find('code = :code', array(':code'=>$patient->country_code))){
            $this->_view->countryName = $country->country_name;
        }
        if($state = States::model()->find('country_code = :country_code AND code = :code', array(':country_code'=>$patient->country_code, ':code'=>$patient->state))){
            $this->_view->stateName = $state->state_name;
        }
        if($language = Languages::model()->find('code = :code', array(':code'=>$patient->language_code))){
            $this->_view->langName = $language->name;
        }

        $this->_view->render('patients/myAccount');
    }

    /**
     * Patient edit Account action handler
     * @return void
     */
    public function editAccountAction()
    {
        // block access to this controller for not-logged patients
        CAuth::handleLogin('patients/login', 'patient');
        // set meta tags according to active language
        Website::setMetaTags(array('title'=>A::t('appointments', 'Edit Account')));
        // set frontend settings
        Website::setFrontend();

        $states    = array();
        $alert     = A::app()->getSession()->getFlash('alert');
        $alertType = A::app()->getSession()->getFlash('alertType');
        $this->_view->actionMessage = CWidget::create('CMessage', array($alertType, $alert));

        $patient = $this->_checkAccountsAccess(A::app()->getSession()->get('loggedId'));
        $this->_prepareAccountFields();

        $cRequest = A::app()->getRequest();
        if($cRequest->isPostRequest()){
            $countryCode = $cRequest->getPost('country_code');
            $stateCode = $cRequest->getPost('state');
        }else{
            $countryCode = $patient->country_code;
            $stateCode = $patient->state;
        }

        if(!empty($countryCode)){
            // prepare countries
            $states = array(''=>'- '.A::t('appointments', 'select').' -');
            $statesResult = States::model()->findAll(array('condition'=>'is_active = 1 AND '.CConfig::get('db.prefix').'states.country_code = :country_code', 'order'=>'sort_order DESC, state_name ASC'), array(':country_code'=>$countryCode));
            if(is_array($statesResult)){
                foreach($statesResult as $key => $val){
                    $states[$val['code']] = $val['state_name'];
                }
            }
        }

        // prepare salt
        $this->_view->salt = '';
        if(A::app()->getRequest()->getPost('password') != ''){
            $this->_view->salt = CConfig::get('password.encryptSalt') ? CHash::salt() : '';
            A::app()->getRequest()->setPost('salt', $this->_view->salt);
        }

        $this->_view->id = $patient->id;
        $this->_view->countryCode = $countryCode;
        $this->_view->stateCode = $stateCode;
        $this->_view->states = $states;
        // fetch datetime format from settings table
        $this->_view->dateTimeFormat = Bootstrap::init()->getSettings('datetime_format');
        $this->_view->render('patients/editAccount');
    }

    /**
     * Patient remove account action handler
     * @return void
     */
    public function removeAccountAction()
    {
        // block access to this controller for not-logged patients
        CAuth::handleLogin('patients/login', 'patient');
        // set meta tags according to active language
        Website::setMetaTags(array('title'=>A::t('appointments', 'Remove Account')));
        // set frontend settings
        Website::setFrontend();

        $alert = A::app()->getSession()->getFlash('alert');
        $alertType = A::app()->getSession()->getFlash('alertType');
        $this->_view->actionMessage = CWidget::create('CMessage', array($alertType, $alert));

        $loggedId = A::app()->getSession()->get('loggedId');
        $patient = $this->_checkAccountsAccess($loggedId);
        $alertType = '';
        $alert = '';
        $this->_view->accountRemoved = false;

        $cRequest = A::app()->getRequest();
        if($cRequest->isPostRequest()){
            if($cRequest->getPost('act') != 'send'){
                $this->redirect('patients/myAccount');
            }elseif(APPHP_MODE == 'demo'){
                $alertType = 'warning';
                $alert = A::t('appointments', 'This operation is blocked in Demo Mode!');
            }else{

                // add removing account here
                $removalType = ModulesSettings::model()->param('appointments', 'patient_removal_type');
                $this->_view->accountRemoved = true;
                if($removalType == 'logical'){
                    if(!Accounts::model()->updateByPk($loggedId, array('is_removed'=>1, 'is_active'=>0))){
                        $this->_view->accountRemoved = false;
                    }
                }elseif($removalType == 'physical'){
                    if(!Accounts::model()->deleteByPk($loggedId)){
                        $this->_view->accountRemoved = false;
                    }
                }

                if($this->_view->accountRemoved){
                    $alertType = 'success';
                    $alert = A::t('appointments', 'Your account has been successfully removed!');

                    $result = Website::sendEmailByTemplate(
                        CAuth::getLoggedEmail(),
                        'patients_account_removed_by_patient',
                        CAuth::getLoggedLang(),
                        array('{USERNAME}' => CAuth::getLoggedName())
                    );

                    $this->_logout();
                }else{
                    $alertType = 'error';
                    $alert = A::t('appointments', 'An error occurred while deleting your account! Please try again later.');
                }
            }
        }

        if(!empty($alert)){
            $this->_view->actionMessage = CWidget::create('CMessage', array($alertType, $alert));
        }
        $this->_view->render('patients/removeAccount');
    }

    /**
     * Show page Terms & Conditions
     * @return void
     */
    public function termsConditionsAction()
    {
        // set frontend settings
        Website::setFrontend();

        $this->_view->showOnlyText = false;
        $this->_view->render('patients/termsConditions');
    }


    /**
     * Show page Appointments
     * @param string $status
     * @return void
     */
    public function myAppointmentsAction($status = 'future')
    {
        // block access to this controller for not-logged doctors
        CAuth::handleLogin('patients/login', 'patient');
        // set meta tags according to active language
        Website::setMetaTags(array('title'=>A::t('appointments', 'My Appointments')));
        // set frontend settings
        Website::setFrontend();

		$alert = A::app()->getSession()->getFlash('alert');
		$alertType = A::app()->getSession()->getFlash('alertType');

		if(!empty($alert)){
			$this->_view->actionMessage = CWidget::create(
				'CMessage', array($alertType, $alert, array('button'=>true))
			);
		}

        $tableAccountName 	= CConfig::get('db.prefix').Accounts::model()->getTableName();
        $condition = $tableAccountName.'.is_active = 1 AND '.$tableAccountName.'.is_removed = 0';
        $doctors = Doctors::model()->findAll($condition);
        $patientId = CAuth::getLoggedRoleId();
		if(!empty($patientId)){
			$this->_view->patientId = $patientId;
		}else{
			$this->redirect('Home/index');
		}
        $this->_view->doctors = $doctors;
        $this->_view->status = $status;
        $this->_view->appointmentTimeFormat = ModulesSettings::model()->param('appointments', 'time_format_appointment_time');
        $this->_view->render('patients/myAppointments');
    }


    /**
     * Add Appointment
     * @param int $doctorId
     * @param string $seoLink
     * @param string $status
     */
    public function addMyAppointmentAction($doctorId = 0, $seoLink = '')
    {
        A::app()->getSession()->remove('changeAppointmentId');
        A::app()->getSession()->remove('changeDoctorId');

        $this->redirect('appointments/'.$doctorId.'/'.$seoLink);
    }

    /**
     * Edit page Appointments
     * @param int $id
     * @param string $status
     */
    public function editMyAppointmentAction($id = 0, $status = 'future')
    {
        // block access to this controller for not-logged doctors
        CAuth::handleLogin('patients/login', 'patient');
        // set meta tags according to active language
        Website::setMetaTags(array('title'=>A::t('appointments', 'Edit My Appointment')));
        // set frontend settings
        Website::setFrontend();
        $visitReason = '';
        $forWhom = '';

		$appointment = $this->_checkAppointmentAccess($id, true);
        if($appointment->other_reasons){
            $visitReason = A::t('appointments', 'Other').': '.$appointment->other_reasons;
        }else{
            $reasons = VisitReasons::model()->findAll(array('condition'=>'is_active = 1', 'orderBy'=>'sort_order ASC'));
            foreach($reasons as $reason){
                $arrReasons[$reason['id']] = $reason['name'];
            }
            if(!empty($arrReasons[$appointment->visit_reason_id])){
                $visitReason = $arrReasons[$appointment->visit_reason_id];
            }else{
                $visitReason = '--';
            }
        }
        if($appointment->for_whom_someone_else){
            $forWhom = A::t('appointments', 'Someone else').': '.$appointment->for_whom_someone_else;
        }else{
            $forWhom = A::t('appointments', 'Me');
        }

        $this->_view->forWhom = $forWhom;
        $this->_view->visitReason = $visitReason;
		$this->_view->id = $id;
		$this->_view->status = $status;
        $this->_view->appointmentTimeFormat = ModulesSettings::model()->param('appointments', 'time_format_appointment_time');
        $this->_view->render('patients/editMyAppointment');
    }

    /**
     * Patient cancel appointment action handler
     * @param int $id
     * @param string $status
     */
    public function cancelMyAppointmentAction($id = 0, $status = 'future')
    {
        // block access to this controller for not-logged doctors
        CAuth::handleLogin('patients/login', 'patient');
        $appointment = $this->_checkAppointmentAccess($id, false);

        $alert = '';
        $alertType = '';

        $appointment->status = 2;//canceled
        //$appointment->status_changed = date('Y-m-d H:i:s');
        if($appointment->save()){
            $alert = A::t('appointments', 'Cancel Success Message');
            $alertType = 'success';
        }else{
            if(APPHP_MODE == 'demo'){
                $alert = CDatabase::init()->getErrorMessage();
                $alertType = 'warning';
            }else{
                $alert = A::t('app', 'Cancel Error Message');
                $alertType = 'error';
            }
        }

        if(!empty($alert)){
            A::app()->getSession()->setFlash('alert', $alert);
            A::app()->getSession()->setFlash('alertType', $alertType);
        }
        $this->redirect('patients/myAppointments'.(!empty($status) ? '/status/'.$status : ''));
    }

    /**
     * Patient change appointment action handler
     * @param int $id
     */
    public function changeMyAppointmentAction($id = 0)
    {
        // block access to this controller for not-logged doctors
        CAuth::handleLogin('patients/login', 'patient');
        $appointment = $this->_checkAppointmentAccess($id, false);
        $doctor = $this->_checkDoctorAccess($appointment->doctor_id);

        A::app()->getSession()->set('changeAppointmentId', $id);
        A::app()->getSession()->set('changeDoctorId', $appointment->doctor_id);

        $this->redirect('appointments/'.$doctor->id);
    }

    /**
     * Manage action handler
     * @param int $patientId
     * @return void
     */
    public function medicalCardAction($patientId = 0)
    {
        if (CAuth::isLoggedInAs('doctor')) {
            // block access to this controller for not-logged doctors
            CAuth::handleLogin('doctors/login', 'doctor');
            // set frontend mode
            Website::setFrontend();
        } else {
            // set backend mode
            Website::setBackend();
            Website::prepareBackendAction('manage', 'patient', 'modules/index');
        }

        $patient = $this->_checkPatientAccess($patientId);
        $doctorIds = array();
        $specialtyIds = array();
        $filterSpecialty = array();
        $filterDoctors = array();

        $appointmentsTable = CConfig::get('db.prefix') . Appointments::model()->getTableName();
        $allAppointments = Appointments::model()->findAll($appointmentsTable.'.patient_id = '.$patient->id);
        if (!empty($allAppointments) && is_array($allAppointments)) {
            foreach ($allAppointments as $allAppointment) {
                if (!in_array($allAppointment['doctor_id'], $doctorIds)) {
                    $doctorIds[] = $allAppointment['doctor_id'];
                }

                if (!in_array($allAppointment['doctor_specialty_id'], $specialtyIds)) {
                    $specialtyIds[] = $allAppointment['doctor_specialty_id'];
                }
            }
        }

        if (!empty($doctorIds) && is_array($doctorIds)) {
            $doctorsTable = CConfig::get('db.prefix') . Doctors::model()->getTableName();
            $doctors = Doctors::model()->findAll($doctorsTable.'.id IN('.implode(',', $doctorIds).')');
            if (!empty($doctors) && is_array($doctors)) {
                foreach ($doctors as $doctor) {
                    $filterDoctors[$doctor['id']] = $doctor['full_name'];
                }
            }
        }

        if (!empty($specialtyIds) && is_array($specialtyIds)) {
            $specialtyTable = CConfig::get('db.prefix') . Specialties::model()->getTableName();
            $specialty = Specialties::model()->findAll($specialtyTable.'.id IN('.implode(',', $specialtyIds).')');
            if (!empty($specialty) && is_array($specialty)) {
                foreach ($specialty as $spec) {
                    $filterSpecialty[$spec['id']] = $spec['name'];
                }
            }
        }

        $alert = A::app()->getSession()->getFlash('alert');
        $alertType = A::app()->getSession()->getFlash('alertType');

        if(!empty($alert)){
            $this->_view->actionMessage = CWidget::create('CMessage', array($alertType, $alert, array('button'=>true)));
        }

        $this->_view->patientId = $patient->id;
        $this->_view->filterDoctors = $filterDoctors;
        $this->_view->filterSpecialty = $filterSpecialty;
        $this->_view->patientName = $patient->getFullName();
        $this->_view->patient = $patient;
        $this->_view->appointmentTimeFormat = ModulesSettings::model()->param('appointments', 'time_format_appointment_time');

        if (CAuth::isLoggedInAs('doctor')) {
            $this->_view->render('doctors/patientMedicalCard');
        } elseif (CAuth::isLoggedInAsAdmin()) {
            $this->_view->render('patients/medicalCard');
        }
    }

    /**
     * Change status patient action handler
     * @param int $id 
     * @param int $page 	the page number
     * @return void
     */
    public function activeStatusAction($id, $page = 1)
    {
        Website::prepareBackendAction('edit', 'patient', 'patients/manage');

        $patient = Patients::model()->findbyPk($id);
        if(!empty($patient)){
            if(Accounts::model()->updateByPk($patient->account_id, array('is_active'=>($patient->is_active == 1 ? '0' : '1')))){
                $alert = A::t('appointments', 'Status has been successfully changed!');
                $alertType = 'success';
            }else{
                if(APPHP_MODE == 'demo'){
                    $alert = CDatabase::init()->getErrorMessage();
                    $alertType = 'warning';
                }else{
                    $alert = A::t('appointments', 'Status changing error');
                    $alertType = 'error';
                }
            }
        }
		
        if(!empty($alert)){
            A::app()->getSession()->setFlash('alert', $alert);
            A::app()->getSession()->setFlash('alertType', $alertType);
        }

        $this->redirect('patients/manage'.(!empty($page) ? '?page='.(int)$page : 1));
    }

    /*
     * Return Patients Names
     * @return json
     * */
    public function ajaxGetPatientNamesAction()
    {
        // Block access if this is not AJAX request
        $cRequest = A::app()->getRequest();
        if(!$cRequest->isAjaxRequest()){
            $this->redirect('appointments/manage');
        }

        $arr = array();

        $loggedRole = CAuth::getLoggedRole();
        if(in_array($loggedRole, array('admin', 'owner', 'doctor'))){
            $result = CWidget::create('CFormValidation', array(
                'fields' => array(
                    'search' => array('title'=>A::t('appointments', 'Name'), 'validation'=>array('required'=>true, 'type'=>'name', 'maxLength'=>65)),
                ),
            ));

            if(!empty($result['error'])){
                $errorMessage = str_replace('"', '\"', $result['errorMessage']);

                $arr[] = '{"status": "0"}';
                $arr[] = '{"message": "'.$errorMessage.'"}';
            }else{
                $search = $cRequest->getPost('search');
                $fullName = explode(' ', $search, 2);
                if(!empty($fullName)){
                    if(count($fullName) == 1){
                        $fullName[0] = strip_tags(CString::quote($fullName[0]));
                        $params[':patient_first_name'] = $fullName[0].'%';
                        $params[':patient_last_name']  = $fullName[0].'%';

                        $condition = 'patient_first_name LIKE :patient_first_name OR patient_last_name LIKE :patient_last_name';
                    }else{
                        $fullName[0] = strip_tags(CString::quote($fullName[0]));
                        $fullName[1] = strip_tags(CString::quote($fullName[1]));
                        $params[':patient_first_name_1'] = $fullName[1].'%';
                        $params[':patient_last_name_1']  = $fullName[0].'%';
                        $params[':patient_first_name_2'] = $fullName[0].'%';
                        $params[':patient_last_name_2']  = $fullName[1].'%';

                        $condition = '(patient_first_name LIKE :patient_first_name_1 AND patient_last_name LIKE :patient_last_name_1) OR (patient_first_name LIKE :patient_first_name_2 AND patient_last_name LIKE :patient_last_name_2)';
                    }

                    $accountTableName = CConfig::get('db.prefix').Accounts::model()->getTableName();
                    $patients = Patients::model()->findAll(array(
                        'condition' => $condition.' AND '.$accountTableName.'.is_active = 1 AND '.$accountTableName.'.is_removed = 0',
                        'order'=>'patient_first_name,patient_last_name'
                    ),
                        $params
                    );
                    if(is_array($patients) && !empty($patients)){
                        $arr[] = '{"status": "1"}';
                        foreach($patients as $key => $patient){
                            $arr[] = '{"id": "'.$patient['id'].'", "label": "'.htmlentities($patient['patient_first_name'].' '.$patient['patient_last_name']).'"}';
                        }
                    }
                }
            }
        }else{
            $arr[] = '{"status": "0"}';
            $arr[] = '{"message": "'.A::t('appointments', 'You do not have access to perform this operation').'"}';
        }


        if(empty($arr)){
            $arr = '';
        }

        $this->_outputAjax($arr);
    }

    /**
     * Check if passed Account ID is valid
     * @param int $id
     * @return object Patients
     */
    private function _checkAccountsAccess($id = 0)
    {
        $patient = Patients::model()->find('account_id = :account_id AND is_active = 1', array('i:account_id'=>$id));
        if(!$patient){
            $this->redirect('patients/manage');
        }
		
        return $patient;
    }

	/**
	 * Check if passed Appointment ID is valid
	 * @param int $id
	 * @param bool $editPast
	 * @return object Appointments
	 */
	private function _checkAppointmentAccess($id = 0, $editPast = false)
	{
		if(empty($id)){
			$this->redirect('patients/manage');
		}

		$appointment = array();

		$patientId = CAuth::getLoggedRoleId();
		$tableName = CConfig::get('db.prefix').Appointments::model()->getTableName();
        if($editPast){
            $condition = $tableName.'.id = :id AND '.$tableName.'.patient_id = :patient_id';
        }else{
            $condition = $tableName.'.id = :id AND '.$tableName.'.patient_id = :patient_id AND ('.$tableName.".appointment_date > '".LocalTime::currentDateTime('Y-m-d')."'".' OR ('.$tableName.".appointment_date = '".LocalTime::currentDateTime('Y-m-d')."'".' AND '.$tableName.".appointment_time > '".LocalTime::currentDateTime('H:i:s')."'))";
        }
        $appointment = Appointments::model()->find($condition, array(':patient_id'=>$patientId, ':id'=>$id));
		if(!$appointment){
			$this->redirect('patients/manage');
		}
		return $appointment;
	}

    /**
     * Check if passed Patient ID is valid
     * @param int $id
     * @return Patients
     */
    private function _checkPatientAccess($id = 0)
    {
        $patient = Patients::model()->findByPk($id);
        if(!$patient){
            $this->redirect('patients/manage');
        }
        return $patient;
    }

    /**
     * Check if passed Doctor ID is valid
     * @param int $id
     * @param bool $redirect
     * @return object Doctors
     */
    private function _checkDoctorAccess($id = 0, $redirect = false)
    {
        if(empty($id)){
            $this->redirect('doctors/manage');
        }

        if($redirect){
            $tableName = CConfig::get('db.prefix').Accounts::model()->getTableName();
            $doctor = Doctors::model()->findByPk($id, $tableName.'.is_active = 1 AND '.$tableName.'.is_removed = 0');
        }else{
            $doctor = Doctors::model()->findByPk($id);
        }

        if(!$doctor){
            $this->redirect('doctors/manage');
        }
        return $doctor;
    }

    /**
     * Prepares account fields
     * @return void
     */
    private function _prepareAccountFields()
    {
        // Prepare settings
        $this->_view->removalType    = ModulesSettings::model()->param('appointments', 'patient_removal_type');
        $this->_view->changePassword = ModulesSettings::model()->param('appointments', 'change_patient_password');
        $this->_view->verificationCaptcha = ModulesSettings::model()->param('appointments', 'patients_verification_allow');

        // Prepare gender
        $genders = array('m'=>A::t('appointments', 'Male'), 'f'=>A::t('appointments', 'Female'));

        // Prepare degrees
        $result = Degrees::model()->findAll(array('condition' => 'is_active = 1', 'orderBy' => 'sort_order ASC'));
        $degrees = array();
        if(!empty($result) && is_array($result)){
            foreach($result as $degree){
                $degrees[$degree['id']] = $degree['title'].(!empty($degree['name']) ? ' ('.$degree['name'].')' : '');
            }
        }

        // Prepare titles
		$titles = Titles::getActiveTitles();

        // Prepare experience years
        $experienceYears = array();
        for($i = 0; $i <= 60; $i++){
            $experienceYears[$i] = $i;
        }

        // Prepare countries
        $countries = array(''=>'- '.A::t('appointments', 'select').' -');
        $countriesResult = Countries::model()->findAll(array('condition'=>'is_active = 1', 'order'=>'sort_order DESC, country_name ASC'));
        $this->_view->defaultCountryCode = '';
        if(is_array($countriesResult)){
            foreach($countriesResult as $key => $val){
                $countries[$val['code']] = $val['country_name'];
                if($val['is_default']) $this->_view->defaultCountryCode = $val['code'];
            }
        }

        // Prepare languages
        $langList = array();
        $languagesResult = Languages::model()->findAll(array('condition'=>'is_active = 1', 'orderBy'=>'sort_order ASC'));
        if(is_array($languagesResult)){
            foreach($languagesResult as $lang){
                $langList[$lang['code']] = $lang['name_native'];
            }
        }

        // Prepare locale
        $localesList = A::app()->getLocalTime()->getLocales();

        $this->_view->localesList     = $localesList;
        $this->_view->genders         = $genders;
        $this->_view->degrees         = $degrees;
        $this->_view->titles          = $titles;
        $this->_view->experienceYears = $experienceYears;
        $this->_view->countries       = $countries;
        $this->_view->langList        = $langList;
    }

    /**
     * Patient logout
     * @return void
     */
    private function _logout()
    {
        A::app()->getSession()->endSession();
        A::app()->getCookie()->remove('patientAuth');
        // clear cache
        if(CConfig::get('cache.enable')) CFile::emptyDirectory('protected/tmp/cache/');
    }

    /**
     * Outputs data to browser
     * @param array $data
     * @param bool $returnArray
     * @return void
     */
    private function _outputAjax($data = array(), $returnArray = true)
    {
        $json = '';
        if($returnArray){
            $json .= '[';
            $json .= array($data) ? implode(',', $data) : '';
            $json .= ']';
        }else{
            $json .= '{';
            $json .= implode(',', $data);
            $json .= '}';
        }

        $this->_outputJson($json);
    }

    /**
     * Outputs json to browser
     * @param string $json
     * @return void
     */
    private function _outputJson($json)
    {
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');   // Date in the past
        header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // Always modified
        header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
        header('Pragma: no-cache'); // HTTP/1.0
        header('Content-Type: application/json');

        echo $json;

        exit;
    }
}
