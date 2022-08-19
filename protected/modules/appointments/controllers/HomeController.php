<?php
/**
 * Home controller
 *
 * PUBLIC:                  PRIVATE:
 * ---------------          ---------------
 * __construct
 * indexAction
 *
 */

namespace Modules\Appointments\Controllers;

// Module/s
use \Modules\Appointments\Models\WorkingHours,
    \Modules\Appointments\Models\Clinics,
    \Modules\Appointments\Models\Services,
    \Modules\Appointments\Models\Doctors,
    \Modules\Appointments\Models\DoctorSpecialties,
	Modules\Appointments\Models\Titles,
    \Modules\Appointments\Components\DoctorsComponent,
    \Modules\Appointments\Components\AppointmentsComponent,
    \Banners,
    \Testimonials,
    \News;

// Framework
use \A,
    \CAuth,
    \CArray;

// Application
use \Website,
    \CController,
    \Modules,
    \ModulesSettings,    
    \Bootstrap,
    \CConfig;


class HomeController extends CController
{
    /**
     * Class default constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->_view->actionMessage = '';
        $this->_view->errorField = '';

        A::app()->view->setTemplate('frontend');
        A::app()->view->setLayout('homepage');
    }

    /**
     * Controller default action handler
     * @return void
     */
    public function indexAction()
    {
        // Set frontend mode
        Website::setFrontend();
        
       
        // -------------------------------------------------------
        // SERVICES
        // -------------------------------------------------------        
        $this->_view->services = Services::model()->findAll(array('condition'=>'is_active=1', 'order'=>'id ASC', 'limit'=>'0, 12'), array(), 'services-findall-12');
        
        // -------------------------------------------------------
        // NEWS
        // -------------------------------------------------------
        $this->_view->news = array();
        if(Modules::model()->isInstalled('news')){
            $this->_view->news = News::model()->findAll(array('condition'=>'is_published=1', 'order'=>'id DESC', 'limit'=>'0, 2'), array(), 'news-findall-2');
        }
        
        // -------------------------------------------------------
        // TESTIMONIALS
        // -------------------------------------------------------
        $this->_view->testimonials = array();
        if(Modules::model()->isInstalled('testimonials')){
            $this->_view->testimonials = Testimonials::model()->findAll(array('condition'=>'is_active=1', 'order'=>'sort_order ASC', 'limit'=>'0, 5'), array(), 'testimonials-findall-5');
        }        
        
        // -------------------------------------------------------
        // BANNERS
        // -------------------------------------------------------
        $this->_view->banners = array();
        if(Modules::model()->isInstalled('banners') && Modules::model()->param('banners', 'is_active')){
            $rotationDelay = ModulesSettings::model()->param('banners', 'rotation_delay');
            $viewerType = ModulesSettings::model()->param('banners', 'viewer_type');
            
            $showBanners = false;
            if($viewerType == 'all'){
                $showBanners = true;
            }elseif($viewerType == 'registered only' && CAuth::isLoggedIn()){
                $showBanners = true;
            }elseif($viewerType == 'visitors only' && !CAuth::isLoggedIn()){
                $showBanners = true;
            }
        
            if($showBanners){
                $this->_view->banners = Banners::model()->findAll(array('condition'=>'is_active = 1', 'orderBy'=>'sort_order ASC'));
            }
        }

        // -------------------------------------------------------
        // APPOINTMENTS
        // -------------------------------------------------------
        // Get settings
        $clinicDefault = Clinics::model()->find('is_default = 1');
        $this->_view->workingDays = DoctorsComponent::workingHoursClinic($clinicDefault->id);
        $this->_view->titles = Titles::getActiveTitles();
        $doctors = Doctors::model()->findAll(array('condition'=>'membership_show_in_search = 1 AND '.CConfig::get('db.prefix').'accounts.is_removed = 0 AND '.CConfig::get('db.prefix').'accounts.is_active= 1', 'order'=>'RAND()', 'limit'=>'0, 4'), array(), '');
        if(!empty($doctors)){
            $doctorIds = array();
            foreach($doctors as $doctor){
                $doctorIds[] = $doctor['id'];
            }
            $specialties = DoctorSpecialties::model()->findAll(array('condition'=>'doctor_id IN('.(!empty($doctorIds) ? implode(',', $doctorIds) : '-1').')'));
            $this->_view->doctorSpecialties = !empty($specialties) ? CArray::flipByField($specialties, 'doctor_id', true) : array();
        }
        $this->_view->doctors = $doctors;

       $this->_view->drawAppointmentsBlock = AppointmentsComponent::drawAppointmentsBlock('gorisontal');

        $this->_view->render('home/index');
    }
}

