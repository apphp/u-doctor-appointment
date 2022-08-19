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
 *
 */

namespace Modules\Appointments\Models;

// Framework
use \A,
    \CActiveRecord;

// Application

class DoctorSpecialties extends CActiveRecord
{

    /** @var string */
    protected $_table = 'appt_doctor_specialties';
    /** @var string */
    protected $_tableSpecialties = 'appt_specialty_translations';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Returns the static model of the specified AR class
     * @return DoctorSpecialties
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
            'specialty_id' => array(
                self::HAS_MANY,
                $this->_tableSpecialties,
                'specialty_id',
                'condition'=>"`language_code` = '".A::app()->getLanguage()."'",
                'joinType'=>self::LEFT_OUTER_JOIN,
                'fields'=>array('name'=>'specialty_name', 'description'=>'specialty_description')
            ),
        );
    }

    /**
     * This method is invoked after saving a record successfully
     * @param string $pk
     */
    protected function _beforeSave($id = 0)
    {
        // If this specialty is default - remove this flag from all other specialty for doctor
		$countDoctorSpecialties = $this::model()->count('doctor_id = '.$this->doctor_id);
		if(!$countDoctorSpecialties){
			$this->is_default = true;
		}

		return true;
    }

    /**
     * This method is invoked after saving a record successfully
     * @param string $pk
     */
    protected function _afterSave($id = 0)
    {
        $this->_isError = false;

        // If this specialty is default - remove this flag from all other specialty for doctor
        if($this->is_default){
            if(!$this->_db->update($this->_table, array('is_default'=>0), 'doctor_id = :doctor_id AND id != :id', array(':id'=>$id, ':doctor_id'=>$this->doctor_id))){
                $this->_isError = true;
            }
        }
    }
}

