<?php
/**
* DoctorsComponent
*
* PUBLIC:                                   PRIVATE
* -----------                               ------------------
* getDoctorTitles                           _redirect
* getDoctorName
* getDoctorImage
* getPreparedDurations
* drawRating
* drawReviews
* drawDoctorSchedules
* checkMembershipPlan
* checkAccessAccountUsingMembershipPlan
* updateMembershipPlan
*
* STATIC
* -------------------------------------------
* init
*
*/

namespace Modules\Appointments\Components;

// Models
use Modules\Appointments\Models\Appointments;
use Modules\Appointments\Models\Clinics;
use \Modules\Appointments\Models\DoctorReviews;
use Modules\Appointments\Models\DoctorSchedules;
use Modules\Appointments\Models\DoctorScheduleTimeBlocks;
use \Modules\Appointments\Models\DoctorSpecialties;
use \Modules\Appointments\Models\Doctors;
use Modules\Appointments\Models\Memberships;
use \Modules\Appointments\Models\Patients;
use \Modules\Appointments\Models\Specialties;
use \Modules\Appointments\Models\Titles;
use \Modules\Appointments\Models\WorkingHours;

// Global
use \A,
	\Accounts,
    \CAuth,
    \CConfig,
    \CCurrency,
    \CLoader,
    \CLocale,
    \CHash,
    \CHtml,
    \CWidget,
    \CDebug,
    \CFile,
    \DateTime,
    \DateInterval;

// Application
use \ModulesSettings,
    \Bootstrap,
    \LocalTime,
    \Website;



class DoctorsComponent extends \CComponent{

    const NL = "\n";

    public static function init()
    {
        return parent::init(__CLASS__);
    }

    /**
     * Returns titles
     * @param array $doctor
     * @return string
     */
    public static function getDoctorTitles()
    {
        // Prepare titles
		$titles = array();

		$result = Titles::getActiveTitles();
		if(!empty($result) && is_array($result)){
            foreach($result as $title){
                $titles[$title['id']] = A::t('appointments', $title['title']);
            }
        }
        
        return $titles;
    }

    /**
     * Returns full doctor name
     * @param array $doctor
     * @param bool $showTitle
     * @return string
     */
    public static function getDoctorName($doctor = array(), $showTitle = true)
    {
        $titles = Titles::getActiveTitles();

        $fullName = (! empty($titles[$doctor['title_id']]) ? $titles[$doctor['title_id']] : '').' '.
                    $doctor['doctor_first_name'].' '.
                    $doctor['doctor_middle_name'].' '.
                    $doctor['doctor_last_name'];
        return $fullName;
    }
    
    /**
     * Returns full doctor image
     * @param array $doctor
     * @param bool $srcOnly
     * @return string
     */
    public static function getDoctorImage($doctor = array(), $srcOnly = true)
    {
        $targetPath = 'assets/modules/appointments/images/doctors/';
        $image = ! empty($doctor['avatar']) && CFile::fileExists($targetPath.$doctor['avatar'])
                ? $doctor['avatar']
                : ($doctor['gender'] == 'f' ? 'no_avatar_female.png' : 'no_avatar_male.png');
        
        return $targetPath.$image;
    }

