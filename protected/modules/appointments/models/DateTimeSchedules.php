<?php
/**
 * Appointments model
 *
 * PUBLIC:                 PROTECTED                  PRIVATE
 * ---------------         ---------------            ---------------
 * __construct                                        _prepareAllSchedules
 * initial                                            _cutTimeoff
 * getAllSchedules                                    _addSchedule
 * getSchedules                                       _getSchedule
 * getAllSchedulesForEachDay                          _updateSchedule
 * getSchedulesForDay                                 _updateKeyDate
 *                                                    _copySchedule
 *                                                    _getScheduleParam
 *                                                    _getSchedulesIdsForDoctor
 *                                                    _getScheduleIdForDateFrom
 *                                                    _addScheduleDayWeek
 *                                                    _getScheduleDayWeek
 *                                                    _additionScheduleDayWeek
 *                                                    _deleteScheduleDayWeek
 *                                                    _getDayWeek
 *                                                    _getDateStartAndEndWeek
 *                                                    _updateScheduleByTimeoffLeft
 *                                                    _updateScheduleByTimeoffRight
 *                                                    _updateScheduleByTimeoffCenter
 *                                                    _cutSchduleByTimeoff
 *                                                    _getTimeSchedule
 *                                                    _timeToUnix
 *                                                    _prepareDoctors
 *
 * STATIC:
 * ---------------------------------------------------------------
 * init
 *
 */

namespace Modules\Appointments\Models;

// Framework
use \A,
    \CConfig,
    \CModel,
    \CLocale,
    \CValidator;

// Application
use Bootstrap;

class DateTimeSchedules extends CModel
{
    /** @var array */
    protected $_data = array();
    /** @var object */
    private static $_instance;
    /* @var array */
    private $_keyToDateFrom = array();
    /* @var array */
    private $_keyToDateTo = array();
    /* @var string */
    private $_dateFrom = '';
    /* @var string */
    private $_dateTo = '';
    /* @var array */
    private $_doctorIds = '';
    /* @var int */
    private $_dayInSec = 0;

    public function __construct($params)
    {
        $this->_dayInSec = 24 * 60 * 60;
        $this->initial($params);
    }

    public static function init($params = array())
    {
        if(self::$_instance == null){
            self::$_instance = new self($params);
        }else{
            self::$_instance->initial($params);
        }
        return self::$_instance;
    }

    /**
     * @param array $params
     */
    public function initial($params = array())
    {
        if(!empty($params)){
            $doctorIds = !empty($params['doctorIds']) && is_array($params['doctorIds'])          ? $params['doctorIds'] : array();
            $dateFrom  = !empty($params['dateFrom'])  && CValidator::isDate($params['dateFrom']) ? $params['dateFrom']  : '';
            $dateTo    = !empty($params['dateTo'])    && CValidator::isDate($params['dateTo'])   ? $params['dateTo']    : '';

            if(empty($dateFrom) && empty($dateTo)){
                $today           = CLocale::date('Y-m-d');
				$arrDateStartEndWeek = $this->_getDateStartAndEndWeek($today);
                $dateFrom        = $arrDateStartEndWeek['start_week'];
                $dateTo          = $arrDateStartEndWeek['end_week'];
            }elseif(empty($dateTo)){
                $dateTo = date('Y-m-d', strtotime($dateFrom) + 7 * $this->_dayInSec);
            }elseif(empty($dateFrom)){
                $dateFrom = date('Y-m-d', strtotime($dateTo) - 7 * $this->_dayInSec);
            }

            $this->_dateFrom  = $dateFrom;
            $this->_dateTo    = $dateTo;

            $this->_prepareDoctors($doctorIds);
            $this->_prepareAllSchedules();
            $this->_cutTimeoff();
        }
    }

    /**
     * Get all active schedules
     * @return array
     * */
    public function getAllSchedules()
    {
        return $this->_data;
    }

