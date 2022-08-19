<?php
/**
 * Appointments model
 *
 * PUBLIC:                 PROTECTED                  PRIVATE
 * ---------------         ---------------            ---------------
 * __construct             _relations
 * 						   _customFields
 *                         _beforeDelete
 *
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

class DoctorReviews extends CActiveRecord
{

    /** @var string */
    protected $_table = 'appt_doctor_reviews';
    protected $_tableDoctors = 'appt_doctors';

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
			'doctor_id' => array(
				self::HAS_MANY,
				$this->_tableDoctors,
				'id',
				'condition'=>"",
				'joinType'=>self::LEFT_OUTER_JOIN,
				'fields'=>array('doctor_first_name', 'doctor_middle_name', 'doctor_last_name',)
			),
		);
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
			"CONCAT(".CConfig::get('db.prefix').$this->_tableDoctors.".doctor_first_name, ' ', ".CConfig::get('db.prefix').$this->_tableDoctors.".doctor_middle_name, ' ', ".CConfig::get('db.prefix').$this->_tableDoctors.".doctor_last_name)" => 'doctor_name'
		);

		return $fields;
	}
}

