<?php
/**
 * Appointments controller
 * This controller intended to both Backend and Frontend modes
 *
 * PUBLIC:                          PRIVATE
 * -----------                      ------------------
 * __construct                      _checkPatientOrderAccess
 * indexAction                      _checkDoctorOrderAccess
 * doctorsManageAction              _checkMembershipPlanAccess
 * doctorEditAction                 _checkDoctorAccess
 * checkoutAction                   _getMembershipPlans
 * patientsManageAction             _getPaymentTypes
 * doctorDeleteAction               _getPaymentMethods
 *!patientEditAction                _getStatusesForOrder
 *!patientDeleteAction              _getAllStatuses
 * doctorDownloadInvoiceAction      _getCountries
 *!patientDownloadInvoiceAction     _getStates
 * paymentFormAction				_prepareSubTabsForEditOrder
 * completeAction					_validationCreditCard
 * ! - Unrealized methods           _preparePdf
 */

namespace Modules\Appointments\Controllers;

// Module
use \Modules\Appointments\Components\AppointmentsComponent;
use \Modules\Appointments\Components\DoctorsComponent;
use \Modules\Appointments\Models\Memberships;
use \Modules\Appointments\Models\Orders;
use \Modules\Appointments\Models\Doctors;

// Global
use \A,
    \CAuth,
    \CArray,
    \CDatabase,
    \CHash,
    \CLocale,
    \CConfig,
    \Accounts,
    \CLoader,
    \CNumber,
    \CTime,
    \CWidget,
    \CController;

// Application
use \Website,
    \Bootstrap,
    \CPdf,
    \PaymentProvider,
    \PaymentProviders,
    \Countries,
    \Currencies,
    \LocalTime,
    \States,
    \Modules;


class OrdersController extends \CController
{
    private $_isAdmin;
    /**
     * Class default constructor
     */
    public function __construct()
    {
        $this->_isAdmin = false;

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
            Website::setMetaTags(array('title'=>A::te('appointments', 'Orders Management')));
            // set backend mode
            Website::setBackend();

            $this->_isAdmin = true;
            $this->_view->actionMessage = '';
            $this->_view->errorField = '';

            $this->_view->tabs = AppointmentsComponent::prepareTab('orders');
        }
        $this->_settings             = Bootstrap::init()->getSettings();
        $this->_view->dateTimeFormat = $this->_settings->datetime_format;
        $this->_view->dateFormat     = $this->_settings->date_format;
        $this->_view->numberFormat   = $this->_settings->number_format;
        $this->_view->durations      = DoctorsComponent::getPreparedDurations();
        $this->_view->providers      = PaymentProviders::model()->findAll('is_active = 1');