    /**
     * Get Schedules
     * @param $doctorId
     */
    public function getSchedules($doctorId)
    {
        return isset($this->_data[$doctorId]) ? $this->_data[$doctorId] : array();
    }

    /**
     * Get all active schedules for each day
     * @return array
     */
    public function getAllSchedulesForEachDay()
    {
        $prepareSchedules = array();
        $unixDateTimeForm = strtotime($this->_dateFrom);
        $unixDateTimeTo   = strtotime($this->_dateTo);

        foreach($this->_doctorIds as $doctorId){
            $prepareSchedulesForDoctor = array();
            for($i = $unixDateTimeForm; $i <= $unixDateTimeTo; $i += $this->_dayInSec){
                $currectDate = date('Y-m-d', $i);
                $currectSchedules = $this->getSchedulesForDay($doctorId, $currectDate);
                if(!empty($currectSchedules)){
                    $prepareSchedulesForDoctor[$currectDate] = $currectSchedules;
                }
            }
            if(!empty($prepareSchedulesForDoctor)){
                $prepareSchedules[$doctorId] = $prepareSchedulesForDoctor;
            }
        }

        return $prepareSchedules;
    }

    /**
     * Get Schedules for Day
     * @param int $doctorId
     * @param string $date
     * @return array
     */
    public function getSchedulesForDay($doctorId, $date)
    {
        if(empty($this->_data[$doctorId])){
            return array();
        }

        $timeForDayWeek = array();

        $scheduleId = $this->_getScheduleIdForDate($doctorId, $date);
        if($scheduleId > 0){
            $schedule = $this->_getSchedule($doctorId, $scheduleId);
            $dayWeek = date('w', strtotime($date));
            $timeForDayWeek = $this->_getScheduleDayWeek($doctorId, $scheduleId, $dayWeek);
        }

        return $timeForDayWeek;
    }

    /**
     * Get formatted schedules
     * @param array $doctorIds
     * @param string $dateFrom
     * @param string $dateTo
     * @return void
     */
    private function _prepareAllSchedules()
    {
        $condition = 'date_from <= :date_to AND date_to >= :date_from';
        $params    = array(
            ':date_from' => $this->_dateFrom,
            ':date_to'   => $this->_dateTo
        );

        $tableDoctorSchedules = CConfig::get('db.prefix').DoctorSchedules::model()->getTableName();
        $condition .= ' AND '.$tableDoctorSchedules.'.doctor_id IN ('.implode(',',$this->_doctorIds).')';


        $schedules = DoctorSchedules::model('timeblocks')->findAll($condition, $params);
        if(!empty($schedules)){
            foreach($schedules as $oneSchedule){
                $doctorId  = $oneSchedule['doctor_id'];
                $dateFrom  = (strtotime($oneSchedule['date_from']) < strtotime($this->_dateFrom)) ? $this->_dateFrom : $oneSchedule['date_from'];
                $dateTo    = (strtotime($oneSchedule['date_to'])   > strtotime($this->_dateTo))   ? $this->_dateTo   : $oneSchedule['date_to'];
                $timeFrom  = $oneSchedule['time_from'];
                $timeTo    = $oneSchedule['time_to'];
                $timeSlots = $oneSchedule['time_slots'];
                $weekDay   = $oneSchedule['week_day'];

                $scheduleId = $this->_getScheduleIdForDateFrom($doctorId, $dateFrom);
                if(empty($scheduleId)){
                    $scheduleId = $this->_addSchedule($doctorId, $dateFrom, $dateTo);
                }
                $timeSchedule = $this->_getTimeSchedule($timeFrom, $timeTo, $timeSlots);
                $scheduleDayWeek = $this->_getScheduleDayWeek($doctorId, $scheduleId, $weekDay);

                $this->_addScheduleDayWeek($doctorId, $scheduleId, $weekDay, $timeSchedule);
            }
        };
    }

