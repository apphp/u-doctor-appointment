<?php
/**
 * Appointments controller
 * This controller intended to both Backend and Frontend modes
 *
 * PUBLIC:                      PRIVATE
 * -----------                  ------------------
 * __construct                  _checkMembershipAccess
 * indexAction                  _checkDoctorAccess
 * manageAction
 * addAction
 * editAction
 * deleteAction
 * changeStatusAction
 * viewAllAction
 * membershipPlansAction
 *
 *
 */

namespace Modules\Appointments\Controllers;

// Module
use \Modules\Appointments\Components\AppointmentsComponent;
use \Modules\Appointments\Components\DoctorsComponent;
use \Modules\Appointments\Models\Memberships;
use \Modules\Appointments\Models\Doctors;

// Global
use \A,
	\Accounts,
    \CAuth,
    \CConfig,
    \CLoader,
    \CWidget,
    \Modules,
	\CDatabase;

// Application
use \Website,
    \Bootstrap;


class MembershipsController extends \CController
{
    /* @var array */
    private $_settings;

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

        $configModule = CLoader::config('appointments', 'main');
        $this->_view->multiClinics = (isset($configModule['multiClinics']) && $configModule['multiClinics'] == true) ? true : false;

        if(CAuth::isLoggedInAsAdmin()){
            // set meta tags according to active memberships
            Website::setMetaTags(array('title'=>A::te('appointments', 'Memberships Management')));

            $this->_settings = Bootstrap::init()->getSettings();
            $this->_cSession = A::app()->getSession();

            $this->_view->actionMessage = '';
            $this->_view->errorField = '';

            $this->_view->tabs = AppointmentsComponent::prepareTab('memberships');
        }

