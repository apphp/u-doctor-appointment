<?php
/**
 * Doctors controller
 * This controller intended to both Backend and Frontend modes
 *
 * PUBLIC:                          PRIVATE
 * -----------                      ------------------
 * __construct                      _checkAccountsAccess
 * indexAction                      _checkAppointmentAccess
 * indexAction                      _checkDoctorAccess
 * manageAction                     _checkMembershipPlanAccess
 * addAction                        _getDegrees
 * editAction                       _logout
 * deleteAction                     _prepareAccountFields
 * loginAction                      _prepareImages
 * logoutAction                     _prepareScheduleCounters
 * registrationAction               _prepareSpecialtyCounters
 * restorePasswordAction            _prepareTimeoffs
 * confirmRegistrationAction        _prepareClinicCounters
 * dashboardAction					_outputJson
 * myAccountAction                  _outputAjax
 * editAccountAction
 * removeAccountAction
 * termsConditionsAction
 * ourStaffAction
 * profileAction
 * appointmentsAction
 * editAppointmentAction
 * cancelAppointmentAction
 * activeStatusAction
 * ajaxGetDoctorNamesAction
 *
 *
 */

namespace Modules\Appointments\Controllers;

// Module
use \Modules\Appointments\Components\AppointmentsComponent;
use \Modules\Appointments\Components\DoctorsComponent;
use \Modules\Appointments\Models\DoctorImages;
use \Modules\Appointments\Models\DoctorScheduleTimeBlocks;
use \Modules\Appointments\Models\DoctorTimeoffs;
use \Modules\Appointments\Models\Doctors;
use \Modules\Appointments\Models\Titles;
use \Modules\Appointments\Models\Degrees;
use \Modules\Appointments\Models\DoctorSpecialties;
use \Modules\Appointments\Models\DoctorSchedules;
use \Modules\Appointments\Models\Appointments;
use \Modules\Appointments\Models\Memberships;
use \Modules\Appointments\Models\Patients;
use \Modules\Appointments\Models\Insurance;
use \Modules\Appointments\Models\Clinics;
use \Modules\Appointments\Models\DoctorClinics;
use \Modules\Appointments\Models\VisitReasons;

// Framework
use \A,
    \CAuth,
    \CArray,
    \CDatabase,
    \CCurrency,
    \CHtml,
    \CFile,
    \CImage,
    \CWidget,
    \CHash,
    \CLocale,
    \CTime,
    \CValidator,
    \CString,
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