	/**
     * Draws Reviews
     * @param int $doctorId
     */
    public static function drawReviews($doctorId = 0)
    {
		$currentPage = 1;
        $alert = '';
        $alertType = '';
        $actionMessage = '';
		$appointmentId = '';
        $drawForm = false;

        $pageSize  = ModulesSettings::model()->param('appointments', 'reviews_per_page');
        $totalReviews = DoctorReviews::model()->count('doctor_id = '.$doctorId.' && status = true');

        $doctorReviews = DoctorReviews::model()->findAll(array('condition' => 'doctor_id = '.$doctorId.' && status = true', 'limit' => (($currentPage - 1) * $pageSize).', '.$pageSize, 'orderBy' => 'created_at DESC'));

        $showReviews = count($doctorReviews);

        $lastAppointment = '';

        // Check if you can output a review form
        if(CAuth::getLoggedId() && CAuth::getLoggedRole() == 'patient'){
            $patientId = CAuth::getLoggedRoleId();
            if(!empty($patientId)){
                $lastAppointment = Appointments::model()->find(array('condition' => 'doctor_id = '.$doctorId.' && patient_id = '.$patientId.' && appointment_date < NOW() && appointment_time < NOW()', 'orderBy' => 'appointment_date DESC, appointment_time DESC'));
            }

			if(!empty($lastAppointment)){
				$appointmentId = $lastAppointment->id;
			}
            if(!empty($lastAppointment) && $lastAppointment->status_review == false){
                $drawForm = true;
            }elseif(!empty($lastAppointment) && $lastAppointment->status_review == true){
                $doctorReview = DoctorReviews::model()->find(array('condition' => 'appointment_id = '.$lastAppointment->id, 'orderBy' => 'created_at DESC'));
                if(!empty($doctorReview) && $doctorReview->status == 1){
                    $alert = A::t('appointments', 'You already submitted review for this doctor!');
                }elseif(!empty($doctorReview) && $doctorReview->status == 0){
                    $alert = A::t('appointments', 'Your already places review, it will be published after moderation.');
                }elseif(!empty($doctorReview) && $doctorReview->status == 2){
                    $drawForm = true;
                }
                $alertType =  'warning';
            }else{
                $alert = A::t('appointments', 'You can not leave a review because you did not visit this doctor!');
                $alertType =  'warning';
            }

            if(!empty($alert) && !empty($alertType)){
                $actionMessage = CWidget::create('CMessage', array($alertType, $alert, array('button'=>true)));
            }
        }

        $view = A::app()->view;
        $view->showReviews = $showReviews;
        $view->doctorReviews = $doctorReviews;
        $view->doctorId = $doctorId;
        $view->currentPage = $currentPage;
        $view->pageSize = $pageSize;
        $view->totalReviews = $totalReviews;
        $view->drawForm = $drawForm;
        $view->appointmentId = $appointmentId;
        $view->actionMessage = $actionMessage;
        $view->dateFormat = Bootstrap::init()->getSettings('date_format');
        $view->showRating = ModulesSettings::model()->param('appointments', 'show_rating');
        $view->showRatingForm = ModulesSettings::model()->param('appointments', 'show_rating_form');
        $view->reviewModeration = ModulesSettings::model()->param('appointments', 'review_moderation');

        $view->renderContent('drawReviews');
    }

    /**
     * Draws Rating
     * @param int $doctorId
     */
    public static function drawRating($doctorId = 0)
    {
        $overallRatingPrice = 0;
        $overallRatingWaitTime = 0;
        $overallRatingBedsideManner = 0;
        $overallRate = 0;

		$totalReviews = DoctorReviews::model()->count('doctor_id = '.$doctorId.' && status = true');

		$ratingDoctorReviews = DoctorReviews::model()->findAll(array('condition' => 'doctor_id = '.$doctorId.' && status = true', 'orderBy' => 'created_at DESC'));
        if(!empty($ratingDoctorReviews)){
            foreach ($ratingDoctorReviews as $ratingDoctorReview){
                $overallRatingPrice += $ratingDoctorReview['rating_price'];
                $overallRatingWaitTime += $ratingDoctorReview['rating_wait_time'];
                $overallRatingBedsideManner += $ratingDoctorReview['rating_bedside_manner'];
            }
            $overallRatingPrice = round($overallRatingPrice / $totalReviews,1);
            $overallRatingWaitTime = round($overallRatingWaitTime / $totalReviews,1);
            $overallRatingBedsideManner = round($overallRatingBedsideManner / $totalReviews,1);
            $overallRate = round(($overallRatingPrice + $overallRatingWaitTime + $overallRatingBedsideManner) / 3,1);
        }

        $view = A::app()->view;

        $view->overallRatingPrice = $overallRatingPrice;
        $view->overallRatingWaitTime = $overallRatingWaitTime;
        $view->overallRatingBedsideManner = $overallRatingBedsideManner;
        $view->overallRate = $overallRate;

        $view->renderContent('drawRating');
    }