        $this->_view->allStatus = $this->_getAllStatuses();
    }

    /**
     * Controller default action handler
     */
    public function indexAction()
    {
        if($this->_isAdmin){
            $this->redirect('orders/doctorsManage');
        }else{
            $this->redirect('orders/myOrders');
        }
    }

    /**
     * Manage action handler
     */
    public function doctorsManageAction()
    {
        Website::prepareBackendAction('manage', 'order', 'modules/index');

        $actionMessage = '';
        $filterDoctors = array();
        $doctorIds = array();
        $alert     = A::app()->getSession()->getFlash('alert');
        $alertType = A::app()->getSession()->getFlash('alertType');

        if(!empty($alertType)){
            $actionMessage = CWidget::create('CMessage', array($alertType, $alert, array('button'=>true)));
        }

        $appendCode  = '';
        $prependCode = '';
        $symbol      = A::app()->getCurrency('symbol');
        $symbolPlace = A::app()->getCurrency('symbol_place');

        if($symbolPlace == 'before'){
            $prependCode = $symbol;
        }else{
            $appendCode = $symbol;
        }

        $orders = Orders::model()->findAll();
        if (!empty($orders) && is_array($orders)) {
            foreach ($orders as $order) {
                if (!in_array($order['doctor_id'], $doctorIds)) {
                    $doctorIds[] = $order['doctor_id'];
                }
            }
        }

        if (!empty($doctorIds) && is_array($doctorIds)) {
            $doctorsTable = CConfig::get('db.prefix') . Doctors::model()->getTableName();
            $doctors = Doctors::model()->findAll($doctorsTable.'.id IN('.implode(',', $doctorIds).')');
            if (!empty($doctors) && is_array($doctors)) {
                foreach ($doctors as $doctor) {
                    $filterDoctors[$doctor['id']] = $doctor['full_name'];
                }
            }
        }

        $this->_view->pricePrependCode = $prependCode;
        $this->_view->priceAppendCode  = $appendCode;
        $this->_view->membershipPlans = $this->_getMembershipPlans();
        $this->_view->allPaymentTypes = $this->_getPaymentTypes();
        $this->_view->actionMessage = $actionMessage;
        $this->_view->filterDoctors = $filterDoctors;
        $this->_view->subTabs = AppointmentsComponent::prepareSubTab('orders', 'doctors');
        $this->_view->render('orders/doctors/manage');
    }

    /**
     * Manage action handler
     */
    public function patientsManageAction()
    {
        Website::prepareBackendAction('manage', 'order', 'modules/index');

        $actionMessage = '';
        $alert     = A::app()->getSession()->getFlash('alert');
        $alertType = A::app()->getSession()->getFlash('alertType');

        if(!empty($alertType)){
            $actionMessage = CWidget::create('CMessage', array($alertType, $alert, array('button'=>true)));
        }

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
        $this->_view->actionMessage = $actionMessage;
        $this->_view->subTabs = AppointmentsComponent::prepareSubTab('orders', 'patients');
        $this->_view->render('orders/patients/manage');
    }

    /**
     * Edit doctor order action handler
     * @param int $id
     * @param string $tab
     */
    public function doctorEditAction($id = 0, $tab = 'general')
    {
        Website::prepareBackendAction('edit', 'order', 'orders/doctorsManage');
        $order = $this->_checkDoctorOrderAccess($id);

        $allPaymentTypes    = $this->_getPaymentTypes();
        $allPaymentMethods  = $this->_getPaymentMethods();
        $allStatus          = $this->_getStatusesForOrder($order->status);

        $doctor  = $this->_checkDoctorAccess($order->doctor_id);
        $doctorName         = $doctor->getFullName();
        $orderStatus        = isset($allStatus[$order->status]) ? $allStatus[$order->status] : A::t('appointments', 'Unknown');
        $orderPaymentMethod = isset($allPaymentMethods[$order->payment_method]) ? $allPaymentMethods[$order->payment_method] : A::t('appointments', 'Unknown');

        $beforePrice        = '';
        $afterPrice         = '';
        $planName           = A::t('appointments', 'Unknown');

        $plan = Memberships::model()->findByPk($order->membership_plan_id);
        if(!empty($plan)){
            $planName = $plan->name;
        }

        $arrCountryNames = $this->_getCountries();
        $arrStateNames   = $this->_getStates();

        switch($tab){
            case 'invoice':
                $this->_view->doctor   = Doctors::model()->findByPk($order->doctor_id);
                $this->_view->planName = $planName;

                $tab = 'invoice';
                break;

            case 'general':
            default:
                $tab = 'general';
                $this->_view->id                 = $order->id;
                $this->_view->planName           = $planName;
                $this->_view->doctorName         = $doctorName;
                $this->_view->orderPaymentMethod = $orderPaymentMethod;
                $this->_view->orderStatus        = $orderStatus;
                $this->_view->allStatus          = $allStatus;
                $this->_view->allPaymentTypes    = $allPaymentTypes;
                $this->_view->allPaymentMethods  = $allPaymentMethods;
                break;
        }

        $currency = Currencies::model()->find('code = :code', array(':code'=>$order->currency));
        if(!empty($currency)){
            if($currency->symbol_place == 'before'){
                $beforePrice = $currency->symbol;
                $afterPrice = '';
            }else{
                $beforePrice = '';
                $afterPrice = $currency->symbol;
            }
        }

        $this->_view->id                = $id;
        $this->_view->order             = $order;
        $this->_view->arrCountryNames   = $arrCountryNames;
        $this->_view->arrStateNames     = $arrStateNames;
        $this->_view->beforePrice       = $beforePrice;
        $this->_view->afterPrice        = $afterPrice;
        $this->_view->allStatus         = $allStatus;
        $this->_view->allPaymentTypes   = $allPaymentTypes;
        $this->_view->allPaymentMethods = $allPaymentMethods;
        $this->_view->subTabName        = $tab;
        $this->_view->subTabs           = $this->_prepareSubTabsForEditOrder('doctors', $tab, $id);

        $this->_view->render('orders/doctors/edit');
    }

    /**
     * Doctor delete action handler
     * @param int $id
     * @return void
     */
    public function doctorDeleteAction($id = 0)
    {
        $order = $this->_checkDoctorOrderAccess($id);
        $alert = '';
        $alertType = '';

        $orderId = $order->order_number;
        if($order->delete()){
            $alert = A::t('appointments', 'Doctors deleted successfully');
            $alertType = 'success';
        }else{
            if(APPHP_MODE == 'demo'){
                $alert = CDatabase::init()->getErrorMessage();
                $alertType = 'warning';
            }else{
                $alert = A::t('appointments', 'Doctors deleting error');
                $alertType = 'error';
            }
        }

        if(!empty($alert)){
            A::app()->getSession()->setFlash('alert', $alert);
            A::app()->getSession()->setFlash('alertType', $alertType);
        }

        $this->redirect('orders/doctorsManage');
    }

    /**
     * Doctor delete action handler
     * @param int $id
     * @return void
     */
    public function doctorDownloadInvoiceAction($id = 0)
    {
        Website::prepareBackendAction('delete', 'order', 'orders/manage');

        $order = $this->_checkDoctorOrderAccess($id);

        $content = $this->_preparePdf($order);

        if(!empty($content)){
            CPdf::config(array(
                'page_orientation'  => 'P',             // [P=portrait, L=landscape]
                'unit'              => 'mm',            // [pt=point, mm=millimeter, cm=centimeter, in=inch]
                'page_format'       => 'A4',
                'unicode'           => true,
                'encoding'          => 'UTF-8',
                'creator'           => 'appointments',
                'author'            => 'ApPHP',
                'title'             => 'Orders #'.$order->order_number,
                'subject'           => 'Orders #'.$order->order_number,
                'keywords'          => '',
                //'header_logo'     => '../../../templates/reports/images/logo.png',
                'header_logo_width' => '45',
                'header_title'      => 'Orders #'.$order->order_number,
                'header_enable'     => false,
                'text_shadow'       => false,
                'margin_top'        => '1',
                'margin_left'       => '5'
            ));

            CPdf::createDocument($content, 'Orders #'.$order->order_number, 'D'); // 'I' - inline , 'D' - download
        }
    }

    /**
     * checkout doctor order action handler
     * @param int $membershipId
     */
    public function ordersAction()
    {
        // block access to this controller for not-logged doctors
        CAuth::handleLogin('doctors/login', 'doctor');
        // set meta tags according to active language
        Website::setMetaTags(array('title'=>A::t('appointments', 'Orders')));
        // set frontend settings
        Website::setFrontend();

        $doctorId = CAuth::getLoggedRoleId();
        $doctor = $this->_checkDoctorAccess($doctorId, true);

        $alert = A::app()->getSession()->getFlash('alert');
        $alertType = A::app()->getSession()->getFlash('alertType');

        if(!empty($alert)){
            $this->_view->actionMessage = CWidget::create(
                'CMessage', array($alertType, $alert, array('button'=>true))
            );
        }
        $appendCode  = '';
        $prependCode = '';
        $symbol      = A::app()->getCurrency('symbol');
        $symbolPlace = A::app()->getCurrency('symbol_place');

        if($symbolPlace == 'before'){
            $prependCode = $symbol;
        }else{
            $appendCode = $symbol;
        }

        $allPayments     = PaymentProviders::model()->findAll('is_active = 1');
        if(!empty($allPayments) && is_array($allPayments)){
            foreach($allPayments as $payment){
                $allPaymentTypes[$payment['id']] = $payment['name'];
            }
        }

        if(!empty($alert)){
            $this->_view->actionMessage = CWidget::create(
                'CMessage', array($alertType, $alert, array('button'=>true))
            );
        }
		$status = $this->_getAllStatuses();
		unset($status[0]);



		$this->_view->pricePrependCode = $prependCode;
        $this->_view->priceAppendCode  = $appendCode;
        $this->_view->membershipPlans  = $this->_getMembershipPlans();
        $this->_view->status 		   = $status;
        $this->_view->doctorId 		   = $doctor->id;
        $this->_view->render('orders/doctors/orders');

    }

    /**
     * checkout doctor order action handler
     * @param int $membershipId
     */
    public function checkoutAction($membershipId = 0)
    {
        // block access to this controller for not-logged doctors
        CAuth::handleLogin('doctors/login', 'doctor');
        // set meta tags according to active language
        Website::setMetaTags(array('title'=>A::t('appointments', 'Membership Plan Checkout')));
        // set frontend settings
        Website::setFrontend();

        $actionMessage = '';

        $alert     = A::app()->getSession()->getFlash('alert');
        $alertType = A::app()->getSession()->getFlash('alertType');

        if(!empty($alert)){
            $actionMessage = CWidget::create('CMessage', array($alertType, $alert, array()));
        }


        $doctorId = CAuth::getLoggedRoleId();
        $doctor = $this->_checkDoctorAccess($doctorId, true);

        $this->_view->membershipPlan = $this->_checkMembershipPlanAccess($membershipId);
        $this->_view->doctorFullName = $doctor->getFullName();
        $this->_view->actionMessage  = $actionMessage;
        $this->_view->render('orders/doctors/checkout');
    }

    /**
     * View Form for pay
     * @param int $membershipId
     * @return void
     */
    public function paymentFormAction($membershipId = 0)
    {
        // block access to this controller for not-logged doctors
        CAuth::handleLogin('doctors/login', 'doctor');
        // set meta tags according to active language
        Website::setMetaTags(array('title'=>A::t('appointments', 'Membership Plan Checkout')));
        // set frontend settings
        Website::setFrontend();

        $actionMessage = '';
		$saveOrder = true;

        $doctorId = CAuth::getLoggedRoleId();
		$doctor = $this->_checkDoctorAccess($doctorId, true);
		$membershipPlan = $this->_checkMembershipPlanAccess($membershipId);

        //If the membership plan is free, then we add the doctor account
        if($membershipPlan->price == 0){
			$updateMembershipPlan = DoctorsComponent::updateMembershipPlan($doctorId, $membershipId);
            if($updateMembershipPlan){
            	//Update membership plan id and membership expires in session
				DoctorsComponent::checkMembershipPlan($doctor->membership_plan_id, $doctor->membership_expires);
                $alert = A::t('appointments', 'Thank You! Your order has been successfully completed.');
                $alertType = 'success';
            }else{
                $alert = A::t('appointments', 'An error when adding a membership plan, please try again later.');
                $alertType = 'error';
            }

            if(!empty($alert)){
                A::app()->getSession()->setFlash('alert', $alert);
                A::app()->getSession()->setFlash('alertType', $alertType);
                $this->redirect('doctors/dashboard/');
            }
        }

        $act = A::app()->getRequest()->getPost('act', 'string');
        $type = A::app()->getRequest()->getPost('type', 'string');
        if(empty($act)){
            $alert = '';
            $alertType = '';
        }elseif(empty($type)){
            $alert = A::t('core', 'The field {title} cannot be empty! Please re-enter.', array('{title}'=>A::t('appointments', 'Payment Method')));
            $alertType = 'validation';
        }else{
            $providers = PaymentProviders::model()->findAll('is_active = 1');
            $providers = CArray::flipByField($providers, 'code');
            if(!in_array($type, array_keys($providers))){
                $alert = A::t('appointments', 'Input incorrect parameters');
                $alertType = 'error';
            }
        }

        if(!empty($alertType) || $act != 'send'){
            A::app()->getSession()->setFlash('alert', $alert);
            A::app()->getSession()->setFlash('alertType', $alertType);
            $this->redirect('orders/checkout/'.$membershipId);
        }

        CLoader::library('ipgw/PaymentProvider.php');
        $provider = PaymentProvider::init($type);
        $providerSettings = PaymentProviders::model()->find("code = :code", array(':code'=>$type));

        $back = 'orders/checkout/'.$membershipPlan->id;
        $currencyCode = CAuth::getLoggedParam('currency_code');

		$lastOrderNumber =  A::app()->getSession()->get('lastOrderNumber');
		//Create or Update Order
        if(empty($lastOrderNumber)){
            $order = new Orders();

            $order->order_number   		 = CHash::getRandomString(10, array('case' => 'upper'));
            $order->order_description 	 = '';
            $order->order_price 		 = $membershipPlan->price;
            $order->total_price 		 = $membershipPlan->price;
            $order->currency 			 = $currencyCode;
            $order->membership_plan_id 	 = $membershipPlan->id;
            $order->doctor_id 			 = $doctor->id;
            $order->transaction_number 	 = '';
            $order->created_date 		 = date('Y-m-d H:i:s');
            $order->payment_id 			 = $providerSettings->id;
            $order->payment_method 		 = 0;
            $order->status 				 = 0;
			$order->status_changed 		 = date('Y-m-d H:i:s');
            $order->payer 				 = 'doctor';

            A::app()->getSession()->set('lastOrderNumber', $order->order_number);
        }else{
            $order = Orders::model()->find('order_number = :order_number', array(':order_number' => $lastOrderNumber));
            //If not exists order in database redirect orders/checkout/
            if(empty($order)){
                A::app()->getSession()->remove('lastOrderNumber');
                $alert = A::t('appointments', 'Order cannot be found in the database');
                $alertType = 'error';
                A::app()->getSession()->setFlash('alert', $alert);
                A::app()->getSession()->setFlash('alertType', $alertType);
                $this->redirect('orders/checkout/'.$membershipPlan->id);
            }elseif($order->membership_plan_id != $membershipPlan->id || $order->payment_id != $providerSettings->id){
				$order->order_description 	 = '';
				$order->order_price 		 = $membershipPlan->price;
				$order->total_price 		 = $membershipPlan->price;
				$order->currency 			 = $currencyCode;
				$order->membership_plan_id 	 = $membershipPlan->id;
				$order->doctor_id 			 = $doctor->id;
				$order->transaction_number 	 = '';
				$order->created_date 		 = date('Y-m-d H:i:s');
				$order->payment_id 			 = $providerSettings->id;
				$order->payment_method 		 = 0;
				$order->status 				 = 0;
				$order->status_changed 		 = date('Y-m-d H:i:s');
				$order->payer 				 = 'doctor';
            }
        }

        $params = array(
            'item_name'     => $membershipPlan->name,
            'item_number'   => $membershipPlan->id,
            'amount'        => $order->total_price,
            'custom'        => $order->order_number,      // order ID
            // The rm variable takes effect only if the return variable is set.
            'currency_code' => $currencyCode,   // The currency of the payment. The default is USD.
            'no_shipping'   => '',      // Do not prompt buyers for a shipping address.
            'address1'      => $doctor->address,
            'address2'      => $doctor->address_2,
            'city'          => $doctor->city,
            'zip'           => $doctor->zip_code,
            'state'         => $doctor->state,
            'country'       => $doctor->country_code,
            'first_name'    => $doctor->doctor_first_name,
            'last_name'     => $doctor->doctor_last_name,
            'email'         => $doctor->email,
            'phone'         => $doctor->phone,
            'mode'          => $providerSettings->mode,
            'back'          => $back,       // Back to Shopping Cart - defined by developer
            'notify'        => A::app()->getRequest()->getBaseUrl().'paymentProviders/handlePayment/provider/'.$type.'/handler/orders/module/appointments', // IPN processing link
            'cancel'        => A::app()->getRequest()->getBaseUrl().'orders/checkout/'.$membershipPlan->id,                       // Cancel order link
            'cancel_return' => A::app()->getRequest()->getBaseUrl().'orders/checkout/'.$membershipPlan->id,                       // Cancel & return to site link
        );

        if($type == 'paypal'){
//            if(!empty($order)){
//                if(!$order->save()){
//                    if(APPHP_MODE == 'demo'){
//                        $alert = CDatabase::init()->getErrorMessage();
//                        $alertType = 'warning';
//                    }else{
//                        $alert = A::t('appointments', 'Could not add a new record');
//                        $alert .= (APPHP_MODE == 'debug') ? '<br>'.CDatabase::init()->getErrorMessage() : '';
//                        $alertType = 'error';
//                    }
//
//                    A::app()->getSession()->setFlash('alert', $alert);
//                    A::app()->getSession()->setFlash('alertType', $alertType);
//                    $this->redirect('orders/checkout/'.$membershipPlan->id);
//                }
//            }
            $params = array_merge($params, array(
                'merchant_id' => $providerSettings->merchant_id,
            ));
        }else if($type == 'online_credit_card'){

            if(A::app()->getRequest()->isPostExists('cc_type')){
                $saveOrder = false;
                $arrCCType = array('Visa', 'MasterCard', 'American Express', 'Discover');
                $arrCCExpiresMonth = array();
                for($i = 1; $i <= 12; $i++){
                    $arrCCExpiresMonth[] = sprintf('%02s', $i);
                }
                $arrCCExpiresYear = range(LocalTime::currentDate('Y'), LocalTime::currentDate('Y') + 10);
                $fields = array(
                    'fields' => array(
                        'cc_type'          => array('title'=>A::t('appointments', 'Credit Card Type'), 'validation'=>array('required'=>true, 'type'=>'text', 'source'=>'20')),
                        'cc_holder_name'   => array('title'=>A::t('appointments', 'Card Holder\'s Name'), 'validation'=>array('required'=>true, 'type'=>'text', 'maxLength'=>'50')),
                        'cc_number'        => array('title'=>A::t('appointments', 'Credit Card Number'), 'validation'=>array('required'=>true, 'type'=>'text', 'maxLength'=>'50')),
                        'cc_expires_month' => array('title'=>A::t('appointments', 'Expires Month'), 'validation'=>array('required'=>true, 'type'=>'set', 'source'=>$arrCCExpiresMonth)),
                        'cc_expires_year'  => array('title'=>A::t('appointments', 'Expires Year'), 'validation'=>array('required'=>true, 'type'=>'set', 'source'=>$arrCCExpiresYear)),
                        'cc_cvv_code'      => array('title'=>A::t('appointments', 'CVV Code'), 'validation'=>array('required'=>true, 'type'=>'number', 'maxLength'=>'4')),
                    ),
                    'messagesSource' => 'core',
                    'showAllErrors'  => false,
                );
                $result = CWidget::create('CFormValidation', $fields);
                if($result['error']){
                    $alert     = $result['errorMessage'];
                    $alertType = 'validation';
                    $params['error_field'] = $result['errorField'];

                    $actionMessage = CWidget::create('CMessage', array($alertType, $alert, array()));
                }else{
                    $params['cc_type']          = A::app()->getRequest()->getPost('cc_type');
                    $params['cc_holder_name']   = A::app()->getRequest()->getPost('cc_holder_name');
                    $params['cc_number']        = A::app()->getRequest()->getPost('cc_number');
                    $params['cc_expires_month'] = A::app()->getRequest()->getPost('cc_expires_month');
                    $params['cc_expires_year']  = A::app()->getRequest()->getPost('cc_expires_year');
                    $params['cc_cvv_code']      = A::app()->getRequest()->getPost('cc_cvv_code');

                    $result = $this->_validationCreditCard($params);
                    if($result['error']){
                        $alert = $result['errorMessage'];
                        $alertType = 'validation';
                        $params['error_field'] = $result['errorField'];

                        $actionMessage = CWidget::create('CMessage', array($alertType, $alert, array()));
                    }else if(!empty($order)){
                        $order->cc_type          = $params['cc_type'];
                        $order->cc_holder_name   = $params['cc_holder_name'];
                        $order->cc_number        = $params['cc_number'];
                        $order->cc_expires_month = $params['cc_expires_month'];
                        $order->cc_expires_year  = $params['cc_expires_year'];
                        $order->cc_cvv_code      = $params['cc_cvv_code'];
						$order->payment_method 	 = 1;

                        if(!$order->save()){
                            if(APPHP_MODE == 'demo'){
                                $alert = CDatabase::init()->getErrorMessage();
                                $alertType = 'warning';
                            }else{
                                $alert = A::t('appointments', 'Could not add a new record');
                                $alert .= (APPHP_MODE == 'debug') ? '<br>'.CDatabase::init()->getErrorMessage() : '';
                                $alertType = 'error';
                            }

                            A::app()->getSession()->setFlash('alert', $alert);
                            A::app()->getSession()->setFlash('alertType', $alertType);
                            $this->redirect('orders/checkout/'.$membershipPlan->id);
                        }

                        $this->redirect($params['notify'], true);
                    }
                }
            }
            $params['notify'] = A::app()->getRequest()->getBaseUrl().'orders/paymentForm/'.$membershipPlan->id;
        }

        $form = $provider->drawPaymentForm($params);

        if($saveOrder){
            if(!$order->save()){
                if(APPHP_MODE == 'demo'){
                    $alert = CDatabase::init()->getErrorMessage();
                    $alertType = 'warning';
                }else{
                    $alert = A::t('appointments', 'Could not add a new record');
                    $alert .= (APPHP_MODE == 'debug') ? '<br>'.CDatabase::init()->getErrorMessage() : '';
                    $alertType = 'error';
                }

                A::app()->getSession()->setFlash('alert', $alert);
                A::app()->getSession()->setFlash('alertType', $alertType);
                $this->redirect('orders/checkout/'.$membershipPlan->id);
            }
        }

        $this->_view->order = $order;
        $this->_view->actionMessage = $actionMessage;
        $this->_view->form = $form;
        $this->_view->doctor = $doctor;
        $this->_view->membershipPlan = $membershipPlan;
        $this->_view->providerSettings = $providerSettings;
        $this->_view->render('orders/doctors/paymentForm');
    }

    /**
     * Edit patient order action handler
     * @param int $provider
     */
    public function completeAction($provider = '')
    {
		// block access to this controller for not-logged doctors
		CAuth::handleLogin('doctors/login', 'doctor');
		// set meta tags according to active language
		Website::setMetaTags(array('title'=>A::t('appointments', 'Orders Complete')));
		// set frontend settings
		Website::setFrontend();

		$doctorId = CAuth::getLoggedRoleId();
		$doctor = $this->_checkDoctorAccess($doctorId, true);
        $emailAlert      = '';
        $emailAlertType  = '';
        $alert           = A::app()->getSession()->getFlash('alert');
        $alertType       = A::app()->getSession()->getFlash('alertType');
		$lastOrderNumber =  A::app()->getSession()->get('lastOrderNumber');
		$allPaymentTypes = array();
		$allPayments     = PaymentProviders::model()->findAll('is_active = 1');

		if(!empty($allPayments) && is_array($allPayments)){
			foreach($allPayments as $payment){
				$allPaymentTypes[$payment['code']] = $payment['name'];
			}
		}

		if(!empty($lastOrderNumber)){
			$order = Orders::model()->find('order_number = :order_number', array(':order_number'=>$lastOrderNumber));
			if(!empty($order)){
				if($order->status == 1 || $order->status == 2){
					$alert = A::t('appointments', 'Thank you! The order has been placed in our system and will be processed shortly. Your order number is: {NUMBER}', array('{NUMBER}'=>$order->order_number));
					$alertType = 'success';
					if($order->email_sent == 1){
						$emailAlert = A::t('appointments', 'Email has been successfully sent!');
						$emailAlertType = 'success';
					}else{
						$emailAlert = A::t('appointments', 'Email not sent!');
						$emailAlertType = 'error';
					}
				}else{
					if($this->_debug){
						$alert = A::t('appointments', 'Order number {number} is not found', array('{number}'=>$lastOrderNumber));
					}else{
						$alert = A::t('appointments', 'Cannot complete your order! Please try again later.');
					}
					$alertType = 'error';
				}
			}
            A::app()->getSession()->remove('lastOrderNumber');
		}

		if(empty($alert)){
			$this->redirect('doctors/dashboard');
		}

		$this->_view->actionMessage = CWidget::create('CMessage', array($alertType, $alert));
		if(!empty($emailAlert)){
			$this->_view->emailMessage = CWidget::create('CMessage', array($emailAlertType, $emailAlert));
		}else{
			$this->_view->emailMessage = '';
		}
		$this->_view->namePayment   = isset($allPaymentTypes[$provider]) ? $allPaymentTypes[$provider] : A::t('appointments', 'Orders');
		$this->_view->render('orders/doctors/complete');
    }

    /**
     * Edit patient order action handler
     * @param int $id
     */
    public function patientEditAction($id = 0)
    {
        Website::prepareBackendAction('edit', 'order', 'orders/patientsManage');
        $order = $this->_checkPatientOrderAccess($id);

        $this->_view->id = $id;
        $this->_view->render('orders/edit');
    }

    /**
     * Delete action handler
     * @param int $id
     */
    public function deleteAction($id = 0)
    {
        Website::prepareBackendAction('delete', 'order', 'orders/manage');
        $model = $this->_checkOrderAccess($id);

        $alert = '';
        $alertType = '';

        if($model->delete()){
            if($model->getError()){
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
        
        $this->redirect('orders/manage');
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
     * @param int $orderId
     * @return object Orders
     */
    private function _checkDoctorOrderAccess($orderId = 0)
    {
        $order = Orders::model()->findByPk($orderId);
        if(!$order){
            $this->redirect('orders/doctorsManage');
        }
        return $order;
    }

    /**
     * Check if passed record ID is valid
     * @param int $orderId
     * @return object Orders
     */
    private function _checkPatientOrderAccess($orderId = 0)
    {
        $order = Orders::model()->findByPk($orderId);
        if(!$order){
            $this->redirect('orders/patientsManage');
        }
        return $order;
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
            $doctor = Doctors::model('with_appointments_counter')->findByPk($id, $tableName.'.is_active = 1 AND '.$tableName.'.is_removed = 0');
        }else{
            $doctor = Doctors::model()->findByPk($id);
        }

        if(!$doctor){
            $this->redirect('doctors/manage');
        }

        return $doctor;
    }

    /**
     * Check if passed record ID is valid
     * @param int $membershipId
     * @return object Memberships
     */
    private function _checkMembershipPlanAccess($membershipId = 0)
    {
        $membership = Memberships::model()->findByPk($membershipId, 'is_active = 1');
        if(!$membership){
            $this->redirect('memberships/membershipPlans');
        }
        return $membership;
    }

    /**
     * Get active membership plans
     * @return array
     */
    private function _getMembershipPlans()
    {
        $membershipPlans = array();
        $plans = Memberships::model()->findAll('is_active = 1');

        if(!empty($plans) && is_array($plans)){
            foreach($plans as $onePlan){
                $membershipPlans[$onePlan['id']] = $onePlan['name'];
            }
        }

        return $membershipPlans;
    }

	/*
	 * @param array $ccParams
	 * @return array
	 * */
	private function _validationCreditCard($ccParams = array())
	{
		$arrError = array(
			'error' => 1,
			'errorMessage' => '',
			'errorField' => ''
		);
		$cards = array(
			array('name' => 'Visa', 'length' => '13,16', 'prefixes' => '4', 'checkdigit' => true, 'test' => '4111111111111111'),
			array('name' => 'MasterCard', 'length' => '16', 'prefixes' => '51,52,53,54,55', 'checkdigit' => true, 'test' => '5555555555554444'),
			array('name' => 'American Express', 'length' => '15', 'prefixes' => '34,37', 'checkdigit' => true, 'test' => '371449635398431'),
			array('name' => 'Discover', 'length' => '16', 'prefixes' => '6011,622,64,65', 'checkdigit' => true, 'test' => '6011111111111117')
		);

		// check card holder's name
		if(trim($ccParams['cc_holder_name']) == ''){
			$arrError['errorMessage'] = A::t('appointments', 'Card Holder Name Empty');
			$arrError['errorField'] = 'cc_holder_name';

			return $arrError;
		}

		// define card type
		$ccType = -1;
		for($i = 0; $i < count($cards); $i++){
			if(strtolower($ccParams['cc_type']) == strtolower($cards[$i]['name'])){
				$ccType = $i;
				break;
			}
		}
		if($ccType == -1){
			$arrError['errorMessage'] = A::t('appointments', 'Unknown Card Type');
			$arrError['errorField'] = 'cc_type';
			return $arrError;
		}
		if(strlen($ccParams['cc_number']) == 0){
			$arrError['errorMessage'] = A::t('appointments', 'Card Number is empty');
			$arrError['errorField'] = 'cc_number';
			return $arrError;
		}
		$ccNumber = str_replace(array(' ', '-'), '', $ccParams['cc_number']);

		// Check that the number is numeric and of the right sort of length.
		if(!preg_match('/^[0-9]{13,19}$/i',$ccNumber)){
			$arrError['errorMessage'] = A::t('appointments', 'Card Invalid Format');
			$arrError['errorField'] = 'cc_number';

			return $arrError;
		}

		// Check that the number is not a test number
		if(($ccParams['mode'] == 'real') && ($cards[$ccType]['test'] == $ccNumber)){
			$arrError['errorMessage'] = A::t('appointments', 'Card Invalid Number');
			$arrError['errorField'] = 'cc_number';

			return $arrError;
		}

		// check the modulus 10 check digit - if required
		if($cards[$ccType]['checkdigit']){
			$checksum = 0;     // checksum total
			$j = 1;

			// handle each digit starting from the right
			for($i = strlen($ccNumber) - 1; $i >= 0; $i--){
				$calc = $ccNumber[$i] * $j;
				// if the result is in two digits add 1 to the checksum total
				if($calc > 9){
					$checksum = $checksum + 1;
					$calc = $calc - 10;
				}
				$checksum = $checksum + $calc;
				// switch j
				$j = ($j == 1 ? 2 : 1);
			}

			// if checksum is divisible by 10, it is a valid modulus 10 oe error occured
			if($checksum % 10 != 0){
				$arrError['errorMessage'] = A::t('appointments', 'Card Invalid Number');
				$arrError['errorField'] = 'cc_number';

				return $arrError;
			}
		}

		// prepare array with the valid prefixes for this card
		$prefix = explode(',', $cards[$ccType]['prefixes']);

		// check if any of them match what we have in the card number
		$is_prefix_valid = false;
		for ($i = 0; $i < count($prefix); $i++) {
			$exp = '^'.$prefix[$i];
			if(preg_match('/'.$exp.'/i',$ccNumber)) {
				$isPrefixValid = true;
				break;
			}
		}

		// if there is no valid prefix the length is wrong
		if(!$isPrefixValid){
			$arrError['errorMessage'] = A::t('appointments', 'Card Wrong Length');
			$arrError['errorField'] = 'cc_number';

			return $arrError;
		}

		// check if the length is valid
		$is_length_valid = false;
		$lengths = explode(',',$cards[$ccType]['length']);
		for($j = 0; $j < count($lengths); $j++){
			if(strlen($ccNumber) == $lengths[$j]){
				$isLengthValid = true;
				break;
			}
		}

		if(!$isLengthValid){
			$arrError['errorMessage'] = A::t('appointments', 'Card Invalid Number');
			$arrError['errorField'] = 'cc_number';

			return $arrError;
		}

		// check expire date
		if($ccParams['cc_expires_year'].$ccParams['cc_expires_month'] < LocalTime::currentDate('Ym')){
			$arrError['errorMessage'] = A::t('appointments', 'Card Wrong Expires Date');
			$arrError['errorField'] = 'cc_expires_month';

			return $arrError;
		}

		// check cvv number
		if($ccParams['cc_cvv_code'] == ''){
			$arrError['errorMessage'] = A::t('appointments', 'Card No CVV Number');
			$arrError['errorField'] = 'cc_cvv_code';

			return $arrError;
		}

		// The credit card is in the required format.
		return array(
			'error' => 0,
			'errorMessage' => '',
			'errorField' => ''
		);
	}

    /**
     * Get payment types
     * @return array
     */
    private function _getPaymentTypes()
    {
        $allPaymentTypes   = array();
        $allPayments       = PaymentProviders::model()->findAll('is_active = 1');
        if(!empty($allPayments) && is_array($allPayments)){
            foreach($allPayments as $payment){
                $allPaymentTypes[$payment['id']] = $payment['name'];
            }
        }

        return $allPaymentTypes;
    }

    /**
     * Get payment methods
     * @return array
     */
    private function _getPaymentMethods()
    {
        return array('0'=>'Payment Company Account', '1'=>'Credit Card', '2'=>'E-Check');
    }

    /**
     * Get statuses by status number for order
     */
    private function _getStatusesForOrder($statusNumber)
    {
        $outStatuses = array();
        $allStatuses = $this->_getAllStatuses();
        switch($statusNumber){
            case '0':
                $outStatuses[0] = $allStatuses[0];
                break;
            case '1':
                $outStatuses[1] = $allStatuses[1];
                $outStatuses[2] = $allStatuses[2];
                $outStatuses[4] = $allStatuses[4];
                break;
            case '2':
                $outStatuses[2] = $allStatuses[2];
                $outStatuses[3] = $allStatuses[3];
                break;
            case '3':
                $outStatuses[3] = $allStatuses[3];
                break;
            case '4':
                $outStatuses[4] = $allStatuses[4];
                break;
        }

        return $outStatuses;
    }

    /**
     * Get all statuses
     * @return array
     */
    private function _getAllStatuses()
    {
        return array(
            '0'=>A::t('appointments', 'Preparing'),
            '1'=>A::t('appointments', 'Pending'),
            '2'=>A::t('appointments', 'Paid'),
            '3'=>A::t('appointments', 'Refunded'),
            '4'=>A::t('appointments', 'Canceled')
        );
    }

    /**
     * Get Countries
     * @return array
     */
    private function _getCountries()
    {
        // Prepare Countries
        $arrCountryNames = array();
        $countriesResult = Countries::model()->findAll(array('condition'=>'is_active = 1', 'order'=>'sort_order DESC, country_name ASC'));
        if(is_array($countriesResult)){
            foreach($countriesResult as $key => $val){
                $arrCountryNames[$val['code']] = $val['country_name'];
            }
        }

        return $arrCountryNames;
    }

    /**
     * Get States
     * @return array
     */
    private function _getStates()
    {
        $arrStateNames = array();
        $statesResult = States::model()->findAll(array('condition'=>'is_active = 1', 'order'=>'sort_order DESC, state_name ASC'));
        if(is_array($statesResult)){
            foreach($statesResult as $key => $val){
                $arrStateNames[$val['code']] = $val['state_name'];
            }
        }

        return $arrStateNames;
    }

    /**
     * Prepare subtabs for edit orders
     */
    private function _prepareSubTabsForEditOrder($type, $activeSubTab, $orderId)
    {
        $arrSubTabs = array('general', 'invoice');
        $arrTabNames = array(
            'doctors'  => A::t('appointments', 'Doctors'),
            'patients' => A::t('appointments', 'Patients'),
            'general'  => A::t('appointments', 'General'),
            'invoice'  => A::t('appointments', 'Invoice')
        );
        $type = in_array($type, array('doctors', 'patients')) ? $type : 'doctors';
        $activeSubTab = in_array($activeSubTab, $arrSubTabs) ? $activeSubTab : $arrSubTabs[0];

        $outHtml = '<div class="sub-title">
            <a class="sub-tab active" href="orders/'.($type == 'doctors' ? 'doctorsManage' : 'patientsManage').'"><b>'.A::t('appointments', $arrTabNames[$type]).'</b></a> »
            '.A::t('appointments', 'Edit Order').' &nbsp;';
        foreach($arrSubTabs as $tabName){
            $outHtml .= '<a class="sub-tab'.($activeSubTab == $tabName ? ' active' : '').'" href="orders/'.($type == 'doctors' ? 'doctorEdit' : 'patientEdit').'/id/'.$orderId.'/tab/'.$tabName.'">'.$arrTabNames[$tabName].'</a>';
        }
        $outHtml .= '</div>';

        return $outHtml;
    }

    /**
     * Prepare PDF
     * @param object(Orders) orderId
     * @return html
     * */
    private function _preparePdf($order = null)
    {
        $output = '';

        if(empty($order) || !is_a($order, 'Modules\Appointments\Models\Orders')){
            return $output;
        }

        $beforePrice        = '';
        $afterPrice         = '';
        $planName           = A::t('appointments', 'Unknown');
        $plan               = Memberships::model()->findByPk($order->membership_plan_id);
        $doctor             = $this->_checkDoctorAccess($order->doctor_id);
        $allPaymentTypes    = $this->_getPaymentTypes();
        $allPaymentMethods  = $this->_getPaymentMethods();
        $allStatus          = $this->_getAllStatuses();
        $arrStateNames      = $this->_getStates();
        $arrCountryNames    = $this->_getCountries();
        $status             = isset($allStatus[$order->status]) ? $allStatus[$order->status] : A::t('appointments', 'Unknown');
        if(!empty($plan)){
            $planName = $plan->name;
        }

        $currency = Currencies::model()->find('code = :code', array(':code'=>$order->currency));
        if(!empty($currency)){
            if($currency->symbol_place == 'before'){
                $beforePrice = $currency->symbol;
                $afterPrice = '';
            }else{
                $beforePrice = '';
                $afterPrice = $currency->symbol;
            }
        }

        if(!empty($doctor)){
            $outputDoctor = '
                <div class="invoice-box">
                    <table class="pb10">
                        <tr>
                            <td class="title" colspan="2">'.A::t('appointments', 'Doctor').':</td>
                        </tr>
                        <tr>
                            <td width="30%">'.A::t('appointments', 'First Name').': </td><td>'.$doctor->doctor_first_name.'</td>
                        </tr>
                        <tr>
                            <td>'.A::t('appointments', 'Last Name').': </td><td>'.$doctor->doctor_last_name.'</td>
                        </tr>
                        <tr>
                            <td>'.A::t('appointments', 'Email').': </td><td>'.$doctor->email.'</td>
                        </tr>
                        <tr>
                            <td>'.A::t('appointments', 'Phone').': </td><td>'.($doctor->phone ? $doctor->phone : A::t('appointments', 'Unknown')).'</td>
                        </tr>
                        <tr>
                            <td>'.A::t('appointments', 'Address').': </td><td>'.($doctor->address ? $doctor->address : A::t('appointments', 'Unknown')).'</td>
                        </tr>
                        <tr>
                            <td>'.A::t('appointments', 'City').': </td><td>'.($doctor->city ? $doctor->city : A::t('appointments', 'Unknown')).'</td>
                        </tr>
                        <tr>
                            <td>'.A::t('appointments', 'Zip Code').': </td><td>'.($doctor->zip_code ? $doctor->zip_code : A::t('appointments', 'Unknown')).'</td>
                        </tr>
                        <tr>
                            <td>'.A::t('appointments', 'State/Province').': </td><td>'.(isset($doctor->state) ? (isset($arrStateNames[$doctor->state]) ? $doctor->state.' ('.$arrStateNames[$doctor->state].')' : $doctor->state) : A::t('appointments', 'Unknown')).'</td>
                        </tr>
                        <tr>
                            <td>'.A::t('appointments', 'Country').': </td><td>'.(isset($arrCountryNames[$doctor->country_code]) ? $arrCountryNames[$doctor->country_code] : A::t('appointments', 'Unknown')).'</td>
                        </tr>
                    </table>
                </div>';
        }else{
            $outputDoctor = '';
        }

        $output .= '<!DOCTYPE HTML>
        <html>
            <head>
                <style>
                    .right {text-align:right;}
                    .center {text-align:center;}
                </style>
            </head>
            <body>
                <table style="width:100%;margin:0 auto;font-size:12px;padding:10px 10px 10px 10px">
                    <h2 style="text-align:center;">'.A::t('appointments', 'Invoice').' #'.$order->order_number.'</h2>
                    <div class="invoice-box">
                        <table class="pb10">
                            <tr>
                                <td class="title" colspan="2">'.A::t('appointments', 'General').':</td>
                            </tr>
                            <tr>
                                <td width="30%">'.A::t('appointments', 'Membership Plan').': </td><td>'.$planName.'</td>
                            </tr>
                            <tr>
                                <td>'.A::t('appointments', 'Order Number').': </td><td>'.$order->order_number.'</td>
                            </tr>
                            <tr>
                                <td>'.A::t('appointments', 'Status').': </td><td>'.$status.'</td>
                            </tr>
                            <tr>
                                <td>'.A::t('appointments', 'Date Created').': </td><td>'.CLocale::date($this->_view->dateTimeFormat, $order->created_date).'</td>
                            </tr>
                            <tr>
                                <td><b>'.A::t('appointments', 'Subtotal').': </b></td><td><b>'.$beforePrice.CNumber::format($order->order_price, $this->_view->numberFormat, array('decimalPoints'=>2)).$afterPrice.'</b></td>
                            </tr>
                            <tr>
                                <td><b>'.A::t('appointments', 'Grand Total').': </b></td><td><b>'.$beforePrice.CNumber::format($order->total_price, $this->_view->numberFormat, array('decimalPoints'=>2)).$afterPrice.'</b></td>
                            </tr>
                        </table>
                    </div>
                    '.$outputDoctor.'
                    <div class="invoice-box">
                        <table class="pb10">
                            <tr>
                                <td class="title" colspan="2">'.A::t('appointments', 'Payment').':</td>
                            </tr>
                            <tr>
                                <td width="30%">'.A::t('appointments', 'Payment Type').': </td><td>'.(isset($allPaymentTypes[$order->payment_id]) ? $allPaymentTypes[$order->payment_id] : A::t('appointments', 'Unknown')).'</td>
                            </tr>
                            <tr>
                                <td>'.A::t('appointments', 'Payment Method').': </td><td>'.(isset($allPaymentMethods[$order->payment_method]) ? $allPaymentMethods[$order->payment_method] : A::t('appointments', 'Unknown')).'</td>
                            </tr>
                            <tr>
                                <td>'.A::t('appointments', 'Payment Date').': </td><td>'.(!CTime::isEmptyDateTime($order->payment_date) ? CLocale::date($this->_view->dateTimeFormat, $order->payment_date) : A::t('appointments', 'Unknown')).'</td>
                            </tr>
                            <tr>
                                <td>'.A::t('appointments', 'Transaction ID').': </td><td>'.($order->transaction_number ? $order->transaction_number : A::t('appointments', 'Unknown')).'</td>
                            </tr>
                        </table>
                    </div>
                    <div style="text-align:left;">'.A::t('appointments', 'Date Created Invoice').': '.CLocale::date($this->_view->dateTimeFormat).'</div>
                </table>
            </body>
        </html>';

        return $output;
    }
}
