<?php
/**
 * News controller
 *
 * PUBLIC:                  PRIVATE:
 * -----------              ------------------
 * __construct              _prepareTab
 * addAction                _checkNewsAccess
 * deleteAction				_updateFeed
 * editAction
 * changeStatusAction
 * indexAction
 * insertAction
 * manageAction
 * updateAction
 * viewAction
 * viewAllAction
 */

class NewsController extends CController
{

    private $_settings;
    
    /**
     * Class default constructor
     */
    public function __construct()
    {
        parent::__construct();
        
        // Block access if the module is not installed
		if(!Modules::model()->isInstalled('news')){	
            if(CAuth::isLoggedInAsAdmin()){
                $this->redirect('modules/index');
            }else{
                $this->redirect(Website::getDefaultPage());
            }
        }
        
        // Set meta tags according to active language
        Website::setMetaTags(array('title'=>A::t('news', 'News Management')));

        $this->_view->actionMessage = '';
        $this->_view->errorField    = '';
        // Fetch site settings info 
        $this->_settings             = Bootstrap::init()->getSettings();
        $this->_view->dateTimeFormat = $this->_settings->datetime_format;
        $this->_view->dateFormat     = $this->_settings->date_format;
        $this->_view->tabs           = $this->_prepareTab('news');
    }
    
    /**
     * Controller default action handler
     */
    public function indexAction()
    {
        if(CAuth::isLoggedInAsAdmin()){
            $this->redirect('news/manage');
        }else{
            $this->redirect('news/viewAll');
        }
    }

    /**
     * Controller view all action handler
     */
    public function viewAllAction()
    {
        // Set frontend mode
        Website::setFrontend();

        $alert = '';
        $alertType = '';

        // Prepare pagination vars
        $this->_view->currentPage = A::app()->getRequest()->getQuery('page', 'integer', 1);
        if($this->_view->currentPage <= 0) {
            $this->_view->currentPage = 1;
        }
        
        $this->_view->pageSize  = ModulesSettings::model()->param('news', 'news_per_page');
        $this->_view->totalNews = News::model()->count('is_published = 1');
        $this->_view->news = News::model()->findAll(array(
            'condition' => 'is_published = 1',
            'limit'     => (($this->_view->currentPage - 1) * $this->_view->pageSize).', '.$this->_view->pageSize,
            'order'     => 'created_at DESC'
        ));
        
        if(!$this->_view->totalNews){
            $alert     = A::t('news', 'No news yet');
            $alertType = 'warning';            
        }elseif(!count($this->_view->news)){
            $alert     = A::t('news', 'Wrong parameter passed! Please try again later.');
            $alertType = 'error';
        }
        $this->_view->actionMessage = CWidget::create('CMessage', array($alertType, $alert));
        $this->_view->render('news/viewAll');        
    }

    /**
     * Controller view news description
     * @param int $nid
     */
    public function viewAction($nid = 0)
    {
        // Set frontend mode
        Website::setFrontend();

        $news = News::model()->findByPk((int)$nid, 'is_published = 1');
        if($news){

            // Counter views
			$hitsTimeCreated = A::app()->getSession()->get('news_hits_'.$nid);
            if($hitsTimeCreated){
                if($hitsTimeCreated + 600 < time()){
                    $news->hits++;
                    $news->save();
                    A::app()->getSession()->set('news_hits_'.$nid, time());
                }
            }else{
                $news->hits++;
                $news->save();
                A::app()->getSession()->set('news_hits_'.$nid, time());
            }

            $this->_view->createdDay = date('d', strtotime($news->created_at));
            $this->_view->createdMonth =  A::t('i18n', 'monthNames.abbreviated.'.date('n', strtotime($news->created_at)));
            $this->_view->createdYear = date('Y', strtotime($news->created_at));
            $this->_view->createdTitle = date('F d, Y', strtotime($news->created_at));
            $this->_view->hits = $news->hits;
            $this->_view->newsHeader    = $news->news_header;
            $this->_view->newsText      = $news->news_text;
            $this->_view->introImage    = $news->intro_image;
            $this->_view->datePublished = CLocale::date($this->_view->dateTimeFormat, $news->created_at);
            $this->_view->render('news/view');        
        }else{
            $this->redirect('error/index');
            ///news special error page
            ///$this->_view->errorHeader = 'Oops!';
            ///$this->_view->errorText .= A::t('news', 'News wrong parameter passed');
            ///$this->_view->render('news/error');
        }
    }
    
