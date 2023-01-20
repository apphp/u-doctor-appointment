<?php
/**
 * Appointments model
 *
 * PUBLIC:                 PROTECTED                  PRIVATE
 * ---------------         ---------------            ---------------
 * __construct             _relations
 *
 * STATIC:
 * ---------------------------------------------------------------
 * model
 * isLogin
 * getFullName
 *
 */

namespace Modules\Appointments\Models;

// Framework
use \A,
    \CConfig,
    \CDebug,
    \CHash,
    \CActiveRecord,
    \CAuth;

// Application
use \Accounts,
    \Website,
    \LocalTime,
    \ModulesSettings;

class Patients extends CActiveRecord
{

    /** @var string */
    protected $_table = 'appt_patients';
    /** @var string */
    protected $_role = 'patient';
    /** @var string */
    protected $_tableAccounts = 'accounts';
    /** @var bool */
    private $_sendApprovalEmail = false;
    /** @var bool */
    private $_sendActivationEmail = false;
    /** @var bool */
    private $_sendPasswordChangedEmail = false;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Returns the static model of the specified AR class
     * @return Patients
     */
    public static function model()
    {
        return parent::model(__CLASS__);
    }

    /**
     * Checked is login by doctor
     */
    public static function isLogin()
    {
        return CAuth::isLoggedInAs('patient');
    }

    /**
     * Defines relations between different tables in database and current $_table
     * @return array
     */
    protected function _relations()
    {
        return array(
            'account_id' => array(
                self::HAS_ONE,
                $this->_tableAccounts,
                'id',
                'condition'=> CConfig::get('db.prefix').$this->_tableAccounts.".role = '".$this->_role."'",
                'joinType'=>self::INNER_JOIN,
                'fields'=>array(
                    'role'=>'role',
                    'email'=>'email',
                    'avatar'=>'avatar',
                    'language_code'=>'language_code',
                    'username'=>'username',
                    'created_at'=>'created_at',
                    'created_ip'=>'created_ip',
                    'last_visited_at'=>'last_visited_at',
                    'last_visited_ip'=>'last_visited_ip',
                    'notifications'=>'notifications',
                    'notifications_changed_at'=>'notifications_changed_at',
                    'password_changed_at'=>'password_changed_at',
                    'is_active'=>'is_active',
                    'is_removed'=>'is_removed',
                    'comments'=>'comments'
                )
            ),
        );
    }

    /**
     * Get full name for current patient
     * @return string
     */
    public function getFullName()
    {
        $output = '';
        if(!empty($this->_columns) && $this->getPrimaryKey() != ''){
            $output .= !empty($this->_columns['patient_first_name']) ? ' '.$this->_columns['patient_first_name'] : '';
            $output .= !empty($this->_columns['patient_last_name']) ? ' '.$this->_columns['patient_last_name'] : '';
        }

        return $output;
    }

    /**
     * This method is invoked before saving a record
     * @param string $id
     * @return bool
     */
    protected function _beforeSave($id = 0)
    {
        $cRequest      = A::app()->getRequest();
        $firstName     = $cRequest->getPost('patient_first_name');
        $lastName      = $cRequest->getPost('patient_last_name');
        $username      = $cRequest->getPost('username');
        $password      = $cRequest->getPost('password');
        $salt          = $cRequest->getPost('salt');
        $email         = $cRequest->getPost('email');
        $avatar        = $cRequest->getPost('avatar');
        $languageCode  = $cRequest->getPost('language_code', 'alpha', A::app()->getLanguage());
        $notifications = (int)$cRequest->getPost('notifications', 'int');
        if($notifications !== 0 && $notifications !== 1) $notifications = 0;
        $isActive       = (int)$cRequest->getPost('is_active', 'int', 1);
        $isRemoved      = (int)$cRequest->getPost('is_removed', 'int');
        $comments       = $cRequest->getPost('comments');
        $ipAddress      = $cRequest->getUserHostAddress();
        $approvalType   = ModulesSettings::model()->param('appointments', 'patient_approval_type');
        $changePassword = ModulesSettings::model()->param('appointments', 'change_patient_password');

        $firstName = empty($firstName) && $this->isColumnExists('patient_first_name') ? $this->patient_first_name : $firstName;
        $lastName  = empty($lastName) && $this->isColumnExists('patient_last_name') ? $this->patient_last_name : $lastName;
        $username  = empty($username) && $this->isColumnExists('username') ? $this->username : $username;
        $email     = empty($email) && $this->isColumnExists('email') ? $this->email : $email;
        $avatar    = $this->isColumnExists('avatar') && $this->avatar != '' ? $this->avatar : $avatar;

        if (CAuth::isLoggedInAs('doctor') && !empty($email)) {
            $username = $email;
        }

        if($id > 0){
            $account = Accounts::model()->findByPk((int)$this->account_id);
            $salt = $account->salt;
        }else{
            $salt = !empty($salt) ? $salt : CHash::getRandomString(33);
        }

        if(CConfig::get('password.encryption')){
            $encryptAlgorithm = CConfig::get('password.encryptAlgorithm');
            $encryptSalt = $salt;
            if(!empty($password)){
                $password = CHash::create($encryptAlgorithm, $password, $encryptSalt);
            }
        }

        // check if patient with the same email already exists
        $patientExists = $this->_db->select('SELECT * FROM '.CConfig::get('db.prefix').$this->_tableAccounts.' WHERE '.CConfig::get('db.prefix').$this->_tableAccounts.'.role = :role AND '.CConfig::get('db.prefix').$this->_tableAccounts.'.email = :email AND id != :id', array(':role'=>$this->_role, ':email'=>$email, ':id'=>$this->account_id));
        if(!empty($email) && $patientExists){
            $this->_error = true;
            $this->_errorMessage = A::t('appointments', 'Patient with such email already exists!');
            $this->_errorField = 'email';
            return false;
        }

        if($id > 0){
            // UPDATE DOCTOR
            // update accounts table
            if(CAuth::isLoggedInAsAdmin()){
                $account->comments = $comments;
                $account->is_active = $isActive;
                $account->is_removed = $isRemoved;
                // logical deleting
                if($isRemoved == 1){
                    $account->is_active = 0;
                }

                // approval by admin (previously created by patient)
                if($approvalType == 'by_admin' && $account->registration_code != '' && $isActive){
                    $account->registration_code = '';
                    $this->_sendApprovalEmail = true;
                }

                // password changed by admin
                if($changePassword && $account->password != $password && !empty($password) && $isActive){
                    $this->_sendPasswordChangedEmail = true;
                }
            }

            // Password was changed
            if($password !== ''){
                $account->password_changed_at = LocalTime::currentDateTime();
            }

            if(!empty($password)) $account->password = $password;
            if(!empty($salt)) $account->salt = $salt;
            if(!empty($avatar)) $account->avatar = $avatar;
            $account->email = $email;
            $account->language_code = $languageCode;
            if($account->notifications != $notifications){
                $account->notifications = $notifications;
                $account->notifications_changed_at = LocalTime::currentDateTime();
            }

            if($account->save()){
                // update existing patient
                if($this->birth_date == '') $this->birth_date = null;
                return true;
            }
            return false;
        }else{
            // NEW ACCOUNT
            // check if patient with the same username already exists
            if($this->_db->select('SELECT * FROM '.CConfig::get('db.prefix').'accounts WHERE role = :role AND username = :username', array(':role'=>$this->_role, ':username'=>$username))){
                $this->_error = true;
                $this->_errorMessage = A::t('appointments', 'Patient with such username already exists!');
                $this->_errorField = 'username';
                return false;
            }

            // insert new patient
            if($accountId = $this->_db->insert($this->_tableAccounts, array(
                'role'          =>$this->_role,
                'username'      =>$username,
                'password'      =>$password,
                'salt'          =>$salt,
                'email'         =>$email,
                'avatar'        => $this->getSpecialField('avatar'),
                'language_code' =>$languageCode,
                'created_at'    =>LocalTime::currentDateTime(),
                'created_ip'    =>$ipAddress,
                'notifications' =>$notifications,
                'registration_code'=>'',
                'is_active'     =>$isActive,
                'comments'      =>$comments
            ))){
                $this->account_id = $accountId;
                if($this->birth_date == '') $this->birth_date = null;

                // account activated by admin (previously created by admin)
                if(CAuth::isLoggedInAsAdmin() && $isActive){
                    $this->_sendActivationEmail = true;
                }
                return true;
            }
            return false;
        }
    }

