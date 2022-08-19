<?php
/**
 * Appointments model
 *
 * PUBLIC:                 PROTECTED                  PRIVATE
 * ---------------         ---------------            ---------------
 * __construct             _relations
 *                         _afterDelete
 * STATIC:
 * ---------------------------------------------------------------
 * model
 *
 */

namespace Modules\Appointments\Models;

// Framework
use \A,
    \Bootstrap,
    \CActiveRecord,
    \CConfig,
    \Website,
    \LocalTime,
	\ModulesSettings;

class Memberships extends CActiveRecord
{

    /** @var string */
    protected $_table = 'appt_membership_plans';
	protected $_tableDoctors = 'appt_doctors';
	protected $_tableAccount = 'accounts';
	
    /** @var string */
    protected $_tableTranslation = 'appt_membership_plans_translations';

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
     * This method is invoked after saving a record successfully
     * @param string $id
     * @return void
     */
    protected function _afterSave($id = 0)
    {
        $this->_isError = false;

        // if this group is default - remove default flag in all other languages
        if($this->is_default){

            if(!$this->_db->update($this->_table, array('is_default'=>0), 'id != :id', array(':id'=>$id))){
                $this->_isError = true;
            }
        }

        // Update features plan for all doctors if update_doctor_features = true
        if($this->update_doctor_features){
            $updateField = array(
                'membership_images_count'       => $this->images_count,
                'membership_clinics_count'      => $this->clinics_count,
                'membership_schedules_count'    => $this->schedules_count,
                'membership_specialties_count'  => $this->specialties_count,
                'membership_show_in_search'     => $this->show_in_search,
                'membership_enable_reviews'     => $this->enable_reviews,
            );
            $this->_db->update(Doctors::model()->getTableName(), $updateField, 'membership_plan_id = :membership_plan_id', array(':membership_plan_id'=>$this->id));
            $this->updateByPk($id, array('update_doctor_features'=>false));
        }
    }

    /**
     * This method is invoked after deleting a record successfully
     * @param string $pk
     * @return void
     */
    protected function _afterDelete($pk = '')
    {
        $this->_isError = false;
        // delete country names from translation table
        if(false === $this->_db->delete($this->_tableTranslation, 'membership_plan_id = :membership_plan_id', array(':membership_plan_id'=>$pk))){
            $this->_isError = true;
        }
    }

    /**
     * Defines relations between different tables in database and current $_table
     */
    protected function _relations()
    {
        return array(
            'id' => array(
                self::HAS_MANY,
                $this->_tableTranslation,
                'membership_plan_id',
                'condition'=>"`language_code` = '".A::app()->getLanguage()."'",
                'joinType'=>self::LEFT_OUTER_JOIN,
                'fields'=>array('name', 'description')
            ),
        );
    }
}