    /**
     * Manage news action handler
     */
    public function manageAction()
    {
        Website::prepareBackendAction('manage', 'news', 'news/manage');

        $alert = A::app()->getSession()->getFlash('alert');
        $alertType = A::app()->getSession()->getFlash('alertType');
        
        if(!empty($alertType)){
            $this->_view->actionMessage = CWidget::create('CMessage', array($alertType, $alert, array('button'=>true)));
        }

        $this->_view->render('news/manage');        
    }

    /**
     * Add news action handler
     */
    public function addAction()
    {
        Website::prepareBackendAction('add', 'news', 'news/manage');

        $this->_view->newsHeader  = '';
        $this->_view->newsText    = '';
        $this->_view->isPublished = 1;
        $this->_view->langList    = Languages::model()->findAll(array('condition'=>'is_active = 1', 'orderBy'=>'sort_order ASC'));
        $this->_view->language    = A::app()->getLanguage();

        $this->_view->render('news/add');
    }
    
    /**
     * Insert new record action handler
     */
    public function insertAction()
    {
        Website::prepareBackendAction('insert', 'news', 'news/manage');

        $cRequest = A::app()->getRequest();
        $cRequest->getCsrfTokenValue();
        $alert     = '';
        $alertType = '';
        $removeIntro = false;

        if($cRequest->getPost('act') == 'send'){
            // Add news form submit
            $this->_view->newsHeader  = $cRequest->getPost('news_header');
            $this->_view->newsText    = $cRequest->getPost('news_text');
            $this->_view->isPublished = $cRequest->getPost('is_published');
            
            $this->_view->langList   = Languages::model()->findAll(array('condition'=>'is_active = 1', 'orderBy'=>'sort_order ASC'));
            $this->_view->language   = A::app()->getLanguage();
			if(APPHP_MODE != 'demo'){
				$this->_view->introImage = !empty($_FILES['intro_image']['name']) ? $_FILES['intro_image']['name'] : '';
			}

            $result = CWidget::create('CFormValidation', array(
                'fields'=>array(
                    'news_header'  => array(
                        'title'      =>A::t('news', 'News Header'),
                        'validation' =>array('required'=>true, 'type'=>'text', 'maxLength'=>255)
                    ),
                    'news_text'	   => array(
                        'title'      =>A::t('news', 'News Text'),
                        'validation' =>array('required'=>true, 'type'=>'any', 'maxLength'=>10000)
                    ),
                    'intro_image'  => array(
                        'title'      =>A::t('news', 'Intro Image'),
                        'validation' =>array(
                            'required'   =>false,
                            'type'       =>'image',
                            'targetPath' =>'assets/modules/news/images/intro_images/',
                            'maxSize'    =>'500k',
                            'mimeType'   =>'image/jpeg, image/jpg, image/png, image/gif'
                        )
                    ),
                    'is_published' => array(
                        'title'      => A::t('news', 'Published'),
                        'validation' => array('required'=>true, 'type'=>'set', 'source'=>array(0,1))),
                ),
            ));
            if($result['error']){
                $alert     = $result['errorMessage'];
                $alertType = 'validation';    
                $this->_view->errorField = $result['errorField'];
            }else{
                $news = new News();
                $news->is_published = $this->_view->isPublished;
                $news->created_at   = LocalTime::currentDateTime();
                
                // Use the same translation fields for all active languages 
                $translationsArray = array();
                if(is_array($this->_view->langList)){
                    foreach($this->_view->langList as $lang){
                        $translationsArray[$lang['code']] = array(
                            'news_header' => $this->_view->newsHeader,
                            'news_text'   => $this->_view->newsText,
                        );
                    }
                }
                
                if($this->_view->introImage != ''){
                    $news->intro_image = $this->_view->introImage;
                }

                $news->setTranslationsArray($translationsArray);
                if($news->save()){
                    if($news->getError()){
                        $alert     = A::t('news', 'News new record warning');
                        $alertType = 'warning';
                    }else{
                        $alert     = A::t('news', 'News successfully added');
                        $alertType = 'success';
                    }
					
					// Update RSS feed
					$this->_updateFeed();
					
                    $this->_view->actionMessage = CWidget::create('CMessage', array($alertType, $alert, array('button'=>true)));
                    $this->_view->render('news/manage');
                    return;   
                }else{
                    if(APPHP_MODE == 'demo'){
                        $alert     = CDatabase::init()->getErrorMessage();
                        $alertType = 'warning';
                    }else{
                        $alert     = A::t('news', 'News new record error');
                        $alertType = 'error';
                    }
                }
            }
            
            if(!empty($alert)){
                $this->_view->actionMessage = CWidget::create('CMessage', array($alertType, $alert, array('button'=>true)));
            }
            $this->_view->render('news/add');
        }else{
            $this->redirect('news/manage');
        }
    }

