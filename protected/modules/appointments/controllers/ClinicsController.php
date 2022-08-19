<?php
/**
 * Appointments controller
 * This controller intended to both Backend and Frontend modes
 *
 * PUBLIC:                  PRIVATE
 * -----------              ------------------
 * __construct              _checkClinicAccess
 * indexAction
 * manageAction
 * addAction
 * editAction
 * changeStatusAction
 * deleteAction
 * ourClinicsAction
 * viewClinicAction
 * ajaxFindCoordinatesAction
 * ajaxGetClinicNamesAction
 *
 */

namespace Modules\Appointments\Controllers;

// Module
use \Modules\Appointments\Components\AppointmentsComponent;
use \Modules\Appointments\Components\DoctorsComponent;
use \Modules\Appointments\Models\Clinics;

// Framework
use \A,
    \CAuth,
    \CLoader,
	\CString,
    \CWidget,
    \CController,
    \CConfig,
    \CGeoLocation,
    \Modules;

// Application
use \Website,
    \Admins,
	\CDebug,
	\CDatabase;


class ClinicsController extends CController
{
    /* @var boolean */
    private $isAjax = false;

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

        $this->_cRequest = A::app()->getRequest();

        // Determine the type of query. Is he ajax
        if($this->_cRequest->isAjaxRequest()){
            $this->isAjax = true;
        }

        $configModule = CLoader::config('appointments', 'main');
        $this->_view->multiClinics = (isset($configModule['multiClinics']) && $configModule['multiClinics'] == true) ? true : false;

        $timeZonesList = A::app()->getLocalTime()->getTimeZones();
        // Prepare time zones
        $timeZonesPreparedList = array();
        foreach($timeZonesList as $key => $val){
            if(!isset($timeZonesPreparedList[$key])){
                $timeZonesPreparedList[$key] = array();
            }
            foreach($val as $vKey => $vVal){
                if(is_array($vVal)){
                    $timeZonesPreparedList[$key][$vKey] = $vVal['offset_text'].' '.$vVal['name'];
                }else{
                    $timeZonesPreparedList[$vKey] = $vVal;
                }
            }
        }
        $this->_view->timeZonesList = $timeZonesPreparedList;

        $this->_view->utcTime = A::t('app', 'UTC time is').' '.gmdate('Y-m-d H:i:s');
        $this->_view->language = A::app()->getLanguage();

