<?php
/**
 * Appointments model
 *
 * PUBLIC:                 	PROTECTED                	PRIVATE
 * ---------------         	---------------          	---------------
 * __construct             	_relations					_sendEmail
 *						  	_customFields               _sendEmailDoctorArrivalReminder
 * STATIC:                	_afterSave
 * model				  	_beforeSave
 * cron
 * 
 *
 */

namespace Modules\Appointments\Models;

// Framework
use \A,
	\CAuth,
	\CActiveRecord,
	\CConfig,
	\CHtml,
	\CLocale;

// Application
use Modules\Appointments\Components\DoctorsComponent;
use \ModulesSettings,
	\Website,
	\LocalTime,
	\Bootstrap;

class Appointments extends CActiveRecord
{

	/** @var string */
	protected $_table = 'appt_appointments';
	protected $_tableDoctors = 'appt_doctors';
	protected $_tableAccounts = 'accounts';
	protected $_tablePatients = 'appt_patients';
	protected $_tableClinicTranslations = 'appt_clinic_translations';
	protected $_tableSpecialtyTranslations = 'appt_specialty_translations';

	protected $_oldStatus = 0;

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Returns the static model of the specified AR class
	 */
	public static function model()
	{
		return parent::model(__CLASS__);
	}

	/**
	 * Defines relations between different tables in database and current $_table
	 */
	protected function _relations()
	{
		return array(
			'doctor_id' => array(
				self::HAS_MANY,
				$this->_tableDoctors,
				'id',
				'condition'=>"",
				'joinType'=>self::LEFT_OUTER_JOIN,
				'fields'=>array(
					'doctor_first_name',
					'doctor_middle_name',
					'doctor_last_name',
				)
			),
			'patient_id' => array(
				self::HAS_MANY,
				$this->_tablePatients,
				'id',
				'condition'=>"",
				'joinType'=>self::LEFT_OUTER_JOIN,
				'fields'=>array(
					'patient_first_name',
					'patient_last_name',
				)
			),
			'doctor_specialty_id' => array(
				self::HAS_MANY,
				$this->_tableSpecialtyTranslations,
				'specialty_id',
				'condition'=>CConfig::get('db.prefix').$this->_tableSpecialtyTranslations.".language_code = '".A::app()->getLanguage()."'",
				'joinType'=>self::LEFT_OUTER_JOIN,
				'fields'=>array(
					'name' => 'specialty_name',
				)
			),
			'doctor_address_id' => array(
				self::HAS_MANY,
				$this->_tableClinicTranslations,
				'clinic_id',
				'condition'=> CConfig::get('db.prefix').$this->_tableClinicTranslations.".language_code = '".A::app()->getLanguage()."'",
				'joinType'=>self::LEFT_OUTER_JOIN,
				'fields'=>array(
					'name' => 'clinic_name',
					'address' => 'clinic_address',
				)
			),

		);
	}

    /**
     * This method is invoked before saving a record
     * @param int $pk
     * @return bool
     */
    protected function _beforeSave($pk = 0)
    {
        $appointment = new Appointments;
        $appointment->findByPk($pk);
        if($appointment){
            $this->_oldStatus = $appointment->status;
        }

        return true;
    }