    /**
     * Draws Doctor Schedules
     * @param int $doctorId
     * @param int $clinicId
     * @param int $page
	 * @return string
     */
    public static function drawDoctorSchedules($doctorId = 0, $clinicId = 0, $page = 1)
    {
    	$output                         = '';
        $arrDoctorSchedules             = array();
        $doctorSpecialtyIds             = array();
		$showMoreButton                 = false;
        $bannedAppointment              = false;
        $bannedAppointmentToSpecialist  = false;

    	$prevPage           = $page - 1;
    	$nextPage           = $page + 1;
        $sizeSchedule       = 7;
		$mobileDetect       = A::app()->getMobileDetect();
        $loggedRole         = CAuth::getLoggedRole();
        $adminLogin         = false;

        if($mobileDetect->isMobile() && !$mobileDetect->isTablet()){
            $sizeSchedule = 4;
        }

        if(in_array($loggedRole, array('admin', 'owner'))){
            $adminLogin = true;
            Website::setBackend();
        }else{
            Website::setFrontend();
        }

        //Calculate the date of the schedule
    	$nextWeekDateFrom   = (($page-1)*$sizeSchedule);
        $nextWeekDateTo     = ($page*$sizeSchedule)-1;
        $unixDateFrom  		= '+'.$nextWeekDateFrom.' days';
        $unixDateTo    		= '+'.$nextWeekDateTo.' days';
		$dateFrom           = CLocale::date('Y-m-d', strtotime($unixDateFrom), true);
		$doctor 			= Doctors::model()->findByPk($doctorId);
		if(!$doctor){
			return false;
		}
        $maxPage = DoctorScheduleTimeBlocks::model()->getMaxPageSchedules($doctorId, $clinicId);

		$unixMembershipExpires = strtotime($doctor->membership_expires);
		if(strtotime($unixDateTo) >= $unixMembershipExpires){
			$dateTo  = CLocale::date('Y-m-d', $unixMembershipExpires, true);
            $maxPage += 1;
		}else{
			$dateTo  = CLocale::date('Y-m-d', strtotime($unixDateTo), true);
		}
        $dateFormat = Bootstrap::init()->getSettings('date_format');
        $timeFormat = ModulesSettings::model()->param('appointments', 'time_format_appointment_time');

        //Check if there is a schedule for the doctor
        $existsSchedules    = DoctorScheduleTimeBlocks::model()->existsSchedulesForDoctor($doctorId, $clinicId);
        if($existsSchedules){
        	//If exists, create an array with a schedule
            $arrDoctorSchedules = DoctorScheduleTimeBlocks::model()->getSchedulesForDate($doctorId, $dateFrom, $dateTo, $clinicId);
        }


        $patientId = CAuth::getLoggedRoleId();
        if(!empty($patientId)){
            $tableAppointmentsName = CConfig::get('db.prefix').Appointments::model()->getTableName();
            // Ban a patient from ordering appointments
            $maxAppointmentPerPatient = ModulesSettings::model()->param('appointments', 'max_allowed_appointment_per_patient');
            $countAppointments = Appointments::model()->count($tableAppointmentsName.'.patient_id = :patient_id AND ('.$tableAppointmentsName.'.status = 0 OR '.$tableAppointmentsName.'.status = 1) AND ('.$tableAppointmentsName.".appointment_date > '".LocalTime::currentDateTime('Y-m-d')."'".' OR ('.$tableAppointmentsName.".appointment_date = '".LocalTime::currentDateTime('Y-m-d')."'".' AND '.$tableAppointmentsName.".appointment_time > '".LocalTime::currentDateTime('H:i:s')."'))", array(':patient_id'=>$patientId));
            if($countAppointments >= $maxAppointmentPerPatient){
                $bannedAppointment = true;
            }
        }

        //If exists schedule, create HTML
        if($existsSchedules){
            $output .= CHtml::openTag('div', array('class' => 'one_first margin-top-20'));
            $output .= CHtml::openTag('a', array('id' => 'prev_page', 'class' => ($prevPage <= 0 ? 'no_active_pagination_appointment' : ''), 'href' => 'javascript:void(0);', 'onclick' => 'slidePageBookAppointments(this);', 'data-page' => $prevPage, 'data-doctor-id' => $doctorId, 'data-clinic-id' => $clinicId, 'data-max-page' => $maxPage, 'title'=>A::t('appointments', 'Prev Page')));
            $output .= CHtml::closeTag('a');
            $output .= CHtml::openTag('a', array('id' => 'next_page', 'class' => ($nextPage > $maxPage ? 'no_active_pagination_appointment' : ''), 'href' => 'javascript:void(0);', 'onclick' => 'slidePageBookAppointments(this);', 'data-page' => $nextPage, 'data-doctor-id' => $doctorId, 'data-clinic-id' => $clinicId, 'data-max-page' => $maxPage, 'title'=>A::t('appointments', 'Next Page')));
            $output .= CHtml::closeTag('a');
            if(!empty($arrDoctorSchedules)){
                //If schedules exist display it on the screen
                foreach($arrDoctorSchedules as $date => $arrTime){
                    $unixDateCurrentDay = strtotime($date);
					$unixTodayDate = strtotime(CLocale::date('Y-m-d'));
					//create date for schedule
                    $currentDay = date('d', $unixDateCurrentDay);
					$currentYear = date('Y', $unixDateCurrentDay);
					$currentMonthWide = A::t('i18n', 'monthNames.wide.'.date('n', $unixDateCurrentDay));
					$currentMonthAbbreviated = A::t('i18n', 'monthNames.abbreviated.'.date('n', $unixDateCurrentDay));
					$currentDateWide = $currentDay.' '.$currentMonthWide.' '.$currentYear;
					$currentDateAbbreviated = $currentDay.' '.$currentMonthAbbreviated.' '.$currentYear;

					$weekDay = (date("N", $unixDateCurrentDay) + 1 > 7) ? 1 : date("N", $unixDateCurrentDay) + 1;

					$countArrTime = 0;
					$hiddenSlots = false;

                    $output .= CHtml::openTag('div', array('class' => 'book_appointment weekday aligncenter'.($unixDateCurrentDay < $unixTodayDate ? ' overdue' : ($unixDateCurrentDay == $unixTodayDate ? ' today_day' : ''))));
                    $output .= CHtml::openTag('div', array('class' => 'header'));
                    $output .= CHtml::tag('div', array('id'=>'week_day_names_wide', 'class' => 'aligncenter'), A::t('i18n', 'weekDayNames.wide.'.$weekDay));
                    $output .= CHtml::tag('div', array('id'=>'week_day_names_abbreviated', 'class' => 'aligncenter'), A::t('i18n', 'weekDayNames.abbreviated.'.$weekDay));
                    $output .= CHtml::tag('h6', array('id'=>'date_wide', 'class' => 'aligncenter'), $currentDateWide);
                    $output .= CHtml::tag('h6', array('id'=>'date_abbreviated','class' => 'aligncenter'), $currentDateAbbreviated);
                    $output .= CHtml::closeTag('div');
                    $output .= CHtml::openTag('div', array('class' => 'content_book_appointments'));
                    if(!empty($arrTime)){
                        foreach($arrTime as $time){
                            $countArrTime++;
                            $dateTime = strtotime($date.' '.$time['time']);
                            $formatingTime = CLocale::date($timeFormat, '1970-01-01 '.$time['time']);
                            if($countArrTime == 11){
                                $output .= CHtml::openTag('div', array('class' => 'hidden_slots', 'style' => 'display: none'));
                                $hiddenSlots = true;
                                $showMoreButton = true;
                            }
                            if($time['status'] == 1){
                                $output .= CHtml::tag('div', array('class' => 'time aligncenter reserved', 'title' => A::t('appointments', 'Appointment Reserved')), $formatingTime);
                            }elseif($time['status'] == 2){
                                $output .= CHtml::tag('div', array('class' => 'time aligncenter not-active', 'title' => $time['message']), $formatingTime);
                            }elseif(!empty($time['time']) && $time['status'] == 0){
                                if($loggedRole == 'doctor'){
                                    $appointmentDetailsLink = 'appointments/appointmentDetails/dateTime/'.$dateTime;
                                }else{
                                    $appointmentDetailsLink = 'appointments/appointmentDetails/doctorId/'.$doctorId.'/dateTime/'.$dateTime;
                                }
                                $output .= CHtml::openTag('div', array('class' => 'time aligncenter', 'title' => A::t('appointments', 'Click on the time to start the reservation process appointment')), $formatingTime);
                                if($bannedAppointment){
                                    $output .= CHtml::tag('a', array('href' => 'javascript:void(0);', 'onclick'=>'return onBannedAppointment(this)'), $formatingTime);
                                }else{
                                    $output .= CHtml::tag('a', array('href' => $appointmentDetailsLink), $formatingTime);
                                }
                                $output .= CHtml::closeTag('div');
                            }else{
                                $output .= CHtml::tag('div', array('class' => 'time aligncenter'));
                            }
                        }
                        if($hiddenSlots){
                            $output .= CHtml::closeTag('div');/* hidden_slots */
                        }
                    }
                    $output .= CHtml::closeTag('div');
                    $output .= CHtml::closeTag('div'); /* book_appointment */
                }
            }else{
                //Otherwise, look for the nearest schedule and make a link to it
				$nearestSchedule = DoctorScheduleTimeBlocks::model()->getNearestSchedule($doctorId, $dateFrom, $dateTo);
				$output .= CHtml::openTag('div', array('class' => 'one_first aligncenter margin-top-20'));
				if(!empty($nearestSchedule)){
					$msg = A::t('appointments', 'Sorry, from {date_from} to {date_to} there is no schedule, the nearest schedule is {link_page_schedule}',
						array(
							'{link_page_schedule}' => CHtml::tag('a', array('id' => 'page_in_nearest_schedule', 'href' => 'javascript:void(0);', 'onclick' => 'slidePageBookAppointments(this);', 'data-page' => $page + $nearestSchedule['countPage'], 'data-doctor-id' => $doctorId, 'data-max-page' => $maxPage), CLocale::date($dateFormat, $nearestSchedule['date'])),
							'{date_from}' => CLocale::date($dateFormat, $dateFrom),
							'{date_to}' => CLocale::date($dateFormat, $dateTo),
						)
					);
					$alertType = 'info';
					$actionMessage = CWidget::create('CMessage', array($alertType, $msg, array('button'=>true)));

					$output .= CHtml::openTag('div', array('class' => 'one_first margin-top-20'));
					$output .= $actionMessage;
					$output .= CHtml::closeTag('div');

				}else{
					$alertType = 'info';
					$alert = A::t('appointments', 'This doctor currently has no active schedules');
					$actionMessage = CWidget::create('CMessage', array($alertType, $alert, array('button'=>true)));

					$output .= CHtml::openTag('div', array('class' => 'one_first margin-top-20'));
					$output .= $actionMessage;
					$output .= CHtml::closeTag('div');
				}
				$output .= CHtml::closeTag('div');
			}
            $output .= CHtml::closeTag('div'); /* one_first */
			if($showMoreButton){
				$output .= CHtml::openTag('div', array('class' => 'one_first')); /* one_first */
					$output .= CHtml::openTag('div', array('class' => 'more_links aligncenter'));
						$output .= CHtml::tag('a', array('class' => 'button_small', 'href' => 'javascript:void(0);', 'onclick' => "appShowElement('.hidden_slots');appHideElement('.more_links');"), A::t('appointments', 'Show More'));
					$output .= CHtml::closeTag('div');
				$output .= CHtml::closeTag('div'); /* one_first */
			}
        }else{
			$alertType = 'info';
			$alert = A::t('appointments', 'Sorry, this doctor does not have schedules yet.');
			$actionMessage = CWidget::create('CMessage', array($alertType, $alert, array('button'=>true)));

            $output .= CHtml::openTag('div', array('class' => 'one_first margin-top-20'));
			$output .= $actionMessage;
            $output .= CHtml::closeTag('div');
        }

        return $output;

    }