        if(CAuth::isLoggedInAsAdmin()){
            // set meta tags according to active appointments
            Website::setMetaTags(array('title'=>A::te('appointments', $this->_view->multiClinics ? 'Clinics Management' : 'Clinic Management')));
            // set backend mode
            Website::setBackend();

            $this->_view->actionMessage = '';
            $this->_view->errorField = '';

            $this->_view->tabs = AppointmentsComponent::prepareTab('clinics');
        }
    }

    /**
     * Controller default action handler
     */
    public function indexAction()
    {
        $this->redirect('clinics/manage');
    }

    /**
     * Manage action handler
     * @return void
     */
    public function manageAction()
    {
        Website::prepareBackendAction('manage');

        $alert = A::app()->getSession()->getFlash('alert');
        $alertType = A::app()->getSession()->getFlash('alertType');

        if(!$this->_view->multiClinics){
            $clinic = Clinics::model()->find(array('orderBy'=>'sort_order DESC'));
            if(empty($clinic)){
                $alertType = 'error';
                $alert = A::t('appointments', 'It is impossible to set the default clinic');
                $id = 0;
            }else{
                $id = $clinic->id;
            }
            $this->_view->parentContoller = 'manage';
            $this->_view->id = $id;
            $action = 'edit';
        }else{
            $action = 'manage';
        }

        if(!empty($alertType)){
            $this->_view->actionMessage = CWidget::create('CMessage', array($alertType, $alert, array('button'=>true)));
        }

        $this->_view->render('clinics/'.$action);
    }

    /**
     * Add new action handler
     * @return void
     */
    public function addAction()
    {
		if(!$this->_view->multiClinics){
            $this->redirect('clinics/manage');
        }
        Website::prepareBackendAction('add', 'clinic', 'clinics/manage');

        $this->_view->render('clinics/add');
    }

    /**
     * Edit appointments action handler
     * @param int $id
     * @return void
     */
    public function editAction($id = 0)
    {
        if(!$this->_view->multiClinics){
            $this->redirect('clinics/manage');
        }

        Website::prepareBackendAction('edit', 'clinic', 'clinics/manage');
        $clinic = $this->_checkClinicAccess($id);

        $this->_view->parentContoller = 'edit';
        $this->_view->clinic = $clinic;
        $this->_view->id = $id;
        $this->_view->render('clinics/edit');
    }

    /**
     * Change status handler action
     * @param int $id
     * @param int $page 	the page number
     * @return void
     */
    public function changeStatusAction($id = 0, $page = 1)
    {
        if(!$this->_view->multiClinics){
            $this->redirect('clinics/manage');
        }
        Website::prepareBackendAction('edit', 'clinic', 'clinics/managae');
        $clinic = $this->_checkClinicAccess($id);

		if(!empty($clinic) && !$clinic->is_default){
            if(Clinics::model()->updateByPk($clinic->id, array('is_active'=>($clinic->is_active ? 0 : 1)))){
                A::app()->getSession()->setFlash('alert', A::t('appointments', 'Status has been successfully changed!'));
                A::app()->getSession()->setFlash('alertType', 'success');
            }else{
                A::app()->getSession()->setFlash('alert', ((APPHP_MODE == 'demo') ? A::t('core', 'This operation is blocked in Demo Mode!') : A::t('app', 'Status changing error')));
                A::app()->getSession()->setFlash('alertType', ((APPHP_MODE == 'demo') ? 'warning' : 'error'));
            }
        }else{
            A::app()->getSession()->setFlash('alert', A::t('appointments', 'The default entry cannot change the status!'));
            A::app()->getSession()->setFlash('alertType', 'warning');
        }
		
        $this->redirect('clinics/manage'.(!empty($page) ? '?page='.(int)$page : 1));
    }

    /**
     * Delete action handler
     * @param int $id
     * @return void
     */
    public function deleteAction($id = 0)
    {
        if(!$this->_view->multiClinics){
            $this->redirect('clinics/manage');
        }
		
        Website::prepareBackendAction('delete', 'clinic', 'clinics/manage');
        $clinic = $this->_checkClinicAccess($id);

        $alert = '';
        $alertType = '';
		
        if($clinic->is_default){
            $alert = A::t('appointments', 'You cannot delete the clinic by default');
            $alertType = 'warning';
        }elseif($clinic->delete()){
            if($clinic->getError()){
                $alert = $clinic->getErrorMessage();
                $alert = empty($alert) ? A::t('appointments', 'Delete Error Message') : $alert;
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
				$alert = $clinic->getError() ? $clinic->getErrorMessage() : A::t('app', 'Delete Error Message');
				$alertType = 'error';
		   	}			
        }
		
        if(!empty($alert)){
			A::app()->getSession()->setFlash('alert', $alert);
			A::app()->getSession()->setFlash('alertType', $alertType);
        }

        $this->redirect('clinics/manage');
    }

    /**
     * View Clinics on Frontend
     * @return void
     */
    public function ourClinicsAction()
    {
        // set frontend mode
        Website::setFrontend();
        $clinics        = array();
        $clinicDefault  = array();
        if($this->_view->multiClinics){
            $clinics = Clinics::model()->findAll('is_active = 1');
        }else{
            $clinicDefault = Clinics::model()->find('is_default = 1');
            if($clinicDefault){
                $this->redirect('clinics/viewClinic/'.$clinicDefault->id);
            }else{
                $this->redirect('Home/index');
            }
        }

        $this->_view->clinics = $clinics;
        $this->_view->render('clinics/ourClinics');

    }

    /**
     * View clinic on Frontend
     * @param int $id
     * @return void
     */
    public function viewClinicAction($id = 0)
    {
        // set frontend mode
        Website::setFrontend();
        $clinic = $this->_checkClinicAccess($id);

        $this->_view->workingDays = DoctorsComponent::workingHoursClinic($clinic->id);
        $this->_view->clinic = $clinic;
        $this->_view->render('clinics/viewClinic');
    }


    /* * * * * * * * * *  A J A X  * * * * * * * * * * */

    /**
     * Returns coordinates of given address
     * @return json
     */
    public function ajaxFindCoordinatesAction()
    {
        if(!$this->isAjax){
            $this->redirect(Website::getDefaultPage());
        }

        $arr = array(
            'status'      => 0,
            'longitude'   => '',
            'latitude'    => '',
            'alert'       => '',
            'alert_type'  => 'error',
            'error_field' => '',
        );

        if(APPHP_MODE == 'demo'){
			$arr['alert']       = A::te('appointments', 'This operation is blocked in Demo Mode!');
			$arr['alert_type']  = 'warning';
		}elseif(CAuth::isLoggedInAsAdmin()){
            $address = $this->_cRequest->getPost('address');
            $act = $this->_cRequest->getPost('act');
			
            if($act == 'send' && !empty($address)){
                $coordinates = CGeoLocation::coordinatesByAddress($address);
                if(!empty($coordinates['longitude']) && !empty($coordinates['latitude'])){
                    $arr['status']     = 1;
                    $arr['longitude']  = $coordinates['longitude'];
                    $arr['latitude']   = $coordinates['latitude'];
                    $arr['alert']      = A::te('appointments', 'Coordinates have been successfully obtained');
                    $arr['alert_type'] = 'success';
                }else{
                    $arr['alert']       = A::te('appointments', 'Address is incorrect');
                    $arr['alert_type']  = 'warning';
                    $arr['error_field'] = 'address';
                }
            }else{
                $arr['alert']       = A::te('appointments', 'Address is empty');
                $arr['alert_type']  = 'warning';
                $arr['error_field'] = 'address';
            }
        }else{
            $arr['alert']      = A::te('appointments', 'You do not have privileges to perform this operation');
            $arr['alert_type'] = 'error';
        }
		
        $this->_ajaxOutput($arr, false);
    }

    /**
     * Outputs data to browser
     * @param $array $data
     * @param string $returnArray
    */
    private function _ajaxOutput($data = array(), $returnArray = true)
    {
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');   // Date in the past
        header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // Always modified
        header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
        header('Pragma: no-cache'); // HTTP/1.0
        header('Content-Type: application/json');

        $prepareData = array();
        if(!empty($data) && is_array($data)){
            foreach($data as $key => $value){
                $value = str_replace(array('"', "\r\n", "\n", "\t"), array("'", '', '', ''), $value);
                $prepareData[] = '"'.$key.'": "'.$value.'"';
            }
        }

        echo $returnArray ? '[' : '{';
        echo implode(',', $prepareData);
        echo $returnArray ? ']' : '}';

        exit;
    }

    /**
     * Get clinic names by coordinates
    */
	public function ajaxGetClinicNamesAction()
	{
		// Block access if this is not AJAX request
		$cRequest = A::app()->getRequest();
		if(!$cRequest->isAjaxRequest()){
			$this->redirect('doctors/dashboard');
		}

		$result = CWidget::create('CFormValidation', array(
			'fields' => array(
				'search' => array('title'=>A::t('appointments', 'Name'), 'validation'=>array('required'=>true, 'type'=>'name', 'maxLength'=>65)),
			),
		));
		if(!empty($result['error'])){
			$errorMessage = str_replace('"', '\"', $result['errorMessage']);
			$fieldError = $result['errorField'];

			$arr[] = '{"status": "0"}';
			$arr[] = '{"message": "'.$errorMessage.'"}';
		}else{
			$search = trim(preg_replace("/  +/"," ", $cRequest->getPost('search')));
			$location = explode(' ', $search);
			if(!empty($location)){
                $tableClinics = CConfig::get('db.prefix').Clinics::model()->getTableName();
                $tableClinicsTranslation = CConfig::get('db.prefix').Clinics::model()->getTableTranslationName();
				$countLocation = count($location);
				if($countLocation == 1){
					$location[0] = strip_tags(CString::quote($location[0]));
					$params[':location'] = '%'.$location[0].'%';

					$condition = $tableClinicsTranslation.'.address LIKE :location OR '.$tableClinicsTranslation.'.name LIKE :location';
				}else{
					$condition = '';
					for ($i=0;$i<$countLocation;$i++){
						$location[$i] = strip_tags(CString::quote($location[$i]));
						$params[':location_'.$i] = '%'.$location[$i].'%';

						$condition .= $tableClinicsTranslation.'.address LIKE :location_'.$i.' OR '.$tableClinicsTranslation.'.name LIKE :location_'.$i;
						if($i<$countLocation - 1) $condition .= ' AND ';
					}
				}

				$clinics = Clinics::model()->findAll(
					array('condition' => $condition, 'order'=>'address'),
					$params
				);

				if(is_array($clinics) && !empty($clinics)){
					$arr[] = '{"status": "1"}';
					foreach($clinics as $key => $clinic){
						$arr[] = '{"id": "'.$clinic['id'].'", "label": "'.htmlentities($clinic['clinic_name'].', '.$clinic['address']).'"}';
					}
				}
			}
		}

		if(empty($arr)){
			$arr[] = '';
		}

		$json = '';
		$json .= '[';
		$json .= array($arr) ? implode(',', $arr) : '';
		$json .= ']';

		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');   // Date in the past
		header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // Always modified
		header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
		header('Pragma: no-cache'); // HTTP/1.0
		header('Content-Type: application/json');

		echo $json;

		exit;

	}

    /**
     * Check if passed record ID is valid
     * @param int $clinicId
     * @return object Clinics
     */
    private function _checkClinicAccess($clinicId = 0)
    {
        $clinic = Clinics::model()->findByPk($clinicId);
        if(empty($clinic)){
            $this->redirect('clinics/manage');
        }
        return $clinic;
    }
}
