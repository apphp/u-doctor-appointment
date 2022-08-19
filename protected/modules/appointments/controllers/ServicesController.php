<?php
/**
 * Services controller
 * This controller intended to both Backend and Frontend modes
 *
 * PUBLIC:                  PRIVATE
 * -----------              ------------------
 * __construct              _checkServiceAccess
 * indexAction              
 * manageAction
 * addAction
 * editAction
 * deleteAction
 * changeStatusAction
 * viewAction
 * viewAllAction
 *
 */

namespace Modules\Appointments\Controllers;

// Module
use \Modules\Appointments\Components\AppointmentsComponent;
use \Modules\Appointments\Models\Services;

// Global
use \A,
    \CAuth,
    \CLoader,
    \CWidget,
    \Modules,
    \CFile,
    \CDatabase;

// Application
use \Website,
    \Bootstrap;


class ServicesController extends \CController
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

        //$configModule = CLoader::config('appointments', 'main');
        //$this->_view->multiClinics = (isset($configModule['multiClinics']) && $configModule['multiClinics'] == true) ? true : false;
        
        if(CAuth::isLoggedInAsAdmin()){
            // set meta tags according to active services
            Website::setMetaTags(array('title'=>A::te('appointments', 'Services Management')));
        
            $this->_view->actionMessage = '';
            $this->_view->errorField = '';
        
            $this->_view->tabs = AppointmentsComponent::prepareTab('services');
        }
        
        //$this->_settings = Bootstrap::init()->getSettings();
        //$this->_view->typeFormat = $this->_settings->number_format;
    }

    /**
     * Controller default action handler
     */
    public function indexAction()
    {
        $this->redirect('services/manage');
    }

    /**
     * Manage action handler
     */
    public function manageAction()
    {
        Website::prepareBackendAction('manage', 'services', 'modules/index');
        // Set backend mode
        Website::setBackend();
        
        $alert = A::app()->getSession()->getFlash('alert');
        $alertType = A::app()->getSession()->getFlash('alertType');
        
        if(!empty($alertType)){
            $this->_view->actionMessage = CWidget::create('CMessage', array($alertType, $alert, array('button'=>true)));
        }
        
        $this->_view->render('services/manage');
    }

    /**
     * Add services action handler
     * @param int $id
     */
    public function addAction()
    {
        Website::prepareBackendAction('edit', 'services', 'services/manage');
        // Set backend mode
        Website::setBackend();
        
        $this->_view->render('services/add');
    }

    /**
     * Edit services action handler
     * @param int $id
     * @param string $delete
     */
    public function editAction($id = 0, $delete = '')
    {
        Website::prepareBackendAction('edit', 'services', 'services/manage');
        // Set backend mode
        Website::setBackend();
        
        $service = $this->_checkServiceAccess($id);
        
        if($delete == 'delete'){
            if($service->image_file != ''){
                $deleteImage = 'assets/modules/appointments/images/services/'.$service->image_file;
                $service->image_file = '';
                if($service->save()){
                    if(CFile::deleteFile($deleteImage)){
                        $alert = A::t('appointments', 'Image successfully deleted');
                        $alertType = 'success';
                    }else{
                        $alert = A::t('appointments', 'Image Delete Warning');
                        $alertType = 'warning';
                    }
                }else{
                    $alert = A::t('appointments', 'Image Delete Warning');
                    $alertType = 'error';
                }
            }
        }
        
        if(!empty($alert)){
            $this->_view->actionMessage = CWidget::create('CMessage', array($alertType, $alert, array('button'=>true)));
        }
        
        $this->_view->id = $id;
        $this->_view->service = $service;
        
        $this->_view->render('services/edit');
    }

    /**
     * Delete action handler
     * @param int $id
     */
    public function deleteAction($id = 0)
    {
        Website::prepareBackendAction('delete', 'services', 'services/manage');
        $service = $this->_checkServiceAccess($id);
        
        $alert     = '';
        $alertType = '';
        $deleteImage = 'assets/modules/appointments/images/services/'.$service->image_file;

        if($service->delete()){
            if($service->getError()){
                $alert = A::t('appointments', 'Delete Warning Message');
                $alertType = 'warning';
            }else{
        		// Delete images
        		if(!empty($service->image_file) && !CFile::deleteFile($deleteImage)){
        			$alert = A::t('appointments', 'There was a problem removing the service');
        			$alertType = 'warning';
        		}else{
                    $alert = A::t('appointments', 'Delete Success Message');
                    $alertType = 'success';
        		}
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
        
        $this->redirect('services/manage');
    }

    /**
     * Change status handler action
     * @param int $id
     * @param int $page 	the page number
     * @return void
     */
    public function changeStatusAction($id = 0, $page = 1)
    {
        Website::prepareBackendAction('manage', 'services', 'modules/index');
        // Set backend mode
        Website::setBackend();
        
        $alert = '';
        $alertType = '';
        
        $service = $this->_checkServiceAccess($id);
        if(!empty($service)){
            if(Services::model()->updateByPk($service->id, array('is_active'=>($service->is_active ? 0 : 1)))){
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
        
        $this->redirect('services/manage'.(!empty($page) ? '?page='.(int)$page : 1));
    }

    /**
     * View service on Frontend
     * @return void
     */
    public function viewAction($id = 0)
    {
		$service = $this->_checkServiceAccess($id);
        // Set frontend mode
        Website::setFrontend();

        $this->_view->shareLink = AppointmentsComponent::shareLink(Website::getCurrentPage().'/id/'.$id, $service->name);
		$this->_view->service = $service;
		$this->_view->render('services/view');
    }

    /**
     * View all services on Frontend
     * @return void
     */
    public function viewAllAction()
    {
        // Set frontend mode
        Website::setFrontend();

        $this->_view->setLayout('no_columns');

        $services = Services::model()->findAll(array('is_active'=>'1'));
        $tags = array();
        foreach($services as $service){
            if($service['tags']){
                $servicesTags = explode(",", $service['tags']);
                if(count($servicesTags) > 1){
                    foreach($servicesTags as $serviceTags){
                        $tags[] = $serviceTags;
                    }
                }elseif(count($servicesTags) > 0){
                    $tags[] = $servicesTags[0];
                }
            }
        }
        $tags = array_unique($tags);


        $this->_view->services = $services;
        $this->_view->tags = $tags;
        $this->_view->render('services/viewAll');
    }

    /**
     * Check if passed record ID is valid
     * @param int $serviceId
     * @return object Services
     */
    private function _checkServiceAccess($serviceId = 0)
    {
        $services = Services::model()->findByPk($serviceId);
        if(empty($services)){
            $this->redirect('services/manage');
        }
        return $services;
    }

}
