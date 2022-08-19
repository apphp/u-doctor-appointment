<?php
/**
 * Doctors controller
 * This controller intended to both Backend and Frontend modes
 *
 * PUBLIC:                          PRIVATE
 * -----------                      ------------------
 * __construct                      _checkDoctorAccess
 * manageAction                     _checkImageAccess
 * addAction                        _checkDoctorFrontendAccess
 * editAction                       _checkUploadImagesCountAccess
 * deleteAction                     _checkUploadMultiImagesAccess
 * changeStatusAction
 *
 */

namespace Modules\Appointments\Controllers;

// Module
use \Modules\Appointments\Components\AppointmentsComponent;
use \Modules\Appointments\Components\DoctorsComponent;
use \Modules\Appointments\Models\DoctorImages;
use \Modules\Appointments\Models\Doctors;

// Framework
use \A,
    \CAuth,
    \CArray,
    \CFile,
    \CImage,
    \CWidget,
    \CHash,
    \CTime,
    \CValidator,
    \CController,
    \CConfig,
	\CDatabase;

// Application
use \Website,
    \Admins,
    \Accounts,
    \Bootstrap,
    \Languages,
    \ModulesSettings,
    \Countries,
    \Modules,
    \LocalTime,
    \SocialLogin,
    \States,
    \BanLists;



