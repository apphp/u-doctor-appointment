<?php
/**
 * WorkingHours model
 *
 * PUBLIC:                PROTECTED               PRIVATE
 * ---------------        ---------------         ---------------
 * __construct
 *
 * STATIC:
 * ---------------------------------------------------------------
 * model
 *
 */

namespace Modules\Appointments\Models;

// Framework
use \A,
    \CConfig,
    \CDebug,
    \CHash,
    \CActiveRecord,
    \CAuth;

// Application
use \Accounts,
    \Website,
    \LocalTime,
    \ModulesSettings;

class WorkingHours extends CActiveRecord
{

    /** @var string */    
    protected $_table = 'appt_working_hours';
    
    private $isError = false;
    
    /**
	 * Class default constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Returns the static model of the specified AR class
     * @return Doctors
     */
    public static function model()
    {
        return parent::model(__CLASS__);
    }

}