        $this->_settings = Bootstrap::init()->getSettings();
		$this->_view->durations      = DoctorsComponent::getPreparedDurations();
        $this->_view->typeFormat     = $this->_settings->number_format;
        $this->_view->dateFormat     = $this->_settings->date_format;
        $this->_view->timeFormat     = $this->_settings->time_format;
        $this->_view->dateTimeFormat = $this->_settings->datetime_format;
    }

    /**
     * Controller default action handler
     */
    public function indexAction()
    {
        $this->redirect('memberships/manage');
    }

    /**
     * Manage action handler
     */
    public function manageAction()
    {
        Website::prepareBackendAction('manage', 'membership', 'modules/index');
        // set backend mode
        Website::setBackend();

        $alert     = A::app()->getSession()->getFlash('alert');
        $alertType = A::app()->getSession()->getFlash('alertType');

        if(!empty($alertType)){
            $this->_view->actionMessage = CWidget::create('CMessage', array($alertType, $alert, array('button'=>true)));
        }

        $this->_view->render('memberships/manage');
    }

    /**
     * Add membership action handler
     * @param int $id
     */
    public function addAction()
    {
        Website::prepareBackendAction('edit', 'membership', 'memberships/manage');
        // set backend mode
        Website::setBackend();

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
        $this->_view->render('memberships/add');
    }

    /**
     * Edit membership action handler
     * @param int $id
     */
    public function editAction($id = 0)
    {
        Website::prepareBackendAction('edit', 'membership', 'memberships/manage');
        // Set backend mode
        Website::setBackend();

        $membership = $this->_checkMembershipAccess($id);

        $appendCode  = '';
        $prependCode = '';
        $symbol      = A::app()->getCurrency('symbol');
        $symbolPlace = A::app()->getCurrency('symbol_place');

        if($symbolPlace == 'before'){
            $prependCode = $symbol;
        }else{
            $appendCode = $symbol;
        }

        $this->_view->membership = $membership;
        $this->_view->pricePrependCode = $prependCode;
        $this->_view->priceAppendCode  = $appendCode;
        $this->_view->render('memberships/edit');
    }

    /**
     * Delete action handler
     * @param int $id
     */
    public function deleteAction($id = 0)
    {
        Website::prepareBackendAction('delete', 'order', 'memberships/manage');
        $membership = $this->_checkMembershipAccess($id);

        $alert     = '';
        $alertType = '';

        if($membership->is_default){
            $alert = A::t('appointments', 'You cannot delete the membership plan by default');
            $alertType = 'warning';
        }elseif($membership->delete()){
            if($membership->getError()){
                $alert = A::t('appointments', 'Delete Warning Message');
                $alertType = 'warning';
            }else{
                $alert = A::t('appointments', 'Delete Success Message');
                $alertType = 'success';
            }
        }else{
            if(APPHP_MODE == 'demo'){
                $alert = CDatabase::init()->getErrorMessage();
                $alertType = 'warning';
            }else{
                $alert = A::t('appointments', 'Delete Error Message');
                $alertType = 'error';
            }
        }

        if(!empty($alert)){
            A::app()->getSession()->setFlash('alert', $alert);
            A::app()->getSession()->setFlash('alertType', $alertType);
        }
		
        $this->redirect('memberships/manage');
    }

	/**
	 * Membership Plan Action
	 */
    public function membershipPlansAction()
	{
		// block access to this controller for not-logged doctors
		CAuth::handleLogin('doctors/login', 'doctor');
		// set meta tags according to active language
		Website::setMetaTags(array('title'=>A::t('appointments', 'Membership Plans')));
		// set frontend settings
		Website::setFrontend();

		$currentMembership = array();

		$doctorId = CAuth::getLoggedRoleId();
		$doctor = $this->_checkDoctorAccess($doctorId, true);
		$membershipPlans = Memberships::model()->findAll(array('condition'=>'is_active = 1', 'order'=>'price ASC'));
		foreach($membershipPlans as $membershipPlan){
			if($membershipPlan['id'] == $doctor->membership_plan_id){
				$currentMembership = $membershipPlan;
				break;
			}
		}
		$this->_view->doctor	          = $doctor;
		$this->_view->currentMembership	  = $currentMembership;
		$this->_view->expiredMembership   = A::app()->getSession()->get('expiredMembership');
		$this->_view->membershipPlans 	  = $membershipPlans;
		$this->_view->doctorId 			  = $doctorId;
		$this->_view->render('memberships/membershipPlans');
	}

    /**
     * Change Memberships status
     * @param int $id
     * @param int $page 	the page number
     */
    public function changeStatusAction($id = 0, $page = 1)
    {
        Website::prepareBackendAction('edit', 'membership', 'memberships/manage');
        // Set backend mode
        Website::setBackend();

        $membership = $this->_checkMembershipAccess($id);

        if(!$membership->is_default){
			$changeResult = Memberships::model()->updateByPk($id, array('is_active'=>($membership->is_active == 1 ? '0' : '1')));
			if($changeResult){
				$alert = A::t('appointments', 'Memberships status has been successfully changed!');
				$alertType = 'success';
			}else{
				$alert = (APPHP_MODE == 'demo') ? A::t('core', 'This operation is blocked in Demo Mode!') : A::t('appointments', 'Memberships status cannot be changed!');
				$alertType = (APPHP_MODE == 'demo') ? 'warning' : 'error';
			}
		}else{
			$alert = A::t('appointments', 'The default entry cannot change the status!');
			$alertType = 'warning';
		}

		$this->_cSession->setFlash('alert', $alert);
		$this->_cSession->setFlash('alertType', $alertType);

        $this->redirect('memberships/manage'.(!empty($page) ? '?page='.(int)$page : 1));
    }

    /**
     * View the module on Frontend
     */
    public function viewAllAction()
    {
        // set frontend mode
        Website::setFrontend();

        // your code here...
    }

    /**
     * Check if passed record ID is valid
     * @param int $membershipId
     * @return object Memberships
     */
    private function _checkMembershipAccess($membershipId = 0)
    {
        $membership = Memberships::model()->findByPk($membershipId);
        if(empty($membership)){
            $this->redirect('memberships/manage');
        }
        return $membership;
    }

	/**
	 * Check if passed Doctor ID is valid
	 * @param int $id
	 * @param bool $redirect
	 * @return object Doctors
	 */
	private function _checkDoctorAccess($id = 0, $redirect = false)
	{
		if(empty($id)){
			return false;
		}

		$doctor = array();

		if($redirect){
			$tableName = CConfig::get('db.prefix').Accounts::model()->getTableName();
			$doctor = Doctors::model()->findByPk($id, $tableName.'.is_active = 1 AND '.$tableName.'.is_removed = 0');
		}else{
			$doctor = Doctors::model()->findByPk($id);
		}

		if(!$doctor){
			$this->redirect('doctors/manage');
		}

		return $doctor;
	}
}