	/**
	 * This method is invoked after saving a record successfully
	 * @param int $appointmentId
	 */
	protected function _afterSave($appointmentId = 0)
	{
		$toSend          			= array();
		$templatesCode   			= array('patient'=>'', 'doctor'=>'');
		$loggedRole      			= CAuth::getLoggedRole();
		$sendEmailPatient			= '';
		$sendEmailDoctor 			= '';
		$changeAppointmentSendEmail = A::app()->getSession()->get('changeAppointmentSendEmail');
        if($this->_oldStatus != $this->status || $changeAppointmentSendEmail){
            if($this->status == 0 && !$changeAppointmentSendEmail){
                //Who needs to send an email if the appointment is reserved?
                $sendEmailPatient = ModulesSettings::model()->param('appointments', 'send_email_patient_appointment_reserved');
                $sendEmailDoctor = ModulesSettings::model()->param('appointments', 'send_email_doctor_appointment_reserved');
                if($sendEmailPatient) {
                    $templatesCode['patient'] = 'appointment_reserved_by_patient';
                }
                if($sendEmailDoctor) {
                    $templatesCode['doctor'] = 'appointment_reserved_by_doctor';
                }
            }elseif($this->status == 1  && !$changeAppointmentSendEmail){
                //Who needs to send an email if the appointment is verified?
                $sendEmailPatient = ModulesSettings::model()->param('appointments', 'send_email_patient_appointment_verified');
                $sendEmailDoctor = ModulesSettings::model()->param('appointments', 'send_email_doctor_appointment_verified');
                if($sendEmailPatient) {
                    $templatesCode['patient'] = 'appointment_verified_by_patient';
                }
                if($sendEmailDoctor) {
                    $templatesCode['doctor'] = 'appointment_verified_by_doctor';
                }
            }elseif($this->status == 2 && !$changeAppointmentSendEmail){
                //Who needs to send an email if the appointment is canceled?
                $sendEmailPatient = ModulesSettings::model()->param('appointments', 'send_email_patient_appointment_canceled');
                $sendEmailDoctor = ModulesSettings::model()->param('appointments', 'send_email_doctor_appointment_canceled');
                if($sendEmailPatient) {
                    $templatesCode['patient'] = 'appointment_canceled_by_patient';
                }
                if($sendEmailDoctor) {
                    $templatesCode['doctor'] = 'appointment_canceled_by_doctor';
                }
            }elseif($changeAppointmentSendEmail){
                //Who needs to send an email if the appointment is changed?
                $sendEmailPatient = ModulesSettings::model()->param('appointments', 'send_email_patient_appointment_changed');
                $sendEmailDoctor = ModulesSettings::model()->param('appointments', 'send_email_doctor_appointment_changed');
                if($sendEmailPatient) {
                    $templatesCode['patient'] = 'appointment_changed_by_patient';
                }
                if($sendEmailDoctor) {
                    $templatesCode['doctor'] = 'appointment_changed_by_doctor';
                }
                A::app()->getSession()->remove('changeAppointmentSendEmail');
            }

            //Whom to send email
            if($loggedRole == 'doctor' && $sendEmailPatient){
                $toSend[] = 'patient';
            }elseif($loggedRole == 'patient' && $sendEmailDoctor){
                $toSend[] = 'doctor';
            }elseif(CAuth::isLoggedInAsAdmin()){
                if($sendEmailPatient){
                    $toSend[] = 'patient';
                }
                if($sendEmailDoctor){
                    $toSend[] = 'doctor';
                }
            }
            if(!empty($toSend)){
                //Send Email
                $this->_sendEmail($this->doctor_id, $this->patient_id, $appointmentId, $toSend, $templatesCode);
            }

        $this->status_changed = date('Y-m-d H:i:s');
        $this->save();
        }
	}

	/**
	 * Used to define custom fields
	 * This method should be overridden
	 * Usage: 'CONCAT(last_name, " ", first_name)' => 'fullname'
	 *        '(SELECT COUNT(*) FROM '.CConfig::get('db.prefix').$this->_tableTranslation.')' => 'records_count'
	 */
	protected function _customFields()
	{
		$fields = array(
			'CONCAT('.CConfig::get('db.prefix').$this->_tableDoctors.'.doctor_first_name, " ", '.CConfig::get('db.prefix').$this->_tableDoctors.'.doctor_middle_name, " ", '.CConfig::get('db.prefix').$this->_tableDoctors.'.doctor_last_name)' => 'doctor_name',
			'CONCAT('.CConfig::get('db.prefix').$this->_tablePatients.'.patient_first_name, " ", '.CConfig::get('db.prefix').$this->_tablePatients.'.patient_last_name)' => 'patient_name',
		);

		return $fields;
	}