    /**
     * News edit action handler
     * @param int $id 
     */
    public function editAction($id = 0)
    {
        Website::prepareBackendAction('edit', 'news', 'news/manage');
        
        $cRequest = A::app()->getRequest();       
        if($cRequest->getPost('act') == 'send'){
            $this->_view->language = $cRequest->getPost('language'); 
            $id = $cRequest->getPost('id'); 
        }else{
            $this->_view->language = A::app()->getLanguage();
        }

        $news = $this->_checkNewsAccess($id);
        
        ///$this->_view->news = $news;
        $translationsArray = $news->selectTranslations();
        $this->_view->newsHeader = isset($translationsArray[$this->_view->language]) 
            ? $translationsArray[$this->_view->language]['news_header']
            : '';
        $this->_view->newsText   = isset($translationsArray[$this->_view->language]) 
            ? $translationsArray[$this->_view->language]['news_text']
            : '';
            
        $this->_view->id          = $news->id;
        $this->_view->introImage  = $news->intro_image;
        $this->_view->isPublished = $news->is_published;
        $this->_view->langList    = Languages::model()->findAll(array('condition'=>'is_active = 1', 'orderBy'=>'sort_order ASC'));
        
        $this->_view->render('news/edit');
    }

	/**
     * Change news state method
	 * @param int $id 		the news ID
	 * @param int $page 	the page number
     */
    public function changeStatusAction($id, $page = 1)
    {
		Website::prepareBackendAction('edit', 'news', 'news/manage');

		$news = $this->_checkNewsAccess($id);
		if(!empty($news)){
            if(News::model()->updateByPk($id, array('is_published'=>($news->is_published == 1 ? '0' : '1')))){
			A::app()->getSession()->setFlash('alert', A::t('news', 'News status has been successfully changed!'));
			A::app()->getSession()->setFlash('alertType', 'success');
            }else{
				A::app()->getSession()->setFlash('alert', (APPHP_MODE == 'demo') ? A::t('core', 'This operation is blocked in Demo Mode!') : A::t('app', 'Status changing error'));
                A::app()->getSession()->setFlash('alertType', (APPHP_MODE == 'demo') ? 'warning' : 'error');
            }
        }
		
		$this->redirect('news/manage'.(!empty($page) ? '?page='.(int)$page : 1));
    }