class DoctorsController extends CController
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
        // Block access if the module is not installed
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
		$this->_view->doctorsWatermark = ModulesSettings::model()->param('appointments', 'doctors_watermark');
		$this->_view->watermarkText = ModulesSettings::model()->param('appointments', 'doctors_watermark_text');

		$settings = Bootstrap::init()->getSettings();
		$this->_view->dateFormat     = $settings->date_format;
		$this->_view->timeFormat     = $settings->time_format;
		$this->_view->dateTimeFormat = $settings->datetime_format;
		$this->_view->numberFormat   = $settings->number_format;
		$this->_view->currencySymbol = A::app()->getCurrency('symbol');
        $this->_view->typeFormat     = $settings->number_format;

		$this->_view->labelStatusAppointments = array(
			'0'=>'<span class="label-yellow label-square">'.A::t('appointments', 'Reserved').'</span>',
			'1'=>'<span class="label-green label-square">'.A::t('appointments', 'Verified').'</span>',
			'2'=>'<span class="label-red label-square">'.A::t('appointments', 'Canceled').'</span>',
		);

        $this->_view->labelPatientArrivalStatus = array(
            '0'=>'<span class="label-red label-square">'.A::t('appointments', 'No').'</span>',
            '1'=>'<span class="label-green label-square">'.A::t('appointments', 'Yes').'</span>',
        );

        if(CAuth::isLoggedInAsAdmin()){
            // Set meta tags according to active doctors
            Website::setMetaTags(array('title'=>A::t('appointments', 'Doctors Management')));

            $this->_view->tabs = AppointmentsComponent::prepareTab('doctors');
        }
    }

    /**
     * Controller default action handler
     * @return void
     */
    public function indexAction()
    {
        if(CAuth::isLoggedInAs('doctor')){
            $this->redirect('doctors/dashboard');    // !Not done
        }elseif(CAuth::isLoggedInAsAdmin()){
            $this->redirect('doctors/manage');
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
        // Set backend mode
        Website::setBackend();
        Website::prepareBackendAction('manage', 'doctor', 'modules/index');
        $this->_prepareAccountFields();
        $configModule = \CLoader::config('appointments', 'main');
        $multiClinics = $configModule['multiClinics'];
        $alert = A::app()->getSession()->getFlash('alert');
        $alertType = A::app()->getSession()->getFlash('alertType');

        if(!empty($alert)){
            $this->_view->actionMessage = CWidget::create('CMessage', array($alertType, $alert, array('button'=>true)));
        }

        $this->_view->specialtyCounters = $this->_prepareSpecialtyCounters();
        $this->_view->scheduleCounters = $this->_prepareScheduleCounters();
        $this->_view->clinicCounters = $this->_prepareClinicCounters();
        $this->_view->arrImages = $this->_prepareImages();
        $this->_view->arrTimeoffs = $this->_prepareTimeoffs();
        $this->_view->page = !empty(A::app()->getRequest()->get('page')) ? A::app()->getRequest()->get('page') : 1;
        $this->_view->multiClinics = $multiClinics;

        $this->_view->render('doctors/manage');
    }

    /**
     * Add new action handler
     * @return void
     */
    public function addAction()
    {
        // Set backend mode
        Website::setBackend();
        Website::prepareBackendAction('add', 'doctor', 'doctors/manage');
        $this->_prepareAccountFields();

        $cRequest = A::app()->getRequest();
        if($cRequest->isPostRequest()){
            $this->_view->countryCode = $cRequest->getPost('country_code');
            $this->_view->stateCode = $cRequest->getPost('state');
        }else{
            $this->_view->countryCode = $this->_view->defaultCountryCode;
            $this->_view->stateCode = '';
        }

        // Prepare salt
        $this->_view->salt = '';
        if(A::app()->getRequest()->getPost('password') != ''){
            $this->_view->salt = CConfig::get('password.encryptSalt') ? CHash::salt() : '';
        }

        $this->_view->doctorsWatermark = ModulesSettings::model()->param('appointments', 'doctors_watermark');
        $this->_view->watermarkText = ModulesSettings::model()->param('appointments', 'doctors_watermark_text');


        $this->_view->render('doctors/add');
    }

    /**
     * Edit doctors action handler
     * @param int $id
     * @param string $delete
     * @return void
     */
    public function editAction($id = 0, $delete = '')
    {
        // Set backend mode
        Website::setBackend();
        Website::prepareBackendAction('edit', 'doctor', 'doctors/manage');
        $doctor = $this->_checkDoctorAccess($id);
        $this->_prepareAccountFields();

        if (CAuth::isLoggedInAsAdmin()) {
            $arrMembershipPlans = array();
            $membershipPlans = Memberships::model()->findAll('is_active = 1');

            if (!empty($membershipPlans) && is_array($membershipPlans)) {
                foreach ($membershipPlans as $membershipPlan) {
                    $arrMembershipPlans[$membershipPlan['id']] = $membershipPlan['name'];
                }
            }

            $this->_view->arrMembershipPlans             = $arrMembershipPlans;
            $this->_view->page                           = !empty(A::app()->getRequest()->get('page')) ? A::app()->getRequest()->get('page') : 1;
            $this->_view->arrMembershipImagesCount       = array(1=>1, 2=>2, 3=>3, 4=>4, 5=>5, 6=>6, 7=>7, 8=>8, 9=>9, 10=>10);
            $this->_view->arrMembershipClinicsCount      = array(1=>1, 2=>2, 3=>3, 4=>4, 5=>5,);
            $this->_view->arrMembershipSchedulesCount    = array(1=>1, 2=>2, 3=>3, 4=>4, 5=>5,);
            $this->_view->arrMembershipSpecialtiesCount  = array(1=>1, 2=>2, 3=>3, 4=>4, 5=>5,);
        }

        $cRequest = A::app()->getRequest();
        if($cRequest->isPostRequest()){
            $this->_view->countryCode = $cRequest->getPost('country_code');
            $this->_view->stateCode = $cRequest->getPost('state');
        }else{
            $this->_view->countryCode = $doctor->country_code;
            $this->_view->stateCode = $doctor->state;
        }

        if($delete == 'avatar'){
            $account = Accounts::model()->findByPk($doctor->account_id);
            if(!empty($account) && $account->avatar != ''){
                $deleteImage = 'assets/modules/appointments/images/doctors/'.$account->avatar;
                $account->avatar = '';
                if($account->save()){
                    if(CFile::deleteFile($deleteImage)){
                        $alert = A::t('appointments', 'Image successfully deleted');
                        $alertType = 'success';
                    }else{
                        $alert = A::t('appointments', 'Image Delete Warning');
                        $alertType = 'warning';
                    }
                }else{
                    $alert = A::t('appointments', 'Image Delete Warning');
                    $alertType = 'error';
                }
            }
        }

        $this->_view->id = $doctor->id;
        $this->_view->doctorsWatermark = ModulesSettings::model()->param('appointments', 'doctors_watermark');
        $this->_view->watermarkText = ModulesSettings::model()->param('appointments', 'doctors_watermark_text');

        // Prepare salt
        $this->_view->salt = '';
        if(A::app()->getRequest()->getPost('password') != ''){
            $this->_view->salt = CConfig::get('password.encryptSalt') ? CHash::salt() : '';
            A::app()->getRequest()->setPost('salt', $this->_view->salt);
        }

        if(!empty($alert)){
            $this->_view->actionMessage = CWidget::create('CMessage', array($alertType, $alert, array('button'=>true)));
        }

        $this->_view->page = !empty(A::app()->getRequest()->get('page')) ? A::app()->getRequest()->get('page') : 1;

        $this->_view->render('doctors/edit');
    }

    /**
     * Delete action handler
     * @param int $id
     * @return void
     */
    public function deleteAction($id = 0)
    {
        // Set backend mode
        Website::setBackend();
        Website::prepareBackendAction('delete', 'doctor', 'doctors/manage');
        $doctor = $this->_checkDoctorAccess($id);
        $this->_prepareAccountFields();

        $alert = '';
        $alertType = '';

        $page = !empty(A::app()->getRequest()->get('page')) ? A::app()->getRequest()->get('page') : 1;

        if($doctor->delete()){
            $alert = A::t('appointments', 'Doctors deleted successfully');
            $alertType = 'success';
        }else{
            if(APPHP_MODE == 'demo'){
                $alert = CDatabase::init()->getErrorMessage();
                $alertType = 'warning';
            }else{
                $alert = A::t('appointments', 'Doctors deleting error');
                $alertType = 'error';
            }
        }
        
        if(!empty($alert)){
            A::app()->getSession()->setFlash('alert', $alert);
            A::app()->getSession()->setFlash('alertType', $alertType);            
        }

        $this->redirect('doctors/manage'.(!empty($page) ? '?page='.(int)$page : 1));
    }

    /**
     * Customer login action handler
     * @param string $type
     * @return void
     */
    public function loginAction($type = '')
    {
        // Redirect logged in doctors
        CAuth::handleLoggedIn('doctors/dashboard', 'doctor');

        // Check if login action is allowed
        if(!ModulesSettings::model()->param('appointments', 'doctor_allow_login')){
            $this->redirect(Website::getDefaultPage());
        }

        // Social login
        if(!empty($type)){
            if(APPHP_MODE == 'demo'){
                A::app()->getSession()->setFlash(
                    'msgLoggedOut',
                    CWidget::create('CMessage', array('warning', A::t('core', 'This operation is blocked in Demo Mode!')))
                );
                $this->redirect('doctors/login');
            }

            $config = array();
			$config['returnUrl'] = 'doctors/login';
			$config['model'] = 'Modules\Appointments\Models\Doctors';

            SocialLogin::config($config);
            SocialLogin::login(strtolower($type));
        }

        // Set frontend mode
        Website::setFrontend();

        $this->_view->allowRememberMe = ModulesSettings::model()->param('appointments', 'doctor_allow_remember_me');
        $this->_view->allowRegistration = ModulesSettings::model()->param('appointments', 'doctor_allow_registration');
        $this->_view->allowResetPassword = ModulesSettings::model()->param('appointments', 'doctor_allow_restore_password');

        //#000
        $this->_checkBruteforce = CConfig::get('validation.bruteforce.enable');
        $this->_redirectDelay = (int)CConfig::get('validation.bruteforce.redirectDelay', 3);
        $this->_badLogins = (int)CConfig::get('validation.bruteforce.badLogins', 5);
        $alert = '';
        $alertType = '';
        $errors = array();
        $cRequest = A::app()->getRequest();

        $doctor = new Accounts();

        // Check if access is blocked to this IP address
        $ipBanned = Website::checkBan('ip_address', $cRequest->getUserHostAddress(), $errors);
        if($ipBanned){
            // Do nothing
            $this->_view->actionMessage = CWidget::create('CMessage', array($errors['alertType'], $errors['alert']));
        }else{
            // -------------------------------------------------
            // Perform auto-login "remember me"
            // --------------------------------------------------
            if(!CAuth::isLoggedIn()){
                if($this->_view->allowRememberMe){
					parse_str(A::app()->getCookie()->get('doctorAuth'), $output);
					if(!empty($output['usr']) && !empty($output['hash'])){
						$username = CHash::decrypt($output['usr'], CConfig::get('password.hashKey'));
						$password = $output['hash'];

                        // Check if access is blocked to this username
                        $usernameBanned = Website::checkBan('username', $username);
                        if($usernameBanned){
                            // do nothing
                        }elseif($doctor->login($username, $password, 'doctor', true, true)){
                            // Check Membership plan
                            $doctorAccount = Doctors::model()->find('account_id = :account_id', array(':account_id' => (int)$doctor->id));
                            if($doctorAccount){
                                A::app()->getSession()->set('loggedRoleId', $doctorAccount->id);
                                DoctorsComponent::checkMembershipPlan($doctorAccount->membership_plan_id, $doctorAccount->membership_expires);
                                $this->redirect('doctors/dashboard');
                            }
                        }
                    }
                }
            }

            $this->_view->username = $cRequest->getPost('login_username');
            $this->_view->password = $cRequest->getPost('login_password');
            $this->_view->remember = $cRequest->getPost('remember');
            $alert = A::t('appointments', 'Doctor Login Message');
            $alertType = '';

            // -------------------------------------------------
            // Handle form submission
            // --------------------------------------------------
            if($cRequest->getPost('act') == 'send'){
                // Perform login form validation
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
                        // Do nothing
                        $alert = $errors['alert'];
                        $alertType = $errors['alertType'];
                    }else{                        
                        if($doctor->login($this->_view->username, $this->_view->password, 'doctor', false, ($this->_view->allowRememberMe && $this->_view->remember))){
                            if($this->_view->allowRememberMe && $this->_view->remember){
                                // Username may be decoded
                                $usernameHash = CHash::encrypt($this->_view->username, CConfig::get('password.hashKey'));
                                // Password cannot be decoded, so we save ID + username + salt + HTTP_USER_AGENT
                                $httpUserAgent = A::app()->getRequest()->getUserAgent();
                                $passwordHash = CHash::create(CConfig::get('password.encryptAlgorithm'), $doctor->id.$doctor->username.$doctor->getPasswordSalt().$httpUserAgent);
                                A::app()->getCookie()->set('doctorAuth', 'usr='.$usernameHash.'&hash='.$passwordHash, (time() + 3600 * 24 * 14));
                            }
                            //#001 clean login attempts counter
                            if($this->_checkBruteforce){
                                A::app()->getSession()->remove('doctorLoginAttempts');
                                A::app()->getCookie()->remove('doctorLoginAttemptsAuth');
                            }

                            // Save doctor role ID And Check Membership plan
                            $doctorAccount = Doctors::model()->find('account_id = :account_id', array(':account_id' => (int)$doctor->id));
                            if($doctorAccount){
                                A::app()->getSession()->set('loggedRoleId', $doctorAccount->id);
								DoctorsComponent::checkMembershipPlan($doctorAccount->membership_plan_id, $doctorAccount->membership_expires);
                            }

                            $lastVisitedPage = Website::getLastVisitedPage();
                            if(!empty($lastVisitedPage) && !preg_match('/(login|registration|Home\/index|index\/index)/i', $lastVisitedPage)){
                                $this->redirect($lastVisitedPage, true);
                            }
                            $this->redirect('doctors/dashboard');
                        }else{
                            $alert = $doctor->getErrorDescription();
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
                        $logAttempts = A::app()->getSession()->get('doctorLoginAttempts', 1);
                        if($logAttempts < $this->_badLogins){
                            A::app()->getSession()->set('doctorLoginAttempts', $logAttempts+1);
                        }else{
                            A::app()->getCookie()->set('doctorLoginAttemptsAuth', md5(uniqid()));
                            sleep($this->_redirectDelay);
                            $this->redirect('doctors/login');
                        }
                    }
                }
            }else{
                //#003 validate login attempts coockie
                if($this->_checkBruteforce){
                    $logAttempts = A::app()->getSession()->get('doctorLoginAttempts', 0);
                    $logAttemptsAuthCookie = A::app()->getCookie()->get('doctorLoginAttemptsAuth');
                    $logAttemptsAuthPost = $cRequest->getPost('doctorLoginAttemptsAuth');
                    if($logAttempts >= $this->_badLogins){
                        if($logAttemptsAuthCookie != '' && $logAttemptsAuthCookie == $logAttemptsAuthPost){
                            A::app()->getSession()->remove('doctorLoginAttempts');
                            A::app()->getCookie()->remove('doctorLoginAttemptsAuth');
                            $this->redirect('doctors/login');
                        }
                    }elseif($logAttempts == 0 && !empty($logAttemptsAuthPost)){
                        // If the lifetime of the session ended, and confirm the button has not been pressed
                        A::app()->getCookie()->remove('doctorLoginAttemptsAuth');
                        $this->redirect('doctors/login');
                    }
                }
                $this->_view->actionMessage = CWidget::create('CMessage', array('info', $alert));
            }
        }

        $this->_view->buttons = SocialLogin::drawButtons(array(
            'facebook'=>'doctors/login/type/facebook',
            'twitter'=>'doctors/login/type/twitter',
            'google'=>'doctors/login/type/google')
        );

		// Logged out messages
		if(A::app()->getSession()->hasFlash('msgLoggedOut')){
			$this->_view->actionMessage = A::app()->getSession()->getFlash('msgLoggedOut');	
		}

        $this->_view->render('doctors/login');
    }

    /**
     * Doctor logout action handler
     * @return void
     */
    public function logoutAction()
    {
        if(CAuth::isLoggedInAs('doctor')){
            $this->_logout();
            $this->_cSession->startSession();
            $this->_cSession->setFlash('msgLoggedOut', CWidget::create('CMessage', array('info', A::t('appointments', 'You have been successfully logged out. We hope to see you again soon.'))));
        }
		
        $this->redirect('doctors/login');
    }

    /**
     * Doctor registration action handler
     * @return void
     */
    public function registrationAction()
    {
        // Redirect logged in doctors
        CAuth::handleLoggedIn('doctors/dashboard', 'doctor');

        // Check if action allowed
        if(!ModulesSettings::model()->param('appointments', 'doctor_allow_registration')){
            $this->redirect(Website::getDefaultPage());
        }

        // Set frontend mode
        Website::setFrontend();

        $this->_prepareAccountFields();
        $cRequest = A::app()->getRequest();
        $approvalType = ModulesSettings::model()->param('appointments', 'doctor_approval_type');
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
                    $fields['title_id'] = array('title'=>A::t('appointments', 'Title'), 'validation'=>array('required'=>true, 'type'=>'set', 'source'=>array_keys($this->_view->titles)));
                    $fields['first_name'] = array('title'=>A::t('appointments', 'First Name'), 'validation'=>array('required'=>true, 'type'=>'text', 'maxLength'=>32));
                    //$fields['middle_name'] = array('title'=>A::t('appointments', 'Middle Name'), 'validation'=>array('required'=>false, 'type'=>'text', 'maxLength'=>32));
                    $fields['last_name'] = array('title'=>A::t('appointments', 'Last Name'), 'validation'=>array('required'=>true, 'type'=>'text', 'maxLength'=>32));
                    $fields['gender'] = array('title'=>A::t('appointments', 'Gender'), 'validation'=>array('required'=>true, 'type'=>'set', 'source'=>array_keys($this->_view->genders)));
                    //$fields['birth_date'] = array('title'=>A::t('appointments', 'Birth Date'), 'validation'=>array('required'=>false, 'type'=>'date', 'maxLength'=>10, 'minValue'=>'1900-00-00', 'maxValue'=>date('Y-m-d')));
                    //$fields['work_phone'] = array('title'=>A::t('appointments', 'Work Phone'), 'validation'=>array('required'=>false, 'type'=>'phoneString', 'maxLength'=>32));
                    //$fields['work_mobile_phone'] = array('title'=>A::t('appointments', 'Work Mobile Phone'), 'validation'=>array('required'=>false, 'type'=>'phoneString', 'maxLength'=>32));
                    //$fields['phone'] = array('title'=>A::t('appointments', 'Phone'), 'validation'=>array('required'=>false, 'type'=>'phoneString', 'maxLength'=>32));
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
                    //$fields['degree_id'] = array('title'=>A::t('appointments', 'Degree'), 'validation'=>array('required'=>false, 'type'=>'set', 'source'=>array_keys($this->_view->degrees)));
                    //$fields['additional_degree'] = array('title'=>A::t('appointments', 'Additional Degree'), 'validation'=>array('required'=>false, 'type'=>'text', 'maxLength'=>50));
                    //$fields['license_number'] = array('title'=>A::t('appointments', 'License Number'), 'validation'=>array('required'=>false, 'type'=>'type', 'maxLength'=>30));
                    //$fields['education'] = array('title'=>A::t('appointments', 'Education'), 'validation'=>array('required'=>false, 'type'=>'type', 'maxLength'=>'255'));
                    //$fields['experience'] = array('title'=>A::t('appointments', 'Experience'), 'validation'=>array('required'=>true, 'type'=>'set', 'source'=>array_keys($this->_view->genders)));
                    //$fields['residency_training'] = array('title'=>A::t('appointments', 'Residency Training'), 'validation'=>array('required'=>false, 'type'=>'text', 'maxLength'=>255));
                    //$fields['hospital_affiliations'] = array('title'=>A::t('appointments', 'Hospital Affiliations'), 'validation'=>array('required'=>false, 'type'=>'text', 'maxLength'=>255));
                    //$fields['board_certifications'] = array('title'=>A::t('appointments', 'Board Certifications'), 'validation'=>array('required'=>false, 'type'=>'text', 'maxLength'=>255));
                    //$fields['awards_and_publications'] = array('title'=>A::t('appointments', 'Awards and Publications'), 'validation'=>array('required'=>false, 'type'=>'text', 'maxLength'=>255));
                    //$fields['languages_spoken'] = array('title'=>A::t('appointments', 'Languages Spoken'), 'validation'=>array('required'=>false, 'type'=>'text', 'maxLength'=>125));
                    //$fields['insurances_accepted'] = array('title'=>A::t('appointments', 'Insurances Accepted'), 'validation'=>array('required'=>false, 'type'=>'text', 'minLength'=>6, 'maxLength'=>255));
                    $fields['notifications'] = array('title'=>A::t('appointments', 'Notifications'), 'validation'=>array('required'=>false, 'type'=>'set', 'source'=>array('1')));
                    $fields['i_agree'] = array('title'=>A::t('appointments', 'Terms & Conditions'), 'validation'=>array('required'=>true, 'type'=>'set', 'source'=>array('1')));
                    $captcha = $cRequest->getPost('captcha');

                    $result = CWidget::create('CFormValidation', array(
                        'fields' => $fields
                    ));
                    if($result['error']){
                        $arr[] = '"status": "0"';
                        $arr[] = '"error": "'.$result['errorMessage'].'"';
                    }elseif($this->_view->verificationCaptcha && $captcha === ''){
                        $arr[] = '"status": "0"';
                        $arr[] = '"error_field": "captcha_validation"';
                        $arr[] = '"error": "'.A::t('appointments', 'The field captcha cannot be empty!').'"';
                    }elseif($this->_view->verificationCaptcha && $captcha != A::app()->getSession()->get('captchaResult')){
                        $arr[] = '"status": "0"';
                        $arr[] = '"error_field": "captcha_validation"';
                        $arr[] = '"error": "'.A::t('appointments', 'Sorry, the code you have entered is invalid! Please try again.').'"';
                    // these operations is done in model class
                    //}elseif(Accounts::model()->count('role = :role AND email = :email', array(':role'=>'doctor', ':email'=>$cRequest->getPost('email')))){
                    //    $arr[] = '"status": "0"';
                    //    $arr[] = '"error_field": "email"';
                    //    $arr[] = '"error": "'.A::t('appointments', 'Customer with such email already exists!').'"';
                    //}elseif(Accounts::model()->count('role = :role AND username = :username', array(':role'=>'doctor', ':username'=>$cRequest->getPost('username')))){
                    //    $arr[] = '"status": "0"';
                    //    $arr[] = '"error_field": "username"';
                    //    $arr[] = '"error": "'.A::t('appointments', 'Customer with such username already exists!').'"';
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

                            $doctor = new Doctors();
                            $doctor->title_id                = $cRequest->getPost('title_id');
                            $doctor->doctor_first_name       = $cRequest->getPost('first_name');
                            //$doctor->middle_name             = $cRequest->getPost('middle_name');
                            $doctor->doctor_last_name        = $cRequest->getPost('last_name');
                            $doctor->gender                  = $cRequest->getPost('gender');
                            //$doctor->birth_date              = $cRequest->getPost('birth_date');
                            //$doctor->work_phone              = $cRequest->getPost('work_phone');
                            //$doctor->work_mobile_phone       = $cRequest->getPost('work_mobile_phone');
                            //$doctor->phone                   = $cRequest->getPost('phone');
                            //$doctor->fax                     = $cRequest->getPost('fax');
                            //$doctor->address                 = $cRequest->getPost('address');
                            //$doctor->address_2               = $cRequest->getPost('address_2');
                            //$doctor->city                    = $cRequest->getPost('city');
                            //$doctor->zip_code                = $cRequest->getPost('zip_code');
                            //$doctor->country_code            = $cRequest->getPost('country_code');
                            //$doctor->state                   = $cRequest->getPost('state');
                            //$doctor->degree_id               = $cRequest->getPost('degree_id');
                            //$doctor->additional_degree       = $cRequest->getPost('additional_degree');
                            //$doctor->license_number          = $cRequest->getPost('license_number');
                            //$doctor->education               = $cRequest->getPost('education');
                            //$doctor->experience              = $cRequest->getPost('experience');
                            //$doctor->residency_training      = $cRequest->getPost('residency_training');
                            //$doctor->hospital_affiliations   = $cRequest->getPost('hospital_affiliations');
                            //$doctor->board_certifications    = $cRequest->getPost('board_certifications');
                            //$doctor->awards_and_publications = $cRequest->getPost('awards_and_publications');
                            //$doctor->languages_spoken        = $cRequest->getPost('languages_spoken');
                            //$doctor->insurances_accepted     = $cRequest->getPost('insurances_accepted');

                            $accountCreated = false;
                            if($doctor->save()){
                                $doctor = $doctor->refresh();

                                // update accounts table
                                $account = Accounts::model()->findByPk((int)$doctor->account_id);
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
                                    $accountCreated = true;
                                }
                            }

                            if(!$accountCreated){
                                $arr[] = '"status": "0"';
                                if(APPHP_MODE == 'demo'){
                                    $arr[] = '"error": "'.A::t('appointments', 'This operation is blocked in Demo Mode!').'"';
                                }else{
                                    $arr[] = '"error": "'.(($doctor->getError() != '') ? $doctor->getError() : A::t('appointments', 'An error occurred while creating doctor account! Please try again later.')).'"';
                                    $arr[] = '"error_field": "'.$doctor->getErrorField().'"';
                                }
                            }else{
                                $firstName = $doctor->doctor_first_name;
                                $lastName = $doctor->doctor_last_name;
                                $doctorEmail = $cRequest->getPost('email');
                                $emailResult = true;

                                // Send notification to admin about new registration
                                if(ModulesSettings::model()->param('appointments', 'doctor_new_registration_alert')){
                                    $adminLang = '';
                                    if($defaultLang = Languages::model()->find('is_default = 1')){
                                        $adminLang = $defaultLang->code;
                                    }
                                    $emailResult = Website::sendEmailByTemplate(
                                        $this->_settings->general_email,
                                        'doctors_account_created_notify_admin',
                                        $adminLang,
                                        array('{FIRST_NAME}' => $firstName, '{LAST_NAME}' => $lastName, '{CUSTOMER_EMAIL}' => $doctorEmail, '{USERNAME}' => $username)
                                    );
                                }

                                // Send email to doctor according to approval type
                                if(!empty($doctorEmail)){
                                    if($approvalType == 'by_admin'){
                                        // approval by admin
                                        $emailResult = Website::sendEmailByTemplate(
                                            $doctorEmail,
                                            'doctors_account_created_admin_approval',
                                            A::app()->getLanguage(),
                                            array('{FIRST_NAME}' => $firstName, '{LAST_NAME}' => $lastName, '{USERNAME}' => $username, '{PASSWORD}' => $password)
                                        );
                                    }elseif($approvalType == 'by_email'){
                                        // confirmation by email
                                        $emailResult = Website::sendEmailByTemplate(
                                            $doctorEmail,
                                            'doctors_account_created_email_confirmation',
                                            A::app()->getLanguage(),
                                            array('{FIRST_NAME}' => $firstName, '{LAST_NAME}' => $lastName, '{USERNAME}' => $username, '{PASSWORD}' => $password, '{REGISTRATION_CODE}' => $account->registration_code)
                                        );
                                    }else{
                                        // auto approval
                                        $emailResult = Website::sendEmailByTemplate(
                                            $doctorEmail,
                                            'doctors_account_created_auto_approval',
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
                $messageInfo    = A::t('appointments', 'Admin approve registration? Click <a href="{url}">here</a> to proceed.', array('{url}'=>'doctors/login'));
            }elseif($approvalType == 'by_email'){
                $messageSuccess = A::t('appointments', 'Account successfully created! Email confirmation required.');
                $messageInfo    = A::t('appointments', 'Already confirmed your registration? Click <a href="{url}">here</a> to proceed.', array('{url}'=>'doctors/login'));
            }else{
                $messageSuccess = A::t('appointments', 'Account successfully created!');
                $messageInfo    = A::t('appointments', 'Click <a href="{url}">here</a> to proceed.', array('{url}'=>'doctors/login'));
            }
            $this->_view->messageSuccess = $messageSuccess;
            $this->_view->messageInfo    = $messageInfo;
        }

        $this->_view->showOnlyText = true;

        $this->_view->setLayout('no_columns');
        $this->_view->textTermsConditions = $this->_view->render('doctors/termsconditions', true, true);

        $this->_view->setLayout('default');
        $this->_view->render('doctors/registration');
    }

    /**
     * Customer restore password action handler
     * @return void
     */
    public function restorePasswordAction()
    {
        // Redirect logged in doctors
        CAuth::handleLoggedIn('doctors/dashboard', 'doctor');

        // Check if action allowed
        if(!ModulesSettings::model()->param('appointments', 'doctor_allow_restore_password')){
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
                        $account = Accounts::model()->find('role = :role AND email = :email', array(':role'=>'doctor', ':email'=>$email));
                        if(empty($account)){
                            $alertType = 'error';
                            $alert = A::t('appointments', 'Sorry, but we were not able to find a doctor with that login information!');
                        }else{
                            $username = $account->username;
                            $preferedLang = $account->language_code;
                            // generate new password
                            if(CConfig::get('password.encryption')){
                                $password = CHash::getRandomString(8);
                                $account->password = CHash::create(CConfig::get('password.encryptAlgorithm'), $password, $account->salt);
                                if(!$account->save()){
                                    $alertType = 'error';
                                    $alert = A::t('appointments', 'An error occurred while password recovery process! Please try again later.');
                                }
                            }else{
                                $password = $account->password;
                            }

                            if(!$alert){
                                $result = Website::sendEmailByTemplate(
                                    $email,
                                    'doctors_password_forgotten',
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

        $this->_view->render('doctors/restorePassword');
    }

    /**
     * Customer confirm registration action handler
     * @param string $code
     * @return void
     */
    public function confirmRegistrationAction($code)
    {
        // redirect logged in directory
        CAuth::handleLoggedIn('doctors/dashboard', 'doctor');

        // set frontend mode
        Website::setFrontend();

        if($doctor = Accounts::model()->find('is_active = 0 AND registration_code = :code', array(':code'=>$code))){
            $doctor->is_active = 1;
            $doctor->registration_code = '';
            if($doctor->save()){
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
        $this->_view->render('doctors/confirmRegistration');
    }

    /**
     * Dashboard action handler
     * @return void
     */
    public function dashboardAction()
    {
        // block access to this controller for not-logged doctors
        CAuth::handleLogin('doctors/login', 'doctor');
        // set meta tags according to active language
        Website::setMetaTags(array('title'=>A::t('appointments', 'Dashboard')));
        // set frontend settings
        Website::setFrontend();

		$alert = A::app()->getSession()->getFlash('alert');
		$alertType = A::app()->getSession()->getFlash('alertType');
        $membershipPlan = '';

        $membershipPlanId = A::app()->getSession()->get('membershipPlanId');
		$expiredMembership = A::app()->getSession()->get('expiredMembership');
        $reminderExpiredMembership = A::app()->getSession()->get('reminderExpiredMembership');
		if(empty($membershipPlanId)){
			$alert = A::t('appointments', 'No membership pelected was not selected. Please select the appropriate membership plan.');
			$alertType = 'error';
		}elseif(!empty($expiredMembership)){
			$alert = A::t('appointments', 'Your membership plan has expired {param}. Please renew membership or select a new membership plan.', array('{param}'=>$expiredMembership));
			$alertType = 'error';
		}elseif(!empty($reminderExpiredMembership)){
			$alert =  A::t('appointments', 'Your membership plan will expire in {param} days.', array('{param}'=>$reminderExpiredMembership));
			$alertType = 'warning';
		}

		if(!empty($membershipPlanId)){
            $membershipPlan = $this->_checkMembershipPlanAccess($membershipPlanId);
			$this->_view->showInReview = $membershipPlan->enable_reviews;
        }

		if(!empty($alert)){
			$this->_view->actionMessage = CWidget::create('CMessage', array($alertType, $alert, array('button'=>true)));
		}
        
		$this->_view->doctor = $this->_checkAccountsAccess(A::app()->getSession()->get('loggedId'));
		$this->_view->membershipPlanId = $membershipPlanId;
		$this->_view->expiredMembership = $expiredMembership;

        $this->_view->render('doctors/dashboard');
    }

    /**
     * Info Account action handler
     * @return void
     */
    public function myAccountAction()
    {
        // block access to this controller for not-logged doctors
        CAuth::handleLogin('doctors/login', 'doctor');
        // set meta tags according to active language
        Website::setMetaTags(array('title'=>A::t('appointments', 'My Account')));
        // set frontend settings
        Website::setFrontend();

        $alert = A::app()->getSession()->getFlash('alert');
        $alertType = A::app()->getSession()->getFlash('alertType');
        $this->_view->actionMessage = !empty($alertType) ? CWidget::create('CMessage', array($alertType, $alert)) : '';

        $doctor = $this->_checkAccountsAccess(A::app()->getSession()->get('loggedId'));
        $this->_prepareAccountFields();
        // fetch datetime format from settings table
        $dateTimeFormat = Bootstrap::init()->getSettings('datetime_format');
        $dateFormat = Bootstrap::init()->getSettings('date_format');

        $this->_view->doctor = $doctor;
        // prepare some fields
        if($doctor->doctor_first_name || $doctor->doctor_last_name){
            $this->_view->fullName = (!empty($this->_view->titles[$doctor->title_id]) ? $this->_view->titles[$doctor->title_id].' ' : '').$doctor->doctor_first_name.($doctor->doctor_middle_name != '' ? ' ('.$doctor->doctor_middle_name.')' : '').($doctor->doctor_last_name != '' ? ' '.$doctor->doctor_last_name : '');
        }else{
            $this->_view->fullName = '';
        }
        $this->_view->countryName = $doctor->country_code;
        $this->_view->stateName = $doctor->state;
        $this->_view->langName = $doctor->language_code;
        $this->_view->notifications = ($doctor->notifications) ? A::t('appointments', 'Yes') : A::t('appointments', 'No');
        $this->_view->birthDate = ($doctor->birth_date && ! CTime::isEmptyDate($doctor->birth_date)) ? date($dateFormat, strtotime($doctor->birth_date)) : '';
        $this->_view->createdAt = ($doctor->created_at && ! CTime::isEmptyDateTime($doctor->created_at)) ? date($dateTimeFormat, strtotime($doctor->created_at)) : '- '.A::t('appointments', 'Unknown').' -';
        $this->_view->lastVisitedAt = ($doctor->last_visited_at && ! CTime::isEmptyDateTime($doctor->last_visited_at)) ? date($dateTimeFormat, strtotime($doctor->last_visited_at)) : '- '.A::t('appointments', 'Unknown').' -';
        $this->_view->degree = (isset($this->_view->degrees[$doctor->medical_degree_id]) ? $this->_view->degrees[$doctor->medical_degree_id] : '- '.A::t('appointments', 'Unknown').' -');
        if(isset($this->_view->experienceYears[$doctor->experience_years])){
            $experience = $this->_view->experienceYears[$doctor->experience_years];
            $this->_view->experience = $experience.' '.($experience > 1 ? A::t('appointments', 'Years') : A::t('appointments', 'Year'));
        }else{
            $this->_view->experience = '- '.A::t('appointments', 'Unknown').' -';
        }
        $languagesSpoken = '';
        $arrLanguagesSpoken = explode(';', $doctor->languages_spoken);
        foreach($arrLanguagesSpoken as $key => $languageSpoken){
            $languagesSpoken .= $this->_view->localesList[$languageSpoken];
            if(isset($arrLanguagesSpoken[$key + 1])){
                $languagesSpoken .= ', ';
            }
        }
        $this->_view->languagesSpoken = $languagesSpoken;

        $insurancesList = $this->_insurancesList();
        $insurancesAccepted = '';
        $arrInsurancesAccepted = explode(';', $doctor->insurances_accepted);
        foreach($arrInsurancesAccepted as $key => $insuranceAccepted){
            $insurancesAccepted .= $insurancesList[CHtml::encode($insuranceAccepted)];
            if(isset($arrInsurancesAccepted[$key + 1])){
                $insurancesAccepted .= ', ';
            }
        }

        $this->_view->insurancesAccepted = $insurancesAccepted;

        if($country = Countries::model()->find('code = :code', array(':code'=>$doctor->country_code))){
            $this->_view->countryName = $country->country_name;
        }
        if($state = States::model()->find('country_code = :country_code AND code = :code', array(':country_code'=>$doctor->country_code, ':code'=>$doctor->state))){
            $this->_view->stateName = $state->state_name;
        }
        if($language = Languages::model()->find('code = :code', array(':code'=>$doctor->language_code))){
            $this->_view->langName = $language->name;
        }

        $this->_view->render('doctors/myAccount');
    }

    /**
     * Customer edit Account action handler
     * @param string $delete
     * @return void
     */
    public function editAccountAction($delete = '')
    {
        // block access to this controller for not-logged doctors
        CAuth::handleLogin('doctors/login', 'doctor');
        // set meta tags according to active language
        Website::setMetaTags(array('title'=>A::t('appointments', 'Edit Account')));
        // set frontend settings
        Website::setFrontend();

        $states    = array();
        $alert     = A::app()->getSession()->getFlash('alert');
        $alertType = A::app()->getSession()->getFlash('alertType');

        $doctor = $this->_checkAccountsAccess(A::app()->getSession()->get('loggedId'));
        $this->_prepareAccountFields();

        $cRequest = A::app()->getRequest();
        if($cRequest->isPostRequest()){
            $countryCode = $cRequest->getPost('country_code');
            $stateCode = $cRequest->getPost('state');
        }else{
            $countryCode = $doctor->country_code;
            $stateCode = $doctor->state;
        }

        if($delete == 'avatar'){
            $account = Accounts::model()->findByPk($doctor->account_id);
            if(!empty($account) && $account->avatar != ''){
                $deleteImage = 'assets/modules/appointments/images/doctors/'.$account->avatar;
                $account->avatar = '';
                if($account->save()){
                    if(CFile::deleteFile($deleteImage)){
                        $alert = A::t('appointments', 'Image successfully deleted');
                        $alertType = 'success';
                    }else{
                        $alert = A::t('appointments', 'Image Delete Warning');
                        $alertType = 'warning';
                    }
                }else{
                    $alert = A::t('appointments', 'Image Delete Warning');
                    $alertType = 'error';
                }
            }
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

        $this->_view->actionMessage = CWidget::create('CMessage', array($alertType, $alert));
        $this->_view->id = $doctor->id;
        $this->_view->countryCode = $countryCode;
        $this->_view->stateCode = $stateCode;
        $this->_view->states = $states;
        $this->_view->render('doctors/editAccount');
    }

    /**
     * Customer remove account action handler
     * @return void
     */
    public function removeAccountAction()
    {
        // block access to this controller for not-logged doctors
        CAuth::handleLogin('doctors/login', 'doctor');
        // set meta tags according to active language
        Website::setMetaTags(array('title'=>A::t('appointments', 'Remove Account')));
        // set frontend settings
        Website::setFrontend();

        $alert = A::app()->getSession()->getFlash('alert');
        $alertType = A::app()->getSession()->getFlash('alertType');
        $this->_view->actionMessage = CWidget::create('CMessage', array($alertType, $alert));

        $loggedId = A::app()->getSession()->get('loggedId');
        $doctor = $this->_checkAccountsAccess($loggedId);
        $alertType = '';
        $alert = '';
        $this->_view->accountRemoved = false;

        $cRequest = A::app()->getRequest();
        if($cRequest->isPostRequest()){
            if($cRequest->getPost('act') != 'send'){
                $this->redirect('doctors/myAccount');
            }elseif(APPHP_MODE == 'demo'){
                $alertType = 'warning';
                $alert = A::t('appointments', 'This operation is blocked in Demo Mode!');
            }else{
                // add removing account here
                $removalType = ModulesSettings::model()->param('appointments', 'doctor_removal_type');
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
                        'doctors_account_removed_by_doctor',
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
        
        $this->_view->render('doctors/removeAccount');
    }

    /**
     * Show page Terms & Conditions
     * @return void
     */
    public function termsConditionsAction()
    {
        // Set frontend settings
        Website::setFrontend();

        $this->_view->showOnlyText = false;
        $this->_view->render('doctors/termsConditions');
    }
    
    /**
     * Show page Our Staff
     * @param int $top
     * @return void
     */
    public function ourStaffAction($top = 0)
    {
        // Set frontend settings
        Website::setFrontend();
        
        $top = in_array($top, array('10', '20')) ? $top : 10;

		$doctorTableName = CConfig::get('db.prefix').Doctors::model()->getTableName();
		$accountTableName = CConfig::get('db.prefix').Accounts::model()->getTableName();
		$doctors = Doctors::model('with_appointments_counter')->findAll( $doctorTableName.'.membership_show_in_search = 1 AND '.$doctorTableName.".membership_expires >= '".LocalTime::currentDateTime('Y-m-d')."'".' AND '.$accountTableName.'.is_active = 1 AND '.$accountTableName.'.is_removed = 0', array('order'=>'appointments_count DESC', 'limit'=>'0, '.$top));
        $this->_view->doctors = $doctors;
        $specialties = DoctorSpecialties::model()->findAll();
        $this->_view->doctorSpecialties = CArray::flipByField($specialties, 'doctor_id', true);
        $this->_view->degrees            = $this->_getDegrees();
        $this->_view->render('doctors/ourStaff');
    }
    
    /**
     * Show page of doctor profile
     * @param int $doctorId
     * @return void
     */
    public function profileAction($doctorId = 0)
    {
        // Set frontend settings
        Website::setFrontend();

		$showFields = false;
        $doctorClinics = array();

        $profileDoctor = $this->_checkDoctorAccess($doctorId, true);
		$fullname = Doctors::model()->getFullName();
		$specialty = DoctorSpecialties::model()->findAll(array('condition' => 'doctor_id = '.$doctorId, 'orderBy' => 'sort_order ASC', 'limit' => $profileDoctor->membership_specialties_count));
        $doctorImages = DoctorImages::model()->findAll(array('condition' => 'doctor_id = '.$doctorId.' && is_active = true', 'orderBy' => 'sort_order ASC'));

		$showFieldsForUnregisteredUsers = ModulesSettings::model()->param('appointments', 'show_fields_for_unregistered_users');
		$loggedId = CAuth::getLoggedId();
		if(!empty($loggedId)){
			$showFields = true;
		}elseif($showFieldsForUnregisteredUsers){
			$showFields = true;
		}

        //Search clinics in which the doctor takes
        $timeBlockIds = DoctorScheduleTimeBlocks::model()->count(
            array(
                'condition'=>'doctor_id = '.$profileDoctor->id,
                'select'=>'address_id',
                'groupBy'=>'address_id',
                'allRows'=>true
            )
        );
        if(!empty($timeBlockIds)){
            foreach($timeBlockIds as $timeBlockId){
                $clinic = Clinics::model()->findByPk($timeBlockId['address_id']);
                if(!empty($clinic)){
                    if(isset($doctorClinics[$clinic->id]['clinic_name'])){
                        continue;
                    }
                    $doctorClinics[$clinic->id]['clinic_name'] = $clinic->clinic_name;
                    $doctorClinics[$clinic->id]['address'] = $clinic->address;
                }
            }
        }
        $countClinic = count($doctorClinics);

        $countDoctorImages = count($doctorImages);
        if($countDoctorImages > $profileDoctor->membership_images_count){
            $countDoctorImages = $profileDoctor->membership_images_count;
        }

        $this->_view->doctorClinics 	= $doctorClinics;
        $this->_view->countClinic 		= $countClinic;
        $this->_view->openHours 		= DoctorScheduleTimeBlocks::getOpenHoursDoctors($doctorId);
        $this->_view->specialty 		= $specialty;
        $this->_view->doctorImages  	= $doctorImages;
        $this->_view->countDoctorImages = $countDoctorImages;
        $this->_view->profileDoctor 	= $profileDoctor;
        $this->_view->fullname	 		= $fullname;
        $this->_view->showFields 		= $showFields;
        $this->_view->showRating 	 	= ModulesSettings::model()->param('appointments', 'show_rating');
        $this->_view->showRating 	 	= ModulesSettings::model()->param('appointments', 'show_rating');
        $this->_view->showRatingForm 	= ModulesSettings::model()->param('appointments', 'show_rating_form');
        $this->_view->reviewModeration 	= ModulesSettings::model()->param('appointments', 'review_moderation');

        $this->_view->render('doctors/profile');
    }

	/**
	 * Show page Appointments for doctor
     * @param string $status
	 * @return void
	 */
	public function appointmentsAction($status = 'future')
	{
		// block access to this controller for not-logged doctors
		CAuth::handleLogin('doctors/login', 'doctor');
		// set meta tags according to active language
		Website::setMetaTags(array('title'=>A::t('appointments', 'Appointments')));
		// set frontend settings
		Website::setFrontend();

		$alert = A::app()->getSession()->getFlash('alert');
		$alertType = A::app()->getSession()->getFlash('alertType');
        $condition = '';

		if(!empty($alert)){
			$this->_view->actionMessage = CWidget::create(
				'CMessage', array($alertType, $alert, array('button'=>true))
			);
		}

		$doctorId = CAuth::getLoggedRoleId();
		if(!empty($doctorId)){
			$this->_view->doctorId = $doctorId;
		}else{
			$this->redirect('Home/index');
		}

        $tableName = CConfig::get('db.prefix').Appointments::model()->getTableName();
        if($status == 'all'){
            $condition	= $tableName.'.doctor_id = '.$doctorId.' AND ('.$tableName.'.status = 1 OR '.$tableName.'.status = 0)';
        }elseif($status == 'future'){
            $condition	= $tableName.'.doctor_id = '.$doctorId.' AND ('.$tableName.'.status = 1 OR '.$tableName.'.status = 0) AND ('.$tableName.".appointment_date > '".LocalTime::currentDateTime('Y-m-d')."'".' OR ('.$tableName.".appointment_date = '".LocalTime::currentDateTime('Y-m-d')."'".' AND '.$tableName.".appointment_time > '".LocalTime::currentDateTime('H:i:s')."'))";
        }elseif($status == 'past'){
            $condition	= $tableName.'.doctor_id = '.$doctorId.' AND ('.$tableName.'.status = 1 OR '.$tableName.'.status = 0) AND ('.$tableName.".appointment_date < '".LocalTime::currentDateTime('Y-m-d')."'".' OR ('.$tableName.".appointment_date = '".LocalTime::currentDateTime('Y-m-d')."'".' AND '.$tableName.".appointment_time < '".LocalTime::currentDateTime('H:i:s')."'))";
        }

        $this->_view->status = $status;
        $this->_view->condition = $condition;
        $this->_view->checkAccessAccountUsingMembershipPlan = DoctorsComponent::checkAccessAccountUsingMembershipPlan(false);
        $this->_view->appointmentTimeFormat = ModulesSettings::model()->param('appointments', 'time_format_appointment_time');
        $this->_view->render('doctors/appointments');
	}

	/**
	 * Add Appointment
	 * @param int $id
	 * @param string $status
	 */
	public function addAppointmentAction()
	{
        // block access to this controller for doctors without membership plan or expired membership plan
        DoctorsComponent::checkAccessAccountUsingMembershipPlan();

        // block access to this controller for not-logged doctors
		CAuth::handleLogin('doctors/login', 'doctor');
		// set meta tags according to active language
        A::app()->getSession()->remove('changeAppointmentId');
        A::app()->getSession()->remove('changeDoctorId');

        $this->redirect('appointments/appointments/');
    }

	/**
	 * Edit page Appointment
	 * @param int $id
	 * @param string $status
	 */
	public function editAppointmentAction($id = 0, $status = 'future')
	{
        // block access to this controller for doctors without membership plan or expired membership plan
        DoctorsComponent::checkAccessAccountUsingMembershipPlan();

        // block access to this controller for not-logged doctors
		CAuth::handleLogin('doctors/login', 'doctor');
		// set meta tags according to active language
		Website::setMetaTags(array('title'=>A::t('appointments', 'Edit Appointment')));
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

        $appendCode  = '';
        $prependCode = '';
        $symbol      = A::app()->getCurrency('symbol');
        $symbolPlace = A::app()->getCurrency('symbol_place');

        if($symbolPlace == 'before'){
            $prependCode = $symbol;
        }else{
            $appendCode = $symbol;
        }

        $this->_view->pricePrependCode = $prependCode;
        $this->_view->priceAppendCode  = $appendCode;
        $this->_view->forWhom = $forWhom;
        $this->_view->visitReason = $visitReason;
		$this->_view->id = $id;
		$this->_view->status = $status;
        $this->_view->editPatientArrivalStatus = array('0'=>A::t('appointments', 'Not Arrived'), '1'=>A::t('appointments', 'Arrived'));
        $this->_view->appointmentTimeFormat = ModulesSettings::model()->param('appointments', 'time_format_appointment_time');
        $this->_view->render('doctors/editAppointment');
	}

    /**
     * Doctor cancel appointment action handler
     * @param int $id
     * @param string $status
     */
    public function cancelAppointmentAction($id = 0, $status = 'future')
    {
        // block access to this controller for not-logged doctors
        CAuth::handleLogin('doctors/login', 'doctor');
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
        $this->redirect('doctors/appointments'.(!empty($status) ? '/status/'.$status : ''));
    }

    /**
     * Patient change appointment action handler
     * @param int $id
     */
    public function changeAppointmentAction($id = 0)
    {
        // block access to this controller for not-logged doctors
        CAuth::handleLogin('doctors/login', 'doctor');
        $appointment = $this->_checkAppointmentAccess($id, false);
        $this->_checkDoctorAccess($appointment->doctor_id);

        A::app()->getSession()->set('changeAppointmentId', $id);
        A::app()->getSession()->set('changeDoctorId', $appointment->doctor_id);

        $this->redirect('appointments/appointments');
    }

    /**
     * Doctor confirm appointment action handler
     * @param int $id
     * @param string $status
     */
    public function confirmAppointmentAction($id = 0, $status = 'future')
    {
        // block access to this controller for not-logged doctors
        CAuth::handleLogin('doctors/login', 'doctor');
        $appointment = $this->_checkAppointmentAccess($id, true);

        $alert = '';
        $alertType = '';

        $appointment->status = 1;//verified
        //$appointment->status_changed = date('Y-m-d H:i:s');
        if($appointment->save()){
            $alert = A::t('appointments', 'Confirm Success Message');
            $alertType = 'success';
        }else{
            if(APPHP_MODE == 'demo'){
                $alert = CDatabase::init()->getErrorMessage();
                $alertType = 'warning';
            }else{
                $alert = A::t('app', 'Confirm Error Message');
                $alertType = 'error';
            }
        }

        if(!empty($alert)){
            A::app()->getSession()->setFlash('alert', $alert);
            A::app()->getSession()->setFlash('alertType', $alertType);
        }
        $this->redirect('doctors/appointments'.(!empty($status) ? '/status/'.$status : ''));
    }

    /**
     * Show page Patients for doctor
     * @return void
     */
    public function patientsAction()
    {
        // block access to this controller for not-logged doctors
        CAuth::handleLogin('doctors/login', 'doctor');
        // set meta tags according to active language
        Website::setMetaTags(array('title'=>A::t('appointments', 'Patients')));
        // set frontend settings
        Website::setFrontend();

        $alert = A::app()->getSession()->getFlash('alert');
        $alertType = A::app()->getSession()->getFlash('alertType');

        if(!empty($alert)){
            $this->_view->actionMessage = CWidget::create(
                'CMessage', array($alertType, $alert, array('button'=>true))
            );
        }

        $doctorId = CAuth::getLoggedRoleId();
        if(!empty($doctorId)){
            $this->_view->doctorId = $doctorId;
        }else{
            $this->redirect('Home/index');
        }

        $this->_view->render('doctors/patients');
    }

    /**
     * Add new action handler
     * @return void
     */
    public function addPatientAction()
    {
        // block access to this controller for not-logged doctors
        CAuth::handleLogin('doctors/login', 'doctor');
        // set frontend mode
        Website::setFrontend();

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

        $this->_view->render('doctors/addPatient');
    }

    /**
     * Edit patients action handler
     * @param int $id
     * @return void
     */
    public function editPatientAction($id = 0)
    {
        // block access to this controller for not-logged doctors
        CAuth::handleLogin('doctors/login', 'doctor');
        // set frontend mode
        Website::setFrontend();
        $patient = $this->_checkPatientAccess($id);
        $this->_prepareAccountFields();

        $alert = '';
        $alertType = '';

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

        if(!empty($alert)){
            $this->_view->actionMessage = CWidget::create('CMessage', array($alertType, $alert, array('button'=>true)));
        }

        $this->_view->render('doctors/editPatient');
    }

    /**
     * Displays a calendar with all appointments.
     */
    public function calendarAction()
    {

        // block access to this controller for not-logged doctors
        CAuth::handleLogin('doctors/login', 'doctor');
        // set meta tags according to active language
        Website::setMetaTags(array('title'=>A::t('appointments', 'Calendar')));
        // set frontend settings
        Website::setFrontend();

        $doctorId = CAuth::getLoggedRoleId();
        $doctor = $this->_checkDoctorAccess($doctorId);
        $appointments = Appointments::model()->findAll('doctor_id = '.$doctor->id);

        $drawCalendar = AppointmentsComponent::drawCalendar($doctor->id);
        $actionMessage = '';

        if (!$drawCalendar) {
            $actionMessage = CWidget::create('CMessage', array('info', A::t('app', 'No records found!'), array('button'=>false)));
        }

        $this->_view->doctorFullName = $doctor->getFullName();
        $this->_view->actionMessage = $actionMessage;
        $this->_view->page = !empty(A::app()->getRequest()->get('page')) ? A::app()->getRequest()->get('page') : 1;
        $this->_view->render('doctors/calendar');
    }

    /**
     * Delete action handler
     * @param int $id
     * @return void
     */
    // public function deletePatientAction($id = 0)
    // {
    //     // block access to this controller for not-logged doctors
    //     CAuth::handleLogin('doctors/login', 'doctor');
    //     // set frontend mode
    //     Website::setFrontend();
    //     $patient = $this->_checkPatientAccess($id);
    //     $this->_prepareAccountFields();
    //
    //     $alert = '';
    //     $alertType = '';
    //
    //     if($patient->delete()){
    //         $alert = A::t('appointments', 'Patients deleted successfully');
    //         $alertType = 'success';
    //     }else{
    //         if(APPHP_MODE == 'demo'){
    //             $alert = CDatabase::init()->getErrorMessage();
    //             $alertType = 'warning';
    //         }else{
    //             $alert = A::t('appointments', 'Patients deleting error');
    //             $alertType = 'error';
    //         }
    //     }
    //
    //     if(!empty($alert)){
    //         A::app()->getSession()->setFlash('alert', $alert);
    //         A::app()->getSession()->setFlash('alertType', $alertType);
    //     }
    //
    //     $this->redirect('patients/manage');
    // }


    /**
     * Change status doctor action handler
     * @param int $id       the doctor ID
     * @param int $page 	the page number
     * @return void
     */
    public function activeStatusAction($id, $page = 1)
    {
        Website::prepareBackendAction('edit', 'doctor', 'doctors/manage');

        $doctor = Doctors::model()->findByPk($id);
        if(!empty($doctor)){            
            if(Accounts::model()->updateByPk($doctor->account_id, array('is_active'=>($doctor->is_active == 1 ? '0' : '1')))){
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
        
        $this->redirect('doctors/manage'.(!empty($page) ? '?page='.(int)$page : 1));
    }

    /*
     * Return doctors
     * @return json
     * */
    public function ajaxGetDoctorNamesAction()
    {
        // Block access if this is not AJAX request
        $cRequest = A::app()->getRequest();
        if(!$cRequest->isAjaxRequest()){
            $this->redirect('doctors/dashboard');
        }

        $result = CWidget::create('CFormValidation', array(
            'fields' => array(
                'search' => array('title'=>A::t('appointments', 'Name'), 'validation'=>array('required'=>true, 'type'=>'name', 'maxLength'=>65)),
            ),
        ));
        if(!empty($result['error'])){
            $errorMessage = str_replace('"', '\"', $result['errorMessage']);
            $fieldError = $result['errorField'];

            $arr[] = '{"status": "0"}';
            $arr[] = '{"message": "'.$errorMessage.'"}';
        }else{
            $search = $cRequest->getPost('search');
            $fullName = explode(' ', $search, 2);
            if(!empty($fullName)){
                if(count($fullName) == 1){
                    $fullName[0] = strip_tags(CString::quote($fullName[0]));
                    $params[':doctor_first_name'] = $fullName[0].'%';
                    $params[':doctor_last_name']  = $fullName[0].'%';

                    $condition = 'membership_show_in_search = 1 AND doctor_first_name LIKE :doctor_first_name OR doctor_last_name LIKE :doctor_last_name';
                }else{
                    $fullName[0] = strip_tags(CString::quote($fullName[0]));
                    $fullName[1] = strip_tags(CString::quote($fullName[1]));
                    $params[':doctor_first_name_1'] = $fullName[1].'%';
                    $params[':doctor_last_name_1']  = $fullName[0].'%';
                    $params[':doctor_first_name_2'] = $fullName[0].'%';
                    $params[':doctor_last_name_2']  = $fullName[1].'%';

                    $condition = 'membership_show_in_search = 1 AND (doctor_first_name LIKE :doctor_first_name_1 AND doctor_last_name LIKE :doctor_last_name_1) OR (doctor_first_name LIKE :doctor_first_name_2 AND doctor_last_name LIKE :doctor_last_name_2)';
                }

                $accountTableName = CConfig::get('db.prefix').Accounts::model()->getTableName();
                $doctors = Doctors::model()->findAll(array(
                        'condition' => $condition.' AND '.$accountTableName.'.is_active = 1 AND '.$accountTableName.'.is_removed = 0',
                        'order'=>'doctor_first_name,doctor_last_name'
                    ),
                    $params
                );
                if(is_array($doctors) && !empty($doctors)){
                    $arr[] = '{"status": "1"}';
                    foreach($doctors as $key => $doctor){
						$specialty = DoctorSpecialties::model()->findAll(
							array(
								'condition'=>'doctor_id = '.$doctor['id'],
								'orderBy'=>'specialty_id ASC',
								'limit'=>'1'
							)
						);
                        $arr[] = '{"id": "'.$doctor['id'].'", "spec": "'.$specialty[0]['specialty_id'].'", "label": "'.htmlentities($doctor['doctor_first_name'].' '.$doctor['doctor_last_name'].(!empty($specialty[0]['specialty_name']) ? ' ('.$specialty[0]['specialty_name'].')' : '')).'"}';
                    }
                }
            }
        }

        if(empty($arr)){
            $arr[] = '';
        }

        $this->_outputAjax($arr, true);
    }

    /**
     * Check if passed Account ID is valid
     * @param int $id
     * @return Doctors
     */
    private function _checkAccountsAccess($id = 0)
    {
		if(empty($id)){
			$this->redirect('doctors/logout');
		}

		$doctor = Doctors::model()->find('account_id = :account_id', array(':account_id'=>$id));
        if(!$doctor){
            $this->redirect('doctors/logout');
        }

        return $doctor;
    }

    /**
     * Check if passed Appointment ID is valid
     * @param int $id
     * @param bool $editPast
     * @return Appointments
     */
    private function _checkAppointmentAccess($id = 0, $editPast = false)
    {
		if(empty($id)){
			$this->redirect('doctors/manage');
		}

		$appointment = array();

		$doctorId = CAuth::getLoggedRoleId();
		$tableName = CConfig::get('db.prefix').Appointments::model()->getTableName();
		if($editPast){
            $condition = $tableName.'.id = :id AND '.$tableName.'.doctor_id = :doctor_id';
        }else{
            $condition = $tableName.'.id = :id AND '.$tableName.'.doctor_id = :doctor_id AND ('.$tableName.".appointment_date > '".LocalTime::currentDateTime('Y-m-d')."'".' OR ('.$tableName.".appointment_date = '".LocalTime::currentDateTime('Y-m-d')."'".' AND '.$tableName.".appointment_time > '".LocalTime::currentDateTime('H:i:s')."'))";
        }
        $appointment = Appointments::model()->find($condition, array(':doctor_id'=>$doctorId, ':id'=>$id));
        if(!$appointment){
            $this->redirect('doctors/manage');
        }
        return $appointment;
    }

    /**
     * Check if passed Patient ID is valid
     * @param int $patientId
     * @return object Patients
     */
    private function _checkPatientAccess($patientId = 0)
    {
		if(empty($patientId)){
			$this->redirect('doctors/manage');
		}

		$patient = array();

		$tableName = CConfig::get('db.prefix').Patients::model()->getTableName();
        $patient = Patients::model()->find($tableName.'.id = :id', array(':id'=>$patientId));
        if(!$patient){
            $this->redirect('doctors/manage');
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
	 * Check membership plan access.
	 * If the reminder is enabled Check how many days left before expires memberships plan.
	 * @param $membershipPlanId
	 * @return object|bool Memberships
	 */
	private function _checkMembershipPlanAccess($membershipPlanId)
	{
		if(empty($membershipPlanId)){
			return false;
		}

        $membershipPlan = Memberships::model()->findByPk($membershipPlanId);
        if(!$membershipPlan){
            $this->redirect('Home/index');
        }
        return $membershipPlan;


	}

    /**
     * Prepares account fields
     * @return void
     */
    private function _prepareAccountFields()
    {
        // Prepare settings
        $this->_view->removalType         = ModulesSettings::model()->param('appointments', 'doctor_removal_type');
        $this->_view->changePassword      = ModulesSettings::model()->param('appointments', 'change_doctor_password');
        $this->_view->verificationCaptcha = ModulesSettings::model()->param('appointments', 'doctors_verification_allow');

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

        $membershipPlanList = array();
        $membershipPlanResult = Memberships::model()->findAll(array('condition'=>'is_active = 1', 'orderBy'=>'price ASC'));
        if(is_array($membershipPlanResult)){
            foreach($membershipPlanResult as $membershipPlan){
                $membershipPlanList[$membershipPlan['id']] = $membershipPlan['name'];
            }
        }

        // Insurances List
		$insurancesList = $this->_insurancesList();

        // Prepare locale
        $localesList = array(
            'sq'=>A::t('appointments', 'Albanian'),
            'ar'=>A::t('appointments', 'Arabic'),
            'eu'=>A::t('appointments', 'Basque'),
            'be'=>A::t('appointments', 'Belarusian'),
            'bg'=>A::t('appointments', 'Bulgarian'),
            'ca'=>A::t('appointments', 'Catalan'),
            'zh'=>A::t('appointments', 'Chinese'),
            'hr'=>A::t('appointments', 'Croatian'),
            'cs'=>A::t('appointments', 'Czech'),
            'da'=>A::t('appointments', 'Danish'),
            'nl'=>A::t('appointments', 'Dutch'),
            'de'=>A::t('appointments', 'German'),
            'en'=>A::t('appointments', 'English'),
            'et'=>A::t('appointments', 'Estonian'),
            'fi'=>A::t('appointments', 'Finnish'),
            'fo'=>A::t('appointments', 'Faroese'),
            'fr'=>A::t('appointments', 'French'),
            'gl'=>A::t('appointments', 'Galician'),
            'gu'=>A::t('appointments', 'Gujarati'),
            'he'=>A::t('appointments', 'Hebrew'),
            'hi'=>A::t('appointments', 'Hindi'),
            'hu'=>A::t('appointments', 'Hungarian'),
            'id'=>A::t('appointments', 'Indonesian'),
            'is'=>A::t('appointments', 'Icelandic'),
            'it'=>A::t('appointments', 'Italian'),
            'ja'=>A::t('appointments', 'Japanese'),
            'ko'=>A::t('appointments', 'Korean'),
            'lt'=>A::t('appointments', 'Lithuanian'),
            'lv'=>A::t('appointments', 'Latvian'),
            'mk'=>A::t('appointments', 'Macedonian'),
            'mn'=>A::t('appointments', 'Mongolian'),
            'ms'=>A::t('appointments', 'Malay'),
            'nb'=>A::t('appointments', 'Norwegian Bokmål'),
            'no'=>A::t('appointments', 'Norwegian'),
            'pl'=>A::t('appointments', 'Polish'),
            'pt'=>A::t('appointments', 'Portuguese'),
            'ro'=>A::t('appointments', 'Romanian'),
            'ru'=>A::t('appointments', 'Russian'),
            'sk'=>A::t('appointments', 'Slovak'),
            'sl'=>A::t('appointments', 'Slovenian'),
            'sr'=>A::t('appointments', 'Serbian'),
            'es'=>A::t('appointments', 'Spanish'),
            'sv'=>A::t('appointments', 'Swedish'),
            'ta'=>A::t('appointments', 'Tamil'),
            'te'=>A::t('appointments', 'Telugu'),
            'th'=>A::t('appointments', 'Thai'),
            'tr'=>A::t('appointments', 'Turkish'),
            'uk'=>A::t('appointments', 'Ukrainian'),
            'ur'=>A::t('appointments', 'Urdu'),
            'vi'=>A::t('appointments', 'Vietnamese'),
        );

        $this->_view->insurancesList     = $insurancesList;
        $this->_view->localesList        = $localesList;
        $this->_view->genders            = $genders;
        $this->_view->degrees            = $degrees;
        $this->_view->titles             = $titles;
        $this->_view->experienceYears    = $experienceYears;
        $this->_view->countries          = $countries;
        $this->_view->langList           = $langList;
        $this->_view->membershipPlanList = $membershipPlanList;
    }

    /**
     * Doctor logout
     * @return void
     */
    private function _logout()
    {
        A::app()->getSession()->endSession();
        A::app()->getCookie()->remove('doctorAuth');
        // clear cache
        if(CConfig::get('cache.enable')) CFile::emptyDirectory('protected/tmp/cache/');
    }

    /**
     * Prepare scheduleCounters
     * @return array
     */
    private function _prepareScheduleCounters()
    {
        $scheduleCounters = array();
        $tableSchedules = CConfig::get('db.prefix').DoctorSchedules::model()->getTableName();
        $result = DoctorSchedules::model()->count(
            array(
                'condition'=>'',
                'select'=>$tableSchedules.'.doctor_id',
                'count'=>'*',
                'groupBy'=>$tableSchedules.'.doctor_id',
                'allRows'=>true
            )
        );

        if(!empty($result)){
            foreach($result as $key => $schedule){
                $scheduleCounters[$schedule['doctor_id']] = $schedule['cnt'];
            }
        }

        return $scheduleCounters;
    }

    /**
     * Prepare specialtyCounters
     * @return array
     */
    private function _prepareSpecialtyCounters()
    {
        $specialtyCounters = array();
        $tableSpecialties = CConfig::get('db.prefix').DoctorSpecialties::model()->getTableName();
        $result = DoctorSpecialties::model()->count(
            array(
                'condition'=>'',
                'select'=>$tableSpecialties.'.doctor_id',
                'count'=>'*',
                'groupBy'=>$tableSpecialties.'.doctor_id',
                'allRows'=>true
            )
        );

        if(!empty($result)){
            foreach($result as $key => $specialty){
                $specialtyCounters[$specialty['doctor_id']] = $specialty['cnt'];
            }
        }

        return $specialtyCounters;
    }

    /**
     * Prepare specialties
     * @return array
     */
    private function _prepareImages()
    {
        $images = array();
        $tableImages = CConfig::get('db.prefix').DoctorImages::model()->getTableName();
        $result = DoctorImages::model()->count(
            array(
                'condition'=>'',
                'select'=>$tableImages.'.doctor_id',
                'count'=>'*',
                'groupBy'=>$tableImages.'.doctor_id',
                'allRows'=>true
            )
        );

        if(!empty($result)){
            foreach($result as $key => $image){
                $images[$image['doctor_id']] = $image['cnt'];
            }
        }

        return $images;
    }

    /**
     * Prepare specialties
     * @return array
     */
    private function _prepareTimeoffs()
    {
        $images = array();
        $tableTimeoffs = CConfig::get('db.prefix').DoctorTimeoffs::model()->getTableName();
        $result = DoctorTimeoffs::model()->count(
            array(
                'condition'=>'',
                'select'=>$tableTimeoffs.'.doctor_id',
                'count'=>'*',
                'groupBy'=>$tableTimeoffs.'.doctor_id',
                'allRows'=>true
            )
        );

        if(!empty($result)){
            foreach($result as $key => $image){
                $images[$image['doctor_id']] = $image['cnt'];
            }
        }

        return $images;
    }

    /**
     * Prepare clinicCounters
     * @return array
     */
    private function _prepareClinicCounters()
    {
        $clinicCounters = array();
        $tableClinics = CConfig::get('db.prefix').DoctorClinics::model()->getTableName();
        $result = DoctorClinics::model()->count(
            array(
                'condition'=>'',
                'select'=>$tableClinics.'.doctor_id',
                'count'=>'*',
                'groupBy'=>$tableClinics.'.doctor_id',
                'allRows'=>true
            )
        );

        if(!empty($result)){
            foreach($result as $key => $clinic){
                $clinicCounters[$clinic['doctor_id']] = $clinic['cnt'];
            }
        }

        return $clinicCounters;
    }

    /**
     * Outputs data to browser
     * @param $array $data
     * @param string $returnArray
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

    /**
     * Get Degrees
     * @return array
     */
    private function _getDegrees()
    {
        $arrOutput = array();

        $result = Degrees::model()->findAll(array(
                'condition' => 'is_active = 1',
                'orderBy'   => 'sort_order ASC')
        );
        if(!empty($result) && is_array($result)){
            foreach($result as $degree){
                $arrOutput[$degree['id']]['title'] = $degree['title'];
                $arrOutput[$degree['id']]['full'] = $degree['title'].' ('.$degree['name'].')';
            }
        }

        return $arrOutput;
    }

    /**
     * Get Degrees
     * @return array
     */
    private function _insurancesList()
    {
        // Insurances List
        $insurancesList = array();
        $insurances = Insurance::model()->findAll(array('condition'=>'is_active = 1', 'orderBy'=>'name ASC'));
        foreach($insurances as $insurance){
            $insurancesList[$insurance['id']] = $insurance['name'];
        }
        return $insurancesList;
    }
}
