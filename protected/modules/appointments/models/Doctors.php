<?php
/**
 * Appointments model
 *
 * PUBLIC:                 PROTECTED                  PRIVATE
 * ---------------         ---------------            ---------------
 * __construct             _relations                 _prepareTitles
 * getFullName             _customFields
 * registrationSocial      _beforeSave
 * getError                _afterSave
 * getErrorField           _afterDelete
 * search
 *
 * STATIC:
 * ---------------------------------------------------------------
 * model
 * isLogin
 *
 */

namespace Modules\Appointments\Models;

// Framework
use \A,
    \CConfig,
    \CDebug,
    \CHash,
	\CLocale,
    \CActiveRecord,
    \CAuth;

// Application
use \Accounts,
    \Website,
    \LocalTime,
    \ModulesSettings;

use Modules\Appointments\Components\DoctorsComponent;

class Doctors extends CActiveRecord
{

    /** @var string */
    protected $_table = 'appt_doctors';
    /** @var string */
    protected $_role = 'doctor';
    /** @var string */
    protected $_tableAccounts = 'accounts';
    /** @var string */
    protected $_tableTitleTranslations = 'appt_title_translations';
    /** @var string */
    protected $_tableAppointments = 'appt_appointments';
    /** @var string */
    protected $_tableDegrees = 'appt_degrees';
    /** @var string */
    protected $_tableDoctorSpecialties = 'appt_doctor_specialties';

    /** @var string */
    protected $_tableMembershipsTranslations = 'appt_membership_plans_translations';

    /* @var string (empty(default)|with_appointments_counter) */
    protected $_typeRelations = 'with_appointments_counter';

    /** @var bool */
    private $_sendApprovalEmail = false;
    /** @var bool */
    private $_sendActivationEmail = false;
    /** @var bool */
    private $_sendPasswordChangedEmail = false;
    private $_oldMembershipPlanId = 0;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Returns the static model of the specified AR class
     * @param string $relationType
     * @return Doctors
     */
    public static function model($relationType = '')
    {
        $model = parent::model(__CLASS__);

        // Set relations type
        if(empty($relationType)){
            if($model->_typeRelations != ''){
                $model->reset();
            }
            $model->_typeRelations = '';
        }elseif(in_array($relationType, array('with_appointments_counter'))){
            $model->_typeRelations = $relationType;
        }

        return $model;
    }

