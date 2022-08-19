<?php
/**
 * DoctorAppointmentTime model
 *
 * PUBLIC:                      PROTECTED                  PRIVATE
 * ---------------              ---------------            ---------------
 * __construct                  _relations
 * getOpenHoursDoctors          _beforeSave
 * getSchedulesForDate          _getScheduleIdForDate
 * getSchedulesForDay           _getTimeSchedule
 * getTimeOffs                  _repairWorkingHoursInTimeBlock
 * getAppointmetsDoctors
 * getMaxPageSchedules
 * existsSchedulesForDoctor
 * getNearestSchedule
 *
 * STATIC:
 * ---------------------------------------------------------------
 * model
 *
 */

namespace Modules\Appointments\Models;

// Framework
use \A,
    \Accounts,
    \Bootstrap,
    \CLocale,
    \CTime,
    \CActiveRecord,
	\CConfig;
use Modules\Appointments\Components\DoctorsComponent;

// Application

class DoctorScheduleTimeBlocks extends CActiveRecord
{

    /** @var string */
    protected $_table = 'appt_doctor_schedule_timeblocks';

    /** @var string */
	protected $_tableClinicTranslation = 'appt_clinic_translations';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Returns the static model of the specified AR class
     * @return DoctorSchedules
     */
    public static function model()
    {
        return parent::model(__CLASS__);
    }

    /**
     * Defines relations between different tables in database and current $_table
     * @return array
     */
    protected function _relations()
    {
		return array(
			'address_id' => array(
				self::HAS_ONE,
				$this->_tableClinicTranslation,
				'clinic_id',
				'condition'=>"`language_code` = '".A::app()->getLanguage()."'",
				'joinType'=>self::LEFT_OUTER_JOIN,
				'fields'=>array(
					'address'=>'address',
					'name'=>'clinic_name',
				),
			),
		);
    }