    /**
     * Check if the doctor has a membership plan.
     * @param string $param
     * @param bool $redirect
     * @return bool
     */
    public static function checkAccessAccountUsingMembershipPlan($redirect = true, $param = '')
    {
        $accessAccountUsingMembershipPlan = true;

        $membershipPlanId = A::app()->getSession()->get('membershipPlanId');
        $expiredMembership = A::app()->getSession()->get('expiredMembership');

        // block access if membership plan ID empty or expired membership not empty
        if(empty($membershipPlanId) || !empty($expiredMembership)){
            $accessAccountUsingMembershipPlan = false;
        }

        // block access if $membershipPlan->enable_reviews = false
        if($param == 'enable_reviews') {
            if (!empty($membershipPlanId)) {
                $membershipPlan = Memberships::model()->findByPk($membershipPlanId);
                if (!$membershipPlan->enable_reviews) {
                    $accessAccountUsingMembershipPlan = false;
                }
            }
        }
        
        if (!$accessAccountUsingMembershipPlan && $redirect){
            self::_redirect('doctors/dashboard/');
        }

        return $accessAccountUsingMembershipPlan;
    }

    /**
     * Get of the prepared array of durations
     * @return array $prepDurations
     */
    public static function getPreparedDurations()
    {
        $prepDurations = array();
        $arrDurations = array(1,2,3,4,5,6,7,8,9,10,14,21,28,30,45,60,90,120,180,240,270,365,730,1095,1460,1825);//,-1);

        foreach($arrDurations as $days){
//            if($days < 0){
//                $prepDurations[$days] = A::t('appointments', 'Unlimited');
//            }else
            if($days < 30){
                $prepDurations[$days] = ($days == 1 ? '1 '.A::t('appointments', 'Day') : $days.' '.A::t('appointments', 'Days'));
            }elseif($days < 365){
                $prepDurations[$days] = (round($days/30,1) == 1 ? '1 '.A::t('appointments', 'Month') : round($days/30,1).' '.A::t('appointments', 'Months'));
            }else{
                $prepDurations[$days] = (round($days/365,1) == 1 ? '1 '.A::t('appointments', 'Year') : round($days/365,1).' '.A::t('appointments', 'Years'));
            }
        }

        return $prepDurations;
    }

