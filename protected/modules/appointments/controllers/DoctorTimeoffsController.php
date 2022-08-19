<?php
/**
 * Doctors controller
 * This controller intended to both Backend and Frontend modes
 *
 * PUBLIC:                          PRIVATE
 * -----------                      ------------------
 * __construct                      _checkDoctorAccess
 * indexAction                      _checkTimeoffAccess
 * manageAction                     _checkDoctorFrontendAccess
 * addAction                        _checkTimeoffFrontendAccess
 * editAction
 * deleteAction
 * myTimeoffsAction
 * addMyTimeoffAction
 * editMyTimeoffAction
 * deleteMyTimeoffAction
 *
 */

namespace Modules\Appointments\Controllers;

// Module
use \Modules\Appointments\Components\AppointmentsComponent;
use \Modules\Appointments\Components\DoctorsComponent;
use \Modules\Appointments\Models\DoctorTimeoffs;
use \Modules\Appointments\Models\Doctors;

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



class DoctorTimeoffsController extends CController
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
     * Manage timeoffs action handler
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

        $this->_view->dateFormat = $this->_settings->date_format;
        $this->_view->timeFormat = $this->_settings->time_format;
        $this->_view->actionMessage = $actionMessage;
        $this->_view->doctorId = $doctor->id;
        $this->_view->doctorName = $doctor->doctor_first_name.' '.$doctor->doctor_last_name;
        $this->_view->render('doctorTimeoffs/manage');
    }

    /**
     * Add timeoff action handler
     * @return void
     */
    public function addAction($doctorId = 0)
    {
        // set backend mode
        Website::setBackend();
        Website::prepareBackendAction('manage', 'doctor', 'doctorTimeoffs/manage/doctorId/'.$doctorId);
        $doctor = $this->_checkDoctorAccess($doctorId);

        $minTime = '';
        if(A::app()->getRequest()->getPost('APPHP_FORM_ACT', 'string', '') == 'send'){
            $minDate = A::app()->getRequest()->getPost('date_from');
            if($minDate == A::app()->getRequest()->getPost('date_to')){
                $minTime = A::app()->getRequest()->getPost('time_from');
            }
        }else{
            $minDate = '';
        }

        $this->_view->timeFormat = $this->_settings->time_format;
        $this->_view->doctorId = $doctor->id;
        $this->_view->minDate = $minDate;
        $this->_view->minTime = $minTime;
        $this->_view->doctorName = $doctor->doctor_first_name.' '.$doctor->doctor_last_name;
        $this->_view->render('doctorTimeoffs/add');
    }

    /**
     * Edit timeoff action handler
     * @return void
     */
    public function editAction($doctorId = 0, $id = 0)
    {
        // set backend mode
        Website::setBackend();
        Website::prepareBackendAction('manage', 'doctor', 'doctorTimeoffs/manage/doctorId/'.$doctorId);
        $doctor = $this->_checkDoctorAccess($doctorId);
        $timeoff = $this->_checkTimeoffAccess($id, $doctorId);

        if(A::app()->getRequest()->getPost('APPHP_FORM_ACT', 'string', '') == 'send'){
            $minDate = A::app()->getRequest()->getPost('date_from');
        }else{
            $minDate = '';
        }

        $this->_view->timeFormat = $this->_settings->time_format;
        $this->_view->id = $timeoff->id;
        $this->_view->doctorId = $doctor->id;
        $this->_view->minDate = $minDate;
        $this->_view->doctorName = $doctor->doctor_first_name.' '.$doctor->doctor_last_name;
        $this->_view->render('doctorTimeoffs/edit');
    }

    /**
     * Delete timeoff action handler
     * @param int $id
     * @return void
     */
    public function deleteAction($doctorId = 0, $id = 0)
    {
        // set backend mode
        Website::setBackend();
        Website::prepareBackendAction('manage', 'doctor', 'doctorTimeoffs/manage/doctorId/'.$doctorId);
        $doctor = $this->_checkDoctorAccess($doctorId);
        $timeoff = $this->_checkTimeoffAccess($id, $doctorId);

        $alert = '';
        $alertType = '';
        $actionMessage = '';

        if($timeoff->delete()){
            $alert = A::t('appointments', 'Timeoff deleted successfully');
            $alertType = 'success';
        }else{
            if(APPHP_MODE == 'demo'){
                $alert = CDatabase::init()->getErrorMessage();
                $alertType = 'warning';
            }else{
                $alert = A::t('appointments', 'Timeoff deleting error');
                $alertType = 'error';
            }
        }

        if(!empty($alert)){
            A::app()->getSession()->setFlash('alert', $alert);
            A::app()->getSession()->setFlash('alertType', $alertType);
        }

        $this->redirect('doctorTimeoffs/manage/doctorId/'.$doctorId);
    }

    /**
     * Manage timeoffs action handler
     * @return void
     */
    public function myTimeoffsAction()
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
        $this->_view->timeFormat = $this->_settings->time_format;
        $this->_view->checkAccessAccountUsingMembershipPlan = DoctorsComponent::checkAccessAccountUsingMembershipPlan(false);
        $this->_view->actionMessage = $actionMessage;
        $this->_view->doctorId = $doctor->id;
        $this->_view->doctorName = $doctor->doctor_first_name.' '.$doctor->doctor_last_name;
        $this->_view->render('doctorTimeoffs/myTimeoffs');
    }

    /**
     * Add timeoff action handler for frontend
     * @return void
     */
    public function addMyTimeoffAction()
    {
		// block access to this controller for doctors without membership plan or expired membership plan
		DoctorsComponent::checkAccessAccountUsingMembershipPlan();

        // Set frontend mode
        Website::setFrontend();
        // Block access to this controller for not-logged doctors
        CAuth::handleLogin('doctors/login', 'doctor');

        $doctorId = CAuth::getLoggedRoleId();
        $doctor = $this->_checkDoctorFrontendAccess($doctorId);

        $minTime = '';
        if(A::app()->getRequest()->getPost('APPHP_FORM_ACT', 'string', '') == 'send'){
            $minDate = A::app()->getRequest()->getPost('date_from');
            if($minDate == A::app()->getRequest()->getPost('date_to')){
                $minTime = A::app()->getRequest()->getPost('time_from');
            }
        }else{
            $minDate = '';
        }

        $this->_view->timeFormat = $this->_settings->time_format;
        $this->_view->doctorId = $doctor->id;
        $this->_view->minDate = $minDate;
        $this->_view->minTime = $minTime;
        $this->_view->doctorName = $doctor->doctor_first_name.' '.$doctor->doctor_last_name;
        $this->_view->render('doctorTimeoffs/addMytimeoff');
    }

    /**
     * Edit timeoff action handler for frontend
     * @param int $id
     * @return void
     */
    public function editMyTimeoffAction($id = 0)
    {
		// block access to this controller for doctors without membership plan or expired membership plan
		DoctorsComponent::checkAccessAccountUsingMembershipPlan();

        // Set frontend mode
        Website::setFrontend();
        // Block access to this controller for not-logged doctors
        CAuth::handleLogin('doctors/login', 'doctor');

        $doctorId = CAuth::getLoggedRoleId();
        $doctor = $this->_checkDoctorFrontendAccess($doctorId);
        $timeoff = $this->_checkTimeoffFrontendAccess($id);
        $timeFormat = $this->_settings->time_format;

        $minTime = '';
        if(A::app()->getRequest()->getPost('APPHP_FORM_ACT', 'string', '') == 'send'){
            $minDate = A::app()->getRequest()->getPost('date_from');
            if($minDate == A::app()->getRequest()->getPost('date_to')){
                $minTime = A::app()->getRequest()->getPost('time_from');
                $arrMinTime = CTime::dateParseFromFormat($timeFormat, $minTime);
            }
        }else{
            $minDate = '';
        }

        $this->_view->timeFormat = $timeFormat;
        $this->_view->id = $timeoff->id;
        $this->_view->doctorId = $doctor->id;
        $this->_view->minDate = $minDate;
        $this->_view->minTime = $minTime;
        $this->_view->doctorName = $doctor->doctor_first_name.' '.$doctor->doctor_last_name;
        $this->_view->render('doctorTimeoffs/editMytimeoff');
    }

    /**
     * Delete timeoff action handler
     * @param int $id
     * @return void
     */
    public function deleteMyTimeoffAction($id = 0)
    {
		// block access to this controller for doctors without membership plan or expired membership plan
		DoctorsComponent::checkAccessAccountUsingMembershipPlan();

        // Set frontend mode
        Website::setFrontend();
        // Block access to this controller for not-logged doctors
        CAuth::handleLogin('doctors/login', 'doctor');

        $doctorId = CAuth::getLoggedRoleId();
        $doctor = $this->_checkDoctorFrontendAccess($doctorId);
        $timeoff = $this->_checkTimeoffFrontendAccess($id);

        $alert = '';
        $alertType = '';
        $actionMessage = '';

        if($timeoff->delete()){
            $alert = A::t('appointments', 'Timeoff deleted successfully');
            $alertType = 'success';
        }else{
            if(APPHP_MODE == 'demo'){
                $alert = CDatabase::init()->getErrorMessage();
                $alertType = 'warning';
            }else{
                $alert = A::t('appointments', 'Timeoff deleting error');
                $alertType = 'error';
            }
        }

        if(!empty($alert)){
            A::app()->getSession()->setFlash('alert', $alert);
            A::app()->getSession()->setFlash('alertType', $alertType);
        }

        $this->redirect('doctorTimeoffs/myTimeoffs');
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
     * Check if passed Doctor ID is valid
     * @param int $id
     * @param int $doctorId
     * @return Timeoff
     */
    private function _checkTimeoffAccess($id = 0, $doctorId = 0)
    {
        $timeoff = DoctorTimeoffs::model()->findByPk($id, 'doctor_id = :doctor_id', array(':doctor_id'=>$doctorId));
        if(!$timeoff){
            $this->redirect('doctorTimeoffs/manage/doctorId/'.$doctorId);
        }
        return $timeoff;
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
            $this->redirect('doctors/dashboard');
        }
        return $doctor;
    }

    /**
     * Check if passed Doctor ID is valid
     * @param int $id
     * @return Timeoff
     */
    private function _checkTimeoffFrontendAccess($id = 0)
    {
        $doctorId = CAuth::getLoggedRoleId();
        $timeoff = DoctorTimeoffs::model()->findByPk($id, 'doctor_id = :doctor_id', array(':doctor_id'=>$doctorId));
        if(!$timeoff){
            $this->redirect('doctorTimeoffs/myTimeoffs');
        }
        return $timeoff;
    }
}