    /**
     * This method is invoked before saving a record
     * @param int $id
     * @return bool
     */
    protected function _beforeSave($id = 0)
    {
        $cRequest   = A::app()->getRequest();
        $timeFrom   = $cRequest->getPost('time_from');
        $timeTo     = $cRequest->getPost('time_to');
        $addressId  = $cRequest->getPost('address_id');
        $weekDay    = $cRequest->getPost('week_day');

        $clinicWorkingHours = WorkingHours::model()->find('clinic_id = :clinic_id AND week_day = :week_day', array(':clinic_id'=>$addressId, ':week_day'=>$weekDay));

        $settings = Bootstrap::init()->getSettings();
        $timeFormat = $settings->time_format;

        $arrTimeFrom = CTime::dateParseFromFormat($timeFormat, $timeFrom);
        $arrTimeTo = CTime::dateParseFromFormat($timeFormat, $timeTo);

        $this->time_from = str_pad($arrTimeFrom['hour'], 2, 0, STR_PAD_LEFT).':'.str_pad($arrTimeFrom['minute'], 2, 0, STR_PAD_LEFT).':'.str_pad($arrTimeFrom['second'], 2, 0, STR_PAD_LEFT);
        $this->time_to = str_pad($arrTimeTo['hour'], 2, 0, STR_PAD_LEFT).':'.str_pad($arrTimeTo['minute'], 2, 0, STR_PAD_LEFT).':'.str_pad($arrTimeTo['second'], 2, 0, STR_PAD_LEFT);

        if(!$clinicWorkingHours){
            $this->_error = true;
            $this->_errorMessage = A::t('appointments', 'An error occurred! Please try again later.');

            return false;
        }elseif($clinicWorkingHours->is_day_off){
            $this->_error = true;
            $this->_errorMessage = A::t('appointments', 'The clinic does not work on this of the week day', array('{WEEK_DAY}'=>A::t('i18n', 'weekDayNames.wide.'.$clinicWorkingHours->week_day)));
            return false;
        }else{
            $clinicWorkingHoursFrom = CLocale::date('H:i:00', $clinicWorkingHours->start_time);
            $clinicWorkingHoursTo = CLocale::date('H:i:00', $clinicWorkingHours->end_time);

            if($this->time_from < $clinicWorkingHoursFrom){
                $this->_error = true;
                $this->_errorMessage = A::t('appointments', 'The clinic is closed at this time.', array('{WEEK_DAY}'=>A::t('i18n', 'weekDayNames.wide.'.$clinicWorkingHours->week_day), '{START_TIME}'=>$clinicWorkingHours->start_time, '{END_TIME}'=>$clinicWorkingHours->end_time));
                return false;
            }elseif($this->time_from >= $clinicWorkingHoursTo){
                $this->_error = true;
                $this->_errorMessage = A::t('appointments', 'The clinic is closed at this time.', array('{WEEK_DAY}'=>A::t('i18n', 'weekDayNames.wide.'.$clinicWorkingHours->week_day), '{START_TIME}'=>$clinicWorkingHours->start_time, '{END_TIME}'=>$clinicWorkingHours->end_time));
                return false;
            }elseif($this->time_to < $clinicWorkingHoursFrom){
                $this->_error = true;
                $this->_errorMessage = A::t('appointments', 'The clinic is closed at this time.', array('{WEEK_DAY}'=>A::t('i18n', 'weekDayNames.wide.'.$clinicWorkingHours->week_day), '{START_TIME}'=>$clinicWorkingHours->start_time, '{END_TIME}'=>$clinicWorkingHours->end_time));
                return false;
            }elseif($this->time_to > $clinicWorkingHoursTo){
                $this->_error = true;
                $this->_errorMessage = A::t('appointments', 'The clinic is closed at this time.', array('{WEEK_DAY}'=>A::t('i18n', 'weekDayNames.wide.'.$clinicWorkingHours->week_day), '{START_TIME}'=>$clinicWorkingHours->start_time, '{END_TIME}'=>$clinicWorkingHours->end_time));
                return false;
            }
        }

        if($arrTimeFrom['hour'].str_pad($arrTimeFrom['minute'], 2, 0, STR_PAD_LEFT).str_pad($arrTimeFrom['second'], 2, 0, STR_PAD_LEFT) > $arrTimeTo['hour'].str_pad($arrTimeTo['minute'], 2, 0, STR_PAD_LEFT).str_pad($arrTimeTo['second'], 2, 0, STR_PAD_LEFT)){
            $this->_error = true;
            $this->_errorMessage = A::t('appointments', 'Finish date must be later than start date! Please re-enter.');
            $this->_errorField = 'time_to';

            return false;
        }
        //Check the schedule for replays.
        $prefix = CConfig::get('db.prefix').$this->_table;
        $result = $this->count(array(
            'condition'=> $prefix.'.id != '.(int)$this->getPrimaryKey().' AND
                    '.$prefix.'.schedule_id = :schedule_id AND
                    '.$prefix.'.week_day = :week_day AND
                    '.$prefix.'.doctor_id = :doctor_id AND (
                    ('.$prefix.'.time_from <= :time_from_1 AND '.$prefix.'.time_to > :time_from_2) OR
                    ('.$prefix.'.time_from < :time_to_1 AND '.$prefix.'.time_to >= :time_to_2) OR
                    ('.$prefix.'.time_from >= :time_from_3 AND '.$prefix.'.time_to <= :time_to_3)
                )',
            ),
            array(
                ':schedule_id'=>$this->schedule_id,
                ':week_day'=>$this->week_day,
                ':doctor_id'=>$this->doctor_id,
                ':time_from_1'=>$this->time_from, ':time_from_2'=>$this->time_from, ':time_from_3'=>$this->time_from,
                ':time_to_1'=>$this->time_to, ':time_to_2'=>$this->time_to, ':time_to_3'=>$this->time_to
            )
        );

        if(!empty($result)){
            $this->_error = true;
            $this->_errorMessage = A::t('appointments', 'This period of time (fully or partially) is already chosen for selected doctor! Please re-enter.');

            return false;
        }

        return true;
    }

