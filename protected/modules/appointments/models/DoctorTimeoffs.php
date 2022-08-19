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
 *
 */

namespace Modules\Appointments\Models;

// Framework
use \A,
    \Bootstrap,
    \CTime,
    \CActiveRecord;

// Application

class DoctorTimeoffs extends CActiveRecord
{

    /** @var string */
    protected $_table = 'appt_doctor_timeoffs';

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
        return array();
    }

    /**
     * This method is invoked before saving a record
     * @param string $id
     * @return bool
     */
    protected function _beforeSave($id = 0)
    {
        $cRequest = A::app()->getRequest();
        $timeFrom = $cRequest->getPost('time_from');
        $timeTo   = $cRequest->getPost('time_to');

        $settings = Bootstrap::init()->getSettings();
        $timeFormat = $settings->time_format;

        $minDate = A::app()->getRequest()->getPost('date_from');
        $maxDate = A::app()->getRequest()->getPost('date_to');
        $minTime = A::app()->getRequest()->getPost('time_from');
        $maxTime = A::app()->getRequest()->getPost('time_to');
        $arrMinTimeDate = CTime::dateParseFromFormat('Y-m-d '.$timeFormat, $minDate.' '.$minTime);
        $arrMaxTimeDate = CTime::dateParseFromFormat('Y-m-d '.$timeFormat, $maxDate.' '.$maxTime);
        $unixMinTimeDate = mktime($arrMinTimeDate['hour'], $arrMinTimeDate['minute'], $arrMinTimeDate['second'], $arrMinTimeDate['month'], $arrMinTimeDate['day'], $arrMinTimeDate['year']);
        $unixMaxTimeDate = mktime($arrMaxTimeDate['hour'], $arrMaxTimeDate['minute'], $arrMaxTimeDate['second'], $arrMaxTimeDate['month'], $arrMaxTimeDate['day'], $arrMaxTimeDate['year']);
        if($unixMinTimeDate > $unixMaxTimeDate){
            $this->_error = true;
            $this->_errorMessage = A::t('appointments', 'Finish date must be later than start date! Please re-enter.');
            $this->_errorField = 'time_to';
            return false;
        }

        if(!empty($timeFrom)){
            $arrTimeFrom = CTime::dateParseFromFormat($timeFormat, $timeFrom);
            $this->time_from = str_pad($arrTimeFrom['hour'], 2, 0, STR_PAD_LEFT).':'.str_pad($arrTimeFrom['minute'], 2, 0, STR_PAD_LEFT).':'.str_pad($arrTimeFrom['second'], 2, 0, STR_PAD_LEFT);
        }

        if(!empty($timeFrom)){
            $arrTimeTo = CTime::dateParseFromFormat($timeFormat, $timeTo);
            $this->time_to = str_pad($arrTimeTo['hour'], 2, 0, STR_PAD_LEFT).':'.str_pad($arrTimeTo['minute'], 2, 0, STR_PAD_LEFT).':'.str_pad($arrTimeTo['second'], 2, 0, STR_PAD_LEFT);
        }

        return true;
    }
}