    /**
     * Update news action handler
     */
    public function updateAction()
    {
        Website::prepareBackendAction('update', 'news', 'news/manage');

        $cRequest    = A::app()->getRequest();
        $alert         = '';
        $alertType     = '';
        $doSave      = false;
        $removeIntro = false;
        
        $news = $this->_checkNewsAccess($cRequest->getPost('id', 'int'));
        
        // Retrieve data for actions
        $this->_view->newsHeader  = $cRequest->getPost('news_header');
        $this->_view->newsText    = $cRequest->getPost('news_text');
        $this->_view->isPublished = $cRequest->getPost('is_published');
        $this->_view->langList    = Languages::model()->findAll(array('condition'=>'is_active = 1', 'orderBy'=>'sort_order ASC'));
        $this->_view->language    = $cRequest->getPost('language');
        $this->_view->id          = $news->id;
		if(APPHP_MODE != 'demo'){
			$this->_view->introImage  = !empty($_FILES['intro_image']['name']) ? $_FILES['intro_image']['name'] : $news->intro_image;
		}

        if($cRequest->getPost('act') == 'send'){
            $result = CWidget::create('CFormValidation', array(
                'fields'=>array(
                    'news_header'  => array(
                        'title'      => A::t('news', 'News Header'), 
                        'validation' => array('required'=>true, 'type'=>'text', 'maxLength'=>255)
                    ),
                    'news_text'	   => array(
                        'title'      => A::t('news', 'News Text'),
                        'validation' => array('required'=>true, 'type'=>'any', 'maxLength'=>10000)
                    ),
                    'intro_image'  => array(
                        'title'      => A::t('news', 'Intro Image'),
                        'validation' => array(
                            'required'   => false,
                            'type'       => 'image',
                            'targetPath' => 'assets/modules/news/images/intro_images/',
                            'maxSize'    => '500k',
                            'mimeType'   => 'image/jpeg, image/jpg, image/png, image/gif'
                        )
                    ),
                    'is_published' => array(
                        'title'      => A::t('news', 'Published'),
                        'validation' => array('required' => true, 'type' => 'set', 'source' => array(0,1))
                    ),
                ),
            ));
    
            if($result['error']){
                $alert = $result['errorMessage'];
                $alertType = 'validation';    
                $this->_view->errorField = $result['errorField'];
                
                if($this->_view->errorField == 'intro_image'){
                    $this->_view->introImage = $news->intro_image;
                }
            }else{
                $news->is_published = $this->_view->isPublished;
                
                $translationsArray[$this->_view->language] = array(
                    'news_header' => $this->_view->newsHeader,
                    'news_text'   => $this->_view->newsText,
                );
                 
                unset($news->intro_image);
                if($this->_view->introImage != ''){
                    $news->intro_image = $this->_view->introImage;
                }
                 
                // Save changes and update data in translation table
                $news->setTranslationsArray($translationsArray);
                $doSave = true;
            }
        }elseif($cRequest->getPost('act') == 'delete-intro'){
            $this->_view->introImage = $news->intro_image;
            $news->intro_image = '';
            $doSave      = true;
            $removeIntro = true;
        }
        else{
            $this->redirect('news/manage');
        }

        if($doSave){
            if($news->save()){
                if($news->getError()){
					// Update RSS feed
					$this->_updateFeed();
					
                    $alert = A::t('news', 'News updating record warning');
                    $alertType = 'warning';
                }else{
                    $alert = A::t('news', 'News successfully updated');
                    $alertType = 'success';
                }
                
                $this->_view->actionMessage = CWidget::create('CMessage', array($alertType, $alert, array('button'=>true)));
                if($cRequest->isPostExists('btnUpdateClose')){
                    $this->_view->render('news/manage');
                    return;
                }

                if($removeIntro && APPHP_MODE != 'demo'){
                    CFile::deleteFile('assets/modules/news/images/intro_images/'.$this->_view->introImage);
                    $this->_view->introImage = '';
                }
            }else{
                $this->_view->errorField = '';
                if(APPHP_MODE == 'demo'){
                    $alert     = CDatabase::init()->getErrorMessage();
                    $alertType = 'warning';
                }else{
                    $alert     = A::t('news', 'News new record error');
                    $alertType = 'error';
                }
            }
        }
        
        // render and shopw message
        if(!empty($alert)){
            $this->_view->actionMessage = CWidget::create('CMessage', array($alertType, $alert, array('button'=>true)));
        }
        $this->_view->render('news/edit');
    }

    /**
     * Delete news action handler
     * @param int $id the news id
     */
    public function deleteAction($id = 0)
    {
        Website::prepareBackendAction('delete', 'news', 'news/manage');
        $news = $this->_checkNewsAccess($id);
        
        $alert     = '';
        $alertType = '';
    
        if($news->delete()){
			// Update RSS feed
			$this->_updateFeed();
			
            $alert = A::t('news', 'News successfully deleted');
            $alertType = 'success';
        }else{
            if(APPHP_MODE == 'demo'){
                $alert     = CDatabase::init()->getErrorMessage();
                $alertType = 'warning';
            }else{
                $alert     = A::t('news', 'News deleting error');
                $alertType = 'error';
            }
        }
        
		if(!empty($alert)){
			A::app()->getSession()->setFlash('alert', $alert);
			A::app()->getSession()->setFlash('alertType', $alertType);
        }
        
		$this->redirect('news/manage');
    }