    /**
     * Remove timeoff for schedules
     * @param array $doctorIds
     * @param string $dateFrom
     * @param string $dateTo
     * @return array
     */
    private function _cutTimeoff()
    {
        $condition = 'date_from <= :date_to AND date_to >= :date_from';
        $params    = array(
            ':date_from' => $this->_dateFrom,
            ':date_to' => $this->_dateTo
        );

        $condition = 'doctor_id IN ('.implode(',',$this->_doctorIds).') AND '.$condition;

        $timeoffs = DoctorTimeoffs::model()->findAll($condition, $params);

        if(!empty($timeoffs) && is_array($timeoffs)){
            foreach($timeoffs as $oneTimeoff){
                $doctorId        = $oneTimeoff['doctor_id'];
                $timeoffDateFrom = $oneTimeoff['date_from'];
                $timeoffDateTo   = $oneTimeoff['date_to'];
                $timeoffTimeFrom = $oneTimeoff['time_from'];
                $timeoffTimeTo   = $oneTimeoff['time_to'];

                $scheduleIds = $this->_getSchedulesIdsForDoctor($doctorId);
                if(!empty($scheduleIds)){
                    foreach($scheduleIds as $pk){
                        $unixScheduleDateFrom = strtotime($this->_getScheduleParam($doctorId, $pk, 'dateFrom'));
                        $unixScheduleDateTo   = strtotime($this->_getScheduleParam($doctorId, $pk, 'dateTo'));
                        $unixTimeoffDateFrom  = strtotime($timeoffDateFrom);
                        $unixTimeoffDateTo    = strtotime($timeoffDateTo);
                        $unixTimeoffTimeTo    = $this->_timeToUnix($timeoffTimeTo);
                        $unixTimeoffTimeFrom  = $this->_timeToUnix($timeoffTimeFrom);

                        if($unixTimeoffDateFrom <= $unixScheduleDateFrom && $unixTimeoffDateTo >= $unixScheduleDateFrom){
                            //.......|--schedules--|.
                            //.|--timeoff--|.........
                            $this->_updateScheduleByTimeoffLeft($doctorId, $pk, $unixScheduleDateTo, $unixTimeoffDateTo, $unixTimeoffTimeTo);
                        }elseif($unixTimeoffDateFrom <= $unixScheduleDateTo && $unixTimeoffDateTo >= $unixScheduleDateTo){
                            //.|--schedules--|.......
                            //.........|--timeoff--|.
                            $this->_updateScheduleByTimeoffRight($doctorId, $pk, $unixScheduleDateFrom, $unixTimeoffDateFrom, $unixTimeoffTimeFrom);
                        }elseif($unixTimeoffDateFrom >= $unixScheduleDateFrom && $unixTimeoffDateTo <= $unixScheduleDateTo){
                            //....|--schedules--|....
                            //.....|--timeoff--|.....
                            $this->_updateScheduleByTimeoffCenter($doctorId, $pk, $unixScheduleDateFrom, $unixScheduleDateTo, $unixTimeoffDateFrom, $unixTimeoffDateTo, $unixTimeoffTimeFrom, $unixTimeoffTimeTo);
                        }
                    }
                }
            }
        }
    }

    /**
     * Add schedule
     * @param string $dateFrom
     * @param string $dateTo
     * @param array $scheduleDayWeek
     * @return int
     */
    private function _addSchedule($doctorId, $dateFrom, $dateTo, $scheduleDayWeek = array())
    {
        if(isset($this->_keyToDateFrom[$doctorId][$dateFrom]) || isset($this->_keyToDateTo[$doctorId][$dateTo])){
            return false;
        }

        if(empty($this->_data[$doctorId])){
            $this->_data[$doctorId] = array();
        }

        $pk = count($this->_data[$doctorId]) + 1;

        if(empty($this->_data[$doctorId][$pk])){
            $this->_data[$doctorId][$pk] = array(
                'dateFrom'        => $dateFrom,
                'dateTo'          => $dateTo,
                'scheduleDayWeek' => $scheduleDayWeek
            );

            $this->_updateKeyDate($doctorId);

            return $pk;
        }

        return 0;
    }

