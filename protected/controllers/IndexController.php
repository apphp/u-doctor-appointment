<?php
/**
 * Index controller
 *
 * PUBLIC:                 	PRIVATE:
 * ---------------         	---------------
 * __construct
 * indexAction
 * clearAction
 *
 */

class IndexController extends CController
{
	private $_defaultController = '';
	private $_defaultAction = '';
	
    /**
	 * Class default constructor
     */
	public function __construct()
	{
        parent::__construct();

        // Set frontend mode
        Website::setFrontend();

		$this->_defaultController = CConfig::get('defaultController');
		$this->_defaultAction = CConfig::get('defaultAction');
	}
	
	/**
	 * Controller default action handler
	 */
	public function indexAction()
	{
        $this->_view->title = '';
        $this->_view->text = '';
		
		$renderPath = strtolower($this->_defaultController.'/'.$this->_defaultAction);
		if(in_array($renderPath, array('/', 'index', 'index/', 'index/index'))){
			//$this->_view->setLayout('wide');
			$this->_view->render('index/index');	
		}else{
			$this->redirect($this->_defaultController.'/'.$this->_defaultAction);
		}		
	}
	
	/**
	 * Controller clears action handler
	 * Clears session, cookies and cache, works only in Debug mode
	 * @param string $type
	 */
	public function clearAction($type = '')
	{
		$controller = $this->_defaultController;
		$action = $this->_defaultAction;

		if(APPHP_MODE == 'debug'){
			if(CAuth::isLoggedInAsAdmin()){
				$controller = 'backend';
				$action = 'login';
			}
			
			if($type == 'session_and_cookie'){
				// Clear session
				A::app()->getSession()->removeAll();
				// Clear cookies
				A::app()->getCookie()->clearAll();				
			}elseif($type == 'cache_and_minified'){
				// Clear cache
				CFile::emptyDirectory(CConfig::get('cache.path'));
				// Clear minified
				CFile::emptyDirectory(CConfig::get('compression.css.path'));
			}
		}
		
		$this->redirect($controller.'/'.$action);
	}
	
}