	/**
	 * Check if the doctor has a membership plan.
	 * Check expires memberships plan.
	 * Check Reminder enabled.
	 * If the reminder is enabled Check how many days left before expires memberships plan.
	 * @param $membershipPlanId
	 * @param $membershipExpires
	 */
	public static function checkMembershipPlan($membershipPlanId, $membershipExpires)
	{

		A::app()->getSession()->remove('membershipPlanId');
		A::app()->getSession()->remove('expiredMembership');
		A::app()->getSession()->remove('reminderExpiredMembership');

		$dateFormat = Bootstrap::init()->getSettings('date_format');
		$reminderExpiredMembership = ModulesSettings::model()->param('appointments', 'reminder_expired_membership');
		//$unixMembershipExpires = strtotime($membershipExpires);
        //$unixCurrentDay = strtotime(CLocale::date('Y-m-d'));
        //$dayInSec = 24*60*60;
        $dateTimeObjectMembershipExpires = new DateTime($membershipExpires);
        $dateMembershipExpires = $dateTimeObjectMembershipExpires->format('Y-m-d');
        $currentDay = new DateTime();
        $dateCurrentDay = $currentDay->format('Y-m-d');

//        $membershipExpires = new DateTime($doctor->membership_expires);
//        $membershipExpires->modify($duration);
//        $dateExpires = $membershipExpires->format('Y-m-d');

		// Check if the doctor has a Membership plan
		if(!empty($membershipPlanId)){
			A::app()->getSession()->set('membershipPlanId', $membershipPlanId);
		}
		// Check expires memberships plan
		if($dateMembershipExpires < $dateCurrentDay){
			A::app()->getSession()->set('expiredMembership', CLocale::date($dateFormat, $membershipExpires));
		}
		// Check Reminder enabled
		if(!empty($reminderExpiredMembership)){
            $duration = '+'.$reminderExpiredMembership.' 1 days';
			$dateTimeObjectReminderExpiredMembership = $currentDay->modify($duration);
			if($dateTimeObjectReminderExpiredMembership){
				$dateReminderExpiredMembership = $dateTimeObjectReminderExpiredMembership->format('Y-m-d');
				//If the reminder is enabled Check how many days left before expires memberships plan
				if(($dateReminderExpiredMembership) >= $dateMembershipExpires){
					$daysToMembershipExpires = $interval = $dateTimeObjectReminderExpiredMembership->diff($dateTimeObjectMembershipExpires);
					A::app()->getSession()->set('reminderExpiredMembership', $daysToMembershipExpires->format('%a'));
				}
			}
		}
	}

