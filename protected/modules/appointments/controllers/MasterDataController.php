<?php
/**
 * Appointments controller
 * This controller intended to both Backend and Frontend modes
 *
 * PUBLIC:                      PRIVATE
 * -----------                  ------------------
 * __construct                  _checkSpecialtyAccess
 * indexAction                  _checkInsurenceAccess
 * specialtiesManageAction
 * specialtyAddAction
 * specialtyEditAction
 * specialtyDeleteAction
 * specialtyChangeStatusAction
 * insuranceManageAction
 * insuranceAddAction
 * insuranceEditAction
 * insuranceDeleteAction
 * insuranceChangeStatusAction
 */

namespace Modules\Appointments\Controllers;

// Module
use \Modules\Appointments\Components\AppointmentsComponent,
    \Modules\Appointments\Models\Specialties,
    \Modules\Appointments\Models\Insurance,
    \Modules\Appointments\Models\VisitReasons,
    \Modules\Appointments\Models\Titles,
    \Modules\Appointments\Models\TimeSlotsType,
    \Modules\Appointments\Models\Degrees;

// Global
use \A,
    \CAuth,
    \CController,
    \CDatabase,
    \CLoader,
    \CWidget,
    \Modules;

// Application
use \Website;


class MasterDataController extends CController
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


        if(CAuth::isLoggedInAsAdmin()){
            // set meta tags according to active appointments
            Website::setMetaTags(array('title'=>A::te('appointments', 'Master Data Management')));
            // set backend mode
            Website::setBackend();

            $this->_view->actionMessage = '';
            $this->_view->errorField = '';

            $this->_view->tabs = AppointmentsComponent::prepareTab('masterdata');
        }
    }

    /**
     * Controller default action handler
     */
    public function indexAction()
    {
        $this->redirect('masterData/specialtiesManage');
    }

    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     *                      Specialties
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

    /**
     * Specialties manage action handler
     * @return void
     */
    public function specialtiesManageAction()
    {
        Website::prepareBackendAction('manage', 'masterdata', 'modules/index');

        $actionMessage = '';
        $alert = A::app()->getSession()->getFlash('alert');
        $alertType = A::app()->getSession()->getFlash('alertType');

        if(!empty($alertType)){
            $actionMessage = CWidget::create('CMessage', array($alertType, $alert, array('button'=>true)));
        }

        $this->_view->actionMessage = $actionMessage;
        $this->_view->subTabs = AppointmentsComponent::prepareSubTab('masterdata', 'specialties');
        $this->_view->render('masterdata/specialties/manage');
    }

    /**
     * Add specialty action handler
     * @return void
     */
    public function specialtyAddAction()
    {
        Website::prepareBackendAction('add', 'masterdata', 'masterData/specialtiesManage');

        $this->_view->subTabs = AppointmentsComponent::prepareSubTab('masterdata', 'specialties', A::t('appointments', 'Add Specialty'));
        $this->_view->render('masterdata/specialties/add');
    }

    /**
     * Edit specialty action handler
     * @param int $id
     * @return void
     */
    public function specialtyEditAction($id = 0)
    {
        Website::prepareBackendAction('edit', 'masterdata', 'masterData/specialtiesManage');
        $order = $this->_checkSpecialtyAccess($id);

        $this->_view->id = $id;
        $this->_view->subTabs = AppointmentsComponent::prepareSubTab('masterdata', 'specialties', A::t('appointments', 'Edit Specialty'));
        $this->_view->render('masterdata/specialties/edit');
    }

    /**
     * Delete specialty action handler
     * @param int $id
     * @return void
     */
    public function specialtyDeleteAction($id = 0)
    {
        Website::prepareBackendAction('delete', 'masterdata', 'masterData/specialtiesManage');
        $specialty = $this->_checkSpecialtyAccess($id);

        $page = !empty(A::app()->getRequest()->get('page')) ? A::app()->getRequest()->get('page') : 1;

        $alert = '';
        $alertType = '';

        if($specialty->delete()){
            if($specialty->getError()){
                $alert = A::t('app', 'Delete Warning Message');
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
                $alert = A::t('app', 'Delete Error Message');
                $alertType = 'error';
            }
        }

        if(!empty($alert)){
            A::app()->getSession()->setFlash('alert', $alert);
            A::app()->getSession()->setFlash('alertType', $alertType);
        }
        
        $this->_view->subTabs = AppointmentsComponent::prepareSubTab('masterdata', 'specialties');
        $this->redirect('masterdata/specialtiesManage'.(!empty($page) ? '?page='.(int)$page : 1));
    }

    /**
     * Change status specialty action handler
     * @param int $id       the specialty ID
     * @param int $page 	the page number
     * @return void
     */
    public function specialtyChangeStatusAction($id, $page = 1)
    {
        Website::prepareBackendAction('edit', 'masterdata', 'masterData/specialtiesManage');

        $alert = '';
        $alertType = '';

        $specialty = Specialties::model()->findbyPk($id);
        if($specialty){
            $specialty->is_active = $specialty->is_active == 1 ? '0' : '1';
            if($specialty->save()){
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
        }
        
        $this->redirect('masterdata/specialtiesManage'.(!empty($page) ? '?page='.(int)$page : 1));
    }

    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     *                        Insurance
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

    /**
     * Insurance manage action handler
     * @return void
     */
    public function insuranceManageAction()
    {
        Website::prepareBackendAction('manage', 'masterdata', 'modules/index');

        $actionMessage = '';
        $alert = A::app()->getSession()->getFlash('alert');
        $alertType = A::app()->getSession()->getFlash('alertType');

        if(!empty($alertType)){
            $actionMessage = CWidget::create('CMessage', array($alertType, $alert, array('button'=>true)));
        }

        $this->_view->actionMessage = $actionMessage;
        $this->_view->subTabs = AppointmentsComponent::prepareSubTab('masterdata', 'insurance');
        $this->_view->render('masterdata/insurance/manage');
    }

    /**
     * Add insurance action handler
     * @return void
     */
    public function insuranceAddAction()
    {
        Website::prepareBackendAction('add', 'masterdata', 'masterData/insuranceManage');

        $this->_view->subTabs = AppointmentsComponent::prepareSubTab('masterdata', 'insurance', A::t('appointments', 'Add Insurance'));
        $this->_view->render('masterdata/insurance/add');
    }

    /**
     * Edit insurance action handler
     * @param int $id
     * @return void
     */
    public function insuranceEditAction($id = 0)
    {
        Website::prepareBackendAction('edit', 'masterdata', 'masterData/insuranceManage');
        $this->_checkInsurenceAccess($id);

        $this->_view->id = $id;
        $this->_view->subTabs = AppointmentsComponent::prepareSubTab('masterdata', 'insurance', A::t('appointments', 'Edit Insurance'));
        $this->_view->render('masterdata/insurance/edit');
    }

    /**
     * Delete action handler
     * @param int $id
     * @return void
     */
    public function insuranceDeleteAction($id = 0)
    {
        Website::prepareBackendAction('delete', 'masterdata', 'masterData/insuranceManage');
        $insurance = $this->_checkInsurenceAccess($id);

        $alert = '';
        $alertType = '';

        if($insurance->delete()){
            if($insurance->getError()){
                $alert = A::t('app', 'Delete Warning Message');
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
                $alert = A::t('app', 'Delete Error Message');
                $alertType = 'error';
            }
        }

        if(!empty($alert)){
            A::app()->getSession()->setFlash('alert', $alert);
            A::app()->getSession()->setFlash('alertType', $alertType);
        }

        $this->redirect('masterData/insuranceManage');
    }

    /**
     * Change status insurance action handler
     * @param int $id       the insurance ID
     * @param int $page 	the page number
     * @return void
     */
    public function insuranceChangeStatusAction($id, $page = 1)
    {
        Website::prepareBackendAction('edit', 'masterdata', 'masterData/insuranceManage');
        
        $alert = '';
        $alertType = '';
        
        $insurance = Insurance::model()->findbyPk($id);
        if($insurance){
            $insurance->is_active = $insurance->is_active == 1 ? '0' : '1';
            if($insurance->save()){
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
        }
        
        $this->redirect('masterData/insuranceManage'.(!empty($page) ? '?page='.(int)$page : 1));
    }

    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     *                     Visit Reasons
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

    /**
     * Visit Reasons manage action handler
     * @return void
     */
    public function visitReasonsManageAction()
    {
        Website::prepareBackendAction('manage', 'masterdata', 'modules/index');

        $actionMessage = '';
        $alert = A::app()->getSession()->getFlash('alert');
        $alertType = A::app()->getSession()->getFlash('alertType');

        if(!empty($alertType)){
            $actionMessage = CWidget::create('CMessage', array($alertType, $alert, array('button'=>true)));
        }

        $this->_view->actionMessage = $actionMessage;
        $this->_view->subTabs = AppointmentsComponent::prepareSubTab('masterdata', 'visitreasons');
        $this->_view->render('masterdata/visitreasons/manage');
    }

    /**
     * Add visit reason action handler
     * @return void
     */
    public function visitReasonAddAction()
    {
        Website::prepareBackendAction('add', 'masterdata', 'masterData/visitReasonsManage');

        $this->_view->subTabs = AppointmentsComponent::prepareSubTab('masterdata', 'visitreasons', A::t('appointments', 'Add Visit Reason'));
        $this->_view->render('masterdata/visitreasons/add');
    }

    /**
     * Edit visit reason action handler
     * @param int $id
     * @return void
     */
    public function visitReasonEditAction($id = 0)
    {
        Website::prepareBackendAction('edit', 'masterdata', 'masterData/visitReasonsManage');
        $this->_checkVisitReasonAccess($id);

        $this->_view->id = $id;
        $this->_view->subTabs = AppointmentsComponent::prepareSubTab('masterdata', 'visitreasons', A::t('appointments', 'Edit Visit Reason'));
        $this->_view->render('masterdata/visitreasons/edit');
    }

    /**
     * Delete action handler
     * @param int $id
     * @return void
     */
    public function visitReasonDeleteAction($id = 0)
    {
        Website::prepareBackendAction('delete', 'masterdata', 'masterData/visitReasonsManage');
        $reason = $this->_checkVisitReasonAccess($id);

        $alert = '';
        $alertType = '';

        if($reason->delete()){
            if($reason->getError()){
                $alert = A::t('app', 'Delete Warning Message');
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
                $alert = A::t('app', 'Delete Error Message');
                $alertType = 'error';
            }
        }

        if(!empty($alert)){
            A::app()->getSession()->setFlash('alert', $alert);
            A::app()->getSession()->setFlash('alertType', $alertType);
        }

        $this->redirect('masterdata/visitReasonsManage');
    }

    /**
     * Change status visit reasons action handler
     * @param int $id       the reason ID
     * @param int $page 	the page number
     * @return void
     */
    public function visitReasonChangeStatusAction($id, $page = 1)
    {
        Website::prepareBackendAction('edit', 'masterdata', 'masterData/visitReasonsManage');

        $reason = VisitReasons::model()->findbyPk($id);
        if($reason){
            $reason->is_active = $reason->is_active == 1 ? '0' : '1';
            if($reason->save()){
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
        }

        if(!empty($alert)){                
            A::app()->getSession()->setFlash('alert', $alert);
            A::app()->getSession()->setFlash('alertType', $alertType);
        }

        $this->redirect('masterData/visitReasonsManage'.(!empty($page) ? '?page='.(int)$page : 1));
    }

    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     *                         Titles
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

    /**
     * Titles manage action handler
     * @return void
     */
    public function titlesManageAction()
    {
        Website::prepareBackendAction('manage', 'masterdata', 'modules/index');

        $actionMessage = '';
        $alert = A::app()->getSession()->getFlash('alert');
        $alertType = A::app()->getSession()->getFlash('alertType');

        if(!empty($alertType)){
            $actionMessage = CWidget::create('CMessage', array($alertType, $alert, array('button'=>true)));
        }

        $this->_view->actionMessage = $actionMessage;
        $this->_view->subTabs = AppointmentsComponent::prepareSubTab('masterdata', 'titles');
        $this->_view->render('masterdata/titles/manage');
    }

    /**
     * Add title action handler
     * @return void
     */
    public function titleAddAction()
    {
        Website::prepareBackendAction('add', 'masterdata', 'masterData/titlesManage');

        $this->_view->subTabs = AppointmentsComponent::prepareSubTab('masterdata', 'titles', A::t('appointments', 'Add Title'));
        $this->_view->render('masterdata/titles/add');
    }

    /**
     * Edit title action handler
     * @param int $id
     * @return void
     */
    public function titleEditAction($id = 0)
    {
        Website::prepareBackendAction('edit', 'masterdata', 'masterData/titlesManage');
        $this->_checkTitleAccess($id);

        $this->_view->id = $id;
        $this->_view->subTabs = AppointmentsComponent::prepareSubTab('masterdata', 'titles', A::t('appointments', 'Edit Title'));
        $this->_view->render('masterdata/titles/edit');
    }

    /**
     * Delete action handler
     * @param int $id
     * @return void
     */
    public function titleDeleteAction($id = 0)
    {
        Website::prepareBackendAction('delete', 'masterdata', 'masterData/titlesManage');
        $title = $this->_checkTitleAccess($id);

        $alert = '';
        $alertType = '';

        if($title->delete()){
            if($title->getError()){
                $alert = A::t('app', 'Delete Warning Message');
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
                $alert = A::t('app', 'Delete Error Message');
                $alertType = 'error';
            }
        }

        if(!empty($alert)){
            A::app()->getSession()->setFlash('alert', $alert);
            A::app()->getSession()->setFlash('alertType', $alertType);
        }

        $this->redirect('masterdata/titlesManage');
    }

    /**
     * Change status doctor action handler
     * @param int $id       the title ID
     * @param int $page 	the page number
     * @return void
     */
    public function titleChangeStatusAction($id, $page = 1)
    {
        Website::prepareBackendAction('edit', 'masterdata', 'masterData/titlesManage');

        $title = Titles::model()->findbyPk($id);
        if($title){
            $title->is_active = $title->is_active == 1 ? '0' : '1';
            if($title->save()){
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
        }
        
        if(!empty($alert)){                
            A::app()->getSession()->setFlash('alert', $alert);
            A::app()->getSession()->setFlash('alertType', $alertType);
        }
            
        $this->redirect('masterData/titlesManage'.(!empty($page) ? '?page='.(int)$page : 1));
    }

    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     *                        Degrees
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

    /**
     * Degrees manage action handler
     * @return void
     */
    public function degreesManageAction()
    {
        Website::prepareBackendAction('manage', 'masterdata', 'modules/index');

        $actionMessage = '';
        $alert = A::app()->getSession()->getFlash('alert');
        $alertType = A::app()->getSession()->getFlash('alertType');

        if(!empty($alertType)){
            $actionMessage = CWidget::create('CMessage', array($alertType, $alert, array('button'=>true)));
        }

        $this->_view->actionMessage = $actionMessage;
        $this->_view->subTabs = AppointmentsComponent::prepareSubTab('masterdata', 'degrees');
        $this->_view->render('masterdata/degrees/manage');
    }

    /**
     * Add degree action handler
     * @return void
     */
    public function degreeAddAction()
    {
        Website::prepareBackendAction('add', 'masterdata', 'masterData/degreesManage');

        $this->_view->subTabs = AppointmentsComponent::prepareSubTab('masterdata', 'degrees', A::t('appointments', 'Add Degree'));
        $this->_view->render('masterdata/degrees/add');
    }

    /**
     * Edit degree action handler
     * @param int $id
     * @return void
     */
    public function degreeEditAction($id = 0)
    {
        Website::prepareBackendAction('edit', 'masterdata', 'masterData/degreesManage');
        $this->_checkDegreeAccess($id);

        $this->_view->id = $id;
        $this->_view->subTabs = AppointmentsComponent::prepareSubTab('masterdata', 'degrees', A::t('appointments', 'Edit Degree'));
        $this->_view->render('masterdata/degrees/edit');
    }

    /**
     * Delete action handler
     * @param int $id
     * @return void
     */
    public function degreeDeleteAction($id = 0)
    {
        Website::prepareBackendAction('delete', 'masterdata', 'masterData/degreesManage');
        $degree = $this->_checkDegreeAccess($id);

        $alert = '';
        $alertType = '';

        if($degree->delete()){
            if($degree->getError()){
                $alert = A::t('app', 'Delete Warning Message');
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
                $alert = A::t('app', 'Delete Error Message');
                $alertType = 'error';
            }
        }
        
        if(!empty($alert)){
            A::app()->getSession()->setFlash('alert', $alert);
            A::app()->getSession()->setFlash('alertType', $alertType);
        }

        $this->redirect('masterData/degreesManage');
    }

    /**
     * Change status degree action handler
     * @param int $id       the degree ID
     * @param int $page 	the page number
     * @return void
     */
    public function degreeChangeStatusAction($id, $page = 1)
    {
        Website::prepareBackendAction('edit', 'masterdata', 'masterData/degreesManage');

        $degree = Degrees::model()->findbyPk($id);
        if($degree){
            $degree->is_active = $degree->is_active == 1 ? '0' : '1';
            if($degree->save()){
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
        }
        
        if(!empty($alert)){
            A::app()->getSession()->setFlash('alert', $alert);
            A::app()->getSession()->setFlash('alertType', $alertType);
        }
        
        $this->redirect('masterData/degreesManage'.(!empty($page) ? '?page='.(int)$page : 1));
    }


    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     *                      Time Slots Type
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

    /**
     * Time Slots Type manage action handler
     * @return void
     */
    public function timeSlotsTypeManageAction()
    {
        Website::prepareBackendAction('manage', 'masterdata', 'modules/index');

        $actionMessage = '';
        $alert = A::app()->getSession()->getFlash('alert');
        $alertType = A::app()->getSession()->getFlash('alertType');

        if(!empty($alertType)){
            $actionMessage = CWidget::create('CMessage', array($alertType, $alert, array('button'=>true)));
        }

        $this->_view->actionMessage = $actionMessage;
        $this->_view->subTabs = AppointmentsComponent::prepareSubTab('masterdata', 'timeSlotsType');
        $this->_view->render('masterData/timeSlotsType/manage');
    }

    /**
     * Add Time Slot Type action handler
     * @return void
     */
    public function typeTimeSlotAddAction()
    {
        Website::prepareBackendAction('add', 'masterdata', 'masterData/timeSlotsTypeManage');

        $this->_view->subTabs = AppointmentsComponent::prepareSubTab('masterdata', 'timeSlotsType', A::t('appointments', 'Add Time Slot Type'));
        $this->_view->render('masterData/timeSlotsType/add');
    }

    /**
     * Edit Time Slot Type action handler
     * @param int $id
     * @return void
     */
    public function typeTimeSlotEditAction($id = 0)
    {
        Website::prepareBackendAction('edit', 'masterdata', 'masterData/timeSlotsTypeManage');
        $order = $this->_checkTypeTimeSlotAccess($id);

        $this->_view->id = $id;
        $this->_view->subTabs = AppointmentsComponent::prepareSubTab('masterdata', 'timeSlotsType', A::t('appointments', 'Edit Time Slot Type'));
        $this->_view->render('masterData/timeSlotsType/edit');
    }

    /**
     * Delete type time slot action handler
     * @param int $id
     * @return void
     */
    public function typeTimeSlotDeleteAction($id = 0)
    {
        Website::prepareBackendAction('delete', 'masterdata', 'masterData/timeSlotsTypeManage');
        $typeTimeSlot = $this->_checkTypeTimeSlotAccess($id);

        $page = !empty(A::app()->getRequest()->get('page')) ? A::app()->getRequest()->get('page') : 1;

        $alert = '';
        $alertType = '';

        if($typeTimeSlot->delete()){
            if($typeTimeSlot->getError()){
                $alert = A::t('app', 'Delete Warning Message');
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
                $alert = A::t('app', 'Delete Error Message');
                $alertType = 'error';
            }
        }

        if(!empty($alert)){
            A::app()->getSession()->setFlash('alert', $alert);
            A::app()->getSession()->setFlash('alertType', $alertType);
        }

        $this->_view->subTabs = AppointmentsComponent::prepareSubTab('masterdata', 'timeSlotsType');
        $this->redirect('masterData/timeSlotsTypeManage'.(!empty($page) ? '?page='.(int)$page : 1));
    }

    /**
     * Change status type time slot action handler
     * @param int $id       the Type Time Slot ID
     * @param int $page 	the page number
     * @return void
     */
    public function typeTimeSlotChangeStatusAction($id, $page = 1)
    {
        Website::prepareBackendAction('edit', 'masterdata', 'masterData/timeSlotsTypeManage');

        $alert = '';
        $alertType = '';

        $typeTimeSlot = $this->_checkTypeTimeSlotAccess($id);
        if($typeTimeSlot){
            $typeTimeSlot->is_active = $typeTimeSlot->is_active == 1 ? '0' : '1';
            if($typeTimeSlot->save()){
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
        }

        $this->redirect('masterData/timeSlotsTypeManage'.(!empty($page) ? '?page='.(int)$page : 1));
    }

    /**
     * Check if passed record ID is valid
     * @param int $specialtyId
     * @return object Specialties
     */
    private function _checkSpecialtyAccess($specialtyId = 0)
    {
        $specialty = Specialties::model()->findByPk($specialtyId);
        if(!$specialty){
            $this->redirect('masterData/specialtiesManage');
        }
        return $specialty;
    }

    /**
     * Check if passed record ID is valid
     * @param int $insuranceId
     * @return object Insurance
     */
    private function _checkInsurenceAccess($insuranceId = 0)
    {
        $insurance = Insurance::model()->findByPk($insuranceId);
        if(!$insurance){
            $this->redirect('masterData/insuranceManage');
        }
        return $insurance;
    }

    /**
     * Check if passed record ID is valid
     * @param int $reasonId
     * @return object VisitReasons
     */
    private function _checkVisitReasonAccess($reasonId = 0)
    {
        $reason = VisitReasons::model()->findByPk($reasonId);
        if(!$reason){
            $this->redirect('masterData/visitReasonsManage');
        }
        return $reason;
    }

    /**
     * Check if passed record ID is valid
     * @param int $titleId
     * @return object Titles
     */
    private function _checkTitleAccess($titleId = 0)
    {
        $title = Titles::model()->findByPk($titleId);
        if(!$title){
            $this->redirect('masterData/titlseManage');
        }
        return $title;
    }

    /**
     * Check if passed record ID is valid
     * @param int $degreeId
     * @return object Degrees
     */
    private function _checkDegreeAccess($degreeId = 0)
    {
        $degree = Degrees::model()->findByPk($degreeId);
        if(!$degree){
            $this->redirect('masterData/degreesManage');
        }
        return $degree;
    }

    /**
     * Check if passed record ID is valid
     * @param int $typeTimeSlotId
     * @return object Specialties
     */
    private function _checkTypeTimeSlotAccess($typeTimeSlotId = 0)
    {
        $typeTimeSlot = TimeSlotsType::model()->findByPk($typeTimeSlotId);
        if(!$typeTimeSlot){
            $this->redirect('masterData/timeSlotsTypeManage');
        }
        return $typeTimeSlot;
    }
}