    /**
     * Calculation Open Hours
     * @param integer $doctorId
     * @return array $openHours
     */
    public static function getOpenHoursDoctors($doctorId = 0)
    {
        $openHours = array();

        $timeFormat = Bootstrap::init()->getSettings()->time_format;
        $doctorSchedules = DoctorSchedules::model()->findAll(array('condition' => 'doctor_id = '.$doctorId.' && is_active = true && MONTH(date_to) >= '.date('m').'&& YEAR(date_to) >= '.date('Y'), 'limit' => 1, 'orderBy' => 'date_to DESC'));

        if($doctorSchedules){
            $timeBlocks = DoctorScheduleTimeBlocks::model()->findAll(array('condition' => 'doctor_id = '.$doctorId.' && schedule_id = '.$doctorSchedules[0]['id'], 'orderBy' => 'week_day ASC, time_from ASC'));
            if(stristr($timeFormat, ':s') !== FALSE) {
                $timeFormat =  stristr($timeFormat, ':s', true);
            }
            $count = count($timeBlocks);
            for ($i=0;$i<$count;$i++){
                if(isset($openHours[$timeBlocks[$i]['week_day']])){
                    $openHours[$timeBlocks[$i]['week_day']]['time_to'] = date($timeFormat, strtotime($timeBlocks[$i]['time_to']));
                }else{
					$day = ($timeBlocks[$i]['week_day'] == 7) ? 1 : $timeBlocks[$i]['week_day'];
                    $openHours[$timeBlocks[$i]['week_day']]['time_from'] = date($timeFormat, strtotime($timeBlocks[$i]['time_from']));
                    $openHours[$timeBlocks[$i]['week_day']]['time_to'] = date($timeFormat, strtotime($timeBlocks[$i]['time_to']));
                    $openHours[$timeBlocks[$i]['week_day']]['week_day_name'] = A::t('i18n', 'weekDayNames.wide.'.$day);
                }
            }
        }
        return $openHours;
    }


	/**
	 * Get all active schedules for the specified
	 * @param int $doctorId
	 * @param int $clinicId
	 * @param string $dateFrom
	 * @param string $dateTo
	 * @return array
	 * */
	public function getSchedulesForDate($doctorId = 0, $dateFrom = '', $dateTo = '', $clinicId = 0)
	{
		$prepareSchedules = array();
		$existsSchedules = false;

		$unixDateTimeForm = strtotime($dateFrom);
		$unixDateTimeTo   = strtotime($dateTo);
		$dayInSec 		  = 24 * 60 * 60;

		$arrPrepareSchedules = array();
		for($i = $unixDateTimeForm; $i <= $unixDateTimeTo; $i += $dayInSec){
			$currentDate = date('Y-m-d', $i);
			//Find the actual schedule
			$currentSchedules = $this->getSchedulesForDay($doctorId, $currentDate, $clinicId);
			if(!empty($currentSchedules)){
				//Create from the time block array with time visits
				$arrPrepareSchedules[$currentDate] = $this->_getTimeSchedule($doctorId, $currentSchedules, $currentDate);
			}else{
				$arrPrepareSchedules[$currentDate] = '';
			}
		}
		//Check if the schedule exists
		if(!empty($arrPrepareSchedules)){
            foreach($arrPrepareSchedules as $arrPrepareSchedule){
                if(!empty($arrPrepareSchedule)){
                    $existsSchedules = true;
                }
		    }
		    if($existsSchedules){
                $prepareSchedules = $arrPrepareSchedules;
            }
		}

		return $prepareSchedules;
	}

