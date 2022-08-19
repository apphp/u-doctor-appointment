<?php
/**
 * Testimonials controller
 *
 * PUBLIC:                  PRIVATE:
 * -----------         		------------------
 * __construct              _prepareTab 
 * indexAction              _checkTestimonialsAccess
 * manageAction             
 * viewAllAction
 * addAction
 * editAction
 * changeStatusAction
 * deleteAction
 * 
 */

class TestimonialsController extends CController
{
	private $_settings;
	
    /**
	 * Class default constructor
     */
    public function __construct()
	{
        parent::__construct();

        // Block access if module is not installed
		if(!Modules::model()->isInstalled('testimonials')){
            if(CAuth::isLoggedIn()){
                $this->redirect('modules/index');
            }else{
                $this->redirect(Website::getDefaultPage());
            }
        }

		// Set meta tags according to active language
		Website::setMetaTags(array('title'=>A::t('testimonials', 'Testimonials Management')));

		// Fetch datetime format from settings table
    	$this->_settings = Bootstrap::init()->getSettings();
    	$this->_view->dateTimeFormat = $this->_settings->date_format;
		$this->_view->actionMessage = '';

		$this->_view->tabs = $this->_prepareTab('testimonials');		
    }

	/**
	 * Controller default action handler
	 */
    public function indexAction()
	{
		if(CAuth::isLoggedInAsAdmin()){
			$this->redirect('testimonials/manage');
		}else{
			$this->redirect('testimonials/viewAll');
		}		
    }	

    /**
     * Manage testimonials action handler
     */
    public function manageAction()
    {
        Website::prepareBackendAction('manage', 'testimonials', 'testimonials/manage');

        $alert = A::app()->getSession()->getFlash('alert');
        $alertType = A::app()->getSession()->getFlash('alertType');
        
        if(!empty($alertType)){
            $this->_view->actionMessage = CWidget::create('CMessage', array($alertType, $alert, array('button'=>true)));
        }
		
        $this->_view->render('testimonials/manage');        
    }

    /**
     * Add testimonials action handler
     */
    public function addAction()
    {
        Website::prepareBackendAction('add', 'testimonials', 'testimonials/manage');
		
		$this->_view->sortOrder = Testimonials::model()->count() + 1;

        $this->_view->render('testimonials/add');
    }

    /**
     * Edit testimonials action handler
     * @param int $id
     * @param string $image
     */
    public function editAction($id = 0, $image = '')
    {
        Website::prepareBackendAction('edit', 'testimonials', 'testimonials/manage');
        $testimonial = $this->_checkTestimonialsAccess($id);
		
        // Delete the image file
        if($image === 'delete'){
        	$alert = '';
        	$alertType = '';
        	$image = 'assets/modules/testimonials/images/authors/'.$testimonial->author_image;
        	$testimonial->author_image = '';

        	// Save the changes in testimonials table
        	if($testimonial->save()){
        		// Delete image
        		if(CFile::deleteFile($image)){
        			$alert = A::t('testimonials', 'Image has ben successfully deleted');
        			$alertType = 'success';
        		}else{
        			$alert = A::t('testimonials', 'There was a problem removing the image');
        			$alertType = 'warning';
        		}
        	}else{
        		$alert = A::t('testimonials', 'Error removing image');
        		$alertType = 'error';
        	}
        	if(!empty($alert)){
        		$this->_view->actionMessage = CWidget::create('CMessage', array($alertType, $alert, array('button'=>true)));
        	}
        }

		$this->_view->testimonial = $testimonial;
        $this->_view->render('testimonials/edit');
    }

    /**
     * Change status handler action
	 * @param int $id 		the menu ID
	 * @param int $page 	the page number
     * @return void
     */
    public function changeStatusAction($id = 0, $page = 1)
    {
        Website::prepareBackendAction('edit', 'testimonials', 'testimonials/manage');
        
		$testimonial = $this->_checkTestimonialsAccess($id);
        if(!empty($testimonial)){
            if(Testimonials::model()->updateByPk($testimonial->id, array('is_active'=>($testimonial->is_active ? 0 : 1)))){
                A::app()->getSession()->setFlash('alert', A::t('testimonials', 'Status has been successfully changed!'));
                A::app()->getSession()->setFlash('alertType', 'success');
            }else{
				A::app()->getSession()->setFlash('alert', (APPHP_MODE == 'demo') ? A::t('core', 'This operation is blocked in Demo Mode!') : A::t('app', 'Status changing error'));
                A::app()->getSession()->setFlash('alertType', (APPHP_MODE == 'demo') ? 'warning' : 'error');
            }
        }
        
        $this->redirect('testimonials/manage'.(!empty($page) ? '?page='.(int)$page : 1));
    }
	
