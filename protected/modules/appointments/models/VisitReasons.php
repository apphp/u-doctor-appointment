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

class VisitReasons extends CActiveRecord
{

    /** @var string */
    protected $_table = 'appt_visit_reasons';
    /** @var string */
    protected $_tableTranslation = 'appt_visit_reason_translations';

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
            'id' => array(
                self::HAS_MANY,
                $this->_tableTranslation,
                'reason_id',
                'condition'=>"`language_code` = '".A::app()->getLanguage()."'",
                'joinType'=>self::LEFT_OUTER_JOIN,
                'fields'=>array('name', 'description')
            ),
        );
    }
}