	/**
	 * Get Schedules for Day
	 * @param int $doctorId
	 * @param int $clinicId
	 * @param string $date
	 * @return array
	 */
    public function getSchedulesForDay($doctorId = 0, $date = '', $clinicId = 0)
	{
		$timeForDayWeek = array();

		//Get Schedule ID
		$scheduleId = $this->_getScheduleIdForDate($doctorId, $date);
		if(!empty($scheduleId)){
			$numberWeekDay = date('w', strtotime($date)) + 1;
			// Find Doctor Schedule Time Blocks For Week Day
            if(empty($clinicId)){
                $condition = array('condition' => 'doctor_id = :doctor_id AND schedule_id = :schedule_id AND week_day = :week_day', 'orderBy'=>'time_from ASC');
                $param = array(':doctor_id'=>$doctorId, ':schedule_id'=>$scheduleId, ':week_day'=>$numberWeekDay);
            }else{
				$condition = array('condition' => 'doctor_id = :doctor_id AND schedule_id = :schedule_id AND week_day = :week_day AND address_id = :clinic_id', 'orderBy'=>'time_from ASC');
                $param = array(':doctor_id'=>$doctorId, ':schedule_id'=>$scheduleId, ':week_day'=>$numberWeekDay, ':clinic_id'=>$clinicId);
            }
			$timeBlocks = DoctorScheduleTimeBlocks::model()->findAll($condition, $param);

            if(!empty($timeBlocks)){
                $timeForDayWeek = $this->_repairWorkingHoursInTimeBlock($timeBlocks);
			}
		}

        return $timeForDayWeek;
	}

	/**
	 * Get TimeOffs
	 * @param int $doctorId
	 * @return array
	 */
	public function getTimeOffs($doctorId = 0)
	{
		$timeScheduleWithoutTimeoffs = array();
		$timeOffs = DoctorTimeoffs::model()->findAll('doctor_id = :doctor_id', array(':doctor_id'=>$doctorId));
		if(!empty($timeOffs)){
			$timeScheduleWithoutTimeoffs = $timeOffs;
		}

        return $timeScheduleWithoutTimeoffs;
	}

    /**
     * Get Appointments Doctors
     * @param int $doctorId
     * @param string $date
     * @return array
     */
    public function getAppointmetsDoctors($doctorId = 0, $date = '')
    {
        // Prepare an array with arrays for each doctor
        $appointmentsForDoctors = array();

        $appointmentsForDoctors[$doctorId] = array();
        $appointments = Appointments::model()->findAll('doctor_id = :doctor_id AND appointment_date = :date AND status != 2', array(':doctor_id'=>$doctorId, ':date'=>$date));
        if(!empty($appointments) && is_array($appointments)){
            $appointmentsForDoctors = $appointments;
        }

        return $appointmentsForDoctors;
    }

    /**
     * Get Max Page Schedules
     * @param int $doctorId
     * @param int $clinicId
     * @return int
     */
    public function getMaxPageSchedules($doctorId = 0, $clinicId = 0)
    {
        $maxPageSchedules = 0;
        $unixMaxDateTo = '';
		$sizeSchedule       = 7;
		$mobileDetect = A::app()->getMobileDetect();

		if($mobileDetect->isMobile() && !$mobileDetect->isTablet()){
			$sizeSchedule = 4;
		}

		//Find the relevant schedules for the given date
		$schedules = DoctorSchedules::model()->findAll(array('condition'=>'doctor_id = :doctor_id AND date_to >= :date AND is_active = 1', 'order'=>'date_to DESC'), array(':doctor_id'=>$doctorId, ':date'=>CLocale::date('Y-m-d')));
		if(!empty($schedules)){
			//Find the maximum date with a non-empty schedule
			foreach($schedules as $schedule){
			    if(empty($clinicId)){
                    $conditoin = 'doctor_id = :doctor_id AND schedule_id = :schedule_id';
                    $param = array(':doctor_id'=>$doctorId, ':schedule_id'=>$schedule['id']);
                }else{
                    $conditoin = 'doctor_id = :doctor_id AND schedule_id = :schedule_id AND address_id = :clinic_id';
                    $param = array(':doctor_id'=>$doctorId, ':schedule_id'=>$schedule['id'], ':clinic_id'=>$clinicId);
                }
				$sheduleTimeBlocks = DoctorScheduleTimeBlocks::model()->findAll($conditoin, $param);
				if(!empty($sheduleTimeBlocks)){
					$unixSchedulesDateTo = strtotime($schedule['date_to']);
					if($unixSchedulesDateTo > $unixMaxDateTo){
						$unixMaxDateTo = $unixSchedulesDateTo;
					}
				}
			}

			$doctor = Doctors::model()->findByPk($doctorId);
			if(!$doctor){
				return false;
			}else{
				$unixCurrentDate       = strtotime(date('Y-m-d'));
				$unixMaxAllowedDate    = strtotime('+2 years');
				$unixMembershipExpires = strtotime($doctor->membership_expires);
				$dayInSec 		       = 24 * 60 * 60;
				$unixMaxDateTo 		   += $dayInSec;
                $unixMembershipExpires += $dayInSec;
				//Calculate Max Page Schedules
				if($unixMaxDateTo >= $unixMembershipExpires){
					$countDays = ($unixMembershipExpires - $unixCurrentDate)/$dayInSec;
					$maxPageSchedules = ceil($countDays / $sizeSchedule);
				}elseif($unixMaxDateTo > $unixMaxAllowedDate){
					$countDays = ($unixMaxAllowedDate - $unixCurrentDate)/$dayInSec;
					$maxPageSchedules = ceil($countDays / $sizeSchedule);
				}else{
					$countDays = ($unixMaxDateTo - $unixCurrentDate)/$dayInSec;
					$maxPageSchedules = ceil($countDays / $sizeSchedule);
				}
			}
        }

        return $maxPageSchedules;
    }

