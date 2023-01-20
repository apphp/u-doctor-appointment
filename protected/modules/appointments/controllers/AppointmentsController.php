<?php
/**
 * Appointments controller
 * This controller intended to both Backend and Frontend modes
 *
 * PUBLIC:                             PRIVATE
 * -----------                         ------------------
 * __construct				           _ajaxCheckSpecialtyAccess
 * indexAction           	           _ajaxCheckInsuranceAccess
 * manageAction                        _ajaxCheckReasonsAccess
 * addAction                           _ajaxCheckVisitedBeforeAccess
 * editAction                          _ajaxCheckAppointmentForWhomAccess
 * deleteAction                        _ajaxCheckDoctorAccess
 * viewAllAction                       _checkAppointmentAccess
 * findDoctorsAction                   _checkAppointmentDateTime
 * appointmentsAction                  _checkDoctorAccess
 * appointmentDetailsAction	           _getGenders
 * ajaxVerifyAppointmentAction         _getTitle
 * ajaxAppointmentCompleteAction       _getDegrees
 * ajaxBookAppointmentAction           _getWeekDays
 * ajaxShowMoreFindDoctorsAction
 * changeAppointmentAction             _getAllSpecialties
 *									   _getDoctorSpecialties
 *									   _getVisitedDoctorBefore
 *									   _getInsurance
 *									   _getReasons
 *									   _getWhoAppointment
 *
 *
 *
 *
 */

namespace Modules\Appointments\Controllers;

// Module
use \Modules\Appointments\Components\AppointmentsComponent;
use \Modules\Appointments\Components\DoctorsComponent;
use \Modules\Appointments\Models\Appointments;
use \Modules\Appointments\Models\Doctors;
use \Modules\Appointments\Models\DoctorSchedules;
use \Modules\Appointments\Models\DoctorSpecialties;
use \Modules\Appointments\Models\DoctorTimeoffs;
use \Modules\Appointments\Models\Degrees;
use \Modules\Appointments\Models\Titles;
use \Modules\Appointments\Models\Specialties;
use \Modules\Appointments\Models\DateTimeSchedules;
use \Modules\Appointments\Models\VisitReasons;
use \Modules\Appointments\Models\DoctorScheduleTimeBlocks;
use \Modules\Appointments\Models\Clinics;
use \Modules\Appointments\Models\Insurance;
use \Modules\Appointments\Models\Patients;

// Framework
use \Modules,
	\ModulesSettings,
	\Accounts,
	\CArray,
	\CAuth,
	\CConfig,
	\CCurrency,
	\CHash,
	\CHtml,
	\CLocale,
	\LocalTime,
	\CString,
	\CController,
	\CWidget,
	\CTime,
	\CValidator,
	\CDatabase;

// Application
use \Website,
	\Bootstrap,
	\A;

class AppointmentsController extends CController
{
	private $_schedules = array();
	private $_currentDoctorId = 0;
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

		if(CAuth::isLoggedInAsAdmin()){
			// set meta tags according to active appointments
			Website::setMetaTags(array('title'=>A::t('appointments', 'Appointments Management')));
			// set backend mode
			Website::setBackend();

			$this->_view->actionMessage = '';
			$this->_view->errorField = '';

			$this->_view->tabs = AppointmentsComponent::prepareTab('appointments');
		}

		$this->_view->labelStatusAppointments = array(
			'0'=>'<span class="label-yellow label-square">'.A::t('appointments', 'Reserved').'</span>',
			'1'=>'<span class="label-green label-square">'.A::t('appointments', 'Verified').'</span>',
			'2'=>'<span class="label-red label-square">'.A::t('appointments', 'Canceled').'</span>',
		);

		$this->_view->labelPatientArrivalStatus = array(
            '0'=>'<span class="label-red label-square">'.A::t('appointments', 'No').'</span>',
            '1'=>'<span class="label-green label-square">'.A::t('appointments', 'Yes').'</span>',
		);

