<?php
/**
 * Doctor Clinics Controller
 * This controller intended to both Backend and Frontend modes
 *
 * PUBLIC:                          PRIVATE
 * -----------                      ------------------
 * __construct                      _checkDoctorAccess
 * manageAction                     _checkClinicAccess
 * addAction                        _checkUploadClinicsCountAccess
 * editAction
 * deleteAction
 *
 */

namespace Modules\Appointments\Controllers;

// Module
use \Modules\Appointments\Components\AppointmentsComponent;
use \Modules\Appointments\Components\DoctorsComponent;
use \Modules\Appointments\Models\DoctorClinics;
use \Modules\Appointments\Models\Doctors;
use \Modules\Appointments\Models\Clinics;
use \Modules\Appointments\Models\WorkingHours;

// Framework
use \A,
    \CAuth,
    \CArray,
    \CFile,
    \CImage,
    \CWidget,
    \CHash,
    \CTime,
    \CValidator,
    \CController,
    \CConfig,
	\CDatabase;

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



class DoctorClinicsController extends CController
{
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

        if(CAuth::isLoggedInAsAdmin()){
            // set meta tags according to active doctors
            Website::setMetaTags(array('title'=>A::t('appointments', 'Doctors Management')));

            $this->_view->tabs = AppointmentsComponent::prepareTab('doctors');
        }
    }

    /**
     * Manage images action handler
	 * @param int $doctorId
     * @return void
     */
    public function manageAction($doctorId = 0)
    {
        // set backend mode
        Website::setBackend();
        Website::prepareBackendAction('manage', 'doctor', 'doctors/manage');

        $addDoctorClinics = '';
        $doctor = $this->_checkDoctorAccess($doctorId, 'doctors/manage');

        $configModule = \CLoader::config('appointments', 'main');
        $multiClinics = $configModule['multiClinics'];
        if(!$multiClinics) $this->redirect('doctors/manage/');

        $actionMessage = '';
        $alert = A::app()->getSession()->getFlash('alert');
        $alertType = A::app()->getSession()->getFlash('alertType');

        if(!empty($alert)){
            $actionMessage = CWidget::create('CMessage', array($alertType, $alert, array('button'=>true)));
        }

        $clinicsCount = Clinics::model()->count('is_active = 1');
        $clinicsNotHaveThisDoctor = $this->_getClinics($doctor->id);
        if($clinicsCount >= 1 && empty($clinicsNotHaveThisDoctor)){
            $message = A::t('appointments', 'This doctor is attached to all clinics!');
            $addDoctorClinics = CWidget::create('CMessage', array('warning', $message, array('button'=>true)));
        }

        $this->_view->checkUploadClinicsAccess = $this->_checkUploadClinicsCountAccess($doctor->id, $doctor->membership_clinics_count);
        $this->_view->dateFormat = $this->_settings->date_format;
        $this->_view->actionMessage = $actionMessage;
        $this->_view->doctorId = $doctor->id;
        $this->_view->doctorName = $doctor->getFullName();
        $this->_view->addDoctorClinics = $addDoctorClinics;
        $this->_view->render('doctorClinics/manage');
    }

    /**
     * Add doctor clinics action handler
	 * @param int $doctorId
     * @return void
     */
    public function addAction($doctorId = 0)
    {
        // set backend mode
        Website::setBackend();
        Website::prepareBackendAction('manage', 'doctor', 'doctorClinics/manage/doctorId/'.$doctorId);
        $doctor = $this->_checkDoctorAccess($doctorId, 'doctors/manage');
        $this->_checkUploadClinicsCountAccess($doctor->id, $doctor->membership_clinics_count, 'doctorClinics/manage/doctorId/'.$doctorId);

        $clinicsCount = Clinics::model()->count('is_active = 1');
        $clinics = $this->_getClinics($doctor->id);
        if($clinicsCount >= 1 && empty($clinics)){
            $this->redirect('doctorClinics/manage/doctorId/'.$doctor->id);
        }

        $this->_view->clinics = $clinics;
        $this->_view->doctorId = $doctor->id;
        $this->_view->doctorName = $doctor->getFullName();

        $this->_view->render('doctorClinics/add');
    }

    /**
     * Edit doctor clinics action handler
     * @param int $doctorId
     * @param int $id
     * @return void
     */
    public function editAction($doctorId = 0, $id = 0)
    {
        // set backend mode
        Website::setBackend();
        Website::prepareBackendAction('manage', 'doctor', 'doctorClinics/manage/doctorId/'.$doctorId);
        $doctor = $this->_checkDoctorAccess($doctorId, 'doctors/manage');
        $doctorClinic = $this->_checkDoctorClinicAccess($id, $doctorId, 'doctorClinics/manage/doctorId/'.$doctorId);

        $this->_view->clinics = $this->_getClinics($doctor->id);
        $this->_view->doctorClinic = $doctorClinic;
        $this->_view->id = $doctorClinic->id;
        $this->_view->doctorId = $doctor->id;
        $this->_view->doctorName = $doctor->getFullName();
        $this->_view->render('doctorClinics/edit');
    }

    /**
     * Delete specialty action handler
     * @param int $doctorId
     * @param int $id
     * @param int $page
     * @return void
     */
    public function deleteAction($doctorId = 0, $id = 0, $page = 0)
    {
        // set backend mode
        Website::setBackend();
        Website::prepareBackendAction('manage', 'doctor', 'doctorClinics/manage/doctorId/'.$doctorId);
        $doctor = $this->_checkDoctorAccess($doctorId, 'doctors/manage');
        $doctorClinic = $this->_checkDoctorClinicAccess($id, $doctorId, 'doctorClinics/manage/doctorId/'.$doctorId);

        $alert = '';
        $alertType = '';
        $actionMessage = '';

        if($doctorClinic->delete()){
            if($doctorClinic->getError()){
                $alert = $doctorClinic->getErrorMessage();
                $alert = empty($alert) ? A::t('app', 'Delete Error Message') : $alert;
                $alertType = 'warning';
            }else{
                $alert = A::t('app', 'Delete Success Message');
                $alertType = 'success';
            }
        }else{
            if(APPHP_MODE == 'demo'){
                $alert = CDatabase::init()->getErrorMessage();
                $alertType = 'warning';
            }else{
                $alert = $doctorClinic->getError() ? $doctorClinic->getErrorMessage() : A::t('app', 'Delete Error Message');
                $alertType = 'error';
            }
        }

        if(!empty($alert)){
            A::app()->getSession()->setFlash('alert', $alert);
            A::app()->getSession()->setFlash('alertType', $alertType);
        }

        $this->redirect('doctorClinics/manage/doctorId/'.$doctorId.(!empty($page) ? '?page='.(int)$page : ''));
    }

    /**
     * Manage doctor clinics action handler
     * @return void
     */
    public function myClinicsAction()
    {
        // Set frontend mode
        Website::setFrontend();
        // Block access to this controller for not-logged doctors
        CAuth::handleLogin('doctors/login', 'doctor');

        $doctorId = CAuth::getLoggedRoleId();
        $addDoctorClinics = '';
        $doctor = $this->_checkDoctorAccess($doctorId, 'doctors/logout');

        $actionMessage = '';
        $alert = A::app()->getSession()->getFlash('alert');
        $alertType = A::app()->getSession()->getFlash('alertType');

        if(!empty($alert)){
            $actionMessage = CWidget::create('CMessage', array($alertType, $alert, array('button'=>true)));
        }

        $clinicsCount = Clinics::model()->count('is_active = 1');
        $clinicsNotHaveThisDoctor = $this->_getClinics($doctor->id);
        if($clinicsCount >= 1 && empty($clinicsNotHaveThisDoctor)){
            $message = A::t('appointments', 'This doctor is attached to all clinics!');
            $addDoctorClinics = CWidget::create('CMessage', array('warning', $message, array('button'=>true)));
        }

        $this->_view->checkAccessAccountUsingMembershipPlan = DoctorsComponent::checkAccessAccountUsingMembershipPlan(false);
        $this->_view->checkUploadClinicsAccess = $this->_checkUploadClinicsCountAccess($doctor->id, $doctor->membership_clinics_count);
        $this->_view->dateFormat = $this->_settings->date_format;
        $this->_view->actionMessage = $actionMessage;
        $this->_view->doctorId = $doctorId;
        $this->_view->doctorName = $doctor->getFullName();
        $this->_view->addDoctorClinics = $addDoctorClinics;
        $this->_view->render('doctorClinics/myClinics');
    }

    /**
     * Add doctor clinic action handler
     * @return void
     */
    public function addMyClinicAction()
    {
        // block access to this controller for doctors without membership plan or expired membership plan
        DoctorsComponent::checkAccessAccountUsingMembershipPlan();

        // Set frontend mode
        Website::setFrontend();
        // Block access to this controller for not-logged doctors
        CAuth::handleLogin('doctors/login', 'doctor');

        $doctorId = CAuth::getLoggedRoleId();
        $doctor = $this->_checkDoctorAccess($doctorId, 'doctors/logout');

		$this->_checkUploadClinicsCountAccess($doctor->id, $doctor->membership_clinics_count, 'doctorClinics/myClinics');
        $clinics = $this->_getClinics($doctor->id);

        $this->_view->clinics = $clinics;
        $this->_view->doctorId = $doctorId;
        $this->_view->doctorName = $doctor->getFullName();

        $this->_view->render('doctorClinics/addMyClinic');
    }

    /**
     * Edit image action handler
     * @param int $id
     * @return void
     */
    public function editMyClinicAction($id = 0)
    {
        // block access to this controller for doctors without membership plan or expired membership plan
        DoctorsComponent::checkAccessAccountUsingMembershipPlan();

        // Set frontend mode
        Website::setFrontend();
        // Block access to this controller for not-logged doctors
        CAuth::handleLogin('doctors/login', 'doctor');

        $doctorId = CAuth::getLoggedRoleId();
        $doctor = $this->_checkDoctorAccess($doctorId, 'doctors/logout');
        $doctorClinic = $this->_checkDoctorClinicAccess($id, $doctorId, 'doctorClinics/myClinics');

        $this->_view->imageMaxSize = ModulesSettings::model()->param('appointments', 'image_max_size');
        $this->_view->id = $doctorClinic->id;
        $this->_view->doctorId = $doctorId;
        $this->_view->doctorName = $doctor->getFullName();
        $this->_view->render('doctorClinics/editMyClinic');
    }

    /**
     * Delete specialty action handler
     * @param int $id
     * @param int $page
     * @return void
     */
    public function deleteMyClinicAction($id = 0, $page = 0)
    {
        // block access to this controller for doctors without membership plan or expired membership plan
        DoctorsComponent::checkAccessAccountUsingMembershipPlan();

        // Set frontend mode
        Website::setFrontend();
        // Block access to this controller for not-logged doctors
        CAuth::handleLogin('doctors/login', 'doctor');

        $doctorId = CAuth::getLoggedRoleId();
        $doctor = $this->_checkDoctorAccess($doctorId, 'doctors/logout');
        $doctorClinic = $this->_checkDoctorClinicAccess($id, $doctorId, 'doctorClinics/myClinics');

        $alert = '';
        $alertType = '';
        $actionMessage = '';


        if($doctorClinic->delete()){
            if($doctorClinic->getError()){
                $alert = $doctorClinic->getErrorMessage();
                $alert = empty($alert) ? A::t('app', 'Delete Error Message') : $alert;
                $alertType = 'warning';
            }else{
                $alert = A::t('app', 'Delete Success Message');
                $alertType = 'success';
            }
        }else{
            if(APPHP_MODE == 'demo'){
                $alert = CDatabase::init()->getErrorMessage();
                $alertType = 'warning';
            }else{
                $alert = $doctorClinic->getError() ? $doctorClinic->getErrorMessage() : A::t('app', 'Delete Error Message');
                $alertType = 'error';
            }
        }

        if(!empty($alert)){
            A::app()->getSession()->setFlash('alert', $alert);
            A::app()->getSession()->setFlash('alertType', $alertType);
        }

        $this->redirect('doctorClinics/myClinics'.(!empty($page) ? '?page='.(int)$page : ''));
    }

    /**
     * Check if passed Doctor ID is valid
     * @param int $id
     * @param string $redirect
     * @return bool|object Doctors
     */
    private function _checkDoctorAccess($id = 0, $redirect = 'doctors/manage')
    {
        if(empty($id)){
            $this->redirect($redirect);
        }

        $doctor = Doctors::model()->findByPk($id);
        if(!$doctor){
            $this->redirect($redirect);
        }
        return $doctor;
    }

    /**
     * Check Doctor Clinics is valid
     * @param int $id
     * @param int $doctorId
     * @param string $redirect
     * @return bool|object
     */
    private function _checkDoctorClinicAccess($id = 0, $doctorId = 0, $redirect = '')
    {
        if(empty($id) && empty($doctorId)){
            return false;
        }
        if(empty($redirect)){
            $redirect = 'doctorClinics/manage/doctorId/'.$doctorId;
        }

        $doctorClinic =  DoctorClinics::model()->findByPk($id, 'doctor_id = :doctor_id', array(':doctor_id'=>$doctorId));
        if(!$doctorClinic){
            $this->redirect($redirect);
        }
        return $doctorClinic;
    }

    /**
     * Check access to upload clinics count
     * @param int $doctorId
     * @param int $membershipClinicsCount
     * @param string $redirect
     * @return bool
     */
    private function _checkUploadClinicsCountAccess($doctorId = 0, $membershipClinicsCount = 0, $redirect = '')
    {
    	if(empty($membershipClinicsCount) && empty($redirect)){
    		return false;
		}

        $configModule = \CLoader::config('appointments', 'main');
        $multiClinics = $configModule['multiClinics'];
        if(!$multiClinics){
            $this->redirect('doctors/manage');
        }

        $result = true;
    	$countClinics = DoctorClinics::model()->count('doctor_id = '.$doctorId);
        if($countClinics >= $membershipClinicsCount){
        	if(!empty($redirect)){
				$this->redirect($redirect);
			}else{
				$result = false;
			}
        }

        return $result;
    }

    /**
     * Get active clinics. Deleted the clinics to which the doctor is attached.
     * @param int $doctorId
     * @return array
     */
    private function _getClinics($doctorId = 0)
    {
        $clinics = array();

        $clinicsArr = Clinics::model()->findAll(array('is_active = 1', 'orderBy'=>'sort_order DESC'));
        if(!empty($clinicsArr)){
            foreach($clinicsArr as $clinic){
                $clinics[$clinic['id']] = $clinic['clinic_name'].($clinic['address'] ? ', '.$clinic['address'] : '');
            }
        }
        $doctorClinics = DoctorClinics::model()->findAll('doctor_id = :doctor_id', array(':doctor_id'=>$doctorId));
        if(!empty($doctorClinics)){
            foreach($doctorClinics as $doctorClinic){
                if(isset($clinics[$doctorClinic['clinic_id']])){
                    unset($clinics[$doctorClinic['clinic_id']]);
                }
            }
        }

        return $clinics;
    }
}