    /**
     * This method is invoked after saving a record successfully
     * @param string $pk
     * @return void
     * You may override this method
     */
    protected function _afterSave($pk = '')
    {
        $cRequest = A::app()->getRequest();
        $email = $cRequest->getPost('email');
        $firstName = $cRequest->getPost('patient_first_name');
        $lastName = $cRequest->getPost('patient_last_name');
        $username = $cRequest->getPost('username', '', $this->username);
        $password = $cRequest->getPost('password');
        $languageCode = $cRequest->getPost('language_code');
        $isActive = (int)$cRequest->getPost('is_active', 'int');

        // send email to patient on creating new account by admininstrator (if patient is active)
        if($this->_sendActivationEmail){
            $result = Website::sendEmailByTemplate(
                $email,
                'patients_new_account_created_by_admin',
                $languageCode,
                array(
                    '{FIRST_NAME}' => $firstName,
                    '{LAST_NAME}' => $lastName,
                    '{USERNAME}' => $username,
                    '{PASSWORD}' => $password,
                )
            );
        }

        // send email to patient on admin changes patient password
        if($this->_sendPasswordChangedEmail){
            $result = Website::sendEmailByTemplate(
                $email,
                'patients_password_changed_by_admin',
                $languageCode,
                array(
                    '{FIRST_NAME}' => $firstName,
                    '{LAST_NAME}' => $lastName,
                    '{USERNAME}' => $username,
                    '{PASSWORD}' => $password,
                )
            );
        }

        // send email to patient on admin approval
        if($this->_sendApprovalEmail){
            $result = Website::sendEmailByTemplate(
                $email,
                'patients_account_approved_by_admin',
                $languageCode,
                array(
                    '{FIRST_NAME}' => $firstName,
                    '{LAST_NAME}' => $lastName
                )
            );
        }
    }

    /**
     * This method is invoked after deleting a record successfully
     * @param string $pk
     * @return void
     */
    protected function _afterDelete($pk = '')
    {
        // delete record from accounts table
        if(false === $this->_db->delete($this->_tableAccounts, 'id = '.(int)$this->account_id)){
            $this->_error = true;
            $this->_errorMessage = A::t('appointments', 'An error occurred while deleting patient account! Please try again later.');
        }
    }

    /*
     * Social Registration
     * @param array $params
     * @return void
     */
    public function registrationSocial($params = array())
    {
        $this->username     = isset($params['username']) ? $params['username'] : '';
        $this->email        = isset($params['email']) ? $params['email'] : '';
        $this->patient_first_name   = isset($params['patient_firstName']) ? $params['patient_firstName'] : '';
        $this->patient_last_name    = isset($params['patient_lastName']) ? $params['patient_lastName'] : '';
    }

    /**
     * Returns error description
     * @return boolean
     */
    public function getError()
    {
        return $this->_errorMessage;
    }

    /**
     * Returns error field
     * @return boolean
     */
    public function getErrorField()
    {
        return $this->_errorField;
    }
}