    /**
     * Get exists Schedules For Doctor
     * @param int $doctorId
     * @param int $clinicId
     * @return int
     */
    public function existsSchedulesForDoctor($doctorId = 0, $clinicId = 0)
    {
		$existsSchedulesForDoctor = false;
        //Search all schedule
        $schedules = DoctorSchedules::model()->findAll(array('condition'=>'doctor_id = :doctor_id AND is_active = 1', 'order'=>'date_to DESC'), array(':doctor_id'=>$doctorId));
        //Search doctor
        $tableAccountName = CConfig::get('db.prefix').Accounts::model()->getTableName();
        $doctor = Doctors::model()->findByPk($doctorId, $tableAccountName.'.is_active = 1 AND '.$tableAccountName.'.is_removed = 0');
        if($schedules && $doctor){
            $unixCurrentDay = strtotime(CLocale::date('Y-m-d'));
            $unixDoctorMembershipExpires = strtotime($doctor->membership_expires);
            foreach($schedules as $schedule){
                $unixDateTo = strtotime($schedule['date_to']);
                $unixDateFrom = strtotime($schedule['date_from']);
                //Search actual schedule
                if($unixDateFrom <= $unixDoctorMembershipExpires && $unixDateTo >= $unixCurrentDay){
                    if(empty($clinicId)){
                        $conditoin = array('condition'=>'doctor_id = :doctor_id AND schedule_id = :schedule_id');
                        $param = array(':doctor_id'=>$doctorId, ':schedule_id'=>$schedule['id']);
                    }else{
                        $conditoin = array('condition'=>'doctor_id = :doctor_id AND schedule_id = :schedule_id AND address_id = :clinic_id');
                        $param = array(':doctor_id'=>$doctorId, ':schedule_id'=>$schedule['id'], ':clinic_id'=>$clinicId);
                    }
                    $countTimeBlocks = DoctorScheduleTimeBlocks::model()->count($conditoin, $param);
                    //If there is at least one time block in the actual schedule, return true
                    if($countTimeBlocks){
                        $existsSchedulesForDoctor = true;
                        break;
                    }
                }
            }
        }

        return $existsSchedulesForDoctor;
    }