    /**
     * Get one Schedule
     * @param $doctorId
     * @param $pk
     */
    private function _getSchedule($doctorId, $pk)
    {
        return isset($this->_data[$doctorId][$pk]) ? $this->_data[$doctorId][$pk] : array();
    }

    /**
     * Update schedule
     * @param string $pk
     * @param array $param
     * @return void
     */
    private function _updateSchedule($doctorId, $pk, $params)
    {
        $schedule = $this->_getSchedule($doctorId, $pk);
        if(!empty($schedule)){
            $dateFrom = !empty($params['dateFrom']) ? $params['dateFrom'] : $schedule['dateFrom'];
            $dateTo   = !empty($params['dateTo']) ? $params['dateTo'] : $schedule['dateTo'];

            $schedule['dateFrom'] = $dateFrom;
            $schedule['dateTo'] = $dateTo;

            $this->_data[$doctorId][$pk] = $schedule;

            $this->_updateKeyDate($doctorId);

            return true;
        }

        return false;
    }

    private function _updateKeyDate($doctorId)
    {
        $this->_keyToDateFrom[$doctorId] = array();
        $this->_keyToDateTo[$doctorId]   = array();

        $schedules = $this->getSchedules($doctorId);
        if(!empty($schedules)){
            foreach($schedules as $pk => $scheduleInfo){
                $dateFrom = $scheduleInfo['dateFrom'];
                $dateTo   = $scheduleInfo['dateTo'];

                $this->_keyToDateFrom[$doctorId][$dateFrom] = $pk;
                $this->_keyToDateTo[$doctorId][$dateTo]     = $pk;
            }
        }
    }

    /**
     * Copy Schedule
     * @param $doctorId
     * @param $pk
     * @return int
     */
    private function _copySchedule($doctorId, $pk)
    {
        if(isset($this->_data[$doctorId][$pk])){
            $copySchedule = $this->_data[$doctorId][$pk];
            $copyPk = count($this->_data[$doctorId]) + 1;
            $this->_data[$doctorId][$copyPk] = $copySchedule;
            return $copyPk;
        }

        return 0;
    }

    /**
     * Get param for schedule
     * @param int $pk
     * @param int $pk
     * @param string $paramName
     * @return mixed
     */
    private function _getScheduleParam($doctorId, $pk, $paramName)
    {
        $paramValue = null;
        $paramName = in_array($paramName, array('dateFrom', 'dateTo', 'scheduleDayWeek')) ? $paramName : '';
        $schedules = $this->_getSchedule($doctorId, $pk);
        if(!empty($schedules) && !empty($paramName)){
            $paramValue = $schedules[$paramName];
        }

        return $paramValue;
    }

    /**
     * Get Schedule IDs for doctor
     * @param $doctorId
     * @return array
     */
    private function _getSchedulesIdsForDoctor($doctorId)
    {
        $arrScheduleIds = array();
        if(isset($this->_data[$doctorId])){
            foreach($this->_data[$doctorId] as $pk => $infoSchedules){
                $arrScheduleIds[] = $pk;
            }
        }

        return $arrScheduleIds;
    }

    /**
     * Get schedule ID for date from
     * @param string $dateFrom
     * @return int
     * */
    private function _getScheduleIdForDateFrom($doctorId, $dateFrom)
    {
        if(isset($this->_keyToDateFrom[$doctorId][$dateFrom])){
            return $this->_keyToDateFrom[$doctorId][$dateFrom];
        }

        return 0;
    }

    /**
     * Add schedule day week
     * @param int $pk
     * @param int $weekDay
     * @param array $timeSchedule
     * @return bool
     */
    private function _addScheduleDayWeek($doctorId, $pk, $weekDay, $timeSchedule)
    {
        if(isset($this->_data[$doctorId][$pk])){
            $this->_data[$doctorId][$pk]['scheduleDayWeek'][$weekDay] = $timeSchedule;
            return true;
        }

        return false;
    }