    /**
     * Prepares news module tabs
     * @param string $activeTab : default - news
     */
    private function _prepareTab($activeTab = 'news')
    {
        return CWidget::create('CTabs', array(
            'tabsWrapper'=>array('tag'=>'div', 'class'=>'title'),
            'tabsWrapperInner'=>array('tag'=>'div', 'class'=>'tabs'),
            'contentWrapper'=>array(),
            'contentMessage'=>'',
            'tabs'=>array(
                A::t('news', 'Settings')   => array('href' => 'modules/settings/code/news', 'id' => 'tabSettings', 'content' => '', 'active' => false, 'htmlOptions' => array('class'=>'modules-settings-tab')),
                A::t('news', 'News') => array('href' => 'news/manage', 'id' => 'tabNews', 'content' => '', 'active' => ($activeTab == 'news' ? true : false)),
                A::t('news', 'Subscribers') => array('href'=>'newsSubscribers/manage', 'id'=>'tabSubscribe', 'content'=>'', 'active' => false),
            ),
            'events'=>array(),
            'return'=>true,
        ));
    }

    /**
     * Check if passed news ID is valid
     * @param int $newsId
     */
    private function _checkNewsAccess($newsId = 0)
    {        
        $news = News::model()->findByPk((int)$newsId);
        if(!$news){
            $this->redirect('news/manage');
        }
        return $news;
    }    

    /**
     * Function for update feeds list
     */
    private function _updateFeed()
    {
        $feedSettings = Bootstrap::init()->getSettings();

        $countFeedItems = 0;
        $typeFeedPosts = $feedSettings->rss_feed_type;
        $feedChannel = 'news';
        $nameChannel = '';

        // Save rss-file with the default language
        $defaultLang = Languages::model()->find('is_default = 1');
        if(!empty($defaultLang)){
            $lang = $defaultLang->code;
        }else{
            $lang = A::app()->getLanguage();
        }

        $rssData = Rss::model()->find("channel_code = 'news' AND mode_code = 'news'");
        $rssIds = '';
        $rssLastIds = '';

        if(!empty($rssData)){
            $countFeedItems = (int)$rssData->items_count;
            $nameChannel = $rssData->channel_name;
        }else{
            $rssData = new Rss();
            $countFeedItems = (int)$feedSettings->rss_items_per_feed;
            $nameChannel = CConfig::get('name').' - '.A::t('news', 'News');
            $rssData->items_count = $countFeedItems;
            $rssData->channel_name = CConfig::get('name');
            $rssData->channel_code = 'directory';
            $rssData->mode_code = 'news';
        }
        $rssData->updated_at = LocalTime::currentDateTime();

        if($rssData->last_items != ''){
            $rssLastIds = $rssData->last_items;
        }
		
        CRss::setType($typeFeedPosts);
        CRss::setFile('news_rss.xml');
        CRss::setChannel(
            array(
                'url'           => 'feeds/news_rss.xml',
                'title'         => $nameChannel,
                'description'   => $nameChannel,
                'lang'          => $lang,
                'copyright'     => '(c) copyright',
                'creator'       => $this->_view->customerFullNames,
                'author'        => $this->_view->customerFullNames,
                'subject'       => $nameChannel
            )
        );

        CRss::setImage(A::app()->getRequest()->getBaseUrl().'/assets/modules/news/images/icon.png');

        // Get last 10 active news
        $allNews = News::model()->findAll(array('condition'=>'is_published = 1', 'orderBy'=>'created_at DESC', 'limit'=>'0, 10'));

        $totalNews = count($allNews);

        for($i = 0; $i < $totalNews; $i++){
            $rssIds .= (($i > 0) ? '-' : '').$allNews[$i]['id'];
        }

        $rssData->last_items = $rssIds;
        $rssData->save();

        // check if there difference between RSS IDs, so we have to update RSS file
        if($rssLastIds != $rssIds){
            for($i = 0; $i < $totalNews; $i++){
                $rss_text = CRss::cleanTextRss(strip_tags($allNews[$i]['news_text']));
                if(strlen($rss_text) > 512) $rss_text = substr($rss_text, 0, 512).'...';
                //$rss_text = htmlentities($rss_text, ENT_COMPAT, 'UTF-8');
				$link = A::app()->getRequest()->getBaseUrl().Website::prepareLinkByFormat('news', 'news_link_format', $allNews[$i]['id'], $allNews[$i]['news_header']);
                CRss::setItem($link, $allNews[$i]['news_header'], $rss_text, $allNews[$i]['created_at']);
            }
            CRss::saveFeed();
        }
    }
}