	/**
	 * Update Membership Plan Doctor
	 * @param int $doctorId
	 * @param int $membershipId
	 * @return bool
	 */
	public static function updateMembershipPlan($doctorId = 0, $membershipId = 0){

		if(empty($doctorId) || empty($membershipId)){
			return false;
		}

		$result = false;

		$tableNameDocotr = CConfig::get('db.prefix').Accounts::model()->getTableName();
		$doctor = Doctors::model('with_appointments_counter')->findByPk($doctorId, $tableNameDocotr.'.is_active = 1 AND '.$tableNameDocotr.'.is_removed = 0');
		$membershipPlan = Memberships::model()->findByPk($membershipId, 'is_active = 1');

		if($doctor && $membershipPlan){
			$membershipTableName = CConfig::get('db.prefix').Memberships::model()->getTableName();
			$currentMembershipPlan = Memberships::model()->findAll($membershipTableName.'.id = :id AND '.$membershipTableName.'.is_active = 1', array(':id'=>$doctor->membership_plan_id));
			$currentMembershipPlan = $currentMembershipPlan[0];
			$duration = '+'.$membershipPlan->duration.' days';
			$currentDay = CLocale::date('Y-m-d');
			// If Membership plan not expired, add the number of days remaining to the membership duration
			if($currentMembershipPlan['price'] >= $membershipPlan->price && $doctor->membership_expires > $currentDay){
                $membershipExpires = new DateTime($doctor->membership_expires);
                $membershipExpires->modify($duration);
                $dateExpires = $membershipExpires->format('Y-m-d');
			}else{
				$dateExpires = date('Y-m-d', strtotime($duration));
			}
			// Add membership plan to doctor account
			$doctor->membership_plan_id             = $membershipPlan->id;
			$doctor->membership_images_count        = $membershipPlan->images_count;
			$doctor->membership_clinics_count       = $membershipPlan->clinics_count;
			$doctor->membership_schedules_count     = $membershipPlan->schedules_count;
			$doctor->membership_specialties_count   = $membershipPlan->specialties_count;
			$doctor->membership_show_in_search      = $membershipPlan->show_in_search;
			$doctor->membership_enable_reviews      = $membershipPlan->enable_reviews;
			$doctor->membership_expires 		    = $dateExpires;
			$doctor->last_membership_reminder_date  = NULL;

			if($doctor->save()){
				$result = true;
			}
		}
		
		return $result;
	}