    /**
     * Add schedule day week
     * @param int $scheduleId
     * @param int $weekDay
     * @param array $timeSchedule
     * @return array
     */
    private function _getScheduleDayWeek($doctorId, $pk, $weekDay)
    {
        if(isset($this->_data[$doctorId][$pk]['scheduleDayWeek'][$weekDay])){
            return $this->_data[$doctorId][$pk]['scheduleDayWeek'][$weekDay];
        }

        return array();
    }

    /**
     * Addition schedule day week
     * @param int $pk
     * @param int $weekDay
     * @param array $timeSchedule
     * @return bool
     */
    private function _additionScheduleDayWeek($doctorId, $pk, $weekDay, $timeSchedule)
    {
        if(isset($this->_data[$doctorId][$pk])){
            if(isset($this->_data[$doctorId][$pk]['scheduleDayWeek'][$weekDay])){
                $this->_data[$doctorId][$pk]['scheduleDayWeek'][$weekDay] = array_merge($this->_data[$doctorId][$pk]['scheduleDayWeek'][$weekDay], $timeSchedule);
            }else{
                $this->_data[$doctorId][$pk]['scheduleDayWeek'][$weekDay] = $timeSchedule;
            }
            return true;
        }

        return false;
    }

    /**
     * Delete schedule day week
     * @param int $scheduleId
     * @param int $weekDay
     * @param array $timeSchedule
     * @return array
     */
    private function _deleteScheduleDayWeek($doctorId, $pk, $weekDay)
    {
        if(isset($this->_data[$doctorId][$pk]['scheduleDayWeek'][$weekDay])){
            unset($this->_data[$doctorId][$pk]['scheduleDayWeek'][$weekDay]);
            return true;
        }

        return false;
    }

    /**
     * Find the number active day week
     * @param string $date
     * @return int
     */
    private function _getDayWeek($date)
    {
        $weekStartday  = Bootstrap::init()->getSettings('week_startday');
        $unixDate      = strtotime($date);
        $dayWeek       = date('w', $unixDate);
        $activeDayWeek = (8 - $weekStartday + $dayWeek) % 7;

        return $activeDayWeek;
    }

    /**
     * Finds and get the start and end dates of the week
     * @param string $date
     * @return array
     */
    private function _getDateStartAndEndWeek($date)
    {
        $arrOutput = array();
        $dayInSec  = 24 * 60 * 60;
        $unixDate  = strtotime($date);

        $dayWeek = $this->_getDayWeek($date);
        $arrOutput['start_week'] = date('Y-m-d', ($unixDate - $dayWeek * $dayInSec));
        $arrOutput['end_week']   = date('Y-m-d', ($unixDate + (6 - $dayWeek) * $dayInSec));

        return $arrOutput;
    }

    /**
     * Get schedule id for Date
     * @param int $doctorId
     * @param string $date
     * @return int
     */
    private function _getScheduleIdForDate($doctorId, $date)
    {
        $result = 0;
        $keyForDateFrom = array();
        $keyForDateTo   = array();

        $unixDate = strtotime($date);
        foreach($this->_keyToDateFrom[$doctorId] as $dateFrom => $key){
            if(strtotime($dateFrom) <= $unixDate){
                $keyForDateFrom[] = $key;
            }
        }

        foreach($this->_keyToDateTo[$doctorId] as $dateTo => $key){
            if(strtotime($dateTo) >= $unixDate){
                $keyForDateTo[] = $key;
            }
        }

        // After finding the matches between the arrays, there is no more than one value
        $scheduleForDate = array_intersect($keyForDateFrom, $keyForDateTo);
        if(!empty($scheduleForDate)){
            $result = array_pop($scheduleForDate);
        }

        return $result;
    }