class DoctorImagesController extends CController
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

        $this->_settings = Bootstrap::init()->getSettings();
        $this->_cSession = A::app()->getSession();

        $this->_view->actionMessage = '';
        $this->_view->errorField = '';

		$this->_view->doctorsWatermark = ModulesSettings::model()->param('appointments', 'doctors_watermark');
		$this->_view->watermarkText = ModulesSettings::model()->param('appointments', 'doctors_watermark_text');

        if(CAuth::isLoggedInAsAdmin()){
            // set meta tags according to active doctors
            Website::setMetaTags(array('title'=>A::t('appointments', 'Doctors Management')));

            $this->_view->tabs = AppointmentsComponent::prepareTab('doctors');
        }
    }

    /**
     * Manage images action handler
	 * @param int $doctorId
     * @return void
     */
    public function manageAction($doctorId = 0)
    {
        // set backend mode
        Website::setBackend();
        Website::prepareBackendAction('manage', 'doctor', 'doctors/manage');
        $doctor = $this->_checkDoctorAccess($doctorId);

        $actionMessage = '';
        $alert = A::app()->getSession()->getFlash('alert');
        $alertType = A::app()->getSession()->getFlash('alertType');

        if(!empty($alert)){
            $actionMessage = CWidget::create('CMessage', array($alertType, $alert, array('button'=>true)));
        }

        $this->_view->allowMultiImageUpload = ModulesSettings::model()->param('appointments', 'allow_multi_image_upload');
        $this->_view->checkUploadImagesAccess = $this->_checkUploadImagesCountAccess($doctor->id, $doctor->membership_images_count);
        $this->_view->dateFormat = $this->_settings->date_format;
        $this->_view->actionMessage = $actionMessage;
        $this->_view->doctorId = $doctor->id;
        $this->_view->doctorName = $doctor->getFullName();
        $this->_view->render('doctorImages/manage');
    }

    /**
     * Add image action handler
	 * @param int $doctorId
     * @return void
     */
    public function addAction($doctorId = 0)
    {
        // set backend mode
        Website::setBackend();
        Website::prepareBackendAction('manage', 'doctor', 'doctorImages/manage/doctorId/'.$doctorId);
        $doctor = $this->_checkDoctorAccess($doctorId);

        $checkUploadImagesAccess = $this->_checkUploadImagesCountAccess($doctor->id, $doctor->membership_images_count, 'doctorImages/manage/doctorId/'.$doctorId);

        $this->_view->doctorId = $doctor->id;
        $this->_view->doctorName = $doctor->getFullName();

        $this->_view->imageMaxSize = ModulesSettings::model()->param('appointments', 'image_max_size');
        $this->_view->render('doctorImages/add');
    }

    /**
     * Add doctor multiple-image action handler
     * @param int $doctorId
     * @access public
     * @return void
     */
    public function addMultipleAction($doctorId = 0)
    {
        Website::prepareBackendAction('add', 'doctor', 'doctorImages/manage/doctorId/'.$doctorId);
        $doctor = $this->_checkDoctorAccess($doctorId);

		$checkUploadImagesAccess = $this->_checkUploadImagesCountAccess($doctor->id, $doctor->membership_images_count, 'doctorImages/manage/doctorId/'.$doctorId);

        $allowMultiImageUpload = ModulesSettings::model()->param('appointments', 'allow_multi_image_upload');
        $maxImages = ModulesSettings::model()->param('appointments', 'doctor_maximum_images_upload');
        $imageMaxSize = ModulesSettings::model()->param('appointments', 'image_max_size');

        // Block access to multi image upload
        if(!$allowMultiImageUpload){
            $this->redirect('doctorImages/manage/doctorId/'.$doctorId);
        }

        if(A::app()->getRequest()->getPost('act') == 'send'){
            $fieldsImages = array();
            for($i = 1; $i <= $maxImages; $i++){
                $fieldsImages['doctor_image'][] = array('title'=>A::t('appointments', 'Image').' #'.$i, 'validation'=>array('required'=>false, 'type'=>'image', 'targetPath'=>'assets/modules/appointments/images/doctorimages/', 'maxSize'=>$imageMaxSize, 'fileName'=>'l'.$doctorId.'_'.CHash::getRandomString(10), 'mimeType'=>'image/jpeg, image/jpg, image/png, image/gif'));
            }

            $result = CWidget::create('CFormValidation', array('fields'=>$fieldsImages, 'multiArray'=>true));
			$countUploadedImages = count($result['uploadedFiles']);
			$checkUploadMultiImagesAccess = $this->_checkUploadMultiImagesAccess($doctor->id, $countUploadedImages, $doctor->membership_images_count, 'doctorImages/addMultiple/doctorId/'.$doctorId);
            if($result['error']){
                $alert     = $result['errorMessage'];
                $alertType = 'validation';
            }else{
                // Add images here
                if(!empty($result['uploadedFiles'])){
                    $errorSave = false;
                    $width     = '200px';
                    $height    = '200px';
                    $directory = 'thumbs'.DS;
                    $maxOrder  = DoctorImages::model()->max('sort_order', 'doctor_id = :doctor_id', array('i:doctor_id'=>$doctorId));

                    foreach($result['uploadedFiles'] as $pathToImage){
                        // Create thumbnail
                        $imageName         = basename($pathToImage);
                        $path              = APPHP_PATH.DS.str_ireplace($imageName, '', $pathToImage);
                        $thumbFileExt      = substr(strrchr($imageName, '.'), 1);
                        $thumbFileName     = str_replace('.'.$thumbFileExt, '', $imageName);
                        $thumbFileFullName = $thumbFileName.'_thumb.'.$thumbFileExt;
                        CFile::copyFile($path.$imageName, $path.$directory.$thumbFileFullName);
                        $thumbFileRealName = CImage::resizeImage($path.$directory, $thumbFileFullName, $width, $height);

                        if(!empty($thumbFileRealName)){
                            $image = new DoctorImages();
                            $image->doctor_id = $doctorId;
                            $image->image_file = $imageName;
                            $image->image_file_thumb = $thumbFileRealName;
                            $image->sort_order = ++$maxOrder;
                            $image->is_active  = 1;
                            if(!$image->save()){
                                if(APPHP_MODE == 'demo'){
                                    $alert     = CDatabase::init()->getErrorMessage();
                                    $alertType = 'warning';
                                }else{
                                    $alert     = A::t('appointments', 'The error occurred while adding new record!');
                                    $alertType = 'error';
                                }
                                $errorSave = true;
                                break;
                            }
                        }
                    }
                    if(!$errorSave){
                        $alertType = 'success';
                        $alert = A::t('appointments', 'New {item_type} has been successfully added!', array('{item_type}'=>A::t('appointments', 'Images')));
                        A::app()->getSession()->setFlash('alert', $alert);
                        A::app()->getSession()->setFlash('alertType', $alertType);
                        $this->redirect('doctorImages/manage/doctorId/'.$doctorId);
                    }
                }elseif(A::app()->getRequest()->getPost('act') == 'send'){
                    $alertType = 'validation';
                    $alert = A::t('appointments', 'You have to chose at least one image for uploading! Please re-enter.');
                }
            }

            if(!empty($alert)){
                $this->_view->actionMessage = CWidget::create('CMessage', array($alertType, $alert, array('button'=>true)));
            }
        }

		$alert = A::app()->getSession()->getFlash('alert');
		$alertType = A::app()->getSession()->getFlash('alertType');

		if(!empty($alert)){
			$this->_view->actionMessage = CWidget::create('CMessage', array($alertType, $alert, array('button'=>true)));
		}

        $this->_view->maxImages  = $maxImages;
        $this->_view->doctorId   = $doctorId;
        $this->_view->doctorName = $doctor->doctor_first_name.' '.$doctor->doctor_last_name;

        $this->_view->render('doctorImages/addMultiple');
    }

    /**
     * Edit image action handler
     * @return void
     */
    public function editAction($doctorId = 0, $id = 0, $delete = '')
    {
        // set backend mode
        Website::setBackend();
        Website::prepareBackendAction('manage', 'doctor', 'doctorImages/manage/doctorId/'.$doctorId);
        $doctor = $this->_checkDoctorAccess($doctorId);
        $image = $this->_checkImageAccess($id, $doctorId);

        // Delete the image file
        if($delete === 'image'){
            $imagePath = 'assets/modules/appointments/images/doctorimages/'.$image->image_file;
            $imageThumbPath = 'assets/modules/appointments/images/doctorimages/thumbs/'.$image->image_file_thumb;
            $image->image_file = '';
            $image->image_file_thumb = '';
            if($image->save()){
                // Delete the images
                if(CFile::deleteFile($imagePath) && CFile::deleteFile($imageThumbPath)){
                    $alert = A::t('appointments', 'Image has been successfully deleted!');
                    $alertType = 'success';
                }else{
                    $alert = A::t('appointments', 'An error occurred while deleting an image! Please try again later.');
                    $alertType = 'warning';
                }
            }else{
                $alert = A::t('appointments', 'An error occurred while deleting an image! Please try again later.');
                $alertType = 'error';
            }

            if(!empty($alert)){
                $this->_view->actionMessage = CWidget::create('CMessage', array($alertType, $alert, array('button'=>true)));
            }
        }


        $this->_view->imageMaxSize = ModulesSettings::model()->param('appointments', 'image_max_size');
        $this->_view->id = $image->id;
        $this->_view->doctorId = $doctor->id;
        $this->_view->doctorName = $doctor->doctor_first_name.' '.$doctor->doctor_last_name;
        $this->_view->render('doctorImages/edit');
    }

    /**
     * Delete specialty action handler
     * @param int $doctorId
     * @param int $id
     * @return void
     */
    public function deleteAction($doctorId = 0, $id = 0)
    {
        // set backend mode
        Website::setBackend();
        Website::prepareBackendAction('manage', 'doctor', 'doctorImages/manage/doctorId/'.$doctorId);
        $doctor = $this->_checkDoctorAccess($doctorId);
        $image = $this->_checkImageAccess($id, $doctorId);

        $alert = '';
        $alertType = '';
        $actionMessage = '';

        $imagePath = 'assets/modules/appointments/images/doctorimages/'.$image->image_file;
        $imageThumbPath = 'assets/modules/appointments/images/doctorimages/thumbs/'.$image->image_file_thumb;
        if($image->delete()){
            $alert = A::t('appointments', 'Doctor image record has been successfully deleted!');
            // Delete the icon file
            if($image->image_file == '' || (CFile::deleteFile($imagePath) && CFile::deleteFile($imageThumbPath))){
                $alertType = 'success';
            }else{
                $alert .= '<br>'.A::t('appointments', 'Doctor image delete image warning');
                $alertType = 'warning';
            }
        }else{
            if(APPHP_MODE == 'demo'){
                $alert = CDatabase::init()->getErrorMessage();
                $alertType = 'warning';
            }else{
                $alert = A::t('appointments', 'Doctor image deleting error');
                $alertType = 'error';
            }
        }

        if(!empty($alert)){
            A::app()->getSession()->setFlash('alert', $alert);
            A::app()->getSession()->setFlash('alertType', $alertType);
        }

        $this->redirect('doctorImages/manage/doctorId/'.$doctorId);
    }

    /**
     * Change Doctor Image status
     * @param int $doctorId
     * @param int $id
     * @param int $page 	the page number
     */
    public function changeStatusAction($doctorId = 0, $id = 0, $page = 1)
    {
        Website::prepareBackendAction('edit', 'doctor', 'doctorImages/manage/doctorId/'.$doctorId);
        $this->_checkDoctorAccess($doctorId);
        $image = $this->_checkImageAccess($id, $doctorId);

        $changeResult = DoctorImages::model()->updateByPk($id, array('is_active'=>($image->is_active == 1 ? '0' : '1')));
        if($changeResult){
            $alert = A::t('appointments', 'Doctor image status has been successfully changed!');
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

        $this->redirect('doctorImages/manage/doctorId/'.$doctorId.(!empty($page) ? '?page='.(int)$page : 1));
    }

    /**
     * Manage images action handler
     * @return void
     */
    public function myImagesAction()
    {
        // Set frontend mode
        Website::setFrontend();
        // Block access to this controller for not-logged doctors
        CAuth::handleLogin('doctors/login', 'doctor');

        $doctorId = CAuth::getLoggedRoleId();

        $doctor = $this->_checkDoctorFrontendAccess($doctorId);

        $actionMessage = '';
        $alert = A::app()->getSession()->getFlash('alert');
        $alertType = A::app()->getSession()->getFlash('alertType');

        if(!empty($alert)){
            $actionMessage = CWidget::create('CMessage', array($alertType, $alert, array('button'=>true)));
        }

        $this->_view->allowMultiImageUpload = ModulesSettings::model()->param('appointments', 'allow_multi_image_upload');
        $this->_view->checkAccessAccountUsingMembershipPlan = DoctorsComponent::checkAccessAccountUsingMembershipPlan(false);
		$this->_view->checkUploadImagesAccess = $this->_checkUploadImagesCountAccess($doctor->id, $doctor->membership_images_count);
        $this->_view->dateFormat = $this->_settings->date_format;
        $this->_view->actionMessage = $actionMessage;
        $this->_view->doctorId = $doctorId;
        $this->_view->doctorName = $doctor->doctor_first_name.' '.$doctor->doctor_last_name;
        $this->_view->render('doctorImages/myImages');
    }

    /**
     * Add image action handler
     * @return void
     */
    public function addMyImageAction()
    {
        // block access to this controller for doctors without membership plan or expired membership plan
        DoctorsComponent::checkAccessAccountUsingMembershipPlan();

        // Set frontend mode
        Website::setFrontend();
        // Block access to this controller for not-logged doctors
        CAuth::handleLogin('doctors/login', 'doctor');

        $doctorId = CAuth::getLoggedRoleId();
        $doctor = $this->_checkDoctorFrontendAccess($doctorId);

		$checkUploadImagesAccess = $this->_checkUploadImagesCountAccess($doctor->id, $doctor->membership_images_count, 'doctorImages/myImages');

        $this->_view->doctorId = $doctorId;
        $this->_view->doctorName = $doctor->doctor_first_name.' '.$doctor->doctor_last_name;

        $this->_view->doctorsWatermark = ModulesSettings::model()->param('appointments', 'doctors_watermark');
        $this->_view->watermarkText = ModulesSettings::model()->param('appointments', 'doctors_watermark_text');

        $this->_view->imageMaxSize = ModulesSettings::model()->param('appointments', 'image_max_size');
        $this->_view->render('doctorImages/addMyImage');
    }

    /**
     * Add doctor multiple-image action handler
     *
     * @param int $doctorId
     * @access public
     * @return void
     */
    public function addMyMultipleAction()
    {
        // block access to this controller for doctors without membership plan or expired membership plan
        DoctorsComponent::checkAccessAccountUsingMembershipPlan();
        
        // Set frontend mode
        Website::setFrontend();
        // Block access to this controller for not-logged doctors
        CAuth::handleLogin('doctors/login', 'doctor');

        $doctorId = CAuth::getLoggedRoleId();
        $doctor = $this->_checkDoctorFrontendAccess($doctorId);

		$checkUploadImagesAccess = $this->_checkUploadImagesCountAccess($doctor->id, $doctor->membership_images_count, 'doctorImages/myImages');

        $allowMultiImageUpload = ModulesSettings::model()->param('appointments', 'allow_multi_image_upload');
        $maxImages = ModulesSettings::model()->param('appointments', 'doctor_maximum_images_upload');
        $imageMaxSize = ModulesSettings::model()->param('appointments', 'image_max_size');

        // Block access to multi image upload
        if(!$allowMultiImageUpload){
            $this->redirect('doctorImages/myImages');
        }

        if(A::app()->getRequest()->getPost('act') == 'send'){
            $fieldsImages = array();
            for($i = 1; $i <= $maxImages; $i++){
                $fieldsImages['doctor_image'][] = array('title'=>A::t('appointments', 'Image').' #'.$i, 'validation'=>array('required'=>false, 'type'=>'image', 'targetPath'=>'assets/modules/appointments/images/doctorimages/', 'maxSize'=>$imageMaxSize, 'fileName'=>'l'.$doctorId.'_'.CHash::getRandomString(10), 'mimeType'=>'image/jpeg, image/jpg, image/png, image/gif'));
            }

            $result = CWidget::create('CFormValidation', array('fields'=>$fieldsImages, 'multiArray'=>true));
			$countUploadedImages = count($result['uploadedFiles']);
			$checkUploadMultiImagesAccess = $this->_checkUploadMultiImagesAccess($doctor->id, $countUploadedImages, $doctor->membership_images_count, 'doctorImages/addMyMultiple');
            if($result['error']){
                $alert     = $result['errorMessage'];
                $alertType = 'validation';
            }else{
                // Add images here
                if(!empty($result['uploadedFiles'])){
                    $errorSave = false;
                    $width     = '200px';
                    $height    = '200px';
                    $directory = 'thumbs'.DS;
                    $maxOrder  = DoctorImages::model()->max('sort_order', 'doctor_id = :doctor_id', array('i:doctor_id'=>$doctorId));

                    foreach($result['uploadedFiles'] as $pathToImage){
                        // Create thumbnail
                        $imageName         = basename($pathToImage);
                        $path              = APPHP_PATH.DS.str_ireplace($imageName, '', $pathToImage);
                        $thumbFileExt      = substr(strrchr($imageName, '.'), 1);
                        $thumbFileName     = str_replace('.'.$thumbFileExt, '', $imageName);
                        $thumbFileFullName = $thumbFileName.'_thumb.'.$thumbFileExt;
                        CFile::copyFile($path.$imageName, $path.$directory.$thumbFileFullName);
                        $thumbFileRealName = CImage::resizeImage($path.$directory, $thumbFileFullName, $width, $height);

                        if(!empty($thumbFileRealName)){
                            $image = new DoctorImages();
                            $image->doctor_id = $doctorId;
                            $image->image_file = $imageName;
                            $image->image_file_thumb = $thumbFileRealName;
                            $image->sort_order = ++$maxOrder;
                            $image->is_active  = 1;
                            if(!$image->save()){
                                if(APPHP_MODE == 'demo'){
                                    $alert     = CDatabase::init()->getErrorMessage();
                                    $alertType = 'warning';
                                }else{
                                    $alert     = A::t('appointments', 'The error occurred while adding new record!');
                                    $alertType = 'error';
                                }
                                $errorSave = true;
                                break;
                            }
                        }
                    }
                    if(!$errorSave){
                        $alertType = 'success';
                        $alert = A::t('appointments', 'New {item_type} has been successfully added!', array('{item_type}'=>A::t('appointments', 'Images')));
                        A::app()->getSession()->setFlash('alert', $alert);
                        A::app()->getSession()->setFlash('alertType', $alertType);
                        $this->redirect('doctorImages/myImages');
                    }
                }elseif(A::app()->getRequest()->getPost('act') == 'send'){
                    $alertType = 'validation';
                    $alert = A::t('appointments', 'You have to chose at least one image for uploading! Please re-enter.');
                }
            }

            if(!empty($alert)){
                $this->_view->actionMessage = CWidget::create('CMessage', array($alertType, $alert, array('button'=>true)));
            }
        }

		$alert = A::app()->getSession()->getFlash('alert');
		$alertType = A::app()->getSession()->getFlash('alertType');

		if(!empty($alert)){
			$this->_view->actionMessage = CWidget::create('CMessage', array($alertType, $alert, array('button'=>true)));
		}

        $this->_view->maxImages  = $maxImages;
        $this->_view->doctorId   = $doctorId;
        $this->_view->doctorName = $doctor->doctor_first_name.' '.$doctor->doctor_last_name;

        $this->_view->render('doctorImages/addMyMultiple');
    }

    /**
     * Edit image action handler
     * @return void
     */
    public function editMyImageAction($id = 0, $delete = '')
    {
        // block access to this controller for doctors without membership plan or expired membership plan
        DoctorsComponent::checkAccessAccountUsingMembershipPlan();

        // Set frontend mode
        Website::setFrontend();
        // Block access to this controller for not-logged doctors
        CAuth::handleLogin('doctors/login', 'doctor');

        $doctorId = CAuth::getLoggedRoleId();
        $doctor = $this->_checkDoctorFrontendAccess($doctorId);
        $image = $this->_checkImageFrontendAccess($id, $doctorId);

        // Delete the image file
        if($delete === 'image'){
            $imagePath = 'assets/modules/appointments/images/doctorimages/'.$image->image_file;
            $imageThumbPath = 'assets/modules/appointments/images/doctorimages/thumbs/'.$image->image_file_thumb;
            $image->image_file = '';
            $image->image_file_thumb = '';
            if($image->save()){
                // Delete the images
                if(CFile::deleteFile($imagePath) && CFile::deleteFile($imageThumbPath)){
                    $alert = A::t('appointments', 'Image has been successfully deleted!');
                    $alertType = 'success';
                }else{
                    $alert = A::t('appointments', 'An error occurred while deleting an image! Please try again later.');
                    $alertType = 'warning';
                }
            }else{
                $alert = A::t('appointments', 'An error occurred while deleting an image! Please try again later.');
                $alertType = 'error';
            }

            if(!empty($alert)){
                $this->_view->actionMessage = CWidget::create('CMessage', array($alertType, $alert, array('button'=>true)));
            }
        }


        $this->_view->imageMaxSize = ModulesSettings::model()->param('appointments', 'image_max_size');
        $this->_view->id = $image->id;
        $this->_view->doctorId = $doctorId;
        $this->_view->doctorName = $doctor->doctor_first_name.' '.$doctor->doctor_last_name;
        $this->_view->render('doctorImages/editMyImage');
    }

    /**
     * Delete specialty action handler
     * @param int $id
     * @return void
     */
    public function deleteMyImageAction($id = 0)
    {
        // block access to this controller for doctors without membership plan or expired membership plan
        DoctorsComponent::checkAccessAccountUsingMembershipPlan();

        // Set frontend mode
        Website::setFrontend();
        // Block access to this controller for not-logged doctors
        CAuth::handleLogin('doctors/login', 'doctor');

        $doctorId = CAuth::getLoggedRoleId();
        $doctor = $this->_checkDoctorFrontendAccess($doctorId);
        $image = $this->_checkImageFrontendAccess($id, $doctorId);

        $alert = '';
        $alertType = '';
        $actionMessage = '';

        $imagePath = 'assets/modules/appointments/images/doctorimages/'.$image->image_file;
        $imageThumbPath = 'assets/modules/appointments/images/doctorimages/thumbs/'.$image->image_file_thumb;
        if($image->delete()){
            $alert = A::t('appointments', 'Doctor image record has been successfully deleted!');
            // Delete the icon file
            if($image->image_file == '' || (CFile::deleteFile($imagePath) && CFile::deleteFile($imageThumbPath))){
                $alertType = 'success';
            }else{
                $alert .= '<br>'.A::t('appointments', 'Doctor image delete image warning');
                $alertType = 'warning';
            }
        }else{
            if(APPHP_MODE == 'demo'){
                $alert = CDatabase::init()->getErrorMessage();
                $alertType = 'warning';
            }else{
                $alert = A::t('appointments', 'Doctor image deleting error');
                $alertType = 'error';
            }
        }

        if(!empty($alert)){
            A::app()->getSession()->setFlash('alert', $alert);
            A::app()->getSession()->setFlash('alertType', $alertType);
        }

        $this->redirect('doctorImages/myImages');
    }

    /**
     * Change Doctor Image status
     * @param int $id
     */
    public function changeFrontendStatusAction($id = 0)
    {
        // Set frontend mode
        Website::setFrontend();
        // Block access to this controller for not-logged doctors
        CAuth::handleLogin('doctors/login', 'doctor');

        $doctorId = CAuth::getLoggedRoleId();
        $doctor = $this->_checkDoctorFrontendAccess($doctorId);
        $image = $this->_checkImageFrontendAccess($id, $doctorId);

        $changeResult = DoctorImages::model()->updateByPk($id, array('is_active'=>($image->is_active == 1 ? '0' : '1')));
        if($changeResult){
            $alert = A::t('appointments', 'Doctor image status has been successfully changed!');
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

        $this->redirect('doctorImages/myImages');
    }

    /**
     * Check if passed Doctor ID is valid
     * @param int $id
     * @return Doctors
     */
    private function _checkDoctorAccess($id = 0)
    {
        $doctor = Doctors::model()->findByPk($id);
        if(!$doctor){
            $this->redirect('doctors/manage');
        }
        return $doctor;
    }

    /**
     * Check Image is valid
     * @param int $id
     * @param int $doctorId
     * @return Timeoff
     */
    private function _checkImageAccess($id = 0, $doctorId = 0)
    {
        $image = DoctorImages::model()->findByPk($id, 'doctor_id = :doctor_id', array(':doctor_id'=>$doctorId));
        if(!$image){
            $this->redirect('doctorImages/manage/doctorId/'.$doctorId);
        }
        return $image;
    }

    /**
     * Check if passed Doctor ID is valid
     * @param int $id
     * @return Doctors
     */
    private function _checkDoctorFrontendAccess($id = 0)
    {
        $doctor = Doctors::model()->findByPk($id);
        if(!$doctor){
            $this->redirect('doctors/logout');
        }
        return $doctor;
    }

    /**
     * Check Image ID is valid for frontend
     * @param int $id
     * @param int $doctorId
     * @return DoctorImages
     */
    private function _checkImageFrontendAccess($id = 0, $doctorId = 0)
    {
        $image = DoctorImages::model()->findByPk($id, 'doctor_id = :doctor_id', array(':doctor_id'=>$doctorId));
        if(!$image){
            $this->redirect('doctorImages/myImages');
        }
        return $image;
    }

    /**
     * Check access to upload images count
     * @param int $doctorId
     * @param int $membershipImagesCount
     * @param string $redirect
     * @return bool
     */
    private function _checkUploadImagesCountAccess($doctorId = 0, $membershipImagesCount = 0, $redirect = '')
    {
    	if(empty($membershipImagesCount) && empty($redirect)){
    		return false;
		}

		$result = true;

    	$countImage = DoctorImages::model()->count('doctor_id = '.$doctorId);
        if($countImage >= $membershipImagesCount){
        	if(!empty($redirect)){
				$this->redirect($redirect);
			}else{
				$result = false;
			}
        }

        return $result;
    }

   /**
     * Check access to upload multi images
     * @param int $doctorId
     * @param int $countUploadedImages
     * @param int $membershipImagesCount
     * @param string $redirect
	 * @return bool
     */
   private function _checkUploadMultiImagesAccess($doctorId, $countUploadedImages  = 0, $membershipImagesCount = 0, $redirect = '')
   {
		if(empty($countUploadedImages) || empty($membershipImagesCount) || empty($redirect)){
			return false;
		}
		//
		$countImage = DoctorImages::model()->count('doctor_id = '.$doctorId);
		if($countImage + $countUploadedImages > $membershipImagesCount){
			$alert = A::t('appointments', 'You have reached the maximum number of images allowed by your current membership plan: {NUMBER} images.', array('{NUMBER}'=>$membershipImagesCount));
			$alertType = 'warning';
			A::app()->getSession()->setFlash('alert', $alert);
			A::app()->getSession()->setFlash('alertType', $alertType);
			$this->redirect($redirect);
		}
   }


}
