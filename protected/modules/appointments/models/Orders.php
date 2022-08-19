<?php
/**
 * Appointments model
 *
 * PUBLIC:                 PROTECTED                  PRIVATE
 * ---------------         ---------------            ---------------
 * __construct             _relations
 *
 * STATIC:
 * ---------------------------------------------------------------
 * model
 * paymentHandler
 */

namespace Modules\Appointments\Models;

// Framework
use \A,
	\Accounts,
	\Bootstrap,
    \CActiveRecord,
    \CConfig,
    \CLocale,
    \Website,
	\CAuth;

use \PaymentProviders;

use \Modules\Appointments\Components\DoctorsComponent;

class Orders extends CActiveRecord
{

    /** @var string */
    protected $_table = 'appt_orders';
    /** @var string */
    protected $_tableDoctors = 'appt_doctors';
    /** @var string */
    protected $_tablePatients = 'appt_patients';
    /* @var string (simple|doctors|patients|all) */
    protected $_typeRelations;
    /** @var int */
    protected $_oldStatus = 0;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Returns the static model of the specified AR class
     * @param string $relationType
     * @return void
     */
    public static function model($relationType = '')
    {
        $model = parent::model(__CLASS__);

        // Set relations type
        if(empty($relationType)){
            if($model->_typeRelations != 'simple'){
                $model->reset();
            }
            $model->_typeRelations = 'simple';
        }elseif(in_array($relationType, array('simple', 'doctors', 'patients', 'all'))){
            $model->_typeRelations = $relationType;
        }

        return $model;
    }

    /**
     * Defines relations between different tables in database and current $_table
     */
    protected function _relations()
    {
        $arrRelations = array();
        if(in_array($this->_typeRelations, array('doctors', 'all'))){
            $arrRelations[] = array(
                self::HAS_MANY,
                $this->_tableDoctors,
                'id',
                'parent_key' => 'doctor_id',
                'condition' => '',
                'joinType' => self::INNER_JOIN,
                'fields' => array('doctor_first_name', 'doctor_last_name')
            );
        }
        if(in_array($this->_typeRelations, array('patients', 'all'))){
            $arrRelations[] = array(
                self::HAS_MANY,
                $this->_tablePatients,
                'id',
                'parent_key' => 'patient_id',
                'condition' => '',
                'joinType' => self::INNER_JOIN,
                'fields' => array('patient_first_name', 'patient_last_name')
            );
        }

        return $arrRelations;
    }

    /**
     * This method is invoked before saving a record
     * @param int $pk
     * @return bool
     */
    protected function _beforeSave($pk = 0)
    {
        $order = new Orders();
        $order->findByPk($pk);
        if($order){
            $this->_oldStatus = $order->status;
        }
        return true;
    }

	/**
	 * This method is invoked after saving a record successfully
	 * @param int $id
	 */
	protected function _afterSave($id = 0)
	{
		if(!$this->isNewRecord()){
			//If status = 2 (Paid) update membership plan for doctor account
			if($this->status == 2 && $this->_oldStatus != $this->status){
				$updateMembershipPlan = DoctorsComponent::updateMembershipPlan($this->doctor_id, $this->membership_plan_id);
				if($updateMembershipPlan){
					$tableNameAccount = CConfig::get('db.prefix').Accounts::model()->getTableName();
					$doctor = Doctors::model()->findByPk($this->doctor_id, $tableNameAccount.'.is_active = 1');
					$membershipPlan = Memberships::model()->findByPk($doctor->membership_plan_id);

					$status = A::t('appointments', 'Paid');
					$dateTimeFormat = Bootstrap::init()->getSettings()->datetime_format;

					if(!empty($doctor) && !empty($membershipPlan)){
						DoctorsComponent::checkMembershipPlan($doctor->membership_plan_id, $doctor->membership_expires);
						// Send email
						Website::sendEmailByTemplate(
							$doctor->email,
							'paid_order',
							$doctor->language_code,
							array(
								'{FIRST_NAME}'      => $doctor->doctor_first_name,
								'{LAST_NAME}'       => $doctor->doctor_last_name,
								'{ORDER_NUMBER}'    => $this->order_number,
								'{MEMBERSHIP_PLAN}' => $membershipPlan->name,
								'{STATUS}'          => $status,
								'{DATE_CREATED}'    => CLocale::date($dateTimeFormat, strtotime($this->created_date), true),
								'{STATUS_CHANGED}'  => CLocale::date($dateTimeFormat, strtotime($this->status_changed), true),
							)
						);
					}
				$this->payment_date = date('Y-m-d H:i:s');
				$this->save();
				}
			}
		}
	}

    /**
     * Used to define custom fields
     * This method should be overridden
     * Usage: 'CONCAT(last_name, " ", first_name)'=>'fullname'
     *        '(SELECT COUNT(*) FROM '.CConfig::get('db.prefix').$this->_tableTranslation.')'=>'records_count'
     */
    protected function _customFields()
    {
        $arrCustomFields = array();
        if(in_array($this->_typeRelations, array('doctors', 'all'))){
            $tableDoctors = CConfig::get('db.prefix').$this->_tableDoctors;
            $arrCustomFields["CONCAT(".$tableDoctors.".doctor_first_name, ' ', ".$tableDoctors.".doctor_last_name)"] = 'doctor_name';
        }
        if(in_array($this->_typeRelations, array('patients', 'all'))){
            $tablePatinets = CConfig::get('db.prefix').$this->_tablePatients;
            $arrCustomFields["CONCAT(".$tablePatinets.".patient_first_name, ' ', ".$tablePatinets.".patient_last_name)"] = 'patient_name';
        }

        return $arrCustomFields;
    }


