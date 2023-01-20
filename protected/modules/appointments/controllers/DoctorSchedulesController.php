<?php
/**
 * Doctors controller
 * This controller intended to both Backend and Frontend modes
 *
 * PUBLIC:                          PRIVATE
 * -----------                      ------------------
 * __construct                      _checkDoctorAccess
 * indexAction                      _checkScheduleAccess
 * manageAction                     _checkTimeBlockAccess
 * addAction                        _checkDoctorFrontendAccess
 * editAction                       _checkScheduleFrontendAccess
 * deleteAction                     _checkUploadSchedulesCountAccess
 * manageTimeBlocks                 _checkTimeBlockFrontendAccess
 * addTimeBlockAction               _getTimeSlots
 * editTimeBlockAction              _getWeekDays
 * deleteTimeBlockAction            _prepareTimeBlockCounters
 * mySchedulesAction                _prepareCounters
 * addMyScheduleAction              _getDoctorClinics
 * editMyScheduleAction             _checkWorkingHoursClinic
 * deleteMyScheduleAction
 * changeStatusAction
 * activeFrontendStatusAction
 *
 */

namespace Modules\Appointments\Controllers;

// Module
use \Modules\Appointments\Components\AppointmentsComponent;
use \Modules\Appointments\Components\DoctorsComponent;
use \Modules\Appointments\Models\Doctors;
use \Modules\Appointments\Models\DoctorScheduleTimeBlocks;
use \Modules\Appointments\Models\TimeSlotsType;
use \Modules\Appointments\Models\DoctorSchedules;
use \Modules\Appointments\Models\Clinics;
use \Modules\Appointments\Models\DoctorClinics;
use \Modules\Appointments\Models\WorkingHours;

// Framework
use \A,
    \CAuth,
    \CWidget,
    \CController,
    \CConfig,
    \CDatabase;

// Application
use \Website,
    \Bootstrap,
    \Modules,
    \ModulesSettings;



class DoctorSchedulesController extends CController
{
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

