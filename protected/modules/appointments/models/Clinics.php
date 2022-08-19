<?php
/**
 * Appointments model
 *
 * PUBLIC:                 	PROTECTED                  PRIVATE
 * ---------------         	---------------            ---------------
 * __construct             	_relations
 * getTableTranslationName 	_afterDelete
 * model					_afterSave
 * getError
 *
 */

namespace Modules\Appointments\Models;

// Framework
use \A;

class Clinics extends \CActiveRecord
{

    /** @var string */
    protected $_table = 'appt_clinics';
    protected $_tableTranslation = 'appt_clinic_translations';
    protected $_tableWorkingHours = 'appt_working_hours';

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
     * Returns the tableTranslation name value
     * @param bool $usePrefix
     * @return string
     */
    public function getTableTranslationName($usePrefix = false)
    {
        return ($usePrefix ? $this->_dbPrefix : '').$this->_tableTranslation;
    }

    /**
     * Defines relations between different tables in database and current $_table
     */
    protected function _relations()
    {
		return array(
			'id' => array(
				self::HAS_ONE,
				$this->_tableTranslation,
				'clinic_id',
				'condition'=>"`language_code` = '".A::app()->getLanguage()."'",
				'joinType'=>self::LEFT_OUTER_JOIN,
				'fields'=>array(
					'name'=>'clinic_name',
					'address'=>'address',
					'description'=>'description',
				)
			),
		);
    }

	/**
	 * This method is invoked after saving a record successfully
	 * @param string $pk
	 */
	protected function _afterSave($pk = 0)
	{
        $this->_isError = false;

        // if this group is default - remove default flag in all other languages
        if($this->is_default){

            if(!$this->_db->update($this->_table, array('is_default'=>0), 'id != :id', array(':id'=>$pk))){
                $this->_isError = true;
            }
        }
        if($this->isNewRecord()){
            for($i=1;$i<=7;$i++){
                $this->_db->insert($this->_tableWorkingHours, array('clinic_id'=> $pk, 'week_day'=>$i, 'is_day_off'=>1));
            }
        }
	}

	/** 
	 * Returns boolean that indicates if the last operation was successfull
	 * @return boolean
	 */
	public function getError()
	{
		return $this->_isError;
	}
	
	/**
	 * This method is invoked after deleting a record successfully
	 * @param string $pk
	 */
	protected function _afterDelete($pk = 0)
	{
		$this->_db->delete($this->_tableWorkingHours, 'clinic_id = '.$pk);
		$this->_db->delete($this->_tableTranslation, 'clinic_id = '.$pk);
	}

}