    /**
     * Get Nearest Schedule for Doctor
	 * @param int $doctorId
	 * @param int $clinicId
     * @param string $dateFrom
     * @param string $dateTo
     * @return array
     */
    public function getNearestSchedule($doctorId = 0, $dateFrom = '', $dateTo = '', $clinicId = 0)
    {
        $nearestSchedule = array();
        $existScheduleTimeBlocks = false;
        $doctor = Doctors::model()->findByPk($doctorId);
        if($doctor){
            //Find the relevant schedules for the given date
            $schedules = DoctorSchedules::model()->findAll(array('condition'=>'doctor_id = :doctor_id AND date_from >= :date_from AND is_active = 1', 'order'=>'date_to ASC'), array(':doctor_id'=>$doctorId, ':date_from'=>$dateFrom));
            if(!empty($schedules)){
                //Find time block for schedule
                foreach ($schedules as $schedule) {
                    $sheduleTimeBlocks = DoctorScheduleTimeBlocks::model()->find(array('condition'=>'doctor_id = :doctor_id AND schedule_id = :schedule_id', 'order'=>'week_day ASC'), array(':doctor_id' => $doctorId, ':schedule_id' => $schedule['id']));
                    if(!empty($sheduleTimeBlocks)){
                        $existScheduleTimeBlocks = true;
                    }
                }
                //If a time block is found, find the date and the count of pages to check
                if(!empty($existScheduleTimeBlocks)){
                    $existSchedule = false;
                    $weekInSec = 7 * 24 * 60 * 60;
                    $countPage = 1;
                    while(!$existSchedule && $doctor->membership_expires >= $dateTo){
                        $unixDateFrom = strtotime($dateFrom) + $weekInSec;
                        $unixDateTo   = strtotime($dateTo) + $weekInSec;
                        $dateFrom     = date('Y-m-d', $unixDateFrom);
                        $dateTo       = date('Y-m-d', $unixDateTo);

                        $schedulesForDate = $this->getSchedulesForDate($doctorId, $dateFrom, $dateTo, $clinicId);

                        if(!empty($schedulesForDate)){
                            foreach($schedulesForDate as $date => $arrPrepareSchedule){
                                if(!empty($arrPrepareSchedule)){
                                    $nearestSchedule['countPage'] = $countPage;
                                    $nearestSchedule['date'] = $date;
                                    $existSchedule = true;
                                    break;
                                }
                            }
                        }
                        $countPage++;
                    }
                }
            }
        }

        return $nearestSchedule;
    }

    /**
     * Get schedule id for Date
     * @param int $doctorId
     * @param string $date
     * @return int
     */
    private function _getScheduleIdForDate($doctorId = 0, $date = '')
    {
        $scheduleId = 0;

        $result = DoctorSchedules::model()->find('doctor_id = :doctor_id AND date_from <= :date AND date_to >= :date AND is_active = 1', array(':doctor_id'=>$doctorId, ':date'=>$date));
        if(!empty($result)){
            $scheduleId = $result->id;
        }

        return $scheduleId;
    }