	/**
	 * Payment handler
	 * @param string $type
	 * @param array $orderInfo
	 * @return bool
	 * */
	public function paymentHandler($type, $orderInfo = array())
	{
		if(($type == 'completed' && empty($orderInfo)) || !is_array($orderInfo)){
			$this->_errorMessage = A::t('appointments', 'Input incorrect parameters');
			return false;
		}

		$return = true;
		$orderNumber = !empty($orderInfo['order_number']) ? $orderInfo['order_number'] : '';

		// Status Pending
		if($type == 'pending'){
			// Get Order Number
            $lastOrderNumber =  A::app()->getSession()->get('lastOrderNumber');
			if(empty($orderNumber)&& empty($lastOrderNumber)){
				$accountId = CAuth::getLoggedId();
				$doctor = Doctors::model()->find('account_id = :account_id', array(':account_id'=>$accountId));
				if(empty($doctor)){
					$this->_errorMessage = A::t('appointments', 'You are not logged in to your account');
					return false;
				}
				$order = $this->find('doctor_id = :doctor_id AND status = 0', array(':doctor_id'=>$doctor->id));
			}elseif(!empty($lastOrderNumber)){
                $order = $this->find('order_number = :order_number', array(':order_number'=>$lastOrderNumber));
            }else{
				$order = $this->find('order_number = :order_number', array(':order_number'=>$orderNumber));
			}

			if(empty($order)){
				$this->_errorMessage = A::t('appointments', 'Order cannot be found in the database');
				$return = false;
			}else{
				$order->status = 1;
			}
		}elseif($type == 'completed' && $orderNumber){
			// Status Completed
			$order = $this->find('order_number = :order_number', array(':order_number'=>$orderNumber));
			if(empty($order)){
				$this->_errorMessage = A::t('appointments', 'Order cannot be found in the database');
				$return = false;
			}else{
				$order->status = 2;
			}
		}elseif($type == 'canceled' && $orderNumber){
			// Status Rejected
			$order = $this->find('order_number = :order_number', array(':order_number'=>$orderNumber));
			if(empty($order)){
				$this->_errorMessage = A::t('appointments', 'Order cannot be found in the database');
				$return = false;
			}else{
				$order->status = 5;
			}
		}else{
			$this->_errorMessage = A::t('appointments', 'Input incorrect parameters');
			$return = false;
		}

		if($return){
			$order->status_changed = date('Y-m-d H:i:s');

			$tableNameAccount = CConfig::get('db.prefix').Accounts::model()->getTableName();
			$doctor = Doctors::model()->findByPk($order->doctor_id, $tableNameAccount.'.is_active = 1');
			$providersList = array();
			$paymentProviders = PaymentProviders::model()->findAll('is_active = 1');
			if(!empty($paymentProviders) && is_array($paymentProviders)){
				foreach($paymentProviders as $onePayment){
					$providersList[$onePayment['id']] = $onePayment['name'];
				}
			}

			$statusList = array(
				'0'=>A::t('appointments', 'Preparing'),
				'1'=>A::t('appointments', 'Pending'),
				'2'=>A::t('appointments', 'Paid'),
				'3'=>A::t('appointments', 'Completed'),
				'4'=>A::t('appointments', 'Refunded'),
				'5'=>A::t('appointments', 'Canceled')
			);
			$status = $statusList[$order->status];
			$paymentType = $providersList[$order->payment_id];
			$dateTimeFormat = Bootstrap::init()->getSettings()->datetime_format;

			if(!empty($doctor)){
				// Send email
				$emailResult = Website::sendEmailByTemplate(
					$doctor->email,
					'success_order',
					$doctor->language_code,
					array(
						'{FIRST_NAME}'   => $doctor->doctor_first_name,
						'{LAST_NAME}'    => $doctor->doctor_last_name,
						'{ORDER_NUMBER}' => $order->order_number,
						'{STATUS}'       => $status,
						'{DATE_CREATED}' => CLocale::date($dateTimeFormat, strtotime($order->created_date), true),
						'{DATE_PAYMENT}' => $order->payment_date == null ? A::t('appointments', 'Not paid yet', array(), null, $doctor->language_code) : CLocale::date($dateTimeFormat, strtotime($order->payment_date)),
						'{PAYMENT_TYPE}' => $paymentType,
						'{CURRENCY}'     => $order->currency,
						'{PRICE}'        => $order->total_price
					)
				);
				
				$order->email_sent = $emailResult ? 1 : 0;
			}

			// Send notification to admin about order
			Website::sendEmailByTemplate(
				Bootstrap::init()->getSettings()->general_email,
				'success_order_for_admin',
				A::app()->getLanguage(),
				array(
					'{FIRST_NAME}'   => $doctor->doctor_first_name,
					'{LAST_NAME}'    => $doctor->doctor_last_name,
					'{USERNAME}'     => $doctor->username,
					'{ORDER_NUMBER}' => $order->order_number,
					'{STATUS}'       => $status,
					'{DATE_CREATED}' => CLocale::date($dateTimeFormat, strtotime($order->created_date), true),
					'{DATE_PAYMENT}' => $order->payment_date == null ? A::t('appointments', 'Not paid yet', array(), null, $doctor->language_code) : CLocale::date($dateTimeFormat, strtotime($order->payment_date)),
					'{PAYMENT_TYPE}' => $paymentType,
					'{CURRENCY}'     => $order->currency,
					'{PRICE}'        => $order->total_price
				)
			);

			$order->save();

			return true;
		}else{
			return false;
		}
	}
}
