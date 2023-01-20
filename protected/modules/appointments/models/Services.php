<?php
/**
 * Services model
 *
 * PUBLIC:                  PROTECTED                   PRIVATE
 * ---------------          ---------------             ---------------
 * __construct              _relations                  _clearCache  
 *                          _afterSave
 * STATIC:                  _afterDelete
 * model
 * search
 *
 */

namespace Modules\Appointments\Models;

// Framework
use \A,
    \CActiveRecord,
    \CConfig,
    \CFile;

class Services extends CActiveRecord
{

    /** @var string */
    protected $_table = 'appt_services';
    /** @var string */
    protected $_tableTranslation = 'appt_services_translations';

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
                'service_id',
                'condition'=>"`language_code` = '".A::app()->getLanguage()."'",
                'joinType'=>self::LEFT_OUTER_JOIN,
                'fields'=>array('name', 'description', 'tags')
            ),
        );
    }
    
	/**
	 * This method is invoked after saving a record successfully
	 * @param int $pk
	 */
	protected function _afterSave($pk = '')
	{
        $this->_clearCache();
	}
    
	/**
	 * This method is invoked after deleting a record successfully
	 * @param int $pk
	 */
	protected function _afterDelete($pk = '')
	{
        // $pk - key used for deleting operation
		$this->_clearCache();
	}
    
	/**
	 * Clear cache files
	 */
	protected function _clearCache()
	{
		if(CConfig::get('cache.enable')){
			// Delete cache file after BO updates
			$cacheFile = CConfig::get('cache.path').md5('services-findall-12').'.cch';
			CFile::deleteFile($cacheFile);
		}
	}

	/**
	 * Performs search in doctors
	 * @param string $keywords
	 * @param mixed $itemsCount
	 * @return array array('0'=>array(doctors), '1'=>total)
	 */	
	public function search($keywords = '', $itemsCount = 10)
	{
		$result = array();

		if($keywords !== ''){

			$limit = !empty($itemsCount) ? '0, '.(int)$itemsCount : '';
			$condition = CConfig::get('db.prefix').$this->_table.'.is_active = 1 AND ('.
				CConfig::get('db.prefix').$this->_tableTranslation.'.name LIKE :keywords OR '.
				CConfig::get('db.prefix').$this->_tableTranslation.'.tags LIKE :keywords OR '.
				CConfig::get('db.prefix').$this->_tableTranslation.'.description LIKE :keywords)';

			// Count total items in result
			$total = $this->count(array('condition'=>$condition), array(':keywords'=>'%'.$keywords.'%'));

			// Prepare doctors result
			$services = $this->findAll(array('condition'=>$condition, 'limit' => $limit), array(':keywords'=>'%'.$keywords.'%'));
			foreach($services as $key => $val){
				$result[0][] = array(
					'title' 		=> $val['name'],
					'intro_image'	=> (!empty($val['image_file']) ? '<img class="search-image" src="assets/modules/appointments/images/services/'.$val['image_file'].'" alt="'.$val['name'].'" />' : ''),
					'content' 		=> $val['description'],
					'link' 			=> '/services/view/'.$val['id']
				);
			}

			$result[1] = $total;
		}

		return $result;
	}
}