        $settings = Bootstrap::init()->getSettings();
		$this->_view->dateFormat     = $settings->date_format;
		$this->_view->timeFormat     = $settings->time_format;
		$this->_view->dateTimeFormat = $settings->datetime_format;
        $this->_view->numberFormat   = $settings->number_format;
        $this->_view->typeFormat     = $settings->number_format;
        $this->_view->currencySymbol = A::app()->getCurrency('symbol');
	}

	/**
	 * Controller default action handler
	 */
	public function indexAction()
	{
		$this->redirect('appointments/manage');
	}

	/**
	 * Manage action handler
     * @param string $status
	 */
	public function manageAction($status = '')
	{
		Website::prepareBackendAction('manage', 'appointment', 'appointments/manage');

		$alert = A::app()->getSession()->getFlash('alert');
		$alertType = A::app()->getSession()->getFlash('alertType');

		if(!empty($alert)){
			$this->_view->actionMessage = CWidget::create(
				'CMessage', array($alertType, $alert, array('button'=>true))
			);
		}

        if($status == 'reserved'){
            $statusCode = 0;
        }elseif($status == 'verified'){
            $statusCode = 1;
        }elseif($status == 'canceled'){
            $statusCode = 2;
        }else{
            $status = '';
            $statusCode = '';
        }

        $this->_view->status = $status;
        $this->_view->statusCode = $statusCode;
        $this->_view->appointmentTimeFormat = ModulesSettings::model()->param('appointments', 'time_format_appointment_time');
        $this->_view->subTabs = AppointmentsComponent::prepareSubTab('appointments', $status);

		$this->_view->render('appointments/backend/manage');
	}

	/**
	 * Add new action handler
     * @param string $status
	 */
	public function addAction($status = '')
	{
		Website::prepareBackendAction('add', 'appointment', 'appointments/manage');

		$this->_view->drawAppointmentsBlock = AppointmentsComponent::drawAppointmentsBlock('backend');
		$this->_view->subTabs = AppointmentsComponent::prepareSubTab('appointments', $status);
        A::app()->getSession()->remove('changeAppointmentId');
        A::app()->getSession()->remove('changeDoctorId');
		$this->_view->render('appointments/backend/add');
	}

	/**
	 * Edit appointments action handler
	 * @param int $id
     * @param string $status
	 */
	public function editAction($id = 0, $status = '')
	{
		Website::prepareBackendAction('edit', 'appointment', 'appointments/manage');
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
        $this->_view->editStatusAppointments = $this->_getStatuses($appointment->status);
        $this->_view->editPatientArrivalStatus = array('0'=>A::t('appointments', 'Not Arrived'), '1'=>A::t('appointments', 'Arrived'));
		$this->_view->appointment = $appointment;
        $this->_view->status = $status;
		$this->_view->id = $id;
        $this->_view->appointmentTimeFormat = ModulesSettings::model()->param('appointments', 'time_format_appointment_time');
        $this->_view->subTabs = AppointmentsComponent::prepareSubTab('appointments', $status);
		$this->_view->render('appointments/backend/edit');
	}

	/**
	 * Delete action handler
	 * @param int $id
     * @param string $status
	 */
	public function deleteAction($id = 0, $status = '')
	{
		Website::prepareBackendAction('delete', 'appointment', 'appointments/manage');
		$appointment = $this->_checkAppointmentAccess($id, true);

		$alert = '';
		$alertType = '';

		if($appointment->delete()){
			if($appointment->getError()){
				$alert = $appointment->getErrorMessage();
				$alert = empty($alert) ? A::t('appointments', 'Delete Error Message') : $alert;
				$alertType = 'error';
			}else{
				$alert = A::t('appointments', 'Delete Success Message');
				$alertType = 'success';
			}
		}else{
			if(APPHP_MODE == 'demo'){
				$alert = CDatabase::init()->getErrorMessage();
				$alertType = 'warning';
			}else{
				$alert = A::t('app', 'Delete Error Message');
				$alertType = 'error';
			}
		}

        if(!empty($alert)){
            A::app()->getSession()->setFlash('alert', $alert);
            A::app()->getSession()->setFlash('alertType', $alertType);
        }

		$this->redirect('appointments/manage'.(!empty($status) ? '/status/'.$status : ''));
	}

	/**
	 * Find Doctors handler
	 */
	public function findDoctorsAction()
	{
        $loggedRole = CAuth::getLoggedRole();
        if (in_array($loggedRole, array('admin', 'owner'))) {
            Website::setBackend();
        } else {
            Website::setFrontend();
        }

        $cRequest = A::app()->getRequest();
        $showAllSpecialties = true;
        $doctors = array();
        $actionMessage = '';
        $page = 0;
        $pageSize = ModulesSettings::model()->param('appointments', 'doctors_per_page');
        $limit = ($page * $pageSize) . ', ' . $pageSize;
        $genders = $this->_getGenders();
        $degrees = $this->_getDegrees();
        $countAllDoctors = 0;
        $maxPage = 0;

        $getConditionFindDoctors = AppointmentsComponent::getConditionFindDoctors();
        $condition = !empty($getConditionFindDoctors['condition']) ? $getConditionFindDoctors['condition'] : '';
        $params = !empty($getConditionFindDoctors['params']) ? $getConditionFindDoctors['params'] : array();
        $arrDoctorIds = !empty($getConditionFindDoctors['arr_doctor_ids']) ? $getConditionFindDoctors['arr_doctor_ids'] : array();

        if (!empty($condition)) {
            $doctors = Doctors::model()->findAll(array(
                'condition' => $condition,
                'limit'=>$limit
            ),
                $params
            );

            $countAllDoctors  = Doctors::model()->count(array('condition'=> $condition));
        }

        if (!empty($arrDoctorIds) && !empty($doctors)) {
            $maxPage = ceil($countAllDoctors / $pageSize);
            $showAllSpecialties = false;
        } else {
            $allQueryName = array_keys($cRequest->getQuery());
            $findDoctorQueryName = array('doctorId', 'doctorName', 'specialtyId', 'locationId', 'location');

//            if (array_intersect($allQueryName, $findDoctorQueryName)) {
//                $actionMessage = CWidget::create('CMessage', array('warning', A::t('appointments', 'No search criteria selected! Please select or enter any search criteria and try again.'), array('button' => true)));
//            }
        }

        $drawFindDoctorsBlock = AppointmentsComponent::drawFindDoctorsBlock($doctors, $arrDoctorIds, $genders, $degrees);

        $this->_view->drawFindDoctorsBlock = $drawFindDoctorsBlock;
        $this->_view->locationId = !empty($cRequest->get('locationId')) ? (int)$cRequest->get('locationId') : 0;
        $this->_view->location = !empty($cRequest->get('location')) ? (string)$cRequest->get('location') : '';
        $this->_view->specialtyId = !empty($cRequest->get('specialtyId')) ? (int)$cRequest->get('specialtyId') : 0;
        $this->_view->doctorId = !empty($cRequest->get('doctorId')) ? (int)$cRequest->get('doctorId') : 0;
        $this->_view->doctorName = !empty($cRequest->get('doctorName')) ? (string)$cRequest->get('doctorName') : '';
        $this->_view->maxPage = $maxPage;
        $this->_view->showAllSpecialties = $showAllSpecialties;
        $this->_view->allSpecialties = $this->_getAllSpecialties();
        $this->_view->actionMessage = $actionMessage;

        if (in_array($loggedRole, array('admin', 'owner'))) {
            $this->_view->render('appointments/backend/findDoctors');
        } else {
            $this->_view->render('appointments/findDoctors');
        }
	}

	/**
	 * Appointment Action handler
	 * @param int $doctorId
	 */
	public function appointmentsAction($doctorId = 0)
	{
        $cRequest       = A::app()->getRequest();
        $clinicId       = (int)$cRequest->get('clinicId');
        $adminLogin     = false;
        $doctorLogin    = false;

        $loggedRole = CAuth::getLoggedRole();
        if(in_array($loggedRole, array('admin', 'owner'))){
            $adminLogin = true;
            Website::setBackend();
        }else{
            Website::setFrontend();
        }

        if(empty($doctorId) && CAuth::getLoggedId() && CAuth::getLoggedRole() == 'doctor'){
            $doctorLogin = true;
            $doctorId = CAuth::getLoggedRoleId();
        }elseif(!empty($doctorId) && CAuth::getLoggedId() && CAuth::getLoggedRole() == 'doctor'){
            $this->redirect('patients/login');
        }


        $clinicTime = '';
		$doctorId = (int)$doctorId;
		$page 	  = 1;
        $clinics = array();

        if(!empty($clinicId)){
            $clinic = $this->_checkClinicAccess($clinicId);
            $clinicTime = DoctorsComponent::getTimeClinic($clinic->id, false);
        }

        $doctor = $this->_checkDoctorAccess($doctorId);
        $drawDoctorSchedules = DoctorsComponent::drawDoctorSchedules($doctorId, $clinicId, $page);

        $configModule = \CLoader::config('appointments', 'main');
        $multiClinics = $configModule['multiClinics'];
        if($multiClinics){
            $clinics = DoctorsComponent::doctorClinics($doctorId);
        }

        // Validation
		if(empty($doctor)){
			$alertType = 'validation';
			$alert = A::t('appointments', 'A {param} with such an ID does not exist or was blocked', array('{param}'=>A::t('appointments', 'Doctor')));
		}

		if(!empty($alert)){
            if($doctorLogin){
                $redirectPage = 'doctors/appointments/';
            }else{
                $redirectPage = 'appointments/findDoctors/doctorId/'.$doctorId;
            }
			A::app()->getSession()->setFlash('alertType', $alertType);
			A::app()->getSession()->setFlash('alert', $alert);
			$this->redirect($redirectPage);
		}


		$this->_view->multiClinics       	        = $multiClinics;
		$this->_view->clinicId       	  		    = $clinicId;
		$this->_view->doctorId       	  		    = $doctorId;
		$this->_view->fullname			  		    = $doctor->getFullName();
		$this->_view->profileDoctor       		    = $doctor;
		$this->_view->specialty 		  		    = $this->_getDoctorSpecialties($doctorId, $doctor->membership_specialties_count);
		$this->_view->clinics 		  		        = $clinics;
		$this->_view->clinicTime 		  		    = $clinicTime;
		$this->_view->drawDoctorSchedules 		    = $drawDoctorSchedules;

        if($adminLogin){
            $this->_view->render('appointments/backend/appointments');
        }elseif($doctorLogin){
            $this->_view->render('appointments/doctor/appointments');
        }else{
            $this->_view->maxAppointmentToSpecialist    = ModulesSettings::model()->param('appointments', 'max_allowed_appointment_to_specialist');
            $this->_view->maxAppointmentPerPatient 	    = ModulesSettings::model()->param('appointments', 'max_allowed_appointment_per_patient');
            $this->_view->render('appointments/appointments');
        }
	}

	/**
	 * Reserve Time
	 * @param int $doctorId
	 */
	public function appointmentDetailsAction($doctorId = 0)
	{
        $cRequest           = A::app()->getRequest();
        $dateTime           = date('Y-m-d H:i:s', $cRequest->get('dateTime'));
        $alert              = '';
        $patientId          = '';
        $alertType          = '';
        $address            = '';
        $timeVisit          = '';
        $noSpecialty        = '';
        $doctorId           = (int)$doctorId;
        $clinicTime         = array();
        $loggedRole         = CAuth::getLoggedRole();
        $adminLogin         = false;
        $doctorLogin        = false;
        $bannedAppointment  = false;

        if(in_array($loggedRole, array('admin', 'owner'))){
            $adminLogin = true;
            Website::setBackend();
        }else{
            Website::setFrontend();
        }

        if(CAuth::getLoggedId() && CAuth::getLoggedRole() == 'doctor'){
            $doctorLogin = true;
            $doctorId = CAuth::getLoggedRoleId();
        }


		// Prepare datetime
		$parseDateTime = CTime::dateParseFromFormat('Y-m-d H:i:s', $dateTime);

		$year   = $parseDateTime['year'];
		$month  = str_pad($parseDateTime['month'], 2, '0', STR_PAD_LEFT);
		$day    = str_pad($parseDateTime['day'], 2, '0', STR_PAD_LEFT);
		$hour   = str_pad($parseDateTime['hour'], 2, '0', STR_PAD_LEFT);
		$minute = str_pad($parseDateTime['minute'], 2, '0', STR_PAD_LEFT);
		$second = str_pad($parseDateTime['second'], 2, '0', STR_PAD_LEFT);

		$dateAppointment = CLocale::date('Y-m-d', $dateTime);
		$timeAppointment = CLocale::date('H:i:s', $dateTime);

		$doctor = $this->_checkDoctorAccess($doctorId);

        if(CAuth::getLoggedId() && CAuth::getLoggedRole() == 'patient'){
            $patientId = CAuth::getLoggedRoleId();
        }

		$checkAppointmentDateTime = $this->_checkAppointmentDateTime($doctorId, $dateAppointment, $timeAppointment);

		if(!empty($patientId)){
		    // Ban a patient from ordering appointments
			$maxAppointmentPerPatient = ModulesSettings::model()->param('appointments', 'max_allowed_appointment_per_patient');
			$tableAppointmentsName = CConfig::get('db.prefix').Appointments::model()->getTableName();
			$countAppointments = Appointments::model()->count($tableAppointmentsName.'.patient_id = :patient_id AND ('.$tableAppointmentsName.'.status = 0 OR '.$tableAppointmentsName.'.status = 1) AND ('.$tableAppointmentsName.".appointment_date > '".LocalTime::currentDateTime('Y-m-d')."'".' OR ('.$tableAppointmentsName.".appointment_date = '".LocalTime::currentDateTime('Y-m-d')."'".' AND '.$tableAppointmentsName.".appointment_time > '".LocalTime::currentDateTime('H:i:s')."'))", array(':patient_id'=>$patientId));
			if($countAppointments >= $maxAppointmentPerPatient){
				$bannedAppointment = true;
			}
			if($bannedAppointment){
				$this->redirect('appointments/'.$doctor->id);
			}
		}

		// Validation
		if(empty($doctor)){
			$alertType = 'validation';
			$alert = A::t('appointments', 'A {param} with such an ID does not exist or was blocked', array('{param}'=>A::t('appointments', 'Doctor')));
		}elseif(empty($patientId) && !$adminLogin && !$doctorLogin){
            Website::setLastVisitedPage('appointments/appointmentDetails/doctorId/'.$doctorId.'/dateTime/'.urlencode($dateTime));
			$this->redirect('patients/login');
		}elseif(!CTime::isValidDate($year, $month, $day) || !CTime::isValidTime($hour, $minute, $second) && (count($year) == 4 && count($month) == 2 && count($day) == 2)){
			$alertType = 'validation';
            if(APPHP_MODE == 'debug'){
                $alert = A::t('appointments', 'The parameter {param} must be an datetime value', array('{param}'=>'dateTime'));
            }else{
                $alert = A::t('appointments', 'Wrong parameters passed! Please try again later or contact site admin if it occurs again.');
            }
		}elseif(!empty($checkAppointmentDateTime)){
			$alertType = 'validation';
			$alert     = A::t('appointments', 'Can\'t schedule an appointment for required time with the selected doctor.');
		}

		if(!empty($alert)){
            if($doctorLogin){
                $redirectPage = 'doctors/appointments/';
            }else{
                $redirectPage = 'appointments/findDoctors/doctorId/'.$doctorId;
            }
			A::app()->getSession()->setFlash('alertType', $alertType);
			A::app()->getSession()->setFlash('alert', $alert);
			$this->redirect($redirectPage);
		}

        $changeAppointmentId = A::app()->getSession()->get('changeAppointmentId');
		$changeDoctorId = A::app()->getSession()->get('changeDoctorId');
        if(!empty($changeAppointmentId) && $changeDoctorId == $doctor->id){
            if($adminLogin){
                $redirectPage = 'appointments/manage';
            }elseif($doctorLogin){
                $redirectPage = 'doctors/appointments';
            }else{
                $redirectPage = 'patients/myAppointments';
            }
            $appointment = Appointments::model()->findByPk($changeAppointmentId);
            if($appointment){
				$approvalRequired = ModulesSettings::model()->param('appointments', 'approval_required');
                $appointment->date_created = date('Y-m-d H:i:s');
                $appointment->appointment_date = $dateAppointment;
                $appointment->appointment_time = $timeAppointment;
                if($approvalRequired == 'automatic' || $adminLogin || $doctorLogin){
                    $appointment->status = 1;
                }elseif($approvalRequired == 'by_admin_or_doctor'){
					$appointment->status = 0;
				}
                A::app()->getSession()->set('changeAppointmentSendEmail', true);
                if($appointment->save()){
                    $alertType = 'success';
                    $redirectPage = 'patients/myAppointments?appointment_number='.$appointment->appointment_number.'&but_filter=Filter';
                    if($adminLogin){
                        $alert = A::t('appointments', 'Appointment has been successfully changed(for admin)', array('{param}'=>$appointment->appointment_number));
                        $redirectPage = 'appointments/manage?appointment_number='.$appointment->appointment_number.'&but_filter=Filter';
                    }elseif($doctorLogin){
                        $alert = A::t('appointments', 'Appointment has been successfully changed(for admin)', array('{param}'=>$appointment->appointment_number));
                        $redirectPage = 'doctors/appointments?appointment_number='.$appointment->appointment_number.'&but_filter=Filter';
					}elseif($approvalRequired == 'automatic'){
						$alert = A::t('appointments', 'Appointment has been successfully changed', array('{param}'=>$appointment->appointment_number));
					}elseif($approvalRequired == 'by_admin_or_doctor'){
						$alert = A::t('appointments', 'Appointment successfully changed, but not complete', array('{param}'=>$appointment->appointment_number));
					}

                    A::app()->getSession()->remove('changeAppointmentId');
                    A::app()->getSession()->remove('changeDoctorId');
                }else{
					if(APPHP_MODE == 'demo'){
						$alertType = 'warning';
						$alert = A::t('appointments', 'This operation is blocked in Demo Mode!');
					}else{
						$alertType = 'error';
						$alert = A::t('appointments', 'An error occurred! Please try again later.');
					}
                }
            }else{
                $alertType = 'error';
                $alert = A::t('appointments', 'An error occurred! Please try again later.');
            }
            A::app()->getSession()->setFlash('alertType', $alertType);
            A::app()->getSession()->setFlash('alert', $alert);
            $this->redirect($redirectPage);
        }

        //Get the name clinic and visit time for the schedule
		$schedulesForDay = DoctorScheduleTimeBlocks::model()->getSchedulesForDay($doctorId, $dateAppointment);
        if(!empty($schedulesForDay)){
            foreach($schedulesForDay as $schedule){
                if($timeAppointment >= $schedule['time_from'] && $timeAppointment <= $schedule['time_to']){
                    $clinic = $this->_checkClinicAccess($schedule['address_id']);
                    $clinicTime = DoctorsComponent::getTimeClinic($clinic->id, false);
                    $address = $schedule['clinic_name'].', '.$schedule['address'];
                    $timeVisit = $schedule['time_slots'];
                }
            }
        }
        $arrDoctorSpecialties = $this->_getDoctorSpecialties($doctorId, $doctor->membership_specialties_count);

        if(empty($arrDoctorSpecialties)){
            $noSpecialty = CWidget::create('CMessage', array('info', A::t('appointments', 'Sorry, this doctor does not have specialty yet.'), array('button'=>false)));
        }

		$this->_view->doctorId        	            = $doctorId;
		$this->_view->clinicTime        	        = $clinicTime;
		$this->_view->address        	            = $address;
		$this->_view->timeVisit        	            = $timeVisit;
		$this->_view->appointmnetnDate              = $dateAppointment.' '.$timeAppointment;
		$this->_view->profileDoctor                 = $doctor;
		$this->_view->fullname                      = $doctor->getFullName();
		$this->_view->arrDoctorSpecialties          = $arrDoctorSpecialties;
		$this->_view->visitedBefore                 = $this->_getVisitedDoctorBefore(($adminLogin || $doctorLogin) ? 'adminOrDoctor' : 'patient');
		$this->_view->appointmentForWhom            = $this->_getWhoAppointment(($adminLogin || $doctorLogin) ? 'adminOrDoctor' : 'patient');
		$this->_view->insurance                     = $this->_getInsurance();
		$this->_view->appointmentTimeFormat         = ModulesSettings::model()->param('appointments', 'time_format_appointment_time');
        $this->_view->visitReasons                  = $this->_getReasons();
        $this->_view->createPatientPopup            = AppointmentsComponent::createPatientPopup();
        $this->_view->noSpecialty                   = $noSpecialty;

		if($adminLogin){
            $this->_view->render('appointments/backend/appointmentDetails');
        }elseif($doctorLogin){
            $this->_view->render('appointments/doctor/appointmentDetails');
        }else{
            $this->_view->render('appointments/appointmentDetails');
        }
	}

    /**
     * Displays a calendar with all appointments.
     * @param int $doctorId
     */
    public function calendarAction($doctorId = 0)
    {

        // set backend mode
        Website::setBackend();
        Website::prepareBackendAction('manage', 'admin', 'modules/index');

        $doctor = $this->_checkDoctorAccess($doctorId);

        $drawCalendar = AppointmentsComponent::drawCalendar($doctor->id);
        $actionMessage = '';

        if (!$drawCalendar) {
            $actionMessage = CWidget::create('CMessage', array('info', A::t('app', 'No records found!'), array('button'=>false)));
        }

        $this->_view->doctorFullName = $doctor->getFullName();
        $this->_view->actionMessage = $actionMessage;
        $this->_view->page = !empty(A::app()->getRequest()->get('page')) ? A::app()->getRequest()->get('page') : 1;
        $this->_view->render('appointments/backend/calendar');
    }

    /**
     * Admin change appointment action handler
     * @param int $id
     */
    public function changeAppointmentAction($id = 0)
    {
        $appointment = $this->_checkAppointmentAccess($id, false);
        $doctor = $this->_checkDoctorAccess($appointment->doctor_id);

        A::app()->getSession()->set('changeAppointmentId', $id);
        A::app()->getSession()->set('changeDoctorId', $appointment->doctor_id);

        $this->redirect('appointments/'.$doctor->id);
    }


	/**
	 * AJAX Find Doctors
	 */
	public function ajaxShowMoreFindDoctorsAction()
	{
        $cRequest = A::app()->getRequest();
        if(!$cRequest->isAjaxRequest()){
            $this->redirect('doctors/dashboard');
        }

		$output = '';
		$doctors               = array();
		$genders               = $this->_getGenders();
		$degrees               = $this->_getDegrees();
        $page                  = (int)$cRequest->get('page');
        $pageSize              = ModulesSettings::model()->param('appointments', 'doctors_per_page');
		$limit 				   = ($page * $pageSize).', '.$pageSize;

        $getConditionFindDoctors = AppointmentsComponent::getConditionFindDoctors();
        $condition = !empty($getConditionFindDoctors['condition']) ? $getConditionFindDoctors['condition'] : '';
        $params = !empty($getConditionFindDoctors['params']) ? $getConditionFindDoctors['params'] : array();
        $arrDoctorIds = !empty($getConditionFindDoctors['arr_doctor_ids']) ? $getConditionFindDoctors['arr_doctor_ids'] : array();

        if (!empty($condition)) {
            $doctors = Doctors::model()->findAll(array(
                'condition' => $condition,
                'limit'=>$limit
            ),
                $params
            );
        }

		$output .= '<div class="one_first" id="page-'.$page.'">';
        $output .= AppointmentsComponent::drawFindDoctorsBlock($doctors, $arrDoctorIds, $genders, $degrees);
		$output .= '</div>';

		echo $output;

        exit;
	}

	/**
	 * AJAX Appointment Verification
	 */
	public function ajaxVerifyAppointmentAction()
	{
	    $arr = array();
        $patientId = '';

        $cRequest = A::app()->getRequest();
        if(!$cRequest->isAjaxRequest()){
            $this->redirect('doctors/dashboard');
        }

        $loggedRole = CAuth::getLoggedRole();
        if(in_array($loggedRole, array('admin', 'owner', 'doctor', 'patient'))){
            $dateTime             = $cRequest->get('dateTime');
            $doctorId             = (int)$cRequest->get('doctorId');
            $specialtyId          = (int)$cRequest->get('specialtyId');
            $visitedBeforeId      = $cRequest->get('visitedBeforeId');
            $insuranceId          = (int)$cRequest->get('insuranceId');
            $reasonsId            = (int)$cRequest->get('reasonsId');
            $appointmentForWhomId = $cRequest->get('appointmentForWhomId');
            $bannedAppointmentToSpecialist = false;
            if(in_array($loggedRole, array('admin', 'owner', 'doctor'))){
                $patientId = (int)$cRequest->get('patientId');
            }elseif(CAuth::getLoggedId() && $loggedRole == 'patient'){
                $patientId = CAuth::getLoggedRoleId();
            }

            // Prepare datetime
            $parseDateTime = CTime::dateParseFromFormat('Y-m-d H:i:s', $dateTime);

            $year   = $parseDateTime['year'];
            $month  = str_pad($parseDateTime['month'], 2, '0', STR_PAD_LEFT);
            $day    = str_pad($parseDateTime['day'], 2, '0', STR_PAD_LEFT);
            $hour   = str_pad($parseDateTime['hour'], 2, '0', STR_PAD_LEFT);
            $minute = str_pad($parseDateTime['minute'], 2, '0', STR_PAD_LEFT);
            $second = str_pad($parseDateTime['second'], 2, '0', STR_PAD_LEFT);

            $dateAppointment = $year.'-'.$month.'-'.$day;
            $timeAppointment = $hour.':'.$minute.':'.$second;

            $doctor = $this->_ajaxCheckDoctorAccess($doctorId);
            $patient = $this->_ajaxCheckPatientAccess($patientId);

            $checkAppointmentDateTime = $this->_checkAppointmentDateTime($doctorId, $dateAppointment, $timeAppointment);

            if(CAuth::getLoggedId() && $loggedRole == 'patient'){
                // Ban a patient from ordering appointments to this specialist
                $maxAppointmentToSpecialist = ModulesSettings::model()->param('appointments', 'max_allowed_appointment_to_specialist');
                $doctorSpecialties = DoctorSpecialties::model()->findAll('doctor_id = :doctor_id', array(':doctor_id'=>$doctor->id));
                foreach($doctorSpecialties as $doctorSpecialty){
                    $doctorSpecialtyIds[] = $doctorSpecialty['id'];
                }

                $tableAppointmentsName = CConfig::get('db.prefix').Appointments::model()->getTableName();
                $countAppointmentsToSpecialist = Appointments::model()->count($tableAppointmentsName.'.patient_id = :patient_id AND '.$tableAppointmentsName.'.doctor_specialty_id = :doctor_specialty_id AND ('.$tableAppointmentsName.'.status = 0 OR '.$tableAppointmentsName.'.status = 1) AND ('.$tableAppointmentsName.".appointment_date > '".LocalTime::currentDateTime('Y-m-d')."'".' OR ('.$tableAppointmentsName.".appointment_date = '".LocalTime::currentDateTime('Y-m-d')."'".' AND '.$tableAppointmentsName.".appointment_time > '".LocalTime::currentDateTime('H:i:s')."'))", array(':patient_id'=>$patientId, ':doctor_specialty_id' => $specialtyId,));
                if($countAppointmentsToSpecialist >= $maxAppointmentToSpecialist){
                    $bannedAppointmentToSpecialist = true;
                }
            }

            // Validation
            if($bannedAppointmentToSpecialist){
                $arr[] = '"status": "0"';
                $arr[] = '"error": "'.A::t('appointments', 'You have reached a maximum allowed number of the appointments to this specialist: {number}', array('{number}'=>$maxAppointmentToSpecialist)).'"';
            }elseif(empty($patient)){
                $arr[] = '"status": "0"';
                $arr[] = '"error": "'.A::t('appointments', 'The field \"{param}\" cannot be empty!', array('{param}'=>A::t('appointments', 'Patient'))).'"';
            }elseif(empty($specialtyId)){
                $arr[] = '"status": "0"';
                $arr[] = '"error": "'.A::t('appointments', 'The field \"{param}\" cannot be empty!', array('{param}'=>A::t('appointments', 'Doctor\'s Specialty'))).'"';
            }elseif(empty($visitedBeforeId) && $visitedBeforeId == ""){
                $arr[] = '"status": "0"';
                $arr[] = '"error": "'.A::t('appointments', 'The field \"{param}\" cannot be empty!', array('{param}'=>A::t('appointments', 'Have you visited this doctor before?'))).'"';
            }elseif(empty($insuranceId)){
                $arr[] = '"status": "0"';
                $arr[] = '"error": "'.A::t('appointments', 'The field \"{param}\" cannot be empty!', array('{param}'=>A::t('appointments', 'Will you use insurance?'))).'"';
            }elseif(empty($reasonsId)){
                $arr[] = '"status": "0"';
                $arr[] = '"error": "'.A::t('appointments', 'The field \"{param}\" cannot be empty!', array('{param}'=>A::t('appointments', 'Visit Reasons'))).'"';
            }elseif(empty($appointmentForWhomId) && $appointmentForWhomId == ""){
                $arr[] = '"status": "0"';
                $arr[] = '"error": "'.A::t('appointments', 'The field \"{param}\" cannot be empty!', array('{param}'=>A::t('appointments', 'Who is this appointment for?'))).'"';
            }elseif(empty($doctor)){
                $arr[] = '"status": "2"';
                $arr[] = '"error": "'.A::t('appointments', 'A {param} with such an ID does not exist or was blocked', array('{param}'=>A::t('appointments', 'Doctor'))).'"';
            }elseif(!CTime::isValidDate($year, $month, $day) || !CTime::isValidTime($hour, $minute, $second)){
                $arr[] = '"status": "2"';
                if(APPHP_MODE == 'debug'){
                    $arr[] = '"error": "'.A::t('appointments', 'The parameter {param} must be an datetime value', array('{param}'=>'dateTime')).'"';
                }else{
                    $arr[] = '"error": "'.A::t('appointments', 'Wrong parameters passed! Please try again later or contact site admin if it occurs again.').'"';
                }
            }elseif(!empty($checkAppointmentDateTime)){
                $arr[] = '"status": "2"';
                $arr[] = '"error": "'.A::t('appointments', 'Can\'t schedule an appointment for required time with the selected doctor.').'"';
            }else{
                $arr[] = '"status": "1"';
            }
        }else{
            $arr[] = '"status": "0", "message": "'.A::t('appointments', 'You do not have access to perform this operation').'"';
        }

        if(empty($arr)){
            $arr = '';
        }

        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');   // Date in the past
        header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // Always modified
        header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
        header('Pragma: no-cache'); // HTTP/1.0
        header('Content-Type: application/json');

        echo '{';
        echo implode(',', $arr);
        echo '}';

        exit;

	}

	/**
	 * AJAX Appointment Complete
	 */
	public function ajaxAppointmentCompleteAction()
	{
	    $arr = array();
        $address_id = '';
        $timeVisit = '';
        $patientId = '';

        $cRequest = A::app()->getRequest();
        if(!$cRequest->isAjaxRequest()){
            $this->redirect('doctors/dashboard');
        }

        $loggedRole = CAuth::getLoggedRole();
        if(in_array($loggedRole, array('admin', 'owner', 'doctor', 'patient'))){
            $approvalRequired     = ModulesSettings::model()->param('appointments', 'approval_required');
            $specialtyId          = (int)$cRequest->get('specialty');
            $visitedBeforeId      = (int)$cRequest->get('visitedBefore');
            $insuranceId          = (int)$cRequest->get('insurance');
            $reasonsId            = (int)$cRequest->get('reasons');
            $appointmentForWhomId = (int)$cRequest->get('appointmentForWhom');
            $forWhomSomeoneElse   = $cRequest->get('forWhomSomeoneElse');
            $otherReasons    	  = $cRequest->get('otherReasons');
            $dateTime             = $cRequest->get('dateTime');
            $doctorId             = (int)$cRequest->get('doctorId');
            $patientName          = $cRequest->get('patientName');
            $adminOrDoctorLogin   = false;

            if(in_array($loggedRole, array('admin', 'owner', 'doctor'))){
                $adminOrDoctorLogin = true;
                $patientId = (int)$cRequest->get('patientId');
            }elseif(CAuth::getLoggedId() && $loggedRole == 'patient'){
                $patientId = CAuth::getLoggedRoleId();
            }

            // Prepare datetime
            $parseDateTime = CTime::dateParseFromFormat('Y-m-d H:i:s', $dateTime);

            $year   = $parseDateTime['year'];
            $month  = str_pad($parseDateTime['month'], 2, '0', STR_PAD_LEFT);
            $day    = str_pad($parseDateTime['day'], 2, '0', STR_PAD_LEFT);
            $hour   = str_pad($parseDateTime['hour'], 2, '0', STR_PAD_LEFT);
            $minute = str_pad($parseDateTime['minute'], 2, '0', STR_PAD_LEFT);
            $second = str_pad($parseDateTime['second'], 2, '0', STR_PAD_LEFT);

            $dateAppointment = $year.'-'.$month.'-'.$day;
            $timeAppointment = $hour.':'.$minute.':'.$second;

            $doctor             = $this->_ajaxCheckDoctorAccess($doctorId);
            $patient            = $this->_ajaxCheckPatientAccess($patientId);
            $specialty          = $this->_ajaxCheckSpecialtyAccess($specialtyId);
            $insurance          = $this->_ajaxCheckInsuranceAccess($insuranceId);
            $reasons            = $this->_ajaxCheckReasonsAccess($reasonsId);
            $visitedBefore      = $this->_ajaxCheckVisitedBeforeAccess($visitedBeforeId);
            $appointmentForWhom = $this->_ajaxCheckAppointmentForWhomAccess($appointmentForWhomId);
            $checkAppointmentDateTime = $this->_checkAppointmentDateTime($doctorId, $dateAppointment, $timeAppointment);

            // Validation
            if(empty($patient) && $adminOrDoctorLogin){
                $arr[] = '"status": "0"';
                $arr[] = '"error": "'.A::t('appointments', 'A {param} with such an ID does not exist or was blocked', array('{param}'=>A::t('appointments', 'Patient'))).'"';
            }elseif(empty($doctor)){
                $arr[] = '"status": "0"';
                $arr[] = '"error": "'.A::t('appointments', 'A {param} with such an ID does not exist or was blocked', array('{param}'=>A::t('appointments', 'Doctor'))).'"';
            }elseif(empty($specialty)){
                $arr[] = '"status": "0"';
                $arr[] = '"error": "'.A::t('appointments', 'A {param} with such an ID does not exist or was blocked', array('{param}'=>A::t('appointments', 'Specialty'))).'"';
            }elseif(empty($insurance)){
                $arr[] = '"status": "0"';
                $arr[] = '"error": "'.A::t('appointments', 'A {param} with such an ID does not exist or was blocked', array('{param}'=>A::t('appointments', 'Insurance'))).'"';
            }elseif(empty($reasons)){
                $arr[] = '"status": "0"';
                $arr[] = '"error": "'.A::t('appointments', 'A {param} with such an ID does not exist or was blocked', array('{param}'=>A::t('appointments', 'Reasons'))).'"';
            }elseif(empty($visitedBefore)){
                $arr[] = '"status": "0"';
                $arr[] = '"error": "'.A::t('appointments', 'A {param} with such an ID does not exist or was blocked', array('{param}'=>A::t('appointments', 'Visited Before'))).'"';
            }elseif(empty($appointmentForWhom)){
                $arr[] = '"status": "0"';
                $arr[] = '"error": "'.A::t('appointments', 'A {param} with such an ID does not exist or was blocked', array('{param}'=>A::t('appointments', 'Appointment for Whom'))).'"';
            }elseif(!CTime::isValidDate($year, $month, $day) || !CTime::isValidTime($hour, $minute, $second)){
                $arr[] = '"status": "0"';
                if(APPHP_MODE == 'debug'){
                    $arr[] = '"error": "'.A::t('appointments', 'The parameter {param} must be an datetime value', array('{param}'=>'dateTime')).'"';
                }else{
                    $arr[] = '"error": "'.A::t('appointments', 'Wrong parameters passed! Please try again later or contact site admin if it occurs again.').'"';
                }
            }elseif(!empty($checkAppointmentDateTime)){
                $arr[] = '"status": "0"';
                $arr[] = '"error": "'.A::t('appointments', 'Can\'t schedule an appointment for required time with the selected doctor.').'"';
            }else{
                //Get the name clinic and visit time for the schedule
                $schedulesForDay = DoctorScheduleTimeBlocks::model()->getSchedulesForDay($doctorId, $dateAppointment);
                if(!empty($schedulesForDay)){
                    foreach($schedulesForDay as $schedule){
                        if($timeAppointment >= $schedule['time_from'] && $timeAppointment <= $schedule['time_to']){
                            $address_id = $schedule['address_id'];
                            $timeVisit = $schedule['time_slots'];
                        }
                    }
                }

                $doctorProfile = Doctors::model()->findByPk($doctorId);
                //Create new Appointment
                $appointment = new Appointments();

                $appointment->appointment_number = CHash::getRandomString(10, array('case'=>'upper'));
                $appointment->doctor_id = $doctorId;
                $appointment->doctor_specialty_id = $specialtyId;
                $appointment->doctor_address_id = $address_id;
                $appointment->visit_duration = $timeVisit;
                $appointment->patient_id = $patientId;
                $appointment->date_created = date('Y-m-d H:i:s');
                $appointment->appointment_date = $dateAppointment;
                $appointment->appointment_time = $timeAppointment;
                $appointment->visit_price = $doctorProfile->default_visit_price ? $doctorProfile->default_visit_price : '';
                $appointment->for_whom = $appointmentForWhomId;
                if($appointmentForWhomId == 2){
                    $appointment->for_whom_someone_else = $forWhomSomeoneElse;
                }
                $appointment->first_visit = $visitedBeforeId;
                $appointment->insurance_id = $insuranceId;
                $appointment->visit_reason_id = $reasonsId;
                if($reasonsId == 11){
                    $appointment->other_reasons = $otherReasons;
                }
                if($approvalRequired == 'automatic' || $adminOrDoctorLogin){
                    $appointment->status = 1;
                }elseif($approvalRequired == 'by_admin_or_doctor'){
                    $appointment->status = 0;
                }
                $appointment->status_review = 0;
                $appointment->created_by = $loggedRole;

                if($appointment->save()){
                    $arr[] = '"status": "1"';

                    if($adminOrDoctorLogin){
                        if($loggedRole == 'doctor'){
                            $link = 'doctors/appointments';
                        }else{
                            $link = 'appointments/manage/';
                        }
                        $arr[] = '"message": "'.A::t('appointments', 'Appointment has been successfully approved(for admin or doctor)', array('{appointment_number}'=>$appointment->appointment_number, '{patient_name}'=>$patientName, '{link}'=>$link)).'"';
                    }elseif($approvalRequired == 'automatic'){
                        $arr[] = '"message": "'.A::t('appointments', 'Appointment has been successfully approved', array('{param}'=>$appointment->appointment_number)).'"';
                    }elseif($approvalRequired == 'by_admin_or_doctor'){
                        $arr[] = '"message": "'.A::t('appointments', 'Appointment has been successfully reserved', array('{param}'=>$appointment->appointment_number)).'"';
                    }
                }else{
                    $arr[] = '"status": "0"';
					if(APPHP_MODE == 'demo'){
						$arr[] = '"error": "'.A::t('appointments', 'This operation is blocked in Demo Mode!').'"';
					}else{
						$arr[] = '"error": "'.A::t('appointments', 'An error occurred! Please try again later.').'"';
					}
                }
            }
        }else{
            $arr[] = '"status": "0", "message": "'.A::t('appointments', 'You do not have access to perform this operation').'"';
        }

        if(empty($arr)){
            $arr = '';
        }

        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');   // Date in the past
        header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // Always modified
        header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
        header('Pragma: no-cache'); // HTTP/1.0
        header('Content-Type: application/json');

        echo '{';
        echo implode(',', $arr);
        echo '}';

        exit;
	}

    /**
     * Get Ajax Book Appointment
     */
    public function ajaxBookAppointmentAction()
    {
        $cRequest = A::app()->getRequest();
        if(!$cRequest->isAjaxRequest()){
            $this->redirect('doctors/dashboard');
        }

        $page 	  = (int)$cRequest->get('page');
        $doctorId = (int)$cRequest->get('doctorId');
        $clinicId = (int)$cRequest->get('clinicId');

		$doctor = $this->_ajaxCheckDoctorAccess($doctorId);
		if(!empty($clinicId)){
            $clinic = $this->_ajaxCheckClinicAccess($clinicId);
        }

		if(empty($doctor)){
			$alertType = 'validation';
			$alert = A::t('appointments', 'A {param} with such an ID does not exist or was blocked', array('{param}'=>A::t('appointments', 'Doctor')));
		}elseif(empty($clinic) && !empty($clinicId)){
            $alertType = 'validation';
            $alert = A::t('appointments', 'A {param} with such an ID does not exist or was blocked', array('{param}'=>A::t('appointments', 'Clinic')));
        }

		if(!empty($alert)){
			echo '<div class="one_first">';
				echo CWidget::create('CMessage', array($alertType, $alert, array('button'=>true)));
			echo '</div>';
		}else{
			$drawDoctorSchedules = DoctorsComponent::drawDoctorSchedules($doctorId, $clinicId, $page);
			echo $drawDoctorSchedules;
		}

        exit;
    }

	/**
	 * Check if passed record ID is valid
	 * @param int $appointmentId
	 * @param bool $editPast
	 * @return object Appointments
	 */
	private function _checkAppointmentAccess($appointmentId = 0, $editPast = false)
	{
        $tableName = CConfig::get('db.prefix').Appointments::model()->getTableName();
        $condition = '';
        if(!$editPast){
            $condition = '('.$tableName.".appointment_date > '".LocalTime::currentDateTime('Y-m-d')."'".' OR ('.$tableName.".appointment_date = '".LocalTime::currentDateTime('Y-m-d')."'".' AND '.$tableName.".appointment_time > '".LocalTime::currentDateTime('H:i:s')."'))";
        }
		$appointment = Appointments::model()->findByPk($appointmentId, $condition);

		if(!$appointment){
			$this->redirect('appointments/manage');
		}
		return $appointment;
	}

	/**
	 * Check if passed record ID is valid
	 * @param int $doctorId
	 * @param int $clinicId
	 * @param string $date
	 * @param string $time
	 * @return string $status
	 */
	private function _checkAppointmentDateTime($doctorId = 0, $date = '', $time = '', $clinicId = 0)
	{
		if(empty($doctorId) && empty($date) && empty($time)){
			return false;
		}

        $status = '';

        $doctorSchedules     = DoctorScheduleTimeBlocks::model()->getSchedulesForDate($doctorId, $date, $date);
		$appointmentsDoctors = DoctorScheduleTimeBlocks::model()->getAppointmetsDoctors($doctorId, $date);
        $doctorTimeOffs      = DoctorScheduleTimeBlocks::model()->getTimeOffs($doctorId);

        $findScheduleTime    = false;
        $findAppointmentTime = false;
        $findTimeOffs        = false;


		$scheduleTimes = isset($doctorSchedules[$date]) ? $doctorSchedules[$date] : array();
		$appointmentTimes = isset($appointmentsDoctors) ? $appointmentsDoctors : array();
        foreach($scheduleTimes as $scheduleTime){
            if(in_array($time, $scheduleTime)){
                $findScheduleTime = true;
            }
        }

        foreach($appointmentTimes as $appointmentTime){
            if(in_array($time, $appointmentTime)){
                $findAppointmentTime = true;
            }
        }

        foreach($doctorTimeOffs as $doctorTimeOffs){
            if($date >= $doctorTimeOffs['date_from'] && $date <= $doctorTimeOffs['date_to'] && $time >= $doctorTimeOffs['time_from'] && $time < $doctorTimeOffs['time_to']){
                $findTimeOffs = true;
            }
        }

        if(!$findScheduleTime){
            $status = A::t('appointments', 'Appointment not active');
        }elseif($findAppointmentTime){
            $status = A::t('appointments', 'Appointment already reserved');
        }elseif($findTimeOffs){
            $status = A::t('appointments', 'Doctor on holidays');
        }

        return $status;
	}

	/**
	 * Get genders
	 * @return array
	 */
	private function _getGenders()
	{
		return array('m'=>A::t('appointments', 'Male'), 'f'=>A::t('appointments', 'Female'));
	}

	/**
	 * Get titile
	 * @return array
	 */
	private function _getTitle()
	{
		$arrOutput = array();

		$arrOutput = Titles::getActiveTitles();

		return $arrOutput;
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
	 * Get All Active Specialties
	 * @return array
	 */
	private function _getAllSpecialties()
	{
		$outSpecialties = array();
        $doctorSpecialtiesCount = array();

        //calculate the number of active specialties
		$doctorSpecialties = DoctorSpecialties::model()->findAll();
		if(!empty($doctorSpecialties) && is_array($doctorSpecialties)){
			foreach($doctorSpecialties as $doctorSpecialtyCnt){
			    $doctor = Doctors::model()->findByPk($doctorSpecialtyCnt['doctor_id']);
			    if($doctor){
			    	$unixCurrentDay = strtotime(date('Y-m-d'));
			    	$unixMembershipExpires = strtotime($doctor->membership_expires);
			    	//Check profile on Active and Removed. Check membership on show in search and expires
			        if($doctor->is_active == true && $doctor->is_removed == false && $doctor->membership_show_in_search == true && $unixMembershipExpires >= $unixCurrentDay){
                        if(in_array($doctorSpecialtyCnt['specialty_id'], array_keys($doctorSpecialtiesCount))){
                            $doctorSpecialtiesCount[$doctorSpecialtyCnt['specialty_id']]++;
                        }else{
                            $doctorSpecialtiesCount[$doctorSpecialtyCnt['specialty_id']] = 1;
                        }
                    }
                }
			}
			$specialtyTable = CConfig::get('db.prefix').Specialties::model()->getTableName();
			$arrSpecialties = Specialties::model()->findAll($specialtyTable.'.is_active = 1');
			if(!empty($arrSpecialties) && is_array($arrSpecialties)){
				foreach($arrSpecialties as $oneSpecialty){
					isset($doctorSpecialtiesCount[$oneSpecialty['id']]) ? $SpecialtiesCount = $doctorSpecialtiesCount[$oneSpecialty['id']] : $SpecialtiesCount = '0';
					$outSpecialties[$oneSpecialty['id']]['name'] = $oneSpecialty['name'];
					$outSpecialties[$oneSpecialty['id']]['count'] = $SpecialtiesCount;
				}
			}
		}

		return $outSpecialties;
	}

	/**
	 * Get specialties for doctor
	 * @param int $doctorId
	 * @param int $membershipSpecialtiesCount
	 * @return array
	 */
	private function _getDoctorSpecialties($doctorId = 0, $membershipSpecialtiesCount = 0)
	{
		$arrSpecialties = array();

        $specialties = DoctorSpecialties::model()->findAll(array('condition' => 'doctor_id = '.$doctorId, 'orderBy' => 'sort_order ASC', 'limit' => $membershipSpecialtiesCount));
        if(!empty($specialties)){
            foreach($specialties as $specialty){
                $arrSpecialties[$specialty['specialty_id']] =  $specialty['specialty_name'];
            }
        }

		return $arrSpecialties;
	}

	/**
	 * Get the array of answers to the question "did you visit a doctor before?".
     * @param string $type
	 * @return array
	 */
	private function _getVisitedDoctorBefore($type = 'patient')
	{
		if($type == 'patient'){
            $arrAnswers = array(
                '1'=>A::t('appointments', 'I\'m a new patient'),
                '2'=>A::t('appointments', 'I\'m an existing patient of this practice'),
            );
        }elseif($type == 'adminOrDoctor'){
            $arrAnswers = array(
                '1'=>A::t('appointments', 'New patient'),
                '2'=>A::t('appointments', 'Existing patient of this practice'),
            );
        }

		return $arrAnswers;
	}

	/**
	 * Get the array of answers to the question "Will you use insurance?"
	 * @return array
	 */
	private function _getInsurance()
	{
		$arrInsurence = array();

        $insurance = Insurance::model()->findAll(array('condition'=>'is_active = 1', 'orderBy'=>'name ASC'));
        foreach($insurance as $insuranc){
            $arrInsurence[$insuranc['id']] = $insuranc['name'];
        }

		return $arrInsurence;
	}

	/**
	 * Get the Visit Reasons array
	 * @return array
	 */
	private function _getReasons()
	{
        $arrReasons = array();

        $reasons = VisitReasons::model()->findAll(array('condition'=>'is_active = 1', 'orderBy'=>'sort_order ASC'));
        foreach($reasons as $reason){
            $arrReasons[$reason['id']] = $reason['name'];
        }

        return $arrReasons;
	}

	/**
	 * Get the array of answers to the question "Who is this appointment for?"
     * @param string $type
     * @return array
	 */
	private function _getWhoAppointment($type = 'patient')
	{
	    if($type == 'patient'){
            $arrAnswers = array(
                '1'=>A::t('appointments', 'Me'),
                '2'=>A::t('appointments', 'Someone else'),
            );
        }elseif($type == 'adminOrDoctor'){
            $arrAnswers = array(
                '1'=>A::t('appointments', 'Current Patient'),
                '2'=>A::t('appointments', 'Someone else'),
            );
        }

		return $arrAnswers;
	}

    /**
     * Get all statuses
     */
    private function _getAllStatuses()
    {
        $allStatuses = array(
            0 => A::t('appointments', 'Reserved'),
            1 => A::t('appointments', 'Verified'),
            2 => A::t('appointments', 'Canceled'),
        );

        return $allStatuses;
    }

	/**
     * Get statuses by status number
     * @param int $statusNumber
     * @return array $outStatuses
     */
    private function _getStatuses($statusNumber = 0)
    {
        $outStatuses = array();
        $allStatuses = $this->_getAllStatuses();
        switch($statusNumber){
            case 0:
                $outStatuses[0] = $allStatuses[0];
                $outStatuses[1] = $allStatuses[1];
                $outStatuses[2] = $allStatuses[2];
                break;
            case 1:
                $outStatuses[1] = $allStatuses[1];
                $outStatuses[2] = $allStatuses[2];
                //$outStatuses[4] = $allStatuses[4];
                break;
            case 2:
                $outStatuses[2] = $allStatuses[2];
                //$outStatuses[3] = $allStatuses[3];
                break;
            //case '3':
                //$outStatuses[3] = $allStatuses[3];
                //break;
            //case '4':
                //$outStatuses[4] = $allStatuses[4];
                //break;
        }

        return $outStatuses;
    }

	/**
	 * Check if passed Doctor ID is valid
	 * @param int $id
	 * @return object
	 */
	private function _checkDoctorAccess($id = 0)
	{
		if(empty($id)){
			return false;
		}

		$doctor = array();

		$tableAccountsName     = CConfig::get('db.prefix').Accounts::model()->getTableName();
		$doctor = Doctors::model()->findByPk($id, $tableAccountsName.'.is_active = 1 AND '.$tableAccountsName.'.is_removed = 0');
		if(!$doctor){
			$this->redirect('Home/index');
		}
		return $doctor;
	}

	/**
	 * Check if passed Clinic ID is valid
	 * @param int $id
	 * @return object
	 */
	private function _checkClinicAccess($id = 0)
	{
		if(empty($id)){
			return false;
		}

		$clinic = array();

		$clinic = Clinics::model()->findByPk($id, 'is_active = 1');
		if(!$clinic){
			$this->redirect('Home/index');
		}
		return $clinic;
	}

	/**
	 * Check if passed Specialty ID is valid
	 * @param int $id
	 * @return object
	 */
	private function _checkSpecialtyAccess($id = 0)
	{
		if(empty($id)){
			return false;
		}

		$specialty = array();

		$specialty = Specialties::model()->findByPk($id, 'is_active = 1');
		if(!$specialty){
			$this->redirect('Home/index');
		}
		return $specialty;
	}

	/**
	 * Check if passed Doctor ID is valid
	 * @param int $id
	 * @return bool
	 */
	private function _ajaxCheckDoctorAccess($id = 0)
	{
		if(empty($id)){
			return false;
		}

		$doctor = Doctors::model()->findByPk($id, CConfig::get('db.prefix').Accounts::model()->getTableName().'.is_active = 1 AND '.CConfig::get('db.prefix').Accounts::model()->getTableName().'.is_removed = 0');
		if(!empty($doctor)){
			return true;
		}else{
			return false;
		}
	}

	/**
	 * Check if passed Patient ID is valid
	 * @param int $id
	 * @return bool
	 */
	private function _ajaxCheckPatientAccess($id = 0)
	{
		if(empty($id)){
			return false;
		}

		$patient = Patients::model()->findByPk($id, CConfig::get('db.prefix').Accounts::model()->getTableName().'.is_active = 1 AND '.CConfig::get('db.prefix').Accounts::model()->getTableName().'.is_removed = 0');
		if(!empty($patient)){
			return true;
		}else{
			return false;
		}
	}

    /**
     * Check if passed Clinic ID is valid
     * @param int $id
     * @return bool
     */
    private function _ajaxCheckClinicAccess($id = 0)
    {
        if(empty($id)){
            return false;
        }

        $clinic = Clinics::model()->findByPk($id, 'is_active = 1');
        if(!$clinic){
            return false;
        }else{
            return true;
        }
    }

	/**
	 * Check if passed Specialty ID is valid
	 * @param int $id
	 * @return bool
	 */
	private function _ajaxCheckSpecialtyAccess($id = 0)
	{
		if(empty($id)){
			return false;
		}

		$specialty = Specialties::model()->findByPk($id, 'is_active = 1');
		if(!empty($specialty)){
			return true;
		}else{
			return false;
		}
	}

	/**
	 * Check if passed Insurance ID is valid
	 * @param int $id
	 * @return bool
	 */
	private function _ajaxCheckInsuranceAccess($id = 0)
	{
		if(empty($id)){
			return false;
		}

		$insurance = Insurance::model()->findByPk($id, 'is_active = 1');
		if(!empty($insurance)){
			return true;
		}else{
			return false;
		}
	}

	/**
	 * Check if passed Reasons ID is valid
	 * @param int $id
	 * @return bool
	 */
	private function _ajaxCheckReasonsAccess($id = 0)
	{
		if(empty($id)){
			return false;
		}

		$reasons = VisitReasons::model()->findByPk($id, 'is_active = 1');
		if(!empty($reasons)){
			return true;
		}else{
			return false;
		}

	}

	/**
	 * Check if passed Visited Before ID is valid
	 * @param int $id
	 * @return bool
	 */
	private function _ajaxCheckVisitedBeforeAccess($id = 0)
	{
		if($id === ''){
			return false;
		}
		$result = false;

		//Get the array VisitedBefore
		$visitedBefore = $this->_getVisitedDoctorBefore();

		if(!empty($visitedBefore)){
			foreach($visitedBefore as $key => $visitedBefor){
				if($id == $key){
					$result = true;
				}
			}
		}

		return $result;
	}

	/**
	 * Check if passed Appointment For Whom ID is valid
	 * @param int $id
	 * @return bool
	 */
	private function _ajaxCheckAppointmentForWhomAccess($id = 0)
	{
		if($id === ''){
			return false;
		}

		$result = false;

		//Get the array Appointment For Whom
		$appointmentForWhom = $this->_getWhoAppointment();

		if(!empty($appointmentForWhom)){
			foreach($appointmentForWhom as $key => $appointmentWhom){
				if($id == $key){
					$result = true;
				}
			}
		}

		return $result;
	}
}