    /**
     * Checked is login by doctor
     */
    public static function isLogin()
    {
        return CAuth::isLoggedInAs('doctor');
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
                'condition'=>"role = '".$this->_role."'",
                'joinType'=>self::INNER_JOIN,
                'fields'=>array(
                    'role'          => 'role',
                    'email'         => 'email',
                    'avatar'        => 'avatar',
                    'language_code' => 'language_code',
                    'username'      => 'username',
                    'created_at'    => 'created_at',
                    'created_ip'    => 'created_ip',
                    'last_visited_at'=> 'last_visited_at',
                    'last_visited_ip'=> 'last_visited_ip',
                    'notifications' => 'notifications',
                    'notifications_changed_at' => 'notifications_changed_at',
                    'password_changed_at' => 'password_changed_at',
                    'is_active'     => 'is_active',
                    'is_removed'    => 'is_removed',
                    'comments'      => 'comments'
                )
            ),
            'title_id' => array(
                self::HAS_ONE,
                $this->_tableTitleTranslations,
                'id',
                'condition'=>"",
                'joinType'=>self::LEFT_OUTER_JOIN,
                'fields'=>array(
                    'title'=>'title'
                )
            ),
			'medical_degree_id' => array(
                self::HAS_ONE,
                $this->_tableDegrees,
                'id',
                'condition'=>"",
                'joinType'=>self::LEFT_OUTER_JOIN,
                'fields'=>array(
                    'title'=>'degrees_name'
                )
            ),
            'membership_plan_id' => array(
                self::HAS_ONE,
                $this->_tableMembershipsTranslations,
                'membership_plan_id',
                'condition'=>CConfig::get('db.prefix').$this->_tableMembershipsTranslations.".language_code = '".A::app()->getLanguage()."'",
                'joinType'=>self::LEFT_OUTER_JOIN,
                'fields'=>array(
                    'name'=>'membership_plan_name'
                )
            ),
        );
    }

	/**
     * Used to define custom fields
	 * This method should be overridden
	 * Usage: 'CONCAT(last_name, " ", first_name)' => 'fullname'
	 *        '(SELECT COUNT(*) FROM '.CConfig::get('db.prefix').$this->_tableTranslation.')' => 'records_count'
	 */
	protected function _customFields()
	{
        $fields = array(
            "IF(avatar != '', avatar, if(gender = 'f', 'no_avatar_female.png', 'no_avatar_male.png'))" => 'avatar_by_gender',
            "CONCAT(doctor_first_name, ' ', doctor_middle_name, ' ', doctor_last_name)" => 'full_name'
        );

        if($this->_typeRelations == 'with_appointments_counter'){
            $fields['(SELECT COUNT(*) FROM '.CConfig::get('db.prefix').$this->_tableAppointments.' app WHERE app.doctor_id = '.CConfig::get('db.prefix').$this->_table.'.id)'] = 'appointments_count';
        }

        return $fields;
	}

    /**
     * This method is invoked before saving a record
     * @param string $id
     * @return bool
     */
    protected function _beforeSave($id = 0)
    {
        $cRequest         = A::app()->getRequest();
        $firstName        = $cRequest->getPost('doctor_first_name');
        $lastName         = $cRequest->getPost('doctor_last_name');
        $username         = $cRequest->getPost('username');
        $password         = $cRequest->getPost('password');
        $salt             = $cRequest->getPost('salt');
        $email            = $cRequest->getPost('email');
        $languageCode     = $cRequest->getPost('language_code', 'alpha', A::app()->getLanguage());
        $notifications    = (int)$cRequest->getPost('notifications', 'int');
        if($notifications !== 0 && $notifications !== 1) $notifications = 0;
        $isActive         = (int)$cRequest->getPost('is_active', 'int', 1);
        $isRemoved        = (int)$cRequest->getPost('is_removed', 'int');
        $comments         = $cRequest->getPost('comments');
        $ipAddress        = $cRequest->getUserHostAddress();
        $approvalType     = ModulesSettings::model()->param('appointments', 'doctor_approval_type');
        $changePassword   = ModulesSettings::model()->param('appointments', 'change_doctor_password');

        $firstName = empty($firstName) && $this->isColumnExists('doctor_first_name') ? $this->doctor_first_name : $firstName;
        $lastName  = empty($lastName) && $this->isColumnExists('doctor_last_name') ? $this->doctor_last_name : $lastName;
        $username  = empty($username) && $this->isColumnExists('username') ? $this->username : $username;
        $email     = empty($email) && $this->isColumnExists('email') ? $this->email : $email;

        if (in_array(CAuth::getLoggedRole(), array('mainadmin', 'admin'))) {
            $doctor = new Doctors();
            $doctor->findByPk($id);
            if($doctor){
                $this->_oldMembershipPlanId = $doctor->membership_plan_id;
            }
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

        // check if doctor with the same email already exists
        $doctorExists = $this->_db->select('SELECT * FROM '.CConfig::get('db.prefix').'accounts WHERE role = :role AND email = :email AND id != :id', array(':role'=>$this->_role, ':email'=>$email, ':id'=>$this->account_id));
        if(!empty($email) && $doctorExists){
            $this->_error = true;
            $this->_errorMessage = A::t('appointments', 'Doctor with such email already exists!');
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

                // approval by admin (previously created by doctor)
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
            if($this->avatar != '') $account->avatar = $this->avatar;
            $account->email = $email;
            $account->language_code = $languageCode;
            if($account->notifications != $notifications){
                $account->notifications = $notifications;
                $account->notifications_changed_at = LocalTime::currentDateTime();
            }
            
            if($account->save()){
                // update existing doctor
                if($this->birth_date == '') $this->birth_date = null;
                return true;
            }
            return false;
        }else{
            // NEW ACCOUNT
            // check if doctor with the same username already exists
            if($this->_db->select('SELECT * FROM '.CConfig::get('db.prefix').'accounts WHERE role = :role AND username = :username', array(':role'=>$this->_role, ':username'=>$username))){
                $this->_error = true;
                $this->_errorMessage = A::t('appointments', 'Doctor with such username already exists!');
                $this->_errorField = 'username';
                return false;
            }
            
            // Insert new doctor
            $accountId = $this->_db->insert($this->_tableAccounts, array(
                'role'              => $this->_role,
                'username'          => $username,
                'password'          => $password,
                'salt'              => $salt,
                'email'             => $email,
                'avatar'            => $this->getSpecialField('avatar'),
                'language_code'     => $languageCode,
                'created_at'        => LocalTime::currentDateTime(),
                'created_ip'        => $ipAddress,
                'notifications'     => $notifications,
                'registration_code' =>'',
                'is_active'         => $isActive,
                'comments'          => $comments
            ));
            
            if($accountId){
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
        $firstName = $cRequest->getPost('doctor_first_name');
        $lastName = $cRequest->getPost('doctor_last_name');
        $username = $cRequest->getPost('username', '', $this->username);
        $password = $cRequest->getPost('password');
        $languageCode = $cRequest->getPost('language_code');
        $isActive = (int)$cRequest->getPost('is_active', 'int');

        // send email to doctor on creating new account by admininstrator (if doctor is active)
        if($this->_sendActivationEmail){
            $result = Website::sendEmailByTemplate(
                $email,
                'doctors_new_account_created_by_admin',
                $languageCode,
                array(
                    '{FIRST_NAME}' => $firstName,
                    '{LAST_NAME}' => $lastName,
                    '{USERNAME}' => $username,
                    '{PASSWORD}' => $password,
                )
            );
        }

        // send email to doctor on admin changes doctor password
        if($this->_sendPasswordChangedEmail){
            $result = Website::sendEmailByTemplate(
                $email,
                'doctors_password_changed_by_admin',
                $languageCode,
                array(
                    '{FIRST_NAME}' => $firstName,
                    '{LAST_NAME}' => $lastName,
                    '{USERNAME}' => $username,
                    '{PASSWORD}' => $password,
                )
            );
        }

        // send email to doctor on admin approval
        if($this->_sendApprovalEmail){
            $result = Website::sendEmailByTemplate(
                $email,
                'doctors_account_approved_by_admin',
                $languageCode,
                array(
                    '{FIRST_NAME}' => $firstName,
                    '{LAST_NAME}' => $lastName
                )
            );
        }

		if($this->isNewRecord()){
        	//Search and add default mempership plan to doctor account
			$defaultMembershipPlan = Memberships::model()->find('is_default = 1');
			if($defaultMembershipPlan){
				$this->membership_plan_id = $defaultMembershipPlan->id;
				$this->membership_images_count = $defaultMembershipPlan->images_count;
				$this->membership_clinics_count = $defaultMembershipPlan->clinics_count;
				$this->membership_schedules_count = $defaultMembershipPlan->schedules_count;
				$this->membership_specialties_count = $defaultMembershipPlan->specialties_count;
				$this->membership_show_in_search = $defaultMembershipPlan->show_in_search;
				$this->membership_enable_reviews = $defaultMembershipPlan->enable_reviews;
				if(CAuth::isLoggedInAsAdmin() || $defaultMembershipPlan->price == 0){
					$dayInSec = 24*60*60;
					$membershipDuration = $defaultMembershipPlan->duration * $dayInSec;
					$currentDay = CLocale::date('Y-m-d');
					$dateExpires = strtotime($currentDay) + $membershipDuration;
					$this->membership_expires = CLocale::date('Y-m-d', $dateExpires, true);
				}else{
					$this->membership_expires = CLocale::date('Y-m-d', strtotime("-1 days"), true);
				}

				$this->save();
			}
		}

        if (in_array(CAuth::getLoggedRole(), array('mainadmin', 'admin')) && !empty($this->_oldMembershipPlanId) && $this->_oldMembershipPlanId !== $this->membership_plan_id) {
            $membershipPlan = Memberships::model()->findByPk($this->membership_plan_id);
            if($membershipPlan){
                $this->membership_plan_id = $membershipPlan->id;
                $this->membership_images_count = $membershipPlan->images_count;
                $this->membership_clinics_count = $membershipPlan->clinics_count;
                $this->membership_schedules_count = $membershipPlan->schedules_count;
                $this->membership_specialties_count = $membershipPlan->specialties_count;
                $this->membership_show_in_search = $membershipPlan->show_in_search;
                $this->membership_enable_reviews = $membershipPlan->enable_reviews;
                if(CAuth::isLoggedInAsAdmin() || $membershipPlan->price == 0){
                    $dayInSec = 24*60*60;
                    $membershipDuration = $membershipPlan->duration * $dayInSec;
                    $currentDay = CLocale::date('Y-m-d');
                    $dateExpires = strtotime($currentDay) + $membershipDuration;
                    $this->membership_expires = CLocale::date('Y-m-d', $dateExpires, true);
                }else{
                    $this->membership_expires = CLocale::date('Y-m-d', strtotime("-1 days"), true);
                }
                $this->save();
            }else{
                $this->membership_plan_id = $this->_oldMembershipPlanId;
                $this->save();
            }
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
            $this->_errorMessage = A::t('appointments', 'An error occurred while deleting doctor account! Please try again later.');
        }
    }

    private function _getTitles()
    {
        return Titles::getActiveTitles();
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
        $this->doctor_first_name   = isset($params['firstName']) ? $params['firstName'] : '';
        $this->doctor_last_name    = isset($params['lastName']) ? $params['lastName'] : '';
    }

    /**
     * Get full name for current doctor
     * @return string
     */
    public function getFullName()
    {
        $output = '';
        if(!empty($this->_columns) && $this->getPrimaryKey() != ''){
            $titles = $this->_getTitles();
            $output .= isset($titles[$this->_columns['title_id']]) ? $titles[$this->_columns['title_id']] : '';
            $output .= !empty($this->_columns['doctor_first_name']) ? ' '.$this->_columns['doctor_first_name'] : '';
            $output .= !empty($this->_columns['doctor_middle_name']) ? ' '.$this->_columns['doctor_middle_name'] : '';
            $output .= !empty($this->_columns['doctor_last_name']) ? ' '.$this->_columns['doctor_last_name'] : '';
        }

        return $output;
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

	/**
	 * Performs search in doctors
	 * @param string $keywords
	 * @param mixed $itemsCount
	 * @return array array('0'=>array(doctors), '1'=>total)
	 */
	public function search($keywords = '', $itemsCount = 10)
	{
		$result = array();

		if($keywords !== ''){

			$limit = !empty($itemsCount) ? '0, '.(int)$itemsCount : '';
			$condition = CConfig::get('db.prefix').$this->_table.'.membership_show_in_search = 1 AND ('.
                CConfig::get('db.prefix').$this->_table.'.doctor_first_name LIKE :keywords OR '.
                CConfig::get('db.prefix').$this->_table.'.doctor_last_name LIKE :keywords OR '.
                CConfig::get('db.prefix').$this->_table.'.doctor_middle_name LIKE :keywords)';

			// Count total items in result
			$total = $this->count(array('condition'=>$condition), array(':keywords'=>'%'.$keywords.'%'));

			// Prepare doctors result
			$doctors = $this->findAll(array('condition'=>$condition, 'limit' => $limit), array(':keywords'=>'%'.$keywords.'%'));

			foreach($doctors as $key => $val){
			    $content = '';
                $content .= A::t('appointments', 'Name').': '.$val['full_name'];
                $content .= ', '.A::t('appointments', 'Gender').': '.(($val['gender'] == 'm') ? 'Male' : 'Famele');

                if(!empty($val['degrees_name'])){
                    $content .= ', '.A::t('appointments', 'Degree').': '.$val['degrees_name'];
                }

                $specialty = DoctorSpecialties::model()->findAll(array('condition'=>CConfig::get('db.prefix').$this->_tableDoctorSpecialties.'.doctor_id LIKE :doctor_id', 'limit' => $limit), array(':doctor_id'=>$val['id']));

                if(!empty($specialty)){
					$content .= ', '.A::t('appointments', 'Specialty').': ';
					foreach($specialty as $key => $spec){
						$content .= $spec['specialty_name'];
						if(isset($specialty[$key + 1])) $content .= ', ';
						else $content .= '.';

					}
                }

				$result[0][] = array(
                    'title' 		=> $val['full_name'].($val['degrees_name'] ? ', '.$val['degrees_name'] : ''),
                    'intro_image'	=> (!empty($val['avatar']) ? '<img class="search-image" src="assets/modules/appointments/images/doctors/'.$val['avatar'].'" alt="'.$val['full_name'].'" />' : ''),
                    'content' 		=> $content,
                    'link' 			=> Website::prepareLinkByFormat('appointments', 'profile_link_format', $val['id'], DoctorsComponent::getDoctorName($val))
                );
			}

			$result[1] = $total;
		}

		return $result;
	}
}