	/**
	 * Search and send e-mail for the destination patients which will begin in the number hours specified in 'reminder_patient_arrival_reminder'
	 * Search and Send Email for doctors whose membership plan expires through the number days  specified in 'reminder_expired_membership'
	 */
	static public function cron()
	{
		$resultSendEmailAppointments = array();
		$reminderHours = ModulesSettings::model()->param('appointments', 'reminder_patient_arrival_reminder');
        $tableName = CConfig::get('db.prefix').self::model()->_table;
        $tableDoctorName 	= CConfig::get('db.prefix').self::model()->_tableDoctors;
        $tableAccountName 	= CConfig::get('db.prefix').self::model()->_tableAccounts;
        $currentDate = LocalTime::currentDateTime('Y-m-d');
        $currentTime = LocalTime::currentDateTime('H:i:s');
		if(!empty($reminderHours) && $reminderHours != ''){
			$reminderHoursUnix = '+'.$reminderHours.' hours';
			$reminderDateTime = date('Y-m-d H:i:s', strtotime($reminderHoursUnix));
			$reminderDate = date('Y-m-d', strtotime($reminderDateTime));
			$reminderTime = date('H:i:s', strtotime($reminderDateTime));
			// Search for the appointments(today + 'reminder_patient_arrival_reminder' hours)
			$condition	= $tableName.'.status = 1 AND '.$tableName.'.p_arrival_reminder_sent = 0 AND ( 
				("'.$currentDate.'" = "'.$reminderDate.'" AND '.$tableName.'.appointment_date = "'.$currentDate.'" AND '.$tableName.'.appointment_time >= "'.$currentTime.'" AND '.$tableName.'.appointment_time <= "'.$reminderTime.'") 
				OR ("'.$reminderDate.'" > "'.$currentDate.'" AND '.$tableName.'.appointment_date = "'.$currentDate.'" AND '.$tableName.'.appointment_time >= "'.$currentTime.'") 
				OR ("'.$reminderDate.'" > "'.$currentDate.'" AND '.$tableName.'.appointment_date > "'.$currentDate.'" AND '.$tableName.'.appointment_date < "'.$reminderDate.'") 
				OR ("'.$reminderDate.'" > "'.$currentDate.'" AND '.$tableName.'.appointment_date = "'.$reminderDate.'" AND '.$tableName.'.appointment_time <= "'.$reminderTime.'") 
			)';
			$appointments = self::model()->findAll(array('condition'=>$condition, 'order'=>'appointment_date ASC'));
			if(!empty($appointments)){
				// Send a reminder of the appointments to patients
				foreach($appointments as $appointment){
                    $doctorId 		= $appointment['doctor_id'];
                    $patientId 		= $appointment['patient_id'];
                    $appointmentId 	= $appointment['id'];
                    $toSend 		= array('patient');
                    $templatesCode 	= array('patient'=>'appointment_reminder');

                    $resultSendEmailAppointments[$appointment['id']] = self::model()->_sendEmail($doctorId, $patientId, $appointmentId, $toSend, $templatesCode);
				}
			}
		}

        //Set the flag after sending a notification
		if(!empty($resultSendEmailAppointments)){
		    foreach($resultSendEmailAppointments as $appointmentId => $resultSendEmailAppointment){
		        if($resultSendEmailAppointment){
                    $appointment = self::model()->findByPk($appointmentId);
                    if($appointment){
                        $appointment->p_arrival_reminder_sent = true;
                        $appointment->save();
                    }
                }
            }
        }


        /* Doctor Arrival Reminder */
        // Search doctors
        $condition = $tableAccountName.'.is_active = 1 AND '.$tableAccountName.'.is_removed = 0 AND ('.$tableDoctorName.'.last_reminded_date < "'.$currentDate.'" OR '.$tableDoctorName.'.last_reminded_date IS NULL)';
        $doctors = Doctors::model()->findAll(array('condition'=>$condition, 'order'=>'id ASC'));
        if(!empty($doctors)){
            foreach($doctors as $doctor){
                // Search appointments for doctors
                $condition	= $tableName.'.status = 1 AND '.$tableName.'.doctor_id = '.$doctor['id'].' AND '.$tableName.'.appointment_date = "'.$currentDate.'"';
                $appointments = self::model()->findAll(array('condition'=>$condition, 'order'=>'appointment_time ASC'));
                if(!empty($doctors) && !empty($appointments)){
                    $resultSendEmailDoctorArrivalReminder[$doctor['id']] = self::model()->_sendEmailDoctorArrivalReminder($doctor, $appointments, $currentDate);
                }
            }
        }

        //Set Last Reminder Date after sending a notification
        if(!empty($resultSendEmailDoctorArrivalReminder)){
            foreach($resultSendEmailDoctorArrivalReminder as $doctorId => $result){
                if($result){
                    $doctor = Doctors::model('with_appointments_counter')->updateByPk($doctorId, array('last_reminded_date'=>$currentDate));
                }
            }
        }

        $resultSendEmailMemberships = array();
		$reminderDays = ModulesSettings::model()->param('appointments', 'reminder_expired_membership');
		if(!empty($reminderDays)){
			$dateFormat 		= Bootstrap::init()->getSettings()->date_format;
			$reminderDaysUnix 	= '+'.$reminderDays.' days';
			$reminderDate 		= date('Y-m-d', strtotime($reminderDaysUnix));
			// Search doctors whose membership plan expires through the 'reminder_expired_membership' days
			$condition = $tableAccountName.'.is_active = 1 AND '.$tableAccountName.'.is_removed = 0 AND '.$tableDoctorName.'.membership_expires <= "'.$reminderDate.'" AND '.$tableDoctorName.'.last_membership_reminder_date IS NULL';
			$doctors = Doctors::model()->findAll($condition);
			if(!empty($doctors)){
				// Send a reminder to doctors about the expiration of the membership plan
				foreach($doctors as $doctor){
                    $resultSendEmailMemberships[$doctor['id']] = Website::sendEmailByTemplate(
                        $doctor['email'],
                        'reminder_expiries_membership_plan',
                        $doctor['language_code'],
                        array(
                            '{FULL_NAME}'           => $doctor['full_name'],
                            '{EXPIRIES_DATE}'       => date($dateFormat, strtotime($doctor['membership_expires'])),
                        )
                    );
				}
			}
		}