	/**
	 * Update Membership Plan Doctor
	 * @param int $clinicId
	 * @return array
	 */
	public static function workingHoursClinic($clinicId = 0){

		if(empty($clinicId)){
			return false;
		}

        $workingHours = array();
        $workingDays = array();
        //$settings = Bootstrap::init()->getSettings();
        //$weekStartday = (int)($settings->week_startday);

        $arrWorkingHours = WorkingHours::model()->findAll('clinic_id = :clinic_id', array(':clinic_id'=>$clinicId));
        foreach($arrWorkingHours as $workingHour){
            if($workingHour['end_time'] != '00:00'){
                $workingHours[$workingHour['week_day']] = $workingHour;
            }
        }

        if(!empty($workingHours)){
            for($i=1, $day = 0; $i <= 7; $i++){
                $previousDay = isset($workingHours[$i-1]) ? $workingHours[$i-1] : null;
                $currentDay = $workingHours[$i];
                if(!$currentDay['is_day_off'] && $currentDay['end_time'] != '00:00'){
                    if(!empty($previousDay) && $currentDay['start_time'] == $previousDay['start_time'] && $currentDay['end_time'] == $previousDay['end_time']){
                        if(empty($day)){
                            $day = $i-1;
                        }
                        $workingDays[$day]['week_end_day'] = $i;
                    }else{
                        $workingDays[$i]['week_day'] = $i;
                        $workingDays[$i]['start_time'] = $currentDay['start_time'];
                        $workingDays[$i]['end_time'] = $currentDay['end_time'];
                        $workingDays[$i]['is_day_off'] = 0;
                        $day = 0;
                    }
                }else{
                    $day = 0;
                }
            }
        }

		return $workingDays;
	}