    /**
     * Get the schedule broken down by time
     * @param int $doctorId
     * @param array $arrDoctorSchedule
     * @param string $currentDate
     * @return array
     */
    private function _getTimeSchedule($doctorId = 0, $arrDoctorSchedule = array(), $currentDate = '')
    {
        $timeSchedule = array();
        $countTimeSchedule = 0;
        foreach($arrDoctorSchedule as $schedule){
            $unixTimeForm = strtotime($schedule['time_from']);
            $unixTimeTo   = strtotime($schedule['time_to']);
            $timeSlotsInSec   = $schedule['time_slots'] * 60;
            $unixDateNow = strtotime(CLocale::date('Y-m-d'));
            $unixCurrentDate = strtotime($currentDate);
            if($unixDateNow == $unixCurrentDate){
                $timeNow = DoctorsComponent::getTimeClinic($schedule['address_id'], true);
                $unixTimeNow = $timeNow['time'];
            }else{
                $unixTimeNow = strtotime(CLocale::date('H:i:s'));
            }
            $recordedAppointments = $this->getAppointmetsDoctors($doctorId,$currentDate);
            $timeOffs = $this->getTimeOffs($doctorId);

            for($time = $unixTimeForm; $time < $unixTimeTo; $time += $timeSlotsInSec){
                $countTimeSchedule++;
                $timeSchedule[$countTimeSchedule]['time'] = date('H:i:s', $time);
                $timeSchedule[$countTimeSchedule]['status'] = 0;
                //If there is a timeoffs and it intersects with the schedule, change the status = 2(Inactive)
                if(!empty($timeOffs)){
                    foreach($timeOffs as $timeOff){
                        $unixTimeOffDateFrom = strtotime($timeOff['date_from']);
                        $unixTimeOffDateTo = strtotime($timeOff['date_to']);
                        $unixTimeOffTimeFrom = strtotime($timeOff['time_from']);
                        $unixTimeOffTimeTo = strtotime($timeOff['time_to']);
                        if($time >= $unixTimeOffTimeFrom && $time < $unixTimeOffTimeTo && $unixCurrentDate >= $unixTimeOffDateFrom && $unixCurrentDate <= $unixTimeOffDateTo){
                            $timeSchedule[$countTimeSchedule]['status'] = 2;
                            $timeSchedule[$countTimeSchedule]['message'] = A::t('appointments', 'Holidays');
                        }
                    }
                }
                //If there is an entry in the appointment and it coincides in time, we change the status = 1(Recorded)
                if(!empty($recordedAppointments)){
                    foreach($recordedAppointments as $recordedAppointment){
                        if(!empty($recordedAppointment)){
                            $unixRecordedAppointment = strtotime($recordedAppointment['appointment_time']);
                            if($time == $unixRecordedAppointment){
                                $timeSchedule[$countTimeSchedule]['status'] = 1;
                            }
                        }
                    }
                }
                //If if the current time > schedule time, change the status = 2(Inactive)
                if($time <= $unixTimeNow && $unixCurrentDate == $unixDateNow && empty($timeSchedule[$countTimeSchedule]['status'])){
                    $timeSchedule[$countTimeSchedule]['status'] = 2;
                    $timeSchedule[$countTimeSchedule]['message'] = A::t('appointments', 'Not Active');
                }
            }
        }
        return $timeSchedule;
    }

    /**
     * Get the schedule broken down by time
     * @param array $timeBlocks
     * @return array
     */
    private function _repairWorkingHoursInTimeBlock($timeBlocks = array())
    {
        if(!empty($timeBlocks)){
            foreach($timeBlocks as $key => $timeBlock){
                // Get Working Hours for week day
                $clinicWorkingHoursForWeekDay = WorkingHours::model()->find('clinic_id = :clinic_id AND week_day = :week_day AND is_day_off = 0', array(':clinic_id'=>$timeBlock['address_id'], ':week_day'=>$timeBlock['week_day']));
                if($clinicWorkingHoursForWeekDay){
                    //Comparison of working hours and time blocks.
                    $clinicStartTime = \CLocale::date('H:i:00', $clinicWorkingHoursForWeekDay->start_time);
                    $clinicEndTime = \CLocale::date('H:i:00', $clinicWorkingHoursForWeekDay->end_time);
                    //Remove the time block if the time does not match the working hours of the clinic
                    if(($timeBlock['time_from'] < $clinicStartTime && $timeBlock['time_to'] < $clinicStartTime) || (($timeBlock['time_from'] > $clinicEndTime && $timeBlock['time_to'] > $clinicEndTime))){
                        unset($timeBlocks[$key]);
                    }else{
                        //Change "From Time", if it is different from the working hours of the clinic
                        if($timeBlock['time_from'] < $clinicStartTime && $timeBlock['time_to'] > $clinicStartTime){
                            $timeBlocks[$key]['time_from'] = $clinicStartTime;
                        }
                        //Change "To Time", if it is different from the working hours of the clinic
                        if($timeBlock['time_to'] > $clinicEndTime && $timeBlock['time_from'] < $clinicEndTime){
                            $timeBlocks[$key]['time_to'] = $clinicEndTime;
                        }
                    }
                }else{
                    //Remove the time block if the not found working hours of the clinic
                    unset($timeBlocks[$key]);
                }
            }
        }

        return $timeBlocks;

    }
}

