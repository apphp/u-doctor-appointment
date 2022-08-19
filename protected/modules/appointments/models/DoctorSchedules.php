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
    \Bootstrap,
	\CTime,
	\CConfig,
    \CActiveRecord;

// Application

class DoctorSchedules extends CActiveRecord
{

    /** @var string */
    protected $_table = 'appt_doctor_schedules';
    /** @var string */
    protected $_tableTimeblocks = 'appt_doctor_schedule_timeblocks';
    /* @var string (simple(default)|timeblocks) */
    private $_typeRelations = 'simple';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Returns the static model of the specified AR class
     * @return DoctorSchedules
     */
    public static function model($relationType = '')
    {
        $model = parent::model(__CLASS__);

        // Set relations type
        if(empty($relationType)){
            $model->_typeRelations = 'simple';
        }elseif(in_array($relationType, array('simple', 'timeblocks'))){
            $model->_typeRelations = $relationType;
        }

        return $model;
    }

    /**
     * Defines relations between different tables in database and current $_table
     * @return array
     */
    protected function _relations()
    {
        $result = array();
        if($this->_typeRelations == 'timeblocks'){
            $result[] = array(
                self::HAS_MANY,
                $this->_tableTimeblocks,
                'schedule_id',
                'parent_key'=>'id',
                'condition'=>'',
                'joinType'=>self::LEFT_OUTER_JOIN,
                'fields'=>array('week_day', 'time_from', 'time_to', 'time_slots')
            );
        }

        return $result;
    }

	/**
	 * This method is invoked before saving a record
	 * @param int $id
	 * @return bool
	 */
	protected function _beforeSave($id = 0)
	{
		//Check the schedule for replays.
		$prefix = CConfig::get('db.prefix').$this->_table;
		$result = $this->count(array(
			'condition'=> $prefix.'.id != '.(int)$this->getPrimaryKey().' AND
                    '.$prefix.'.doctor_id = :doctor_id AND (
                    	('.$prefix.'.date_from <= :date_from AND '.$prefix.'.date_to > :date_from) OR
                    	('.$prefix.'.date_from < :date_to AND '.$prefix.'.date_to >= :date_to) OR
                    	('.$prefix.'.date_from >= :date_from AND '.$prefix.'.date_to <= :date_to)
                    )',
		),
			array(
				':doctor_id'=>$this->doctor_id,
				':date_from'=>$this->date_from,
				':date_to'=>$this->date_to,
			)
		);

		if(!empty($result)){
			$this->_error = true;
			$this->_errorMessage = A::t('appointments', 'This period (fully or partially) is already chosen for selected doctor! Please re-enter.');

			return false;
		}

		return true;
	}
}