    /**
     * Update schedule by timeoff left
     *.......|--schedules--|.
     *.|--timeoff--|.........
     * @param string $pk
     * @param int $unixScheduleDateTo
     * @param int $unixTimeoffDateTo
     * @param int $unixTimeoffTimeTo
     * @return array
     */
    private function _updateScheduleByTimeoffLeft($doctorId, $pk, $unixScheduleDate, $unixTimeoffDate, $unixTimeoffTime)
    {
        $dayInSec        = 24 * 60 * 60;
        $weekDayDateFrom = date('w', $unixTimeoffDateTo);

        if(($unixTimeoffDateTo + $dayInSec) <= $unixScheduleDateTo){
            $params = array('dateFrom' => date('Y-m-d', $unixTimeoffDateTo + $dayInSec));
            $this->_updateSchedule($doctorId, $pk, $params);
        }

        $this->_cutSchduleByTimeoff($doctorId, $pk, $weekDayDateFrom, $unixTimeoffDateTo, $unixTimeoffTimeTo, true);
    }

    /**
     * Update schedule for timeoff right
     *.|--schedules--|.......
     *.........|--timeoff--|.
     * @param string $pk
     * @param int $unixScheduleDateFrom
     * @param int $unixTimeoffDateFrom
     * @param int $unixTimeoffTimeFrom
     * @return void
     */
    private function _updateScheduleByTimeoffRight($doctorId, $pk, $unixScheduleDateFrom, $unixTimeoffDateFrom, $unixTimeoffTimeFrom)
    {
        $dayInSec        = 24 * 60 * 60;
        $weekDayDateTo   = date('w', $unixTimeoffDateFrom);

        if(($unixTimeoffDateFrom - $dayInSec) >= $unixScheduleDateFrom){
            $params = array('dateTo' => date('Y-m-d', $unixTimeoffDateFrom - $dayInSec));
            $this->_updateSchedule($doctorId, $pk, $params);
        }

        $this->_cutSchduleByTimeoff($doctorId, $pk, $weekDayDateTo, $unixTimeoffDateFrom, $unixTimeoffTimeFrom, false);
    }

    /**
     * Update schedule for timeoff center
     *....|--schedules--|....
     *.....|--timeoff--|.....
     * @param string $pk
     * @param int $unixScheduleDateFrom
     * @param int $unixScheduleDateTo
     * @param int $unixTimeoffDateFrom
     * @param int $unixTimeoffDateTo
     * @param int $unixTimeoffTimeFrom
     * @param int $unixTimeoffTimeTo
     * @return void
     */
    private function _updateScheduleByTimeoffCenter($doctorId, $pk, $unixScheduleDateFrom, $unixScheduleDateTo, $unixTimeoffDateFrom, $unixTimeoffDateTo, $unixTimeoffTimeFrom, $unixTimeoffTimeTo)
    {
        $dayInSec            = 24 * 60 * 60;
        $weekDayDateFrom     = date('w', $unixTimeoffDateTo);
        $weekDayDateTo       = date('w', $unixTimeoffDateFrom);

        if(($unixTimeoffDateFrom - $dayInSec) < $unixScheduleDateFrom || ($unixTimeoffDateTo + $dayInSec) > $unixScheduleDateTo){
            if(($unixTimeoffDateFrom - $dayInSec) >= $unixScheduleDateFrom){
                $params = array('dateFrom' => date('Y-m-d', $unixTimeoffDateFrom - $dayInSec));
                $this->_updateSchedule($doctorId, $pk, $params);
            }elseif(($unixTimeoffDateTo + $dayInSec) <= $unixScheduleDateTo){
                $params = array('dateTo' => date('Y-m-d', $unixTimeoffDateTo + $dayInSec));
                $this->_updateSchedule($doctorId, $pk, $params);
            }
        }else{
            // Split the time of work into 2 arrays
            $copyPk = $this->_copySchedule($doctorId, $pk);

            $params     = array('dateFrom'=>date('Y-m-d', $unixTimeoffDateTo + $dayInSec));
            $paramsCopy = array('dateTo' => date('Y-m-d', $unixTimeoffDateFrom - $dayInSec));

            $this->_updateSchedule($doctorId, $pk, $params);
            $this->_updateSchedule($doctorId, $copyPk, $paramsCopy);
        }

        if($unixTimeoffDateFrom == $unixTimeoffDateTo){
            $scheduleWeek = $this->_getScheduleDayWeek($doctorId, $pk, $weekDayDateTo);
            $copyScheduleWeek = $scheduleWeek;

            foreach($scheduleWeek as $id => $oneSchedule){
                $unixTime = $this->_timeToUnix($oneSchedule);
                if($unixTime < $unixTimeoffTimeTo && $unixTime > $unixTimeoffTimeFrom){
                    unset($copyScheduleWeek[$id]);
                }
            }

            $dateFromTo = date('Y-m-d', $unixTimeoffDateTo);
            $scheduleId = $this->_addSchedule($doctorId, $dateFromTo, $dateFromTo, array($weekDayDateTo => $copyScheduleWeek));
        }else{
            $this->_cutSchduleByTimeoff($doctorId, $pk, $weekDayDateTo, $unixTimeoffDateFrom, $unixTimeoffTimeFrom, false);
            $this->_cutSchduleByTimeoff($doctorId, $pk, $weekDayDateFrom, $unixTimeoffDateTo, $unixTimeoffTimeTo, true);
        }

    }