        if(CAuth::isLoggedInAsAdmin()){
            // Set meta tags according to active doctors
            Website::setMetaTags(array('title'=>A::t('appointments', 'Doctor Schedules Management')));

            $this->_view->tabs = AppointmentsComponent::prepareTab('doctors');
        }
    }

    /**
     * Manage specialties action handler
     * @param int $doctorId
     * @return void
     */
    public function manageAction($doctorId = 0)
    {
        // Set backend mode
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
        $this->_view->checkUploadSchedulesCountAccess = $this->_checkUploadSchedulesCountAccess($doctor->id, $doctor->membership_schedules_count);
        $this->_view->actionMessage = $actionMessage;
        $this->_view->doctorId = $doctor->id;
        $this->_view->doctorName = $doctor->getFullName();
        $this->_view->timeBlockCounters = $this->_prepareTimeBlockCounters($doctorId);
        $this->_view->render('doctorSchedules/manage');
    }

    /**
     * Add specialty action handler
     * @param int $doctorId
     * @return void
     */
    public function addAction($doctorId = 0)
    {
        // Set backend mode
        Website::setBackend();
        Website::prepareBackendAction('manage', 'doctor', 'doctorSchedules/manage/doctorId/'.$doctorId);
        
        $doctor = $this->_checkDoctorAccess($doctorId);
        $checkUploadSchedulesCountAccess = $this->_checkUploadSchedulesCountAccess($doctor->id, $doctor->membership_schedules_count, 'doctorSchedules/manage/doctorId/'.$doctor->id);

        if(A::app()->getRequest()->getPost('APPHP_FORM_ACT', 'string', '') == 'send'){
            $minDate = A::app()->getRequest()->getPost('date_from');
        }else{
            $minDate = '';
        }

        $this->_view->doctorId = $doctor->id;
        $this->_view->minDate = $minDate;
        $this->_view->doctorName = $doctor->getFullName();
        $this->_view->render('doctorSchedules/add');
    }

    /**
     * Edit specialty action handler
     * @param int $doctorId
     * @param int $id
     * @return void
     */
    public function editAction($doctorId = 0, $id = 0)
    {
        // Set backend mode
        Website::setBackend();
        Website::prepareBackendAction('manage', 'doctor', 'doctorSchedules/manage/doctorId/'.$doctorId);
        
        $doctor = $this->_checkDoctorAccess($doctorId);
        $schedule = $this->_checkScheduleAccess($id, $doctorId);

        if(A::app()->getRequest()->getPost('APPHP_FORM_ACT', 'string', '') == 'send'){
            $minDate = A::app()->getRequest()->getPost('date_from');
        }else{
            $minDate = '';
        }

        $this->_view->id = $schedule->id;
        $this->_view->doctorId = $doctor->id;
        $this->_view->minDate = $minDate;
        $this->_view->doctorName = $doctor->getFullName();
        $this->_view->render('doctorSchedules/edit');
    }

    /**
     * Delete specialty action handler
     * @param int $doctorId
     * @param int $id
     * @return void
     */
    public function deleteAction($doctorId = 0, $id = 0)
    {
        // Set backend mode
        Website::setBackend();
        Website::prepareBackendAction('manage', 'doctor', 'doctorSchedules/manage/doctorId/'.$doctorId);
        
        $doctor = $this->_checkDoctorAccess($doctorId);
        $schedule = $this->_checkScheduleAccess($id, $doctorId);

        $alert = '';
        $alertType = '';
        $actionMessage = '';

        if($schedule->delete()){
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

        if(!empty($alert)){
            A::app()->getSession()->setFlash('alert', $alert);
            A::app()->getSession()->setFlash('alertType', $alertType);
        }

        $this->redirect('doctorSchedules/manage/doctorId/'.$doctor->id);
    }

    /**
     * Change status doctor action handler
     * @param int $id
     * @param int $page 	the page number
     * @return void
     */
    public function changeStatusAction($id, $page = 1)
    {
        // Set backend mode
        Website::setBackend();
        Website::prepareBackendAction('edit', 'doctor', 'doctorSchedules/manage/doctorId/'.$schedule->doctor_id);

        $schedule = DoctorSchedules::model()->findByPk($id);
        if(empty($schedule)){
            $this->redirect('doctors/manage');
        }

        if(DoctorSchedules::model()->updateByPk($id, array('is_active'=>($schedule->is_active == 1 ? '0' : '1')))){
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
        
        if(!empty($alert)){
            A::app()->getSession()->setFlash('alert', $alert);
            A::app()->getSession()->setFlash('alertType', $alertType);
        }
        
        $this->redirect('doctorSchedules/manage/doctorId/'.$schedule->doctor_id.(!empty($page) ? '?page='.(int)$page : 1));
    }

    /**
     * Manage specialties action handler
     * @param int $doctorId
     * @param int $scheduleId
     * @return void
     */
    public function manageTimeBlocksAction($doctorId = 0, $scheduleId = 0)
    {
        // Set backend mode
        Website::setBackend();
        Website::prepareBackendAction('manage', 'doctor', 'doctorSchedules/manage/doctorId/'.$doctorId);
        $doctor = $this->_checkDoctorAccess($doctorId);
        $schedule = $this->_checkScheduleAccess($scheduleId, $doctorId);

        $actionMessage = '';
        $messageWorkingHours = '';
        $alert = A::app()->getSession()->getFlash('alert');
        $alertType = A::app()->getSession()->getFlash('alertType');

        if(!empty($alert)){
            $actionMessage = CWidget::create('CMessage', array($alertType, $alert, array('button'=>true)));
        }

        $doctorClinics = array();
        $doctorClinicsTmp = DoctorClinics::model()->findAll('doctor_id = '.$doctor->id);
        foreach($doctorClinicsTmp as $doctorClinic){
            $doctorClinics[$doctorClinic['clinic_id']] = $doctorClinic['clinic_name'].(!empty($doctorClinic['clinic_address']) ? ', '.$doctorClinic['clinic_address'] : '');
        }

        $this->_view->dateTimeFormat        = $this->_settings->datetime_format;
        $this->_view->timeFormat            = $this->_settings->time_format;
        $this->_view->actionMessage         = $actionMessage;
        $this->_view->messageWorkingHours   = $this->_checkWorkingHoursClinic($schedule->id);
        $this->_view->doctorId              = $doctor->id;
        $this->_view->doctorName            = $doctor->getFullName();
        $this->_view->scheduleId            = $schedule->id;
        $this->_view->scheduleName          = $schedule->name;
        $this->_view->doctorClinics         = $doctorClinics;
        $this->_view->arrWeekDays           = $this->_getWeekDays();
        $this->_view->arrTimeSlots          = $this->_getTimeSlots();
        $this->_view->arrTimeSlotsType      = $this->_getTimeSlotsType();

        $this->_view->render('doctorSchedules/timeBlocks/manage');
    }

    /**
     * Add specialty action handler
     * @param int $doctorId
     * @param int $scheduleId
     * @return void
     */
    public function addTimeBlockAction($doctorId = 0, $scheduleId = 0)
    {
        // Set backend mode
        Website::setBackend();
        Website::prepareBackendAction('manage', 'doctor', 'doctorSchedules/manageTimeBlocks/doctorId/'.$doctorId.'/scheduleId/'.$scheduleId);
        $doctor = $this->_checkDoctorAccess($doctorId);
        $schedule = $this->_checkScheduleAccess($scheduleId, $doctorId);

        $configModule = \CLoader::config('appointments', 'main');
        $multiClinics = $configModule['multiClinics'];
        $cRequest = A::app()->getRequest();
        $clinicId = $cRequest->getPost('address_id', 'int', 0);

        if(A::app()->getRequest()->getPost('APPHP_FORM_ACT', 'string', '') == 'send'){
            $minDate = A::app()->getRequest()->getPost('date_from');
        }else{
            $minDate = '';
        }

        $this->_view->clinicId      = !empty($clinicId) ? $clinicId : 0;
        $this->_view->doctorClinics = $this->_getDoctorClinics($doctor->id);
        $this->_view->timeSlotsType = $this->_gettimeSlotsType();
        $this->_view->multiClinics  = $multiClinics;
        $this->_view->doctorId      = $doctor->id;
        $this->_view->minDate       = $minDate;
        $this->_view->doctorName    = $doctor->getFullName();
        $this->_view->scheduleId    = $schedule->id;
        $this->_view->scheduleName  = $schedule->name;
        $this->_view->arrWeekDays   = $this->_getWeekDays();
        $this->_view->arrTimeSlots  = $this->_getTimeSlots();
        $this->_view->timeFormat    = $this->_settings->time_format;
        $this->_view->appointmentTimeFormat = ModulesSettings::model()->param('appointments', 'time_format_appointment_time');
        $this->_view->render('doctorSchedules/timeBlocks/add');
    }

    /**
     * Edit specialty action handler
     * @param int $doctorId
     * @param int $scheduleId
     * @param int $id
     * @return void
     */
    public function editTimeBlockAction($doctorId = 0, $scheduleId = 0, $id = 0)
    {
        // Set backend mode
        Website::setBackend();
        Website::prepareBackendAction('manage', 'doctor', 'doctorSchedules/manageTimeBlocks/doctorId/'.$doctorId.'/scheduleId/'.$scheduleId);
        $actionMessage = '';
        $doctor = $this->_checkDoctorAccess($doctorId);
        $schedule = $this->_checkScheduleAccess($scheduleId, $doctorId);
        $scheduleTimeBlock = $this->_checkTimeBlockAccess($id, $doctorId, $scheduleId);
        $configModule = \CLoader::config('appointments', 'main');
        $multiClinics = $configModule['multiClinics'];
        $cRequest = A::app()->getRequest();
        $clinicId = $cRequest->getPost('address_id', 'int', 0);
        $weekDay = $cRequest->getPost('week_day', 'int', 0);

        if($cRequest->getPost('APPHP_FORM_ACT', 'string', '') == 'send'){
            $minDate = $cRequest->getPost('date_from');
        }else{
            $minDate = '';
        }

        if(empty($clinicId)){
            $actionMessage = $this->_checkWorkingHoursClinic($schedule->id, $id);
        }
        $this->_view->actionMessage = $actionMessage;
        $this->_view->doctorClinics = $this->_getDoctorClinics($doctor->id);
        $this->_view->timeSlotsType = $this->_gettimeSlotsType();
        $this->_view->multiClinics  = $multiClinics;
        $this->_view->id            = $scheduleTimeBlock->id;
        $this->_view->clinicId      = !empty($clinicId) ? $clinicId : $scheduleTimeBlock->address_id;
        $this->_view->weekDay       = !empty($weekDay) ? $weekDay : $scheduleTimeBlock->week_day;
        $this->_view->scheduleId    = $schedule->id;
        $this->_view->doctorId      = $doctor->id;
        $this->_view->minDate       = $minDate;
        $this->_view->doctorName    = $doctor->getFullName();
        $this->_view->scheduleId    = $schedule->id;
        $this->_view->scheduleName  = $schedule->name;
        $this->_view->arrWeekDays   = $this->_getWeekDays();
        $this->_view->arrTimeSlots  = $this->_getTimeSlots();
        $this->_view->timeFormat    = $this->_settings->time_format;
        $this->_view->appointmentTimeFormat = ModulesSettings::model()->param('appointments', 'time_format_appointment_time');
        $this->_view->render('doctorSchedules/timeBlocks/edit');
    }

    /**
     * Delete specialty action handler
     * @param int $doctorId
     * @param int $scheduleId
     * @param int $id
     * @return void
     */
    public function deleteTimeBlockAction($doctorId = 0, $scheduleId = 0, $id = 0)
    {
        // Set backend mode
        Website::setBackend();
        Website::prepareBackendAction('manage', 'doctor', 'doctorSchedules/manageTimeBlocks/doctorId/'.$doctorId.'/scheduleId/'.$scheduleId);
        $doctor = $this->_checkDoctorAccess($doctorId);
        $schedule = $this->_checkScheduleAccess($scheduleId, $doctorId);
        $scheduleTimeBlock = $this->_checkTimeBlockAccess($id, $doctorId, $scheduleId);

        $alert = '';
        $alertType = '';
        $actionMessage = '';

        if($scheduleTimeBlock->delete()){
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

        if(!empty($alert)){
            A::app()->getSession()->setFlash('alert', $alert);
            A::app()->getSession()->setFlash('alertType', $alertType);
        }

        $this->redirect('doctorSchedules/manageTimeBlocks/doctorId/'.$doctorId.'/scheduleId/'.$scheduleId);
    }

    /**
     * Manage specialties action handler
     * @return void
     */
    public function mySchedulesAction()
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
            $actionMessage = CWidget::create('CMessage', array($alertType, $alert, array('button'=>true)));
        }

        $this->_view->checkUploadSchedulesCountAccess = $this->_checkUploadSchedulesCountAccess($doctor->id, $doctor->membership_schedules_count);
        $this->_view->checkAccessAccountUsingMembershipPlan = DoctorsComponent::checkAccessAccountUsingMembershipPlan(false);
        $this->_view->dateFormat = $this->_settings->date_format;
        $this->_view->actionMessage = $actionMessage;
        $this->_view->doctorId = $doctorId;
        $this->_view->doctorName = $doctor->getFullName();
        $this->_view->timeBlockCounters = $this->_prepareTimeBlockCounters($doctorId);
        $this->_view->render('doctorSchedules/mySchedules');
    }

    /**
     * Add specialty action handler
     * @return void
     */
    public function addMyScheduleAction()
    {
        // block access to this controller for doctors without membership plan or expired membership plan
        DoctorsComponent::checkAccessAccountUsingMembershipPlan();

        // Set frontend mode
        Website::setFrontend();
        // Block access to this controller for not-logged doctors
        CAuth::handleLogin('doctors/login', 'doctor');

        $doctorId = CAuth::getLoggedRoleId();
        $doctor = $this->_checkDoctorFrontendAccess($doctorId);

        $checkUploadSchedulesCountAccess = $this->_checkUploadSchedulesCountAccess($doctor->id, $doctor->membership_schedules_count, 'doctorSchedules/mySchedules/doctorId/'.$doctor->id);

        if(A::app()->getRequest()->getPost('APPHP_FORM_ACT', 'string', '') == 'send'){
            $minDate = A::app()->getRequest()->getPost('date_from');
        }else{
            $minDate = '';
        }

        $this->_view->doctorId = $doctorId;
        $this->_view->minDate = $minDate;
        $this->_view->doctorName = $doctor->getFullName();
        $this->_view->render('doctorSchedules/addMySchedule');
    }

    /**
     * Edit specialty action handler
     * @param int $id
     * @return void
     */
    public function editMyScheduleAction($id = 0)
    {
        // block access to this controller for doctors without membership plan or expired membership plan
        DoctorsComponent::checkAccessAccountUsingMembershipPlan();

        // Set frontend mode
        Website::setFrontend();
        // Block access to this controller for not-logged doctors
        CAuth::handleLogin('doctors/login', 'doctor');

        $doctorId = CAuth::getLoggedRoleId();
        $doctor = $this->_checkDoctorFrontendAccess($doctorId);
        $schedule = $this->_checkScheduleFrontendAccess($id);

        if(A::app()->getRequest()->getPost('APPHP_FORM_ACT', 'string', '') == 'send'){
            $minDate = A::app()->getRequest()->getPost('date_from');
        }else{
            $minDate = '';
        }

        $this->_view->id = $schedule->id;
        $this->_view->doctorId = $doctorId;
        $this->_view->minDate = $minDate;
        $this->_view->doctorName = $doctor->getFullName();
        $this->_view->render('doctorSchedules/editMySchedule');
    }

    /**
     * Delete specialty action handler
     * @param int $id
     * @return void
     */
    public function deleteMyScheduleAction($id = 0)
    {
        // block access to this controller for doctors without membership plan or expired membership plan
        DoctorsComponent::checkAccessAccountUsingMembershipPlan();
        // Set frontend mode
        Website::setFrontend();
        // Block access to this controller for not-logged doctors
        CAuth::handleLogin('doctors/login', 'doctor');

        $doctorId = CAuth::getLoggedRoleId();
        $doctor = $this->_checkDoctorFrontendAccess($doctorId);
        $schedule = $this->_checkScheduleFrontendAccess($id);

        $alert = '';
        $alertType = '';
        $actionMessage = '';

        if($schedule->delete()){
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

        if(!empty($alert)){
            A::app()->getSession()->setFlash('alert', $alert);
            A::app()->getSession()->setFlash('alertType', $alertType);
        }

        $this->redirect('doctorSchedules/mySchedules');
    }

    /**
     * Change status doctor action handler (frontend)
     * @param int $id
     * @param int $page 	the page number
     * @return void
     */
    public function activeFrontendStatusAction($id, $page = 1)
    {
        // Block access to this controller for not-logged doctors
        CAuth::handleLogin('doctors/login', 'doctor');

        $schedule = DoctorSchedules::model()->findByPk($id);
        if(empty($schedule)){
            $this->redirect('doctorSchedules/mySchedules');
        }

        if(DoctorSchedules::model()->updateByPk($id, array('is_active'=>($schedule->is_active == 1 ? '0' : '1')))){
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
        
        if(!empty($alert)){
            A::app()->getSession()->setFlash('alert', $alert);
            A::app()->getSession()->setFlash('alertType', $alertType);
        }
        
        
        $this->redirect('doctorSchedules/mySchedules'.(!empty($page) ? '?page='.(int)$page : 1));
    }

    /**
     * Manage specialties action handler
     * @param int $scheduleId
     * @return void
     */
    public function myTimeBlocksAction($scheduleId = 0)
    {
        // Set frontend mode
        Website::setFrontend();
        // Block access to this controller for not-logged doctors
        CAuth::handleLogin('doctors/login', 'doctor');

        $doctorId = CAuth::getLoggedRoleId();
        $doctor = $this->_checkDoctorFrontendAccess($doctorId);
        $schedule = $this->_checkScheduleFrontendAccess($scheduleId);

        $actionMessage = '';
        $alert = A::app()->getSession()->getFlash('alert');
        $alertType = A::app()->getSession()->getFlash('alertType');

        if(!empty($alert)){
            $actionMessage = CWidget::create('CMessage', array($alertType, $alert, array()));
        }

        $this->_view->dateTimeFormat                        = $this->_settings->datetime_format;
        $this->_view->timeFormat                            = $this->_settings->time_format;
        $this->_view->actionMessage                         = $actionMessage;
        $this->_view->doctorId                              = $doctorId;
        $this->_view->checkAccessAccountUsingMembershipPlan = DoctorsComponent::checkAccessAccountUsingMembershipPlan(false);
        $this->_view->doctorName                            = $doctor->getFullName();
        $this->_view->scheduleId                            = $schedule->id;
        $this->_view->scheduleName                          = $schedule->name;
        $this->_view->arrWeekDays                           = $this->_getWeekDays();
        $this->_view->arrTimeSlots                          = $this->_getTimeSlots();
        $this->_view->arrTimeSlotsType                      = $this->_getTimeSlotsType();
        $this->_view->render('doctorSchedules/timeBlocks/myTimeBlocks');
    }

    /**
     * Add specialty action handler
     * @param int $scheduleId
     * @return void
     */
    public function addMyTimeBlockAction($scheduleId = 0)
    {
        // block access to this controller for doctors without membership plan or expired membership plan
        DoctorsComponent::checkAccessAccountUsingMembershipPlan();
        
        // Set frontend mode
        Website::setFrontend();
        // Block access to this controller for not-logged doctors
        CAuth::handleLogin('doctors/login', 'doctor');

        $doctorId = CAuth::getLoggedRoleId();
        $doctor = $this->_checkDoctorFrontendAccess($doctorId);
        $schedule = $this->_checkScheduleFrontendAccess($scheduleId);
        $cRequest = A::app()->getRequest();
        $clinicId = $cRequest->getPost('address_id', 'int', 0);

        if(A::app()->getRequest()->getPost('APPHP_FORM_ACT', 'string', '') == 'send'){
            $minDate = A::app()->getRequest()->getPost('date_from');
        }else{
            $minDate = '';
        }

        $configModule = \CLoader::config('appointments', 'main');
        $multiClinics = $configModule['multiClinics'];

        $this->_view->clinicId      = !empty($clinicId) ? $clinicId : 0;
        $this->_view->doctorClinics = $this->_getDoctorClinics($doctor->id);
        $this->_view->timeSlotsType = $this->_gettimeSlotsType();
        $this->_view->doctorId      = $doctor->id;
        $this->_view->multiClinics  = $multiClinics;
        $this->_view->minDate       = $minDate;
        $this->_view->doctorName    = $doctor->getFullName();
        $this->_view->scheduleId    = $schedule->id;
        $this->_view->scheduleName  = $schedule->name;
        $this->_view->arrWeekDays   = $this->_getWeekDays();
        $this->_view->arrTimeSlots  = $this->_getTimeSlots();
        $this->_view->timeFormat    = $this->_settings->time_format;
        $this->_view->appointmentTimeFormat = ModulesSettings::model()->param('appointments', 'time_format_appointment_time');
        $this->_view->render('doctorSchedules/timeBlocks/addMyTimeBlock');
    }

    /**
     * Edit specialty action handler
     * @param int $doctorId
     * @param int $scheduleId
     * @param int $id
     * @return void
     */
    public function editMyTimeBlockAction($scheduleId = 0, $id = 0)
    {
        // block access to this controller for doctors without membership plan or expired membership plan
        DoctorsComponent::checkAccessAccountUsingMembershipPlan();

        // Set frontend mode
        Website::setFrontend();
        // Block access to this controller for not-logged doctors
        CAuth::handleLogin('doctors/login', 'doctor');

        $actionMessage = '';
        $doctorId = CAuth::getLoggedRoleId();
        $doctor = $this->_checkDoctorFrontendAccess($doctorId);
        $schedule = $this->_checkScheduleFrontendAccess($scheduleId);
        $scheduleTimeBlock = $this->_checkTimeBlockFrontendAccess($id, $scheduleId);
        $cRequest = A::app()->getRequest();
        $clinicId = $cRequest->getPost('address_id', 'int', 0);
        $weekDay = $cRequest->getPost('week_day', 'int', 0);

        if($cRequest->getPost('APPHP_FORM_ACT', 'string', '') == 'send'){
            $minDate = $cRequest->getPost('date_from');
        }else{
            $minDate = '';
        }

        $configModule = \CLoader::config('appointments', 'main');
        $multiClinics = $configModule['multiClinics'];

        $clinicWorkingHours = WorkingHours::model()->count('clinic_id = :clinic_id AND week_day = :week_day AND is_day_off = 0', array(':clinic_id'=>$scheduleTimeBlock->address_id, ':week_day'=>$scheduleTimeBlock->week_day));
        if($clinicWorkingHours == 0 && empty($clinicId)){
            $alert = A::t('appointments', 'The clinic does not work on this of the week day', array('{WEEK_DAY}'=>A::t('i18n', 'weekDayNames.wide.'.$scheduleTimeBlock->week_day)));
            $actionMessage = $actionMessage = CWidget::create('CMessage', array('error', $alert), array('button'=>true));
        }

        $this->_view->actionMessage = $actionMessage;
        $this->_view->doctorClinics = $this->_getDoctorClinics($doctor->id);
        $this->_view->timeSlotsType = $this->_gettimeSlotsType();
        $this->_view->id            = $scheduleTimeBlock->id;
        $this->_view->scheduleId    = $schedule->id;
        $this->_view->multiClinics  = $multiClinics;
        $this->_view->clinicId      = !empty($clinicId) ? $clinicId : $scheduleTimeBlock->address_id;
        $this->_view->weekDay       = !empty($weekDay) ? $weekDay : $scheduleTimeBlock->week_day;
        $this->_view->doctorId      = $doctorId;
        $this->_view->minDate       = $minDate;
        $this->_view->doctorName    = $doctor->getFullName();
        $this->_view->scheduleId    = $schedule->id;
        $this->_view->scheduleName  = $schedule->name;
        $this->_view->arrWeekDays   = $this->_getWeekDays();
        $this->_view->arrTimeSlots  = $this->_getTimeSlots();
        $this->_view->timeFormat    = $this->_settings->time_format;
        $this->_view->appointmentTimeFormat = ModulesSettings::model()->param('appointments', 'time_format_appointment_time');
        $this->_view->render('doctorSchedules/timeBlocks/editMyTimeBlock');
    }

    /**
     * Delete specialty action handler
     * @param int $scheduleId
     * @param int $id
     * @return void
     */
    public function deleteMyTimeBlockAction($scheduleId = 0, $id = 0)
    {
        // block access to this controller for doctors without membership plan or expired membership plan
        DoctorsComponent::checkAccessAccountUsingMembershipPlan();
        
        // Set frontend mode
        Website::setFrontend();
        // Block access to this controller for not-logged doctors
        CAuth::handleLogin('doctors/login', 'doctor');

        $doctorId = CAuth::getLoggedRoleId();
        $doctor = $this->_checkDoctorFrontendAccess($doctorId);
        $schedule = $this->_checkScheduleFrontendAccess($scheduleId);
        $scheduleTimeBlock = $this->_checkTimeBlockFrontendAccess($id, $scheduleId);

        $alert = '';
        $alertType = '';
        $actionMessage = '';

        if($scheduleTimeBlock->delete()){
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

        if(!empty($alert)){
            A::app()->getSession()->setFlash('alert', $alert);
            A::app()->getSession()->setFlash('alertType', $alertType);
        }

        $this->redirect('doctorSchedules/myTimeBlocks/scheduleId/'.$scheduleId);
    }

    /**
     * Prepare scheduleCounters
     * @return array
     */
    private function _prepareCounters()
    {
        $scheduleCounters = array();
        $tables = CConfig::get('db.prefix').DoctorSchedules::model()->getTableName();
        $result = DoctorSchedules::model()->count(
            array(
                'condition'=>'',
                'select'=>$tables.'.doctor_id',
                'count'=>'*',
                'groupBy'=>$tables.'.doctor_id',
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
     * Prepare scheduleCounters
     * @param int $doctorId
     * @return array
     */
    private function _prepareTimeBlockCounters($doctorId = 0)
    {
        $scheduleTimeBlockCounters = array();
        $tableTimeBlocks = CConfig::get('db.prefix').DoctorScheduleTimeBlocks::model()->getTableName();
        $result = DoctorScheduleTimeBlocks::model()->count(
            array(
                'condition'=>'doctor_id = '.(int)$doctorId,
                'select'=>$tableTimeBlocks.'.schedule_id',
                'count'=>'*',
                'groupBy'=>$tableTimeBlocks.'.schedule_id',
                'allRows'=>true
            )
        );

        if(!empty($result)){
            foreach($result as $key => $scheduleTimeBlock){
                $scheduleTimeBlockCounters[$scheduleTimeBlock['schedule_id']] = $scheduleTimeBlock['cnt'];
            }
        }

        return $scheduleTimeBlockCounters;
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
     * @return DoctorSchedules
     */
    private function _checkScheduleAccess($id = 0, $doctorId = 0)
    {
        $schedule = DoctorSchedules::model()->findByPk($id, 'doctor_id = :doctor_id', array(':doctor_id'=>$doctorId));
        if(!$schedule){
            $this->redirect('doctorSchedules/manage/doctorId/'.$doctorId);
        }
        return $schedule;
    }

    /**
     * Check if passed Doctor ID is valid
     * @param int $id
     * @param int $doctorId
     * @param int $scheduleId
     * @return DoctorTimeBlocks
     */
    private function _checkTimeBlockAccess($id = 0, $doctorId = 0, $scheduleId = 0)
    {
        $scheduleTimeBlock = DoctorScheduleTimeBlocks::model()->findByPk($id, 'doctor_id = :doctor_id AND schedule_id = :schedule_id', array(':doctor_id'=>$doctorId, ':schedule_id'=>$scheduleId));
        if(!$scheduleTimeBlock){
            $this->redirect('doctorSchedules/manageTimeBlocks/doctorId/'.$doctorId.'/scheduleId/'.$scheduleId);
        }
        return $scheduleTimeBlock;
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
     * Check if passed Doctor ID is valid
     * @param int $id
     * @return DoctorSchedules
     */
    private function _checkScheduleFrontendAccess($id = 0)
    {
        $doctorId = CAuth::getLoggedRoleId();
        $schedule = DoctorSchedules::model()->findByPk($id, 'doctor_id = :doctor_id', array(':doctor_id'=>$doctorId));
        if(!$schedule){
            $this->redirect('doctorSchedules/mySchedules');
        }
        return $schedule;
    }

    /**
     * Check if passed Doctor ID is valid
     * @param int $id
     * @param int $scheduleId
     * @return DoctorTimeBlocks
     */
    private function _checkTimeBlockFrontendAccess($id = 0, $scheduleId = 0)
    {
        $doctorId = CAuth::getLoggedRoleId();
        $scheduleTimeBlock = DoctorScheduleTimeBlocks::model()->findByPk($id, 'doctor_id = :doctor_id AND schedule_id = :schedule_id', array(':doctor_id'=>$doctorId, ':schedule_id'=>$scheduleId));
        if(!$scheduleTimeBlock){
            $this->redirect('doctorSchedules/myTimeBlocks/scheduleId/'.$scheduleId);
        }
        return $scheduleTimeBlock;
    }

    /**
     * Check access to upload schedules count
     * @param int $doctorId
     * @param int $membershipSchedulesCount
     * @param string $redirect
     * @return bool
     */
    private function _checkUploadSchedulesCountAccess($doctorId = 0, $membershipSchedulesCount = 0, $redirect = '')
    {
        if(empty($membershipSchedulesCount) && empty($redirect)){
            return false;
        }

        $result = true;

        $countSchedules = DoctorSchedules::model()->count('doctor_id = '.$doctorId);
        if($countSchedules >= $membershipSchedulesCount){
            if(!empty($redirect)){
                $this->redirect($redirect);
            }else{
                $result = false;
            }
        }

        return $result;
    }

    /**
     * Get week days
     * @return array
     */
    private function _getWeekDays()
    {
        $weekDays = array();
        $standardWeekDays = array(
            1 => A::t('i18n', 'weekDayNames.wide.1'),
            2 => A::t('i18n', 'weekDayNames.wide.2'),
            3 => A::t('i18n', 'weekDayNames.wide.3'),
            4 => A::t('i18n', 'weekDayNames.wide.4'),
            5 => A::t('i18n', 'weekDayNames.wide.5'),
            6 => A::t('i18n', 'weekDayNames.wide.6'),
            7 => A::t('i18n', 'weekDayNames.wide.7'),
        );

        // Get settings
        $settings = Bootstrap::init()->getSettings();
        $weekStartDay = $settings->week_startday;

        // Re-arrange week days

        if($weekStartDay == 1){
            $weekDays = $standardWeekDays;
        }else{
            foreach($standardWeekDays as $key => $standardWeekDay){
                if($key >= $weekStartDay){
                    $weekDays[$key] = $standardWeekDay;
                }else{
                    $endWeekIds[] = $key;
                }
            }
            if(!empty($endWeekIds)){
                foreach($endWeekIds as $endWeekId){
                    $weekDays[$endWeekId] = $standardWeekDays[$endWeekId];
                }
            }
        }

        return $weekDays;
    }

    /**
     * Get time slots
     * @return array
     */
    private function _getTimeSlots()
    {
        return array(
            5 => '5 '.A::t('appointments', 'min.'),
            10 => '10 '.A::t('appointments', 'min.'),
            15 => '15 '.A::t('appointments', 'min.'),
            20 => '20 '.A::t('appointments', 'min.'),
            25 => '25 '.A::t('appointments', 'min.'),
            30 => '30 '.A::t('appointments', 'min.'),
            40 => '40 '.A::t('appointments', 'min.'),
            45 => '45 '.A::t('appointments', 'min.'),
            50 => '50 '.A::t('appointments', 'min.'),
            60 => '1 '.A::t('appointments', 'hour'),
            90 => '1 '.A::t('appointments', 'hour').' 30 '.A::t('appointments', 'min.'),
            120 => '2 '.A::t('appointments', 'hours'),
            150 => '2 '.A::t('appointments', 'hours').' 30 '.A::t('appointments', 'min.'),
            180 => '3 '.A::t('appointments', 'hours'),
            240 => '4 '.A::t('appointments', 'hours'),
            300 => '5 '.A::t('appointments', 'hours'),
            480 => '8 '.A::t('appointments', 'hours'),
        );
    }

    /**
     * Get Doctor Clinics
     * @param int $doctorId
     * @return bool|array|string
     */
    private function _getDoctorClinics($doctorId = 0)
    {
        if(empty($doctorId)){
            return false;
        }

        $doctorClinics  = array();
        $configModule   = \CLoader::config('appointments', 'main');
        $multiClinics   = $configModule['multiClinics'];

        if($multiClinics){
            $doctorClinicsArr = DoctorClinics::model()->findAll('doctor_id = :doctor_id', array(':doctor_id'=>$doctorId));
            if(!empty($doctorClinicsArr)){
                foreach($doctorClinicsArr as $doctorClinic){
                    $doctorClinics[$doctorClinic['clinic_id']] = $doctorClinic['clinic_name'].(!empty($doctorClinic['clinic_address']) ? ', '.$doctorClinic['clinic_address'] : '');
                }

                $clinics = Clinics::model()->findAll('is_active = 0');
                if(!empty($clinics)){
                    foreach($clinics as $clinic){
                        if(isset($doctorClinics[$clinic['id']])){
                            unset($doctorClinics[$clinic['id']]);
                        }
                    }
                }
            }
        }else{
            $clinicDefault = Clinics::model()->find('is_default = 1');
            $doctorClinics = $clinicDefault->id;
        }

        return $doctorClinics;
    }

    /**
     * Get Time Slots Type
     * @param int $doctorId
     * @return bool|array|string
     */
    private function _getTimeSlotsType()
    {
        $result = array();

        $timeSlotsType = TimeSlotsType::model()->findAll('is_active = 1');
        if (!empty($timeSlotsType) && is_array($timeSlotsType)) {
            foreach ($timeSlotsType as $timeSlotType) {
                $result[$timeSlotType['id']] = $timeSlotType['name'];
            }
        }

        return $result;
    }

    /**
     * Check working hours of the clinic for a change
     * @param int $scheduleId
     * @param int $timeBlockId
     * @return string
     */
    private function _checkWorkingHoursClinic($scheduleId = 0, $timeBlockId = 0)
    {
        $messageWorkingHours = '';

        $tableName = CConfig::get('db.prefix').DoctorScheduleTimeBlocks::model()->getTableName();
        if(empty($timeBlockId)){
            $condition = $tableName.'.schedule_id = :schedule_id';
            $paramsCondition = array(
                ':schedule_id'=>$scheduleId
            );
        }else{
            $condition = $tableName.'.id = :id AND '.$tableName.'.schedule_id = :schedule_id';
            $paramsCondition = array(
                ':id'=>$timeBlockId,
                ':schedule_id'=>$scheduleId,
            );
        }

        $timeBlocks = DoctorScheduleTimeBlocks::model()->findAll($condition, $paramsCondition);
        if(!empty($timeBlocks)){
            $changedWorkingHours = array();
            $clinicNames = array();
            foreach($timeBlocks as $timeBlock){
                $workingHoursForDay = WorkingHours::model()->find('clinic_id = :clinic_id AND week_day = :week_day AND is_day_off = 0', array(':clinic_id'=>$timeBlock['address_id'], ':week_day'=>$timeBlock['week_day']));
                //Comparison of working hours and time blocks
                if($workingHoursForDay){
                    $clinicStartTime = \CLocale::date('H:i:00', $workingHoursForDay->start_time);
                    $clinicEndTime = \CLocale::date('H:i:00', $workingHoursForDay->end_time);
                    if($timeBlock['time_from'] < $clinicStartTime){
                        $changedWorkingHours[$timeBlock['address_id']][$timeBlock['week_day']] = $clinicStartTime.' - '.$clinicEndTime;
                    }
                    if($timeBlock['time_to'] > $clinicEndTime){
                        $changedWorkingHours[$timeBlock['address_id']][$timeBlock['week_day']] = $clinicStartTime.' - '.$clinicEndTime;
                    }
                }else{
                    $changedWorkingHours[$timeBlock['address_id']][$timeBlock['week_day']] = A::t('appointments', 'Is Day Off');
                }
                if(!isset($clinicNames[$timeBlock['address_id']])){
                    $clinicNames[$timeBlock['address_id']] = $timeBlock['clinic_name'];
                }
            }
        }
        //Create Message
        if(!empty($changedWorkingHours)){
            $message = '';
            foreach($changedWorkingHours as $clinicId => $changedWorkingHoursInClinic){
                $clinicName = !empty($clinicNames[$clinicId]) ? $clinicNames[$clinicId] : '';
                $message .= A::t('appointments', 'Working hours of the clinic "{CLINIC_NAME}" were changed:', array('{CLINIC_NAME}'=>$clinicName)).'<br/><br/>';
                foreach($changedWorkingHoursInClinic as $numberWeekDay => $changedWorkingHoursParam){
                    $message .= '<b>'.A::t('i18n', 'weekDayNames.wide.'.$numberWeekDay).': '.$changedWorkingHoursParam.'</b><br/>';
                }
                $message .= '<br/>';
            }
            $message .= A::t('appointments', 'Please change working hours for these days accordingly.');
            $messageWorkingHours = CWidget::create('CMessage', array('error', $message));
        }

        return $messageWorkingHours;
    }
}