	/**
	 * Search for clinics in which the doctor is taking
	 * @param int $doctorId
	 * @return array
	 */
	public static function doctorClinics($doctorId = 0){

		if(empty($doctorId)){
			return false;
		}

        $arrClinicsId = array();
        $clinics        = array();

        $doctorSchedules = DoctorSchedules::model()->find('doctor_id = :doctor_id AND date_from <= :date AND date_to >= :date AND is_active = 1', array(':doctor_id'=>$doctorId, ':date'=>date('Y-m-d')));
        if($doctorSchedules){
            $arrDoctorTimeBlocks = DoctorScheduleTimeBlocks::model()->findAll('doctor_id = :doctor_id AND schedule_id = :schedule_id', array(':doctor_id'=>$doctorId, ':schedule_id'=>$doctorSchedules->id));
            if(!empty($arrDoctorTimeBlocks)){
                foreach($arrDoctorTimeBlocks as $arrDoctorTimeBlock){
                    if(in_array($arrDoctorTimeBlock['address_id'], $arrClinicsId)) continue;
                    $arrClinicsId[] = $arrDoctorTimeBlock['address_id'];
                }
                if(!empty($arrClinicsId)){
                    $tableNameClinic = CConfig::get('db.prefix').Clinics::model()->getTableName();
                    $arrClinics = Clinics::model()->findAll($tableNameClinic.'.id IN ('.implode(',',$arrClinicsId).') AND '.$tableNameClinic.'.is_active = 1');
                    foreach ($arrClinics as $arrClinic){
                        $clinics[$arrClinic['id']] = array(
                            'clinic_name' => $arrClinic['clinic_name'],
                            'address' => $arrClinic['address'],
                        );
                    }
                }
            }
        }

		return $clinics;
	}

	/**
	 * Get the current time of the clinic by the time zone
	 * @param int $clinicId
	 * @param bool $unixFormat
	 * @return array $time
	 */
	public static function getTimeClinic($clinicId = 0, $unixFormat = true){

		if(empty($clinicId)){
			return false;
		}

        $time = array();

		$clinic = Clinics::model()->findByPk($clinicId);
		if(!$clinic){
            $time['time'] = CLocale::date('H:i:s');
        }else{
            $settings = Bootstrap::init()->getSettings();
            $timeFormat = $settings->time_format;
            // Get Clinic Offset
            $clinicOffset = A::app()->getLocalTime()->getTimeZoneInfo($clinic->time_zone, 'offset');
            $secInHours = 60*60;
            $clinicOffsetInSec = $clinicOffset * $secInHours;
            $clinicTime = gmdate('H:i:s',time() + $clinicOffsetInSec);
            $time['time'] = CLocale::date($timeFormat, $clinicTime);
            $time['offset'] = A::app()->getLocalTime()->getTimeZoneInfo($clinic->time_zone, 'offset_name');
        }

        if($unixFormat){
            $time['time'] = strtotime($time['time']);
        }

		return $time;
	}

	/**
	 * Get array of the week days
	 * @return array $time
	 */
	public static function getStandardWeekDays(){

        $standardWeekDays = array(
            '1' => array('day'=>'sunday', 'name'=>A::t('i18n', 'weekDayNames.wide.1'), 'selected'=>'', 'checked'=>true),
            '2' => array('day'=>'monday', 'name'=>A::t('i18n', 'weekDayNames.wide.2'), 'selected'=>'', 'checked'=>false),
            '3' => array('day'=>'tuesday', 'name'=>A::t('i18n', 'weekDayNames.wide.3'), 'selected'=>'', 'checked'=>false),
            '4' => array('day'=>'wednesday', 'name'=>A::t('i18n', 'weekDayNames.wide.4'), 'selected'=>'', 'checked'=>false),
            '5' => array('day'=>'thursday', 'name'=>A::t('i18n', 'weekDayNames.wide.5'), 'selected'=>'', 'checked'=>false),
            '6' => array('day'=>'friday', 'name'=>A::t('i18n', 'weekDayNames.wide.6'), 'selected'=>'', 'checked'=>false),
            '7' => array('day'=>'saturday', 'name'=>A::t('i18n', 'weekDayNames.wide.7'), 'selected'=>'', 'checked'=>false),
        );

		return $standardWeekDays;
	}

    /**
	 * Performs redirection
     * @param string $newLocation
     */
    private function _redirect($newLocation = ''){
        $newLocation = A::app()->getRequest()->getBaseUrl().$newLocation;
        // 301 - Moved Permanently
        header('location: '.$newLocation);
        exit;
    }
}
