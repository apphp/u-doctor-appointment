<?php
/**
 * WorkingHours controller
 *
 * PUBLIC:                          PRIVATE
 * -----------                      ------------------
 * __construct                      _outputAjax
 * indexAction                      _outputJson
 * editAction                       _getWeekDays
 * ajaxGetActiveWeekDaysAction
 * ajaxGetWorkingHoursAction
 *
 */

namespace Modules\Appointments\Controllers;

// Module
use \Modules\Appointments\Components\AppointmentsComponent;
use \Modules\Appointments\Components\DoctorsComponent;
use \Modules\Appointments\Models\Appointments;
use \Modules\Appointments\Models\Orders;
use \Modules\Appointments\Models\WorkingHours;
use \Modules\Appointments\Models\Clinics;

// Global
use \A,
    \CAuth,
    \CLocale,
    \CController,
	\CWidget;

// Application
use \Website,
    \Bootstrap,
    \Modules;


class WorkingHoursController extends CController
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

		if(CAuth::isLoggedInAsAdmin()){
			// Set meta tags according to current menu
			Website::setMetaTags(array('title'=>A::t('appointments', 'Working Hours Management')));

			$this->_view->actionMessage = '';
			$this->_view->errorField = '';			
			$this->_view->tabs = AppointmentsComponent::prepareTab('workinghours');

            $standardWeekDays = DoctorsComponent::getStandardWeekDays();

            $weekDays = $this->_getWeekDays($standardWeekDays);

            $this->_view->weekDays = $weekDays;
		}
	}

	/**
	 * Controller default action handler
	 */
    public function indexAction()
	{
		$this->redirect('workingHours/edit');	   	
    }	
      
    /**
     * Clinic working hours edit action handler
     * @param int $clinicId
     * @return void
     */
    public function editAction($clinicId = 0)
    {
        Website::prepareBackendAction('manage', 'clinic', 'clinics/manage');

    	$cRequest = A::app()->getRequest();
        $alert = '';
        $alertType = '';
        $actionMessage = '';
		$clinics = Clinics::model()->findAll(array('','order'=>'id ASC'));

        $clinicId = empty($clinicId) ? $clinics[0]['id'] : $clinicId;

        if($cRequest->getPost('act') == 'send'){
            for($i = 1; $i <= 7; $i++){
                $day = isset($this->_view->weekDays[$i]['day']) ? $this->_view->weekDays[$i]['day'] : 0;
                $dayName = isset($this->_view->weekDays[$i]['name']) ? $this->_view->weekDays[$i]['name'] : 0;
                $hourFrom = $cRequest->getPost($day.'_hour_from', 'hour');
                $minuteFrom = $cRequest->getPost($day.'_minute_from', 'minute');
                $hourTo = $cRequest->getPost($day.'_hour_to', 'hour');
                $minuteTo = $cRequest->getPost($day.'_minute_to', 'minute');
                $dayOff = (int)$cRequest->getPost($day.'_dayoff');

                // Validate from/to times
                if($hourFrom.$minuteFrom >= $hourTo.$minuteTo && !$dayOff){                    
                    $alert = A::t('appointments', 'Finish time {finish-time} cannot be equal to or earlier than the start time {start-time}! Please check {weekday}.', array('{finish-time}'=>$hourTo.':'.$minuteTo, '{start-time}'=>$hourFrom.':'.$minuteFrom, '{weekday}'=>$dayName));
                    $alertType = 'error';
                    break;
                }else{
                    $workingDay = WorkingHours::model()->find('week_day = :week_day AND clinic_id = :clinic_id', array(':clinic_id' => $clinicId, ':week_day'=> $i));
                    if(!$dayOff){
                        $workingDay->start_time = ($hourFrom != '') ? $hourFrom.':'.$minuteFrom : '00:00';
                        $workingDay->end_time = ($hourTo != '') ? $hourTo.':'.$minuteTo : '00:00';
                    }else{
                        $workingDay->start_time = '00:00';
                        $workingDay->end_time = '00:00';
                    }
                    $workingDay->is_day_off = $dayOff;
					
					if(APPHP_MODE == 'demo'){
						$alert = A::t('appointments', 'This operation is blocked in Demo Mode!');
						$alertType = 'warning';
					}else{
                        $alert = A::t('appointments', 'Working Hours have beed successfully saved!');
                        $alertType = 'success';
                        $workingDay->save();
					}
                }
            }
			
            if(!empty($alertType)){
                A::app()->getSession()->setFlash('alert', $alert);
                A::app()->getSession()->setFlash('alertType', $alertType);
                $this->redirect('workingHours/edit/clinicId/'.$clinicId);
            }
       	}

    	$arrWorkingHours = WorkingHours::model()->findAll('clinic_id = '.$clinicId);
        if(!$arrWorkingHours){
            $this->redirect('modules/settings/code/appointments');
        }
        foreach($arrWorkingHours as $workingHour){
            $workingHours[$workingHour['week_day']] = $workingHour;
        }

        $alert = A::app()->getSession()->getFlash('alert');
        $alertType = A::app()->getSession()->getFlash('alertType');

        if(!empty($alertType)){
            $actionMessage = CWidget::create('CMessage', array($alertType, $alert, array('button'=>true)));
        }

        $this->_view->actionMessage = $actionMessage;
        $this->_view->workingHours = $workingHours;
        $this->_view->clinicId = $clinicId;
        $this->_view->clinics = $clinics;
    	$this->_view->render('workingHours/edit');
    }

    /** AJAX */


    /**
     * Get Clinics Information
     * @return void
     */
    public function ajaxGetActiveWeekDaysAction()
    {
        $arr = array();
        $weeks = array();

        $cRequest = A::app()->getRequest();
        if(!$cRequest->isAjaxRequest()){
            $this->redirect('doctorSchedules/manage');
        }

        $loggedRole = CAuth::getLoggedRole();
        if(in_array($loggedRole, array('admin', 'owner', 'doctor'))){
            $clinicId = $cRequest->getPost('clinicId');

            $workingHours =  WorkingHours::model()->findAll('clinic_id = :clinic_id AND is_day_off = 0', array(':clinic_id' => $clinicId));

            if(!empty($workingHours)){
                $arrWeekDays = array();
                foreach($workingHours as $workingHour){
                    $arrWeekDays[$workingHour['week_day']] = array(
                        'number_day'    => $workingHour['week_day'],
                        'name'          => A::t('i18n', 'weekDayNames.wide.'.$workingHour['week_day']),
                        'start_time'    => $workingHour['start_time'],
                        'end_time'      => $workingHour['end_time'],
                    );
                }
                $weekDays = $this->_getWeekDays($arrWeekDays);
                $count = 1;
                foreach($weekDays as $weekDayKey=>$weekDay){
                    $weeks[] = '"'.$count.'": {"weekDayNumber": "'.$weekDay['number_day'].'", "weekDayName": "'.$weekDay['name'].'", "startTime": "'.$weekDay['start_time'].'", "endTime": "'.$weekDay['end_time'].'"}';
                    $count++;
                }
                $arr[] = '"status": "1", "weekDays": {'.implode(',', $weeks).'}';
            }else{
                $arr[] = '"status": "0", "message": "'.A::t('appointments', 'The clinic is not configured working hours.').'"';
            }
        }else{
            $arr[] = '"status": "0", "message": "'.A::t('appointments', 'You do not have access to perform this operation').'"';
        }

        if(empty($arr)){
            $arr = '';
        }

        $this->_outputAjax($arr, false);
    }

    /**
     * Get Clinics Information
     * @return void
     */
    public function ajaxGetWorkingHoursAction()
    {
        $arr = array();

        $cRequest = A::app()->getRequest();
        if(!$cRequest->isAjaxRequest()){
            $this->redirect('doctorSchedules/manage');
        }

        $loggedRole = CAuth::getLoggedRole();
        if(in_array($loggedRole, array('admin', 'owner', 'doctor'))){
            $clinicId   = $cRequest->post('clinicId');
            $weekDay    = $cRequest->post('weekDay');

            $workingHours =  WorkingHours::model()->find('clinic_id = :clinic_id AND week_day = :week_day AND is_day_off = 0', array(':clinic_id' => $clinicId, ':week_day' => $weekDay));

            if($workingHours){
                $arr[] = '"status": "1", "startTime": "'.$workingHours->start_time.'", "endTime": "'.$workingHours->end_time.'"';
            }else{
                $arr[] = '"status": "0", "message": "'.A::t('appointments', 'The clinic does not work on this of the week day', array('{WEEK_DAY}'=>A::t('i18n', 'weekDayNames.wide.'.$workingHours->week_day))).'"';
            }
        }else{
            $arr[] = '"status": "0", "message": "'.A::t('appointments', 'You do not have access to perform this operation').'"';
        }

        if(empty($arr)){
            $arr = '';
        }

        $this->_outputAjax($arr, false);
    }

    /**
     * Outputs data to browser
     * @param array $data
     * @param bool $returnArray
     * @return void
     */
    private function _outputAjax($data = array(), $returnArray = true)
    {
        $json = '';
        if($returnArray){
            $json .= '[';
            $json .= array($data) ? implode(',', $data) : '';
            $json .= ']';
        }else{
            $json .= '{';
            $json .= implode(',', $data);
            $json .= '}';
        }

        $this->_outputJson($json);
    }

    /**
     * Outputs json to browser
     * @param string $json
     * @return void
     */
    private function _outputJson($json)
    {
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');   // Date in the past
        header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // Always modified
        header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
        header('Pragma: no-cache'); // HTTP/1.0
        header('Content-Type: application/json');

        echo $json;

        exit;
    }

    /**
     * Outputs json to browser
     * @param array $weekDaysInput
     * @return array
     */
    private function _getWeekDays($weekDaysInput = array())
    {
        $weekDaysOutput = array();
        $endWeekIds = array();

        // Get settings
        $settings = Bootstrap::init()->getSettings();
        $weekStartDay = $settings->week_startday;

        // Re-arrange week days

        if($weekStartDay == 1){
            $weekDaysOutput = $weekDaysInput;
        }else{
            foreach($weekDaysInput as $key => $standardWeekDay){
                if($key >= $weekStartDay){
                    $weekDaysOutput[$key] = $standardWeekDay;
                }else{
                    $endWeekIds[] = $key;
                }
            }
            if(!empty($endWeekIds)){
                foreach($endWeekIds as $endWeekId){
                    $weekDaysOutput[$endWeekId] = $weekDaysInput[$endWeekId];
                }
            }
        }

        return $weekDaysOutput;
    }



}