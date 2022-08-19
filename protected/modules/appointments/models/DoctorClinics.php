<?php
/**
 * DoctorClinics model
 *
 * PUBLIC:                  PROTECTED                  PRIVATE
 * ---------------          ---------------            ---------------
 * __construct              _relations
 *                          _beforeDelete
 *                          _afterDelete
 * STATIC:
 * ---------------------------------------------------------------
 * model
 *
 */

namespace Modules\Appointments\Models;

// Framework
use \A,
    \CActiveRecord,
    \CConfig;

// Application
use \LocalTime;

class DoctorClinics extends CActiveRecord
{

    /** @var string */
    protected $_table = 'appt_doctor_clinics';
    /** @var string */
    protected $_tableClinicTranslations = 'appt_clinic_translations';

    protected $_clinicId = 0;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Returns the static model of the specified AR class
     * @return DoctorSchedules
     */
    public static function model()
    {
        return parent::model(__CLASS__);
    }

    /**
     * Defines relations between different tables in database and current $_table
     * @return array
     */
    protected function _relations()
    {
        return array(
            'clinic_id' => array(
                self::HAS_MANY,
                $this->_tableClinicTranslations,
                'clinic_id',
                'condition'=> CConfig::get('db.prefix').$this->_tableClinicTranslations.".language_code = '".A::app()->getLanguage()."'",
                'joinType'=>self::INNER_JOIN,
                'fields'=>array(
                    'name'=>'clinic_name',
                    'address'=>'clinic_address',
                )
            ),
        );
    }

    /**
     * This method is invoked before deleting a record (after validation, if any)
     * You may override this method
     * @param int $id
     * @return boolean
     */
    protected function _beforeDelete($id = 0)
    {
        $doctorClinic = $this->findByPk($id);

        if($doctorClinic){
            $this->_clinicId = $doctorClinic->clinic_id;
            $tableName = CConfig::get('db.prefix').Appointments::model()->getTableName();
            $condition	= 'clinic_id = '.$doctorClinic->clinic_id.' AND ('.$tableName.'.status = 1 OR '.$tableName.'.status = 0) AND ('.$tableName.".appointment_date > '".LocalTime::currentDateTime('Y-m-d')."'".' OR ('.$tableName.".appointment_date = '".LocalTime::currentDateTime('Y-m-d')."'".' AND '.$tableName.".appointment_time > '".LocalTime::currentDateTime('H:i:s')."'))";
            $countAppointments = Appointments::model()->count($condition);

            if($countAppointments > 0){
                $this->_error = true;
                $this->_errorMessage = A::t('appointments', 'You can not delete the clinic {clinic_name}, there are active appointments.', array('{clinic_name}'=>$doctorClinic->clinic_name));
                return false;
            }
        }
        return true;
    }

    /**
     * This method is invoked after deleting a record successfully
     * @param int $id
     * @return void
     */
    protected function _afterDelete($id = 0)
    {
        $doctorTimeBlocks = DoctorScheduleTimeBlocks::model()->findAll('address_id = '.$this->_clinicId);
        if(!empty($doctorTimeBlocks)){
            foreach($doctorTimeBlocks as $doctorTimeBlock){
                DoctorScheduleTimeBlocks::model()->deleteByPk($doctorTimeBlock['id']);
            }
        }
    }
}