        //Set the flag after sending a notification
        if(!empty($resultSendEmailMemberships)){
            foreach($resultSendEmailMemberships as $doctorId => $resultSendEmailMembership){
                if($resultSendEmailMembership){
                    //Update last membership reminder date
                    Doctors::model('with_appointments_counter')->updateByPk($doctorId, array('last_membership_reminder_date'=>date('Y-m-d')));
                }
            }
        }
	}

	/**
	 * Send Email Reminder
	 * @param int $doctorId
	 * @param int $patientId
	 * @param int $appointmentId
	 * @param array $toSend
	 * @param array $templatesCode
	 * @return bool
	 */
	private function _sendEmail($doctorId = 0, $patientId = 0, $appointmentId = 0,  $toSend = array(), $templatesCode = array()){
		if(empty($doctorId) || empty($patientId)|| empty($appointmentId) || empty($toSend)){
			return false;
		}

		$result 	 = false;
		$doctor      = Doctors::model()->findByPk($doctorId);
		$patient     = Patients::model()->findByPk($patientId);
		$appointment = Appointments::model()->findByPk($appointmentId);

		if($doctor && $patient && $appointment){
			$email 				    = '';
			$templateCode 		    = '';
			$languageCode 		    = '';
			$fullName 			    = '';
			$dateFormat      	    = Bootstrap::init()->getSettings()->date_format;
            $appointmentTimeFormat  = ModulesSettings::model()->param('appointments', 'time_format_appointment_time');
            $patientFullName 	= $patient->getFullName();
			$doctorFullName  	= $doctor->getFullName();
            $clinic             = Clinics::model()->findByPk($appointment->doctor_address_id);
            $timeZone           = A::app()->getLocalTime()->getTimeZoneInfo($clinic->time_zone, 'full_name');

			foreach($toSend as $to){
				if($to == 'doctor'){
					$email = $doctor->email;
					$templateCode = $templatesCode['doctor'];
					$languageCode = $doctor->language_code;
					$fullName = $doctorFullName;
				}elseif($to == 'patient'){
					$email = $patient->email;
					$templateCode = $templatesCode['patient'];
					$languageCode = $patient->language_code;
					$fullName = $patientFullName;
				}
				//Create table apppointment details for email message
				$appointmentDetails = '';
				$appointmentDetails .= CHtml::openTag('table');
				$appointmentDetails .= CHtml::openTag('tr');
				$appointmentDetails .= CHtml::tag('td', '', A::t('appointments', 'Appointment ID'));
				$appointmentDetails .= CHtml::tag('td', '', $appointment->appointment_number);
				$appointmentDetails .= CHtml::closeTag('tr');
				$appointmentDetails .= CHtml::openTag('tr');
				if($to == 'doctor'){
					$appointmentDetails .= CHtml::tag('td', '', A::t('appointments', 'Patient Name'));
					$appointmentDetails .= CHtml::tag('td', '', $patientFullName);
				}elseif($to == 'patient'){
					$appointmentDetails .= CHtml::tag('td', '', A::t('appointments', 'Doctor Name'));
					$appointmentDetails .= CHtml::tag('td', '', $doctorFullName);
				}
				$appointmentDetails .= CHtml::closeTag('tr');
				$appointmentDetails .= CHtml::openTag('tr');
				$appointmentDetails .= CHtml::tag('td', '', A::t('appointments', 'Specialty'));
				$appointmentDetails .= CHtml::tag('td', '', $appointment->specialty_name);
				$appointmentDetails .= CHtml::closeTag('tr');
				$appointmentDetails .= CHtml::openTag('tr');
				$appointmentDetails .= CHtml::tag('td', '', A::t('appointments', 'Date'));
				$appointmentDetails .= CHtml::tag('td', '', CLocale::date($dateFormat, $appointment->appointment_date));
				$appointmentDetails .= CHtml::closeTag('tr');
				$appointmentDetails .= CHtml::openTag('tr');
				$appointmentDetails .= CHtml::tag('td', '', A::t('appointments', 'Time'));
				$appointmentDetails .= CHtml::tag('td', '', CLocale::date($appointmentTimeFormat, $appointment->appointment_time));
				$appointmentDetails .= CHtml::closeTag('tr');
				$appointmentDetails .= CHtml::openTag('tr');
				$appointmentDetails .= CHtml::tag('td', '', A::t('app', 'Time Zone'));
				$appointmentDetails .= CHtml::tag('td', '', $timeZone);
				$appointmentDetails .= CHtml::closeTag('tr');
				$appointmentDetails .= CHtml::openTag('tr');
				$appointmentDetails .= CHtml::tag('td', '', A::t('appointments', 'Clinic Name'));
				$appointmentDetails .= CHtml::tag('td', '', $appointment->clinic_name);
				$appointmentDetails .= CHtml::closeTag('tr');
				$appointmentDetails .= CHtml::openTag('tr');
				$appointmentDetails .= CHtml::tag('td', '', A::t('appointments', 'Clinic Address'));
				$appointmentDetails .= CHtml::tag('td', '', $appointment->clinic_address);
				$appointmentDetails .= CHtml::closeTag('tr');
				$appointmentDetails .= CHtml::closeTag('table');

				$sendEmail = Website::sendEmailByTemplate(
					$email,
					$templateCode,
					$languageCode,
					array(
						'{FULL_NAME}'           => $fullName,
						'{APPOINTMENT_DETAILS}' => $appointmentDetails,
					)
				);

				if($sendEmail){
					$result = true;
				}
			}
		}


		return $result;
	}

	/**
	 * Send Email Doctor Arrival Reminder
	 * @param array $doctor
	 * @param array $appointments
	 * @param string $currentDate
	 * @return bool
	 */
	private function _sendEmailDoctorArrivalReminder($doctor = array(), $appointments = array(), $currentDate = ''){
	    if(empty($doctor) || empty($appointments)){
			return false;
		}

        $result 	        = false;
        $dateFormat      	= Bootstrap::init()->getSettings()->date_format;
        $appointmentTimeFormat  = ModulesSettings::model()->param('appointments', 'time_format_appointment_time');
        $appointmentTable = '';
        //Create table apppointment details for email message
        $appointmentTable .= CHtml::openTag('table');
        $appointmentTable .= CHtml::openTag('tr');
        $appointmentTable .= CHtml::tag('td', '', A::t('appointments', 'Appointment ID'));
        $appointmentTable .= CHtml::tag('td', '', A::t('appointments', 'Patient Name'));
        $appointmentTable .= CHtml::tag('td', '', A::t('appointments', 'Date'));
        $appointmentTable .= CHtml::tag('td', '', A::t('appointments', 'Time'));
        $appointmentTable .= CHtml::tag('td', '', A::t('app', 'Time Zone'));
        $appointmentTable .= CHtml::tag('td', '', A::t('appointments', 'Clinic Name'));
        $appointmentTable .= CHtml::tag('td', '', A::t('appointments', 'Clinic Address'));
        $appointmentTable .= CHtml::closeTag('tr');
        foreach($appointments as $appointment){
            $patient    = Patients::model()->findByPk($appointment['patient_id']);
            $clinic     = Clinics::model()->findByPk($appointment['doctor_address_id']);
            $timeZone   = A::app()->getLocalTime()->getTimeZoneInfo($clinic->time_zone, 'full_name');
            if($patient){
                $appointmentTable .= CHtml::openTag('tr');
                $appointmentTable .= CHtml::tag('td', '', $appointment['appointment_number']);
                $appointmentTable .= CHtml::tag('td', '', $patient->getFullName());
                $appointmentTable .= CHtml::tag('td', '', CLocale::date($dateFormat, $appointment['appointment_date']));
                $appointmentTable .= CHtml::tag('td', '', CLocale::date($appointmentTimeFormat, $appointment['appointment_time']));
                $appointmentTable .= CHtml::tag('td', '', $timeZone);
                $appointmentTable .= CHtml::tag('td', '', $appointment['clinic_name']);
                $appointmentTable .= CHtml::tag('td', '', $appointment['clinic_address']);
                $appointmentTable .= CHtml::closeTag('tr');
            }
        }
        $appointmentTable .= CHtml::closeTag('table');

        $sendEmail = Website::sendEmailByTemplate(
            $doctor['email'],
            'appointment_doctor_reminder',
            $doctor['language_code'],
            array(
                '{FULL_NAME}'           => $doctor['full_name'],
                '{APPOINTMENTS_DATE}'   => CLocale::date($dateFormat, $currentDate),
                '{APPOINTMENTS}'        => $appointmentTable,
            )
        );

        if($sendEmail){
            $result = true;
        }


		return $result;
	}

}