    /**
     * Delete testimonials action handler
     * @param int $id the testimonial id
     */
    public function deleteAction($id = 0)
    {
        Website::prepareBackendAction('delete', 'testimonials', 'testimonials/manage');
        $testimonial = $this->_checkTestimonialsAccess($id);
    	
        $alert = '';
    	$alertType = '';
    
		if($testimonial->delete()){				
        	$alert = '';
        	$alertType = '';
        	$image = 'assets/modules/testimonials/images/authors/'.$testimonial->author_image;

			// Delete image
			$alert = A::t('testimonials', 'Testimonial deleted successfully');
			$alertType = 'success';
			if(!empty($testimonial->author_image) && !CFile::deleteFile($image)){
				$alert = A::t('testimonials', 'There was a problem removing the image');
				$alertType = 'warning';
			}
		}else{
			if(APPHP_MODE == 'demo'){
				$alert = CDatabase::init()->getErrorMessage();
				$alertType = 'warning';
		   	}else{
				$alert = A::t('testimonials', 'Testimonial delete error message');
				$alertType = 'error';
		   	}			
		}
		
		if(!empty($alert)){
			A::app()->getSession()->setFlash('alert', $alert);
			A::app()->getSession()->setFlash('alertType', $alertType);
		}
		
		$this->redirect('testimonials/manage');
    }

	/**
	 * View all action handler
	 */
    public function viewAllAction()
	{
        // Set frontend mode
        Website::setFrontend();

		$alert = '';
		$alertType = '';

		// Prepare pagination vars
		$this->_view->currentPage = A::app()->getRequest()->getQuery('page', 'integer', 1);
		if($this->_view->currentPage <= 0) $this->_view->currentPage = 1;
		
        $this->_view->pageSize = ModulesSettings::model()->param('testimonials', 'testimonials_per_page');
		$this->_view->totalTestimonials = Testimonials::model()->count();		
	
		$this->_view->testimonials = Testimonials::model()->findAll(array(
			'limit'=>(($this->_view->currentPage - 1) * $this->_view->pageSize).', '.$this->_view->pageSize,
			'order'=>'created_at DESC'
		));
		
		if(!count($this->_view->testimonials)){
			$alert = A::t('testimonials', 'Wrong parameter passed! Please try again later.');
			$alertType = 'error';
		}		

   		$this->_view->actionMessage = CWidget::create('CMessage', array($alertType, $alert));
		$this->_view->render('testimonials/viewAll');        
    }	

    /**
     * Prepares testimonials module tabs
     * @param string $activeTab : default - $testimonials
     */
    private function _prepareTab($activeTab = 'testimonials')
    {
    	return CWidget::create('CTabs', array(
			'tabsWrapper'=>array('tag'=>'div', 'class'=>'title'),
			'tabsWrapperInner'=>array('tag'=>'div', 'class'=>'tabs'),
			'contentWrapper'=>array(),
			'contentMessage'=>'',
			'tabs'=>array(
				A::t('testimonials', 'Settings') => array('href'=>'modules/settings/code/testimonials', 'id'=>'tabSettings', 'content'=>'', 'active'=>false, 'htmlOptions'=>array('class'=>'modules-settings-tab')),
				A::t('testimonials', 'Testimonials') => array('href'=>'testimonials/manage', 'id'=>'tabTestimonials', 'content'=>'', 'active'=>($activeTab == 'testimonials' ? true : false)),
			),
			'events'=>array(),
			'return'=>true,
    	));
    }

	/**
	 * Check if passed testimonial ID is valid
	 * @param int $id
	 */
	private function _checkTestimonialsAccess($id = 0)
	{        
		$testimonial = Testimonials::model()->findByPk((int)$id);
		if(!$testimonial){
			$this->redirect('testimonials/manage');
		}
        return $testimonial;
	}
   
}
    
