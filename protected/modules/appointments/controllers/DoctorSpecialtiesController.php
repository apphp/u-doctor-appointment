<?php
/**
 * Doctors controller
 * This controller intended to both Backend and Frontend modes
 *
 * PUBLIC:                          PRIVATE
 * -----------                      ------------------
 * __construct                      _checkDoctorAccess
 * manageAction                     _checkSpecialtyAccess
 * addAction                        _prepareSpecialties
 * editAction                       _getSelectedSpecialtyIds
 * deleteAction                     _checkDoctorFrontendAccess
 * mySpecialtiesAction              _checkSpecialtyFrontendAccess
 * addMySpecialtyAction             _checkUploadSpecialtiesCountAccess
 * editMySpecialtyAction
 * deleteMySpecialtyAction
 */

namespace Modules\Appointments\Controllers;

// Module
use \Modules\Appointments\Components\AppointmentsComponent;
use \Modules\Appointments\Components\DoctorsComponent;
use \Modules\Appointments\Models\Doctors;
use \Modules\Appointments\Models\DoctorSpecialties;
use \Modules\Appointments\Models\Specialties;

// Framework
use \A,
    \CAuth,
    \CWidget,
    \CController,
    \CDatabase;

// Application
use \Website,
    \Bootstrap,
    \Modules;



