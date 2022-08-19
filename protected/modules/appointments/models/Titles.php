<?php
/**
 * Appointments model
 *
 * PUBLIC:                 PROTECTED                  PRIVATE
 * ---------------         ---------------            ---------------
 * __construct             _relations
 * 						   _clearCache
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
	\CConfig,
	\Cfile;

class Titles extends CActiveRecord
{

    /** @var string */
    protected $_table = 'appt_titles';
    /** @var string */
    protected $_tableTranslation = 'appt_title_translations';

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
	 */
	protected function _afterSave($id = 0)
	{
		$this->_clearCache();
	}

	/**
	 * This method is invoked after deleting a record successfully
	 * @param string $id
	 */
	protected function _afterDelete($id = 0)
	{
		$this->_clearCache();
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
                'title_id',
                'condition'=>"`language_code` = '".A::app()->getLanguage()."'",
                'joinType'=>self::LEFT_OUTER_JOIN,
                'fields'=>array('title')
            ),
        );
    }

    /**
     * Get all active titles
     * @return array
     */
    public static function getActiveTitles()
    {
        static $titles = array();

        if(empty($titles)){
            $result = Titles::model()->findAll(array('is_active = 1', 'orderBy' => 'sort_order ASC'), array(), 'titles-findall-active');
            if(!empty($result) && is_array($result)){
                foreach($result as $title){
                    $titles[$title['id']] = $title['title'];
                }
            }
        }

        return $titles;
    }

	/**
	 * Clear cache files
	 */
	protected function _clearCache()
	{
		if(CConfig::get('cache.enable')){
			// Delete cache file after BO updates
			$cacheFile = CConfig::get('cache.path').md5('titles-findall-active').'.cch';
			CFile::deleteFile($cacheFile);
		}
	}
}
