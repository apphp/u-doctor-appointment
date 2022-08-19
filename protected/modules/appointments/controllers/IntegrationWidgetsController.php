<?php
/**
 * IntegrationWidgets controller
 *
 * PUBLIC:                  PRIVATE:
 * ---------------          ---------------
 * __construct              
 * codeAction
 * integrationAppointmentAction
 */

namespace Modules\Appointments\Controllers;

// Module
use \Modules\Appointments\Components\AppointmentsComponent;
use \Modules\Appointments\Models\Appointments;

// Framework
use \Modules,
    \CAuth,
    \CController;

// Application
use \Website,
    \A;

class IntegrationWidgetsController extends CController
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
            Website::setMetaTags(array('title'=>A::t('appointments', 'Integration Widgets')));
            // set backend mode
            Website::setBackend();

            $this->_view->actionMessage = '';
            $this->_view->errorField = '';

            $this->_view->tabs = AppointmentsComponent::prepareTab('integration');
        }

        $this->_view->actionMessage = '';
        $this->_view->errorField = '';
    }

    /**
     * Controller default action handler
     */
    public function indexAction()
    {
        $this->redirect('integrationWidgets/code');
    }

    /**
     * Directory Info action handler
     * @return void
     */
    public function codeAction()
    {
        $this->_view->render('integrationWidgets/code');
    }

    /**
     * First step in inquiry block (get form) action handler
     * @return void
     */
    public function integrationAppointmentAction()
    {
        $this->_view->drawAppointmentsBlock = AppointmentsComponent::drawAppointmentsBlock('integration');
        $this->_view->setLayout('');
        $this->_view->render('integrationWidgets/integrationAppointment', true);
    }
	
}