class DoctorSpecialtiesController extends CController
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
     * Manage specialties action handler
     * @return void
     */
    public function manageAction($doctorId = 0)
    {
        // set backend mode
        Website::setBackend();
        Website::prepareBackendAction('manage', 'doctor', 'doctors/manage');
        $doctor = $this->_checkDoctorAccess($doctorId);

        $actionMessage = '';
        $alert = A::app()->getSession()->getFlash('alert');
        $alertType = A::app()->getSession()->getFlash('alertType');

        if(!empty($alert)){
            $actionMessage = CWidget::create('CMessage', array($alertType, $alert, array('button'=>true)));
        }

        $this->_view->actionMessage = $actionMessage;
        $this->_view->checkUploadSpecialtiesCountAccess = $this->_checkUploadSpecialtiesCountAccess($doctor->id, $doctor->membership_specialties_count);
        $this->_view->doctorId = $doctor->id;
        $this->_view->doctorName = $doctor->doctor_first_name.' '.$doctor->doctor_last_name;
        $this->_view->render('doctorSpecialties/manage');
    }

    /**
     * Add specialty action handler
     * @param int $doctorId
     * @return void
     */
    public function addAction($doctorId = 0)
    {
        // set backend mode
        Website::setBackend();
        Website::prepareBackendAction('manage', 'doctor', 'doctorSpecialties/manage/doctorId/'.$doctorId);
        $doctor = $this->_checkDoctorAccess($doctorId);

        $specialtyOptions = array();
        $selectedSpecialtyIds = $this->_getSelectedSpecialtyIds($doctorId);
        foreach($selectedSpecialtyIds as $specialtyId){
            $specialtyOptions[$specialtyId] = array('disabled'=>'disabled');
        }

        $checkUploadSpecialtiesCountAccess = $this->_checkUploadSpecialtiesCountAccess($doctor->id, $doctor->membership_specialties_count, 'doctorSpecialties/manage/doctorId/'.$doctor->id);

        $this->_view->specialties = $this->_prepareSpecialties();
        $this->_view->specialtyOptions = $specialtyOptions;
        $this->_view->doctorId = $doctor->id;
        $this->_view->doctorName = $doctor->doctor_first_name.' '.$doctor->doctor_last_name;
        $this->_view->render('doctorSpecialties/add');
    }

    /**
     * Edit specialty action handler
     * @param int $doctorId
     * @param int $id
     * @return void
     */
    public function editAction($doctorId = 0, $id = 0)
    {
        // set backend mode
        Website::setBackend();
        Website::prepareBackendAction('manage', 'doctor', 'doctorSpecialties/manage/doctorId/'.$doctorId);
        $doctor = $this->_checkDoctorAccess($doctorId);
        $specialty = $this->_checkSpecialtyAccess($id, $doctorId);

        $specialtyOptions = array();
        $selectedSpecialtyIds = $this->_getSelectedSpecialtyIds($doctorId);
        foreach($selectedSpecialtyIds as $specialtyId){
            if($specialtyId != $specialty->specialty_id){
                $specialtyOptions[$specialtyId] = array('disabled'=>'disabled');
            }
        }

        $this->_view->specialties = $this->_prepareSpecialties();
        $this->_view->specialtyOptions = $specialtyOptions;
        $this->_view->specialty = $specialty;
        $this->_view->doctorId = $doctor->id;
        $this->_view->doctorName = $doctor->doctor_first_name.' '.$doctor->doctor_last_name;
        $this->_view->render('doctorSpecialties/edit');
    }

    /**
     * Delete specialty action handler
     * @param int $doctorId
     * @param int $id
     * @return void
     */
    public function deleteAction($doctorId = 0, $id = 0)
    {
        // set backend mode
        Website::setBackend();
        Website::prepareBackendAction('manage', 'doctor', 'doctorSpecialties/manage/doctorId/'.$doctorId);
        $doctor = $this->_checkDoctorAccess($doctorId);
        $specialty = $this->_checkSpecialtyAccess($id, $doctorId);

        $alert = '';
        $alertType = '';
        $actionMessage = '';

        if($specialty->is_default){
            $alertType = 'warning';
            $alert = A::t('appointments', 'You cannot delete the specialty by default');
        }else{
            if($specialty->delete()){
                $alert = A::t('appointments', 'Specialty deleted successfully');
                $alertType = 'success';
            }else{
                if(APPHP_MODE == 'demo'){
                    $alert = CDatabase::init()->getErrorMessage();
                    $alertType = 'warning';
                }else{
                    $alert = A::t('appointments', 'Specialty deleting error');
                    $alertType = 'error';
                }
            }
        }

        if(!empty($alert)){
            A::app()->getSession()->setFlash('alert', $alert);
            A::app()->getSession()->setFlash('alertType', $alertType);
        }

        $this->redirect('doctorSpecialties/manage/doctorId/'.$doctorId);
    }

    /**
     * Manage specialties action handler
     * @return void
     */
    public function mySpecialtiesAction()
    {
        // Set frontend mode
        Website::setFrontend();
        // Block access to this controller for not-logged doctors
        CAuth::handleLogin('doctors/login', 'doctor');

        $doctorId = CAuth::getLoggedRoleId();
        $doctor = $this->_checkDoctorFrontendAccess($doctorId);

        $actionMessage = '';
        $alert = A::app()->getSession()->getFlash('alert');
        $alertType = A::app()->getSession()->getFlash('alertType');

        if(!empty($alert)){
            $actionMessage = CWidget::create('CMessage', array($alertType, $alert, array()));
        }

        $this->_view->dateFormat = $this->_settings->date_format;
        $this->_view->checkUploadSpecialtiesCountAccess = $this->_checkUploadSpecialtiesCountAccess($doctor->id, $doctor->membership_specialties_count);
        $this->_view->checkAccessAccountUsingMembershipPlan = DoctorsComponent::checkAccessAccountUsingMembershipPlan(false);
        $this->_view->actionMessage = $actionMessage;
        $this->_view->doctorId = $doctorId;
        $this->_view->doctorName = $doctor->doctor_first_name.' '.$doctor->doctor_last_name;
        $this->_view->render('doctorSpecialties/mySpecialties');
    }

    /**
     * Add specialty action handler
     * @return void
     */
    public function addMySpecialtyAction()
    {
		// block access to this controller for doctors without membership plan or expired membership plan
		DoctorsComponent::checkAccessAccountUsingMembershipPlan();
		
        // Set frontend mode
        Website::setFrontend();
        // Block access to this controller for not-logged doctors
        CAuth::handleLogin('doctors/login', 'doctor');

        $doctorId = CAuth::getLoggedRoleId();
        $doctor = $this->_checkDoctorFrontendAccess($doctorId);

        $checkUploadSpecialtiesCountAccess = $this->_checkUploadSpecialtiesCountAccess($doctor->id, $doctor->membership_specialties_count, 'doctorSpecialties/mySpecialties/doctorId/'.$doctor->id);

        $specialtyOptions = array();
        $selectedSpecialtyIds = $this->_getSelectedSpecialtyIds($doctorId);
        foreach($selectedSpecialtyIds as $specialtyId){
            $specialtyOptions[$specialtyId] = array('disabled'=>'disabled');
        }
        $this->_view->specialties = $this->_prepareSpecialties();
        $this->_view->specialtyOptions = $specialtyOptions;
        $this->_view->doctorId = $doctorId;
        $this->_view->doctorName = $doctor->doctor_first_name.' '.$doctor->doctor_last_name;
        $this->_view->render('doctorSpecialties/addMySpecialty');
    }

    /**
     * Edit specialty action handler
     * @param int $id
     * @return void
     */
    public function editMySpecialtyAction($id = 0)
    {

		// block access to this controller for doctors without membership plan or expired membership plan
		DoctorsComponent::checkAccessAccountUsingMembershipPlan();
		
        // Set frontend mode
        Website::setFrontend();
        // Block access to this controller for not-logged doctors
        CAuth::handleLogin('doctors/login', 'doctor');

        $doctorId = CAuth::getLoggedRoleId();
        $doctor = $this->_checkDoctorFrontendAccess($doctorId);
        $specialty = $this->_checkSpecialtyFrontendAccess($id);

        $specialtyOptions = array();
        $selectedSpecialtyIds = $this->_getSelectedSpecialtyIds($doctorId);
        foreach($selectedSpecialtyIds as $specialtyId){
            if($specialtyId != $specialty->specialty_id){
                $specialtyOptions[$specialtyId] = array('disabled'=>'disabled');
            }
        }
        $this->_view->specialties = $this->_prepareSpecialties();
        $this->_view->specialtyOptions = $specialtyOptions;

        $this->_view->id = $specialty->id;
        $this->_view->doctorId = $doctorId;
        $this->_view->doctorName = $doctor->doctor_first_name.' '.$doctor->doctor_last_name;
        $this->_view->render('doctorSpecialties/editMySpecialty');
    }

    /**
     * Delete specialty action handler
     * @param int $id
     * @return void
     */
    public function deleteMySpecialtyAction($id = 0)
    {

		// block access to this controller for doctors without membership plan or expired membership plan
		DoctorsComponent::checkAccessAccountUsingMembershipPlan();
		

        // Set frontend mode
        Website::setFrontend();
        // Block access to this controller for not-logged doctors
        CAuth::handleLogin('doctors/login', 'doctor');

        $doctorId = CAuth::getLoggedRoleId();
        $doctor = $this->_checkDoctorFrontendAccess($doctorId);
        $specialty = $this->_checkSpecialtyFrontendAccess($id);

        $alert = '';
        $alertType = '';
        $actionMessage = '';

		if($specialty->is_default){
			$alertType = 'warning';
			$alert = A::t('appointments', 'You cannot delete the specialty by default');
		}else{
			if($specialty->delete()){
				$alert = A::t('appointments', 'Schedule deleted successfully');
				$alertType = 'success';
			}else{
				if(APPHP_MODE == 'demo'){
					$alert = CDatabase::init()->getErrorMessage();
					$alertType = 'warning';
				}else{
					$alert = A::t('appointments', 'Schedule deleting error');
					$alertType = 'error';
				}
			}
		}

        if(!empty($alert)){
            A::app()->getSession()->setFlash('alert', $alert);
            A::app()->getSession()->setFlash('alertType', $alertType);
        }

        $this->redirect('doctorSpecialties/mySpecialties');
    }

    /**
     * Check if passed Doctor ID is valid
     * @param int $id
     * @return Doctors
     */
    private function _checkDoctorAccess($id = 0)
    {
        $doctor = Doctors::model()->findByPk($id);
        if(!$doctor){
            $this->redirect('doctors/manage');
        }
        return $doctor;
    }

    /**
     * Check Specialty is valid
     * @param int $id
     * @param int $doctorId
     * @return DoctorSpecialties
     */
    private function _checkSpecialtyAccess($id = 0, $doctorId = 0)
    {
        $specialty = DoctorSpecialties::model()->findByPk($id, 'doctor_id = :doctor_id', array(':doctor_id'=>$doctorId));
        if(!$specialty){
            $this->redirect('doctorSpecialties/manage/doctorId/'.$doctorId);
        }
        return $specialty;
    }

    /**
     * Check if passed Doctor ID is valid
     * @param int $id
     * @return Doctors
     */
    private function _checkDoctorFrontendAccess($id = 0)
    {
        $doctor = Doctors::model()->findByPk($id);
        if(!$doctor){
            $this->redirect('doctors/logout');
        }
        return $doctor;
    }

    /**
     * Check Specialty is valid for frontend
     * @param int $id
     * @param int $doctorId
     * @return DoctorSpecialties
     */
    private function _checkSpecialtyFrontendAccess($id = 0)
    {
        $doctorId = CAuth::getLoggedRoleId();
        $specialty = DoctorSpecialties::model()->findByPk($id, 'doctor_id = :doctor_id', array(':doctor_id'=>$doctorId));
        if(!$specialty){
            $this->redirect('doctorSpecialties/mySpecialties');
        }
        return $specialty;
    }

    /**
     * Check access to upload specialties count
     * @param int $doctorId
     * @param int $membershipSpecialtiesCount
     * @param string $redirect
     * @return bool
     */
    private function _checkUploadSpecialtiesCountAccess($doctorId = 0, $membershipSpecialtiesCount = 0, $redirect = '')
    {
        if(empty($membershipSpecialtiesCount) && empty($redirect)){
            return false;
        }

        $result = true;

        $countSpecialties = DoctorSpecialties::model()->count('doctor_id = '.$doctorId);
        if($countSpecialties >= $membershipSpecialtiesCount){
            if(!empty($redirect)){
                $this->redirect($redirect);
            }else{
                $result = false;
            }
        }

        return $result;
    }

    /**
     * Prepare specialties
     * @return array
     */
    private function _prepareSpecialties()
    {
        $specialties = array();
        $result = Specialties::model()->findAll();

        if(!empty($result)){
            foreach($result as $key => $specialty){
                $specialties[$specialty['id']] = $specialty['name'];
            }
        }

        return $specialties;
    }


    /**
     * Prepare selected specialties
     * @param int $doctorId
     * @return array
     */
    private function _getSelectedSpecialtyIds($doctorId)
    {
        $selectedSpecialtyIds = array();
        $selectedSpecialties = DoctorSpecialties::model()->findAll('doctor_id = :doctor_id', array(':doctor_id'=>$doctorId));

        if(!empty($selectedSpecialties)){
            foreach($selectedSpecialties as $key => $specialty){
                $selectedSpecialtyIds[] = $specialty['specialty_id'];
            }
        }

        return $selectedSpecialtyIds;
    }
}
