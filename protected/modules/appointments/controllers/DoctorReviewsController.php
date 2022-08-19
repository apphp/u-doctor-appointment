<?php
/**
 * Appointments controller
 * This controller intended to both Backend and Frontend modes
 *
 * PUBLIC:                      PRIVATE
 * -----------                  ------------------
 * manageAction
 * addAction
 * deleteAction
 * doctorReviewsAction
 * editAction

 * ajaxShowMoreDoctorReviewAction
 */

namespace Modules\Appointments\Controllers;

// Module
use \Modules\Appointments\Models\Appointments;
use \Modules\Appointments\Components\AppointmentsComponent;
use \Modules\Appointments\Components\DoctorsComponent;
use \Modules\Appointments\Models\DoctorReviews;
use \Modules\Appointments\Models\Patients;
use \Modules\Appointments\Models\Doctors;
use \Modules\Appointments\Models\Titles;
use \Modules\Appointments\Models\Degrees;

// Framework
use \A,
    \CAuth,
    \CArray,
	\CLocale,
    \CFile,
    \CWidget,
    \CTime,
    \CHash,
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



class DoctorReviewsController extends CController
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

        $this->_view->dateFormat = Bootstrap::init()->getSettings('date_format');
        $this->_view->timeFormat = Bootstrap::init()->getSettings('time_format');
        $this->_view->dateTimeFormat = Bootstrap::init()->getSettings('datetime_format');

        $this->_view->labelStatusReviews = array(
        	'0'=>'<span class="label-red label-square">'.A::t('appointments', 'Pending').'</span>',
			'1'=>'<span class="label-green label-square">'.A::t('appointments', 'Approved').'</span>',
			'2'=>'<span class="label-yellow label-square">'.A::t('appointments', 'Declined').'</span>',
		);

        $this->_view->editStatusReviews = array(
        	'0'=> A::t('appointments', 'Pending'),
			'1'=> A::t('appointments', 'Approved'),
			'2'=> A::t('appointments', 'Declined'),
		);

        $imagePathPrepend = '<img src="templates/default/images/small_star/smallstar-';
        $imagePathAppend = '.png" />';
        $this->_view->ratingStars = array(
            1=> $imagePathPrepend.'1'.$imagePathAppend,
            2=> $imagePathPrepend.'2'.$imagePathAppend,
            3=> $imagePathPrepend.'3'.$imagePathAppend,
            4=> $imagePathPrepend.'4'.$imagePathAppend,
            5=> $imagePathPrepend.'5'.$imagePathAppend,
        );


        if(CAuth::isLoggedInAsAdmin()){
            // set meta tags according to active patients
            Website::setMetaTags(array('title'=>A::t('appointments', 'Reviews Management')));

            $this->_view->tabs = AppointmentsComponent::prepareTab('reviews');
        }
    }

    /**
     * Controller default action handler
     * @return void
     */
    public function indexAction()
    {
        $this->redirect('doctorReviews/manage');
    }

    /**
     * Manage action handler
     * @param string $status
     * @return void
     */
    public function manageAction($status = 'approved')
    {
        // set backend mode
        Website::setBackend();
		Website::prepareBackendAction('manage', 'doctor', 'modules/index');

        $alert = A::app()->getSession()->getFlash('alert');
        $alertType = A::app()->getSession()->getFlash('alertType');

        if(!empty($alert)){
            $this->_view->actionMessage = CWidget::create('CMessage', array($alertType, $alert, array('button'=>true)));
        }

		if($status == 'approved'){
			$statusCode = 1;
		}elseif($status == 'declined'){
			$statusCode = 2;
		}else{
			$status = 'pending';
			$statusCode = 0;
		}

		$this->_view->status = $status;
		$this->_view->statusCode = $statusCode;

		$this->_view->subTabs = AppointmentsComponent::prepareSubTab('doctorreviews', $status);
        $this->_view->render('doctorReviews/manage');
    }

	/**
	 * Add doctorReviews action handler
	 * @return void
	 */
	public function addAction()
	{
        // Block access if this is not AJAX request
        $cRequest = A::app()->getRequest();
        if(!$cRequest->isAjaxRequest()){
            $this->redirect('doctors/dashboard');
        }

        $patientId = '';
        $patientName = '';
        $patientEmail = '';
        $arr = array();
        $errors = array();

        $token = $cRequest->getPost('APPHP_CSRF_TOKEN');
        $message = trim($cRequest->getPost('message'));
        $ratingPrice = $cRequest->getPost('ratingPrice');
        $ratingWaitTime = $cRequest->getPost('ratingWaitTime');
        $ratingBedsideManner = $cRequest->getPost('ratingBedsideManner');
        $captcha = $cRequest->getPost('captcha');
        $doctorId = $cRequest->getPost('doctorId');
        $appointmentId = $cRequest->getPost('appointmentId');

        //Checking for empty data from the form
        if(!$cRequest->isPostRequest()){
            $this->redirect(CConfig::get('defaultController').'/');
        }elseif(APPHP_MODE == 'demo'){
            $arr[] = '"status": "0"';
        }elseif(empty($ratingPrice)){
            $arr[] = '"status": "0"';
            $arr[] = '"error": "'.A::t('appointments', 'The field rating price cannot be empty!').'"';
        }elseif(empty($ratingWaitTime)){
            $arr[] = '"status": "0"';
            $arr[] = '"error": "'.A::t('appointments', 'The field rating wait time cannot be empty!').'"';
        }elseif(empty($ratingBedsideManner)){
            $arr[] = '"status": "0"';
            $arr[] = '"error": "'.A::t('appointments', 'The field rating bedside manner cannot be empty!').'"';
        }elseif(empty($message)){
            $arr[] = '"status": "0"';
            $arr[] = '"error": "'.A::t('appointments', 'The field message cannot be empty!').'"';
        }elseif($captcha === ''){
            $arr[] = '"status": "0"';
            $arr[] = '"error": "'.A::t('appointments', 'The field captcha cannot be empty!').'"';
        }elseif($captcha != A::app()->getSession()->get('captcha')){
            $arr[] = '"status": "0"';
            $arr[] = '"error": "'.A::t('appointments', 'Sorry, the code you have entered is invalid! Please try again.').'"';
        }else{

            if(CAuth::getLoggedId() && CAuth::getLoggedRole() == 'patient'){
                $patient = Patients::model()->find('account_id = '.CAuth::getLoggedId());
            }

            $isBanned = false;
            if(!empty($patient)){
                // Check if access is blocked to this IP address
                if(Website::checkBan('ip_address', $cRequest->getUserHostAddress(), $errors)){
                    $isBanned = true;
                }else{
                    // Check if access is blocked to this email
                    if(Website::checkBan('email_address', $patient->email, $errors)){
                        $isBanned = true;
                    }else{
                        // Check if access is blocked to this email domain
                        if(Website::checkBan('email_domain', $patient->email, $errors)){
                            $isBanned = true;
                        }
                    }
                }
            }

            if($isBanned){
                $arr[] = '"status": "0"';
                $arr[] = '"error": "'.A::t('appointments', 'An error occurred! Please try again later.').'"';
            }else{
                if(!empty($patient)){
                    $patientId = $patient->id;
                    $patientEmail = $patient->email;

                    $reviewModeration = ModulesSettings::model()->param('appointments', 'review_moderation');

                    $appointment = Appointments::model()->find(array('condition' => CConfig::get('db.prefix').Appointments::model()->getTableName().'.id = '.$appointmentId));

                    $result = new DoctorReviews();
                    $result->doctor_id = $doctorId;
                    $result->patient_id = $patientId;
                    $result->appointment_id = $appointmentId;
                    $result->patient_email = $patientEmail;
                    $result->patient_name = CAuth::getLoggedName();
                    $result->message = $message;
                    $result->rating_price = $ratingPrice;
                    $result->rating_wait_time = $ratingWaitTime;
                    $result->rating_bedside_manner = $ratingBedsideManner;
                    $result->created_at = date('Y-m-d h:i:s');
                    $result->status = $reviewModeration ? 0 : 1;

					$appointment->status_review = 1;

                    if($result->save() && $appointment->save()){
                        $arr[] = '"status": "1"';
                    }else{
                        $arr[] = '"status": "0"';
                        $arr[] = '"error": "'.A::t('appointments', 'An error occurred while adding the review! Please try again later.').'"';
                    }
                }else{
                    $arr[] = '"status": "0"';
                    $arr[] = '"error": "'.A::t('appointments', 'An error occurred! Please try again later.').'"';
                }
            }
        }

        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');   // Date in the past
        header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // Always modified
        header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
        header('Pragma: no-cache'); // HTTP/1.0
        header('Content-Type: application/json');

        echo '{';
        echo implode(',', $arr);
        echo '}';

        exit;
	}

	/**
	 * Edit doctorReviews action handler
	 * @param int $id
	 * @param string $status
	 * @return void
	 */
	public function editAction($id = 0, $status = 'pending')
	{
		// Set backend mode
		Website::setBackend();
		Website::prepareBackendAction('edit', 'doctor', 'doctors/manage');

		$this->_view->id = $id;
		$this->_view->status = $status;
		$this->_view->render('doctorReviews/edit');
	}

	/**
	 * Delete doctorReviews action handler
	 * @param int $id
	 * @param string $status
	 * @return void
	 */
	public function deleteAction($id = 0, $status = 'pending')
	{
		// Set backend mode
		Website::setBackend();
		Website::prepareBackendAction('delete', 'doctor', 'doctors/manage');

		$reviews = $this->_checkReviewsAccess($id);

		$alert     = '';
		$alertType = '';

		if($reviews->delete()){
			$alert = A::t('appointments', 'Review has been successfully deleted!');
			$alertType = 'success';
			if($reviews->getError()){
				$alert = A::t('appointments', 'Delete Warning Message');
				$alertType = 'warning';
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

		$this->redirect('doctorReviews/manage'.(!empty($status) ? '/status/'.$status : ''));
	}

	/**
	 * User Manage Reviews action handler
	 * @return void
	 */
	public function doctorReviewsAction()
	{
        // block access to this controller for doctors without membership plan or expired membership plan
        $checkAccessAccount = DoctorsComponent::checkAccessAccountUsingMembershipPlan(true,'enable_reviews');

		// block access to this controller for not-logged doctors
		CAuth::handleLogin('doctors/login', 'doctor');
		// block access to this controller if ModulesSettings - show_rating = false
		$showRating = ModulesSettings::model()->param('appointments', 'show_rating');
		if(!$showRating){
			$this->redirect('doctors/dashboard');
		}
		// set meta tags according to active language
		Website::setMetaTags(array('title'=>A::t('appointments', 'Reviews')));
		// set frontend settings
		Website::setFrontend();

		$doctor = Doctors::model()->find('account_id = :account_id', array(':account_id'=>A::app()->getSession()->get('loggedId')));
		if(!$doctor){
			$this->redirect('doctors/manage');
		}

		$this->_view->doctorId = $doctor->id;
		$this->_view->render('doctorReviews/doctorReviews');
	}

	/**
	 * Check if passed record ID is valid
	 * @param int $reviewsId
	 * @return object DoctorReviews
	 */
	private function _checkReviewsAccess($reviewsId = 0)
	{
		$reviews = DoctorReviews::model()->findByPk($reviewsId);
		if(empty($reviews)){
			$this->redirect('doctorReviews/manage');
		}
		return $reviews;
	}

	/*
     * Show More Doctor Reviews
     * @return json
     * */
	public function ajaxShowMoreDoctorReviewAction()
	{
		$arr = array();
		$nextPage = 0;

		// Block access if this is not AJAX request
		$cRequest = A::app()->getRequest();
		if(!$cRequest->isAjaxRequest()){
			$this->redirect('doctors/dashboard');
		}

		$doctorId = $cRequest->get('doctorId');
		$currentPage = $cRequest->get('currentPage');
		$pageSize  = ModulesSettings::model()->param('appointments', 'reviews_per_page');
		$totalReviews = DoctorReviews::model()->count('doctor_id = '.$doctorId.' && status = true');
		$dateFormat = Bootstrap::init()->getSettings('date_format');

		if($currentPage + 1 < ceil($totalReviews/$pageSize)) $nextPageExist = '1';
		else $nextPageExist = '0';

		if(!empty($pageSize) && $currentPage < ceil($totalReviews/$pageSize)){
			$doctorReviews = DoctorReviews::model()->findAll(array('condition' => 'doctor_id = '.$doctorId.' && status = true', 'limit' => ($currentPage * $pageSize).', '.$pageSize, 'orderBy' => 'created_at DESC'));
			if(is_array($doctorReviews) && !empty($doctorReviews)){
				foreach($doctorReviews as $key => $doctorReview){
					$arr[] = '{	"id": "'.$doctorReview['id'].'", 
								"message": "'.$doctorReview['message'].'",
								"ratingPrice": "'.$doctorReview['rating_price'].'", 
								"ratingWaitTime": "'.$doctorReview['rating_wait_time'].'", 
								"ratingBedsideManner": "'.$doctorReview['rating_bedside_manner'].'", 
								"patientName": "'.$doctorReview['patient_name'].'", 
								"createdAt": "'.CLocale::date($dateFormat, $doctorReview['created_at']).'",
								"nextPageExist": "'.$nextPageExist.'"
							  }';
				}
			}
		}

		if(empty($arr)){
			$arr = '';
		}

		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');   // Date in the past
		header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // Always modified
		header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
		header('Pragma: no-cache'); // HTTP/1.0
		header('Content-Type: application/json');

		echo '[';
		echo implode(',', $arr);
		echo ']';

		exit;
	}
}