    /**
     * The function removes non-working time from the doctor's schedule
     * @param string $pk
     * @param int $weekDay
     * @param int $unixTimeoffDate
     * @param int $unixTimeoffTime
     * @param bool $removeBeforTimeoff
     * @return void
     */
    private function _cutSchduleByTimeoff($doctorId, $pk, $weekDay, $unixTimeoffDate, $unixTimeoffTime, $removeBeforTimeoff = true)
    {
        $scheduleWeek = $this->_getScheduleDayWeek($doctorId, $pk, $weekDay);
        $copyScheduleWeek = $scheduleWeek;

        foreach($scheduleWeek as $id => $time){
            if($removeBeforTimeoff ? $this->_timeToUnix($time) <= $unixTimeoffTime : $this->_timeToUnix($time) >= $unixTimeoffTime){
                unset($copyScheduleWeek[$id]);
            }
        }

        $dateFromTo = date('Y-m-d', $unixTimeoffDate);
        $scheduleId = $this->_getScheduleIdForDateFrom($doctorId, $dateFromTo);
        if(empty($scheduleId)){
            $scheduleId = $this->_addSchedule($doctorId, $dateFromTo, $dateFromTo);
        }
        $this->_addScheduleDayWeek($doctorId, $scheduleId, $weekDay, $copyScheduleWeek);
    }

    /**
     * Prepare time schedule
     * @param string $timeFrom
     * @param string $timeTo
     * @param int $timeSlotsInMinutes
     * @return array
     */
    private function _getTimeSchedule($timeFrom, $timeTo, $timeSlotsInMinutes)
    {
        $arrOutput = array();
        $unixDateTimeForm = $this->_timeToUnix($timeFrom);
        $unixDateTimeTo   = $this->_timeToUnix($timeTo);
        $timeSlotsInSec   = $timeSlotsInMinutes * 60;

        for($i = $unixDateTimeForm; $i < $unixDateTimeTo; $i += $timeSlotsInSec){
            $arrOutput[] = date('H:i:s', $i);
        }

        return $arrOutput;
    }

    /**
     * Translates time into the number of seconds since the beginning of the day
     * @param string $time
     * @return int
     */
    private function _timeToUnix($time)
    {
        return strtotime('1970-01-01 '.$time);
    }

    private function _prepareDoctors($doctorIds)
    {
        $this->_doctorIds = array();

        if(!empty($doctorIds) && is_array($doctorIds)){
            foreach($doctorIds as $doctorId){
                $doctorId = (int)$doctorId;
                if($doctorId != 0 && !in_array($doctorId, $this->_doctorIds)){
                    $this->_doctorIds[] = $doctorId;
                }
            }
        }
    }
}

