<?php
/**
* AppointmentsComponent
*
* PUBLIC:                   PRIVATE
* -----------               ------------------
* prepareTab
* prepareSubTab
* drawAppointmentsBlock
* drawLoginBlock
* drawFindDoctorsBlock
* drawFooterLinks
* priceFormating
* drawShortcode
* shareLink
* getConfirmLink
*
*
* STATIC
* -------------------------------------------
* init
*
*/

namespace Modules\Appointments\Components;

// Models
use Modules\Appointments\Models\Clinics;
use \Modules\Appointments\Models\Degrees;
use \Modules\Appointments\Models\DoctorSchedules;
use \Modules\Appointments\Models\DoctorScheduleTimeBlocks;
use \Modules\Appointments\Models\DoctorSpecialties;
use \Modules\Appointments\Models\Doctors;
use \Modules\Appointments\Models\Patients;
use \Modules\Appointments\Models\Specialties;
use \Modules\Appointments\Models\Titles;

// Global
use \A,
    \Admins,
    \Bootstrap,
    \CArray,
    \CAuth,
    \CConfig,
    \CCurrency,
    \CLoader,
    \CLocale,
    \CHtml,
    \CWidget,
    \CString,
    \CDebug,
    \CFile,
	\ModulesSettings,
	\Website;


class AppointmentsComponent extends \CComponent{

    const NL = "\n";

    public static function init()
    {
        return parent::init(__CLASS__);
    }

    /**
     * Prepares Appointments module tabs
     * @param string $activeTab
     * @return html
     */
    public static function prepareTab($activeTab = 'info')
    {
        $configModule = CLoader::config('appointments', 'main');
        $multiClinics = (isset($configModule['multiClinics']) && $configModule['multiClinics'] == 'true') ? true : false;

        return CWidget::create('CTabs', array(
            'tabsWrapper'=>array('tag'=>'div', 'class'=>'title'),
            'tabsWrapperInner'=>array('tag'=>'div', 'class'=>'tabs'),
            'contentWrapper'=>array(),
            'contentMessage'=>'',
            'tabs'=>array(
                A::t('appointments', 'Settings') => array('href'=>'modules/settings/code/appointments', 'id'=>'tabSettings', 'content'=>'', 'active'=>false, 'htmlOptions'=>array('class'=>'modules-settings-tab')),
                A::t('appointments', 'Integration') => array('href'=>'integrationWidgets/code', 'id'=>'tabIntegrationWidgets', 'content'=>'', 'active'=>($activeTab == 'integration' ? true : false)),
                A::t('appointments', $multiClinics ? 'Clinics' : 'Clinic Info') => array('href'=>'clinics/manage', 'id'=>'tabClinics', 'content'=>'', 'active'=>($activeTab == 'clinics' ? true : false)),
                A::t('appointments', 'Working Hours') => array('href'=>'workingHours/index', 'id'=>'tabWorkingHours', 'content'=>'', 'active'=>($activeTab == 'workinghours' ? true : false)),
                A::t('appointments', 'Services') => array('href'=>'services/index', 'id'=>'tabServices', 'content'=>'', 'active'=>($activeTab == 'services' ? true : false)),
                A::t('appointments', 'Master Data') => array('href'=>'masterData/index', 'id'=>'tabMasterData', 'content'=>'', 'active'=>($activeTab == 'masterdata' ? true : false)),
                A::t('appointments', 'Doctors') => array('href'=>'doctors/manage', 'id'=>'tabDoctors', 'content'=>'', 'active'=>($activeTab == 'doctors' ? true : false)),
                A::t('appointments', 'Reviews') => array('href'=>'doctorReviews/manage', 'id'=>'tabReviews', 'content'=>'', 'active'=>($activeTab == 'reviews' ? true : false)),
                A::t('appointments', 'Patients') => array('href'=>'patients/manage', 'id'=>'tabPatients', 'content'=>'', 'active'=>($activeTab == 'patients' ? true : false)),
                A::t('appointments', 'Appointments') => array('href'=>'appointments/manage', 'id'=>'tabAppointments', 'content'=>'', 'active'=>($activeTab == 'appointments' ? true : false)),
                A::t('appointments', 'Membership Plans') => array('href'=>'memberships/manage', 'id'=>'tabMembership', 'content'=>'', 'active'=>($activeTab == 'memberships' ? true : false)),
                A::t('appointments', 'Orders') => array('href'=>'orders/index', 'id'=>'tabOrders', 'content'=>'', 'active'=>($activeTab == 'orders' ? true : false)),
                A::t('appointments', 'Statistics') => array('href'=>'statistics/manage', 'id'=>'tabStatistics', 'content'=>'', 'active'=>($activeTab == 'statistics' ? true : false)),
            ),
            'events'=>array(
                //'click'=>array('field'=>$errorField)
            ),
            'return'=>true,
        ));
    }

    /**
     * Prepares Appointments module tabs
     * @param string $parentTab
     * @param string $activeSubTab
     * @return html
     */
    public static function prepareSubTab($parentTab = 'masterdata', $activeSubTab = '', $additionText = '')
    {
        $output = '';
        $activeSubTab = strtolower($activeSubTab);

        $arrPrepareTabs = array(
            'masterdata' => array(
                array('title'=>'specialties','url'=>'masterData/specialtiesManage','text'=>A::t('appointments', 'Specialties')),
                array('title'=>'insurance','url'=>'masterData/insuranceManage','text'=>A::t('appointments', 'Insurance')),
                array('title'=>'visitreasons','url'=>'masterData/visitReasonsManage','text'=>A::t('appointments', 'Visit Reasons')),
                array('title'=>'titles','url'=>'masterData/titlesManage','text'=>A::t('appointments', 'Titles')),
                array('title'=>'degrees','url'=>'masterData/degreesManage','text'=>A::t('appointments', 'Degrees')),
            ),
            'orders' => array(
                array('title'=>'doctors','url'=>'orders/doctorsManage','text'=>A::t('appointments', 'Doctors')),
                //array('title'=>'patients','url'=>'orders/patientsManage','text'=>A::t('appointments', 'Patients')),
            ),
			'doctorreviews' => array(
				array('title'=>'approved','url'=>'doctorReviews/manage/status/approved','text'=>A::t('appointments', 'Approved')),
				array('title'=>'pending','url'=>'doctorReviews/manage/status/pending','text'=>A::t('appointments', 'Pending')),
				array('title'=>'declined','url'=>'doctorReviews/manage/status/declined','text'=>A::t('appointments', 'Declined')),
			),
            'appointments' => array(
				array('title'=>'','url'=>'appointments/manage/status/all','text'=>A::t('appointments', 'All')),
				array('title'=>'reserved','url'=>'appointments/manage/status/reserved','text'=>A::t('appointments', 'Reserved')),
				array('title'=>'verified','url'=>'appointments/manage/status/verified','text'=>A::t('appointments', 'Verified')),
				array('title'=>'canceled','url'=>'appointments/manage/status/canceled','text'=>A::t('appointments', 'Canceled')),
			),
        );

        if(isset($arrPrepareTabs[$parentTab])){
            foreach($arrPrepareTabs[$parentTab] as $tab){
                $output .= '<a class="sub-tab'.($activeSubTab == $tab['title'] ? ' active' : ' previous').'" href="'.$tab['url'].'">'.$tab['text'].'</a>';
                $output .= $activeSubTab == $tab['title'] && !empty($additionText) ? '» <a class="sub-tab active"><b>'.$additionText.'</b></a>&nbsp;' : '';
            }
        }

        return $output;
    }

    /**
     * Draws Doctors Key
     */
    public static function drawShortcode()
    {
        $output = '';

        $doctors = Doctors::model('with_appointments_counter')->findAll(array('order'=>'appointments_count DESC'));
        $result = Degrees::model()->findAll(array(
                'condition' => 'is_active = 1',
                'orderBy'   => 'sort_order ASC')
        );
        if(!empty($result) && is_array($result)){
            foreach($result as $degree){
                $degrees[$degree['id']]['title'] = $degree['title'];
                $degrees[$degree['id']]['full'] = $degree['title'].' ('.$degree['name'].')';
            }
        }
        $specialties = DoctorSpecialties::model()->findAll();
        $doctorSpecialties = CArray::flipByField($specialties, 'doctor_id', true);
        $count = 0;
        if(count($doctors) > 0):
            $output .= CHtml::openTag('div', array('class'=>'cmsms_cc'));
            foreach($doctors as $doctor):
                $output .= CHtml::openTag('div', array('class'=>'one_third'.($count++ == 0 ? ' first_column' : '')));
                $output .= CHtml::openTag('div', array('class'=>'cmsms_our_team_wrap'));
                $output .= CHtml::openTag('div', array('class'=>'cmsms_our_team'));
                $output .= CHtml::openTag('div', array('class'=>'wrap_person'));
                $output .= CHtml::openTag('figure');
                $output .= CHtml::tag('img', array('src'=>'assets/modules/appointments/images/doctors/'.($doctor['avatar'] ?  CHtml::encode($doctor['avatar']) : CHtml::encode($doctor['avatar_by_gender'])), 'class'=>'fullwidth'));
                $output .= CHtml::closeTag('figure');
                $output .= CHtml::openTag('div', array('class'=>'cmsms_team_rollover glow_blue'));
                $output .= CHtml::openTag('a', array('class'=>'cmsms_link', 'href'=>Website::prepareLinkByFormat('appointments', 'profile_link_format', $doctor['id'], DoctorsComponent::getDoctorName($doctor))));
                $output .= CHtml::tag('span');
                $output .= CHtml::closeTag('a');
                $output .= CHtml::closeTag('div');
                $output .= CHtml::closeTag('div');
                $output .= CHtml::openTag('header', array('class'=>'entry-header'));
                $output .= CHtml::openTag('h6');
                $output .= CHtml::tag('a', array('class'=>'cmsms_link', 'href'=>Website::prepareLinkByFormat('appointments', 'profile_link_format', $doctor['id'], DoctorsComponent::getDoctorName($doctor))), (!empty($doctor['title']) ? $doctor['title'] : '').' '.CHtml::encode($doctor['full_name']).(!empty($degrees[$doctor['medical_degree_id']]) ? ', '.$degrees[$doctor['medical_degree_id']]['title'] : ''));
                $output .= CHtml::closeTag('h6');
                $doctorSpecialtiesArray = array();
                if(!empty($doctorSpecialties[$doctor['id']])):
                    foreach($doctorSpecialties[$doctor['id']] as $doctorSpecialty):
                        $doctorSpecialtiesArray[] = $doctorSpecialty['specialty_name'];
                    endforeach;
                endif;
                $output .= CHtml::tag('p', array('class'=>'person_subtitle person_subtitle_h20', 'title' => implode(', ', $doctorSpecialtiesArray)), !empty($doctorSpecialtiesArray[0]) ? $doctorSpecialtiesArray[0] : '');
                $output .= CHtml::closeTag('header');
                $output .= CHtml::closeTag('div');
                $output .= CHtml::closeTag('div');
                $output .= CHtml::closeTag('div');
            endforeach;
            $output .= CHtml::closeTag('div');
        endif;

        return $output;
    }


    /**
     * Draws categories tree menu
     * @param string $params
     * @return html
     */
    public static function drawAppointmentsBlock($params = '')
    {
        $output = '';

        $configModule = \CLoader::config('appointments', 'main');
        $multiClinics = $configModule['multiClinics'];

        $cRequest = A::app()->getRequest();
        $specialtyId = $cRequest->getQuery('specialtyId', 'int', 0);
        $doctorId = $cRequest->getQuery('doctorId', 'int', 0);
		$locationId = $cRequest->getQuery('locationId', 'string', 0);
		$location = $cRequest->getQuery('location', 'string', '');
		$doctorName = $cRequest->getQuery('doctorName', 'string', '');
        $autocompleteMinLength = 3;
        $autocompleteSearchByLocationMinLength = 2;
        $doctorSpecialtiesCount = array();
        $doctorsAllowSearchByName 		= ModulesSettings::model()->param('appointments', 'doctors_allow_search_by_name');
        $doctorsAllowSearchByLocation 	= ModulesSettings::model()->param('appointments', 'doctors_allow_search_by_location');

        //calculate the number of active specialties
        $doctorSpecialties = DoctorSpecialties::model()->findAll();
        if(!empty($doctorSpecialties) && is_array($doctorSpecialties)){
            foreach($doctorSpecialties as $doctorSpecialtyCnt){
                $doctor = Doctors::model()->findByPk($doctorSpecialtyCnt['doctor_id']);
                if($doctor){
					$unixCurrentDay = strtotime(date('Y-m-d'));
					$unixMembershipExpires = strtotime($doctor->membership_expires);
					//Check profile on Active and Removed. Check membership on show in search and expires
					if($doctor->is_active == true && $doctor->is_removed == false && $doctor->membership_show_in_search == true && $unixMembershipExpires >= $unixCurrentDay){
                        if(in_array($doctorSpecialtyCnt['specialty_id'], array_keys($doctorSpecialtiesCount))){
                            $doctorSpecialtiesCount[$doctorSpecialtyCnt['specialty_id']]++;
                        }else{
                            $doctorSpecialtiesCount[$doctorSpecialtyCnt['specialty_id']] = 1;
                        }
                    }
                }
            }
            $specialtyTable = CConfig::get('db.prefix').Specialties::model()->getTableName();
            $specialtyIds = array_keys($doctorSpecialtiesCount);
            $arrSpecialties = Specialties::model()->findAll($specialtyTable.'.id IN ('.implode(', ',$specialtyIds).') AND is_active = 1');
            if(!empty($arrSpecialties) && is_array($arrSpecialties)){
                if($params == 'backend'){
                    if(Admins::hasPrivilege('modules', 'edit') && Admins::hasPrivilege('appointment', 'add')){
                        $output .= '<a href="appointments/findDoctors" class="add-new">'.A::t('appointments', 'All Specialties').'</a>';
                    }

                    $output .= CHtml::openForm('appointments/findDoctors', 'get', array());

                    if($doctorsAllowSearchByName) $output .= CHtml::hiddenField('doctorId', $doctorId, array('id'=>'appointments_form_doctor_id'));
                    if($multiClinics && $doctorsAllowSearchByLocation) $output .= CHtml::hiddenField('locationId', $locationId, array('id'=>'appointments_form_location_id'));

                    $selectSpecialties = array(''=>'-- '.A::t('appointments', 'select').' --');
                    foreach($arrSpecialties as $oneSpecialty){
                        $selectSpecialties[$oneSpecialty['id']] = $oneSpecialty['name'].' ('.$doctorSpecialtiesCount[$oneSpecialty['id']].')';
                    }

                    $output .= CHtml::openTag('div', array('class'=> 'mb20'));
                    $output .= CHtml::tag('h6', array(), A::t('appointments', 'Find a Doctor by Specialty'));
                    $output .= CHtml::dropDownList('specialtyId', $specialtyId, $selectSpecialties, array('id'=>'appointments_form_specialty'));
                    $output .= CHtml::closeTag('div');

                    if($doctorsAllowSearchByName){
                        $output .= CHtml::openTag('div', array('class'=> 'mb20'));
                        $output .= CHtml::tag('h6', array(), A::t('appointments', 'Search by Name'));
                        $output .= CHtml::textField('doctorName', $doctorName, array('id'=>'appointments_form_doctor_name', 'placeholder'=>(APPHP_MODE == 'demo' ? A::te('appointments', 'e.g. John Smith') : '')));
                        $output .= CHtml::closeTag('div');
                    }
                    if($multiClinics && $doctorsAllowSearchByLocation){
                        $output .= CHtml::openTag('div', array('class'=> 'mb20'));
                        $output .= CHtml::tag('h6', array(), A::t('appointments', 'Search by Location'));
                        $output .= CHtml::textField('location', $location, array('id'=>'appointments_form_location', 'placeholder'=>(APPHP_MODE == 'demo' ? A::te('appointments', 'e.g. New York') : '')));
                        $output .= CHtml::closeTag('div');
                    }

                    $output .= CHtml::openTag('div', array('class'=> 'mb20'));
                    $output .= CHtml::submitButton(A::t('appointments', 'Find Doctors'), array());
                    $output .= CHtml::closeTag('div');

                    $output .= CHtml::closeForm();
                    $output .= CHtml::closeTag('div');
                    $output .= CHtml::closeTag('div');
                }elseif($params == 'integration'){
                    $output .= CHtml::openTag('div', array('class'=>''));
                    $output .= CHtml::tag('h3', array('class'=>'title'), A::t('appointments', 'Appointments'));
                    $output .= CHtml::openTag('div', array('class'=>''));
                    $output .= CHtml::openForm('appointments/findDoctors', 'get', array('id'=>'integration-form', 'target'=>'_top'));

                    if($doctorsAllowSearchByName) $output .= CHtml::hiddenField('doctorId', $doctorId, array('id'=>'appointments_form_doctor_id'));
                    if($multiClinics && $doctorsAllowSearchByLocation) $output .= CHtml::hiddenField('locationId', $locationId, array('id'=>'appointments_form_location_id'));

                    $selectSpecialties = array(''=>'-- '.A::t('appointments', 'select').' --');
                    foreach($arrSpecialties as $oneSpecialty){
                        $selectSpecialties[$oneSpecialty['id']] = $oneSpecialty['name'].' ('.$doctorSpecialtiesCount[$oneSpecialty['id']].')';
                    }

                    $classForm = 'margin-bottom-20';
                    $classFormButton = 'margin-bottom-20';

                    $output .= CHtml::openTag('div', array('class'=> $classForm));
                    $output .= CHtml::tag('h6', array(), A::t('appointments', 'Find a Doctor by Specialty'));
                    $output .= CHtml::dropDownList('specialtyId', $specialtyId, $selectSpecialties, array('id'=>'appointments_form_specialty'));
                    $output .= CHtml::closeTag('div');

                    if($doctorsAllowSearchByName){
                        $output .= CHtml::openTag('div', array('class'=> $classForm));
                        $output .= CHtml::tag('h6', array(), A::t('appointments', 'Search by Name'));
                        $output .= CHtml::textField('doctorName', $doctorName, array('id'=>'appointments_form_doctor_name', 'placeholder'=>(APPHP_MODE == 'demo' ? A::te('appointments', 'e.g. John Smith') : '')));
                        $output .= CHtml::closeTag('div');
                    }
                    if($multiClinics && $doctorsAllowSearchByLocation){
                        $output .= CHtml::openTag('div', array('class'=> $classForm));
                        $output .= CHtml::tag('h6', array(), A::t('appointments', 'Search by Location'));
                        $output .= CHtml::textField('location', $location, array('id'=>'appointments_form_location', 'placeholder'=>(APPHP_MODE == 'demo' ? A::te('appointments', 'e.g. New York') : '')));
                        $output .= CHtml::closeTag('div');
                    }

                    $output .= CHtml::openTag('div', array('class'=> $classFormButton));
                    $output .= CHtml::submitButton(A::t('appointments', 'Find Doctors'), array());
                    $output .= CHtml::closeTag('div');

                    $output .= CHtml::closeForm();
                }elseif($params == 'mobile'){
                    $output .= CHtml::openTag('div', array('class'=>'form-agileits fullwidth'));

                    $output .= CHtml::tag('h3', array(), A::t('appointments', 'Find Doctors'));
                    $output .= CHtml::openForm('mobile/doctors', 'get', array('id'=>'integration-form', 'target'=>'_top'));

                    if($doctorsAllowSearchByName) $output .= CHtml::hiddenField('doctorId', $doctorId, array('id'=>'appointments_form_doctor_id'));
                    if($multiClinics && $doctorsAllowSearchByLocation) $output .= CHtml::hiddenField('locationId', $locationId, array('id'=>'appointments_form_location_id'));

                    $selectSpecialties = array(''=>A::t('appointments', 'Find a Doctor by Specialty'));
                    foreach($arrSpecialties as $oneSpecialty){
                        $selectSpecialties[$oneSpecialty['id']] = $oneSpecialty['name'].' ('.$doctorSpecialtiesCount[$oneSpecialty['id']].')';
                    }

                    $output .= CHtml::dropDownList('specialtyId', $specialtyId, $selectSpecialties, array('id'=>'appointments_form_specialty', 'class'=>'form-control name'));


                    if($doctorsAllowSearchByName){
                        $output .= CHtml::textField('doctorName', $doctorName, array('id'=>'appointments_form_doctor_name', 'class'=>'name', 'placeholder'=>(APPHP_MODE == 'demo' ? A::te('appointments', 'e.g. John Smith') : A::t('appointments', 'Search by Name'))));
                    }
                    if($multiClinics && $doctorsAllowSearchByLocation){
                        $output .= CHtml::textField('location', $location, array('id'=>'appointments_form_location', 'class'=>'name', 'placeholder'=>(APPHP_MODE == 'demo' ? A::te('appointments', 'e.g. New York') : A::t('appointments', 'Search by Location'))));
                    }

                    $output .= CHtml::submitButton(A::t('appointments', 'Find Doctors'), array());

                    $output .= CHtml::closeForm();

                    $output .= CHtml::closeTag('div'); /* form-agileits */
                    /*
                    +<div class="form-agileits">
                     +   <h3>Make an appointment</h3>
                        <form action="#" method="post">
                            <input class="name" type="text" name="Patient Name" placeholder="Patient Name" required="">
                            <input type="text" name="Number" placeholder="Number" required="">
                            <input class="name" type="email" name="Email" placeholder="Email" required="">
                            <input id="datepicker1" name="Text" type="text" value="" onfocus="this.value = '';" onblur="if (this.value == '') {this.value = 'mm/dd/yyyy';}" placeholder="mm/dd/yyyy" required="" class="hasDatepicker">
                            <select class="form-control name" placeholder="department">
                                <option>Department</option>
                                <option>Cardiology</option>
                                <option>Ophthalmology</option>
                                <option>Neurology</option>
                                <option>Psychology</option>
                                <option>Dermatology</option>
                            </select>
                            <select class="form-control">
                                <option>Gender</option>
                                <option>Male</option>
                                <option>Female</option>
                            </select>
                            <input type="submit" value="Make an appointment">
                        </form>
                    </div>
                    */
                }else{
                    $output .= CHtml::openTag('div', array('class'=>'side-panel-block'));
                    $output .= CHtml::tag('h3', array('class'=>'title'), A::t('appointments', 'Appointments'));
                    $output .= CHtml::openTag('div', array('class'=>'block-body'));
                    $output .= CHtml::openForm('appointments/findDoctors', 'get', array());

                    if($doctorsAllowSearchByName) $output .= CHtml::hiddenField('doctorId', $doctorId, array('id'=>'appointments_form_doctor_id'));
                    if($multiClinics && $doctorsAllowSearchByLocation) $output .= CHtml::hiddenField('locationId', $locationId, array('id'=>'appointments_form_location_id'));

                    $selectSpecialties = array(''=>'-- '.A::t('appointments', 'select').' --');
                    foreach($arrSpecialties as $oneSpecialty){
                        $selectSpecialties[$oneSpecialty['id']] = $oneSpecialty['name'].' ('.$doctorSpecialtiesCount[$oneSpecialty['id']].')';
                    }

                    if($params == 'gorisontal') $output .= CHtml::openTag('div', array('class'=>'cmsms_cc'));
                    if($params == 'gorisontal'){
                        $output .= CHtml::openTag('div', array('class'=>'five_sixth'));
                    }

                    if($params == 'gorisontal'){
                        if($multiClinics && $doctorsAllowSearchByName && $doctorsAllowSearchByLocation){
                            $classForm = 'one_third';
                        }elseif($doctorsAllowSearchByName || $doctorsAllowSearchByLocation){
                            $classForm = 'one_half';
                        }elseif($doctorsAllowSearchByName && $doctorsAllowSearchByLocation) {
                            $classForm = 'five_sixth';
                        }
                        $classFormButton = 'one_sixth find-button';
                    }else{
                        $classForm = 'margin-bottom-20';
                        $classFormButton = 'margin-bottom-20';
                    }
                    $output .= CHtml::openTag('div', array('class'=> $classForm));
                    $output .= CHtml::tag('h6', array(), A::t('appointments', 'Find a Doctor by Specialty'));
                    $output .= CHtml::dropDownList('specialtyId', $specialtyId, $selectSpecialties, array('id'=>'appointments_form_specialty'));
                    $output .= CHtml::closeTag('div'); /* $classForm */

                    if($doctorsAllowSearchByName){
                        $output .= CHtml::openTag('div', array('class'=> $classForm));
                        $output .= CHtml::tag('h6', array(), A::t('appointments', 'Search by Name'));
                        $output .= CHtml::textField('doctorName', $doctorName, array('id'=>'appointments_form_doctor_name', 'placeholder'=>(APPHP_MODE == 'demo' ? A::te('appointments', 'e.g. John Smith') : '')));
                        $output .= CHtml::closeTag('div'); /* $classForm */
                    }
                    if($multiClinics && $doctorsAllowSearchByLocation){
                        $output .= CHtml::openTag('div', array('class'=> $classForm));
                        $output .= CHtml::tag('h6', array(), A::t('appointments', 'Search by Location'));
                        $output .= CHtml::textField('location', $location, array('id'=>'appointments_form_location', 'placeholder'=>(APPHP_MODE == 'demo' ? A::te('appointments', 'e.g. New York') : '')));
                        $output .= CHtml::closeTag('div'); /* $classForm */
                    }

                    if($params == 'gorisontal') $output .= CHtml::closeTag('div'); /* five_sixth */

                    $output .= CHtml::openTag('div', array('class'=> $classFormButton));
                    $output .= CHtml::submitButton(A::t('appointments', 'Find Doctors'), array());
                    $output .= CHtml::closeTag('div'); /* $classFormButton */

                    if($params == 'gorisontal') $output .= CHtml::closeTag('div'); /* block-body */
                    if($params == 'gorisontal') $output .= CHtml::closeTag('div'); /* cmsms_cc */

                    $output .= CHtml::closeForm();
                }

                A::app()->getClientScript()->registerScript(
                    'autocompleteAppointments',
                    'jQuery("#appointments_form_doctor_name").autocomplete({
                        source: function(request, response){
                            $.ajax({
                                url: "doctors/ajaxGetDoctorNames",
                                global: false,
                                type: "POST",
                                data: ({
                                    '.$cRequest->getCsrfTokenKey().': "'.$cRequest->getCsrfTokenValue().'",
                                    act: "send",
                                    search : jQuery("#appointments_form_doctor_name").val(),
                                }),
                                dataType: "json",
                                async: true,
                                error: function(html){
                                    '.((APPHP_MODE == 'debug') ? 'console.error("AJAX: cannot connect to the server or server response error! Please try again later.");' : '').'
                                },
                                success: function(data){
                                    if(data.length == 0){
                                        jQuery("#appointments_form_doctor_id").val("");
                                        response({label: "'.A::te('core', 'No matches found').'"});
                                    }else{
                                        response($.map(data, function(item){
                                            if(item.label !== undefined){
                                                return {id: item.id, label: item.label, spec: item.spec}
                                            }else{
                                                // Empty search value if nothing found
                                                jQuery("#appointments_form_doctor_id").val("");
                                            }
                                        }));
                                    }
                                }
                            });
                        },
                        minLength: '.(int)$autocompleteMinLength.',
                        select: function(event, ui) {
                            jQuery("#appointments_form_location_id").val("");
                            jQuery("#appointments_form_location").val("");
                            jQuery("#appointments_form_specialty").val(ui.item.spec);
                            jQuery("#appointments_form_doctor_id").val(ui.item.id);
                            if(typeof(ui.item.id) == "undefined"){
                                jQuery("#appointments_form_doctor_name").val("");
                                return false;
                            }
                        }
                    });',
                    4
                );
				A::app()->getClientScript()->registerScript(
					'autocompleteSearchByLocation',
					'jQuery("#appointments_form_location").autocomplete({
                        source: function(request, response){
                            $.ajax({
                                url: "clinics/ajaxGetClinicNames",
                                global: false,
                                type: "POST",
                                data: ({
                                    '.$cRequest->getCsrfTokenKey().': "'.$cRequest->getCsrfTokenValue().'",
                                    act: "send",
                                    search : jQuery("#appointments_form_location").val(),
                                }),
                                dataType: "json",
                                async: true,
                                error: function(html){
                                    '.((APPHP_MODE == 'debug') ? 'console.error("AJAX: cannot connect to the server or server response error! Please try again later.");' : '').'
                                },
                                success: function(data){
                                    if(data.length == 0){
                                        jQuery("#appointments_form_location_id").val("");
                                        response({label: "'.A::te('core', 'No matches found').'"});
                                    }else{
                                        response($.map(data, function(item){
                                            if(item.label !== undefined){
                                                return {id: item.id, label: item.label}
                                            }else{
                                                // Empty search value if nothing found
                                                jQuery("#appointments_form_location_id").val("");
                                            }
                                        }));
                                    }
                                }
                            });
                        },
                        minLength: '.(int)$autocompleteSearchByLocationMinLength.',
                        select: function(event, ui) {
                            jQuery("#appointments_form_specialty").val("");
                            jQuery("#appointments_form_doctor_id").val("");
                            jQuery("#appointments_form_doctor_name").val("");
                            jQuery("#appointments_form_location_id").val(ui.item.id);
                            if(typeof(ui.item.id) == "undefined"){
                                jQuery("#appointments_form_location").val("");
                                return false;
                            }
                        }
                    });',
					4
				);

				A::app()->getClientScript()->registerScript(
					'clearInput',
                    'function clearInput() {
                        
                    };',
					4
				);
				A::app()->getClientScript()->registerScript(
					'clearInput',
					'$(function() {
						$("#appointments_form_specialty").on("change", function() {
							$("#appointments_form_doctor_name").val("");
                            $("#appointments_form_doctor_id").val("");
                            $("#appointments_form_location").val("");
                            $("#appointments_form_location_id").val("");
						})
                    });',
					4
				);
            }
        }

        return $output;
    }

    /**
     * Prepare html for login block
     * @return html
     */
    public static function drawLoginBlock()
    {
        $output = '';

        if(!Patients::isLogin() && !Doctors::isLogin()){
            $patientAllowLogin = ModulesSettings::model()->param('appointments', 'patient_allow_login');
            if($patientAllowLogin){
                $output .= CHtml::link(A::t('appointments', 'Patient Login'), 'patients/login', array('class'=>'patients-login login-link'));
            }

            $doctorAllowLogin = ModulesSettings::model()->param('appointments', 'doctor_allow_login');
            if($doctorAllowLogin){
                $output .= CHtml::link(A::t('appointments', 'Doctor Login'), 'doctors/login', array('class'=>'doctors-login login-link'));
            }
        }elseif(Doctors::isLogin()){
            $output .= CHtml::link(A::t('appointments', 'Dashboard'), 'doctors/dashboard', array('class'=>'doctors-dashboard login-link'));
        }elseif(Patients::isLogin()){
            $output .= CHtml::link(A::t('appointments', 'Dashboard'), 'patients/dashboard', array('class'=>'patients-dashboard login-link'));
        }

        return $output;
    }

    /**
     * Prepare html for find doctors block.
     * @param array $doctors
     * @param array $arrDoctorIds
     * @param array $titles
     * @param array $genders
     * @param array $genders
     * @param array $degrees
     * @return string
     */
    public static function drawFindDoctorsBlock($doctors = array(), $arrDoctorIds = array(), $genders = array(), $degrees = array())
    {
        $arrDoctorSpecialties  	= array();
        $arrDoctorClinicId 	   	= array();
        $openHoursDoctors 	   	= array();
		$showFields 			= false;
        $loggedRole             = CAuth::getLoggedRole();
        $loggedId               = CAuth::getLoggedId();
        $showFieldsForUnregisteredUsers = ModulesSettings::model()->param('appointments', 'show_fields_for_unregistered_users');
		if(!empty($loggedId)){
			$showFields = true;
		}elseif($showFieldsForUnregisteredUsers){
			$showFields = true;
		}

        if(!empty($arrDoctorIds) && !empty($doctors)){
            for($i=0;$i<count($arrDoctorIds);$i++){
                //Search Doctor Specialties
                $specialties = DoctorSpecialties::model()->findAll(array('condition' => 'doctor_id = '.$arrDoctorIds[$i], 'orderBy' => 'sort_order ASC'));
                if(!empty($specialties)){
                    foreach($specialties as $specialty){
                        $arrDoctorSpecialties[$arrDoctorIds[$i]][] = $specialty['specialty_name'];
                    }
                }

                //Search clinics in which the doctor takes
                $timeBlockIds = DoctorScheduleTimeBlocks::model()->findAll(array('condition' => 'doctor_id = '.$arrDoctorIds[$i], 'groupBy'=>'address_id'));
                if(!empty($timeBlockIds)){
                    foreach($timeBlockIds as $timeBlockId){
                        $clinic = Clinics::model()->findByPk($timeBlockId['address_id']);
                        if(!empty($clinic)){
                            if(isset($arrDoctorClinicId[$arrDoctorIds[$i]]['clinic_name'])){
                                continue;
                            }
                            $arrDoctorClinicId[$arrDoctorIds[$i]][$clinic->id]['clinic_name'] = $clinic->clinic_name;
                            $arrDoctorClinicId[$arrDoctorIds[$i]][$clinic->id]['address'] = $clinic->address;
                        }
                    }
                }
                //Search open hours the doctor
                $openHoursDoctor = DoctorScheduleTimeBlocks::getOpenHoursDoctors($arrDoctorIds[$i]);
                if(!empty($openHoursDoctor)){
                    foreach($openHoursDoctor as $key => $openHoursDoctorTmp){
                        $openHoursDoctors[$arrDoctorIds[$i]][$key]['week_day_name'] = $openHoursDoctorTmp['week_day_name'];
                        $openHoursDoctors[$arrDoctorIds[$i]][$key]['time_from'] = $openHoursDoctorTmp['time_from'];
                        $openHoursDoctors[$arrDoctorIds[$i]][$key]['time_to'] = $openHoursDoctorTmp['time_to'];
                    }
                }
            }
        }

        $output = '';
        if(in_array($loggedRole, array('admin', 'owner'))){
            if(!empty($doctors)){
                $output .= '<table>';
                $output .= '<thead>';
                $output .= '<tr>';
                $output .= '<th>'.A::t('appointments', 'Photo').'</th>';
                $output .= '<th>'.A::t('appointments', 'Name').'</th>';
                $output .= '<th>'.A::t('appointments', 'Gender').'</th>';
                $output .= '<th>'.A::t('appointments', 'Phone').'</th>';
                $output .= '<th>'.A::t('appointments', 'Mobile Phone').'</th>';
                $output .= '<th>'.A::t('appointments', 'Specialties').'</th>';
                $output .= '<th>'.A::t('appointments', 'Clinics').'</th>';
                $output .= '<th>'.A::t('appointments', 'Opening Hours').'</th>';
                $output .= '<th></th>';
                $output .= '</tr>';
                $output .= '</thead>';
                $output .= '<tbody>';

                foreach($doctors as $doctor){

                    $clinics = '';
                    if(!empty($arrDoctorClinicId[$doctor['id']])){
                        foreach($arrDoctorClinicId[$doctor['id']] as $clinicId => $doctorClinic){
                            $clinicLink = 'clinics/manage/?id='.CHtml::encode($clinicId).'&but_filter=Filter';
                            $clinics .= '<a href="'.$clinicLink.'">'.CHtml::encode($doctorClinic['clinic_name']).(!empty($doctorClinic['address'])? ', '.CHtml::encode($doctorClinic['address']) : '').'</a><br>';
                        }
                    }


                    $openingHours = '';
                    if(!empty($openHoursDoctors[$doctor['id']])){
                        foreach($openHoursDoctors[$doctor['id']] as $openHoursDoctor){
                            $openingHours .= '<strong>'.$openHoursDoctor['week_day_name'].':</strong>';
                            $openingHours .= $openHoursDoctor['time_from'].' - '.$openHoursDoctor['time_to'].'</br>';
                        }
                    }

                    $output .= '<tr>';
                    $output .= '<td class="left"><img width="30px" height="27px" title="doctor1.jpg" src="assets/modules/appointments/images/doctors/'.$doctor['avatar'].'" alt=""></td>';
                    $output .= '<td class="left"><a href="doctors/manage/?id='.$doctor['id'].'&but_filter=Filter">'.DoctorsComponent::getDoctorName($doctor).'</a></td>';
                    $output .= '<td class="left">'.(!empty($doctor['gender']) ? CHtml::encode($genders[$doctor['gender']]) : '--').'</td>';
                    $output .= '<td class="left">'.(!empty($doctor['work_phone']) ? CHtml::encode($doctor['work_phone']) : '--').'</td>';
                    $output .= '<td class="left">'.(!empty($doctor['work_mobile_phone']) ? CHtml::encode($doctor['work_mobile_phone']) : '--').'</td>';
                    $output .= '<td class="left">'.implode('<br/>', $arrDoctorSpecialties[$doctor['id']]).'</td>';
                    $output .= '<td class="left">'.(!empty($clinics) ? $clinics : '--').'</td>';
                    $output .= '<td class="left">'.(!empty($openingHours) ? $openingHours : '--').'</td>';
                    $output .= '<td class="left">[ <a href="appointments/'.$doctor['id'].'/'.CString::seoString(DoctorsComponent::getDoctorName($doctor)).'">'.A::t('appointments', 'Book Appointment').'</a> ]</td>';
                    $output .= '</tr>';
                }
                $output .= '</tbody>';
                $output .= '</table>';
            }
        }else{
            foreach($doctors as $doctor){
                $output .= '<div class="one_first">';
                $output .= '<div class="one_third">';
                $output .= '<div class="cmsms_our_team_wrap">';
                $output .= '<div class="cmsms_our_team">';
                $output .= '<div class="wrap_person">';
                $output .= '<figure>';
                $output .= '<img width="440" height="440" src="assets/modules/appointments/images/doctors/'.(!empty($doctor['avatar']) ? $doctor['avatar'] : $doctor['avatar_by_gender']).'" class="fullwidth" alt="female-practitioner-s-1">';
                $output .= '</figure>';
                $output .= '<div class="cmsms_team_rollover glow_blue">';
                $output .= '<a href="'.Website::prepareLinkByFormat('appointments', 'profile_link_format', $doctor['id'], DoctorsComponent::getDoctorName($doctor)).'" class="cmsms_link">';
                $output .= '<span></span>';
                $output .= '</a>';
                $output .= '</div>';
                $output .= '</div>';
                $output .= '<header class="entry-header">';
                $output .= '<div>';
                $output .= '<h6 class="person_title tac">';
                $output .= '<a href="'.Website::prepareLinkByFormat('appointments', 'profile_link_format', $doctor['id'], DoctorsComponent::getDoctorName($doctor)).'">'.DoctorsComponent::getDoctorName($doctor).'</a>';
                $output .= '</h6>';
                $output .= '</div>';
                $output .= '</header>';
                $output .= '<div class="aligncenter margin-top-20">';
                $output .= '<a class="btn-book-appointment button_small" href="patients/addMyAppointment/doctorId/'.$doctor['id'].'/seoLink/'.CString::seoString(DoctorsComponent::getDoctorName($doctor)).'">&#128197; &nbsp;'.A::t('appointments', 'Book Appointment').'</a>';
                $output .= '</div>';
                $output .= '</div>';
                $output .= '</div>';
                $output .= '</div>';
                $output .= '<div class="two_third">';
                $output .= '<div class="cmsms_features_item">';
                $output .= '<span class="cmsms_features_item_title"><strong>'.A::t('appointments', 'Name').':</strong></span>';
                $output .= '<span class="cmsms_features_item_desc">'.DoctorsComponent::getDoctorName($doctor).'</span>';
                $output .= '</div>';
                if(isset($genders[$doctor['gender']])){
                    $output .= '<div class="cmsms_features_item">';
                    $output .= '<span class="cmsms_features_item_title"><strong>'.A::t('appointments', 'Gender').':</strong></span>';
                    $output .= '<span class="cmsms_features_item_desc">'.CHtml::encode($genders[$doctor['gender']]).'</span>';
                    $output .= '</div>';
                }
                if(!empty($doctor['work_phone']) && $showFields){
                    $output .= '<div class="cmsms_features_item">';
                    $output .= '<span class="cmsms_features_item_title"><strong>'.A::t('appointments', 'Phone').':</strong></span>';
                    $output .= '<span class="cmsms_features_item_desc">'.CHtml::encode($doctor['work_phone']).'</span>';
                    $output .= '</div>';
                }
                if(!empty($doctor['work_mobile_phone']) && $showFields){
                    $output .= '<div class="cmsms_features_item">';
                    $output .= '<span class="cmsms_features_item_title"><strong>'.A::t('appointments', 'Mobile Phone').':</strong></span>';
                    $output .= '<span class="cmsms_features_item_desc">'.CHtml::encode($doctor['work_mobile_phone']).'</span>';
                    $output .= '</div>';
                }
                if(isset($degrees[$doctor['medical_degree_id']])){
                    $output .= '<div class="cmsms_features_item">';
                    $output .= '<span class="cmsms_features_item_title"><strong>'.A::t('appointments', 'Degree').':</strong></span>';
                    $output .= '<span class="cmsms_features_item_desc"> '.CHtml::encode($degrees[$doctor['medical_degree_id']]['full']).'</span>';
                    $output .= '</div>';
                }
                if(!empty($arrDoctorSpecialties[$doctor['id']])){
                    $output .= '<div class="cmsms_features_item">';
                    $output .= '<span class="cmsms_features_item_title"><strong>'.A::t('appointments', 'Specialties').':</strong></span>';
                    $output .= '<span class="cmsms_features_item_desc">  '.implode('<br/>', $arrDoctorSpecialties[$doctor['id']]).'</span>';
                    $output .= '</div>';
                }
                if(!empty($arrDoctorClinicId[$doctor['id']]) && $showFields){
                    $countClinic = count($arrDoctorClinicId[$doctor['id']]);
                    $output .= '<div class="cmsms_features_item">';
                    $output .= '<span class="cmsms_features_item_title"><strong>'.A::t('appointments', $countClinic > 1 ? 'Clinics' : 'Clinic').':</strong></span>';
                    $output .= '<span class="cmsms_features_item_desc">';
                    foreach($arrDoctorClinicId[$doctor['id']] as $clinicId => $doctorClinic){
                        $clinicLink = 'clinics/'.CHtml::encode($clinicId).'/'.\CString::seoString($doctorClinic['clinic_name']);
                        $output .= '<a href="'.$clinicLink.'" class="link-find-doctor-by-specialty" data-id="1">'.CHtml::encode($doctorClinic['clinic_name']).(!empty($doctorClinic['address'])? ', '.CHtml::encode($doctorClinic['address']) : '').'</a><br>';
                    }
                    $output .= '</span>';
                    $output .= '</div>';
                }
                if(!empty($openHoursDoctors[$doctor['id']])){
                    $output .= '<div class="cmsms_features_item">';
                    $output .= '<span class="cmsms_features_item_title"><strong>'.A::t('appointments', 'Opening Hours').':</strong></span>';
                    $output .= '</div>';
                    foreach($openHoursDoctors[$doctor['id']] as $openHoursDoctor){
                        $output .= '<div class="cmsms_features_item">';
                        $output .= '<span class="cmsms_features_item_title">'.$openHoursDoctor['week_day_name'].':</span>';
                        $output .= '<span class="cmsms_features_item_desc">  '.$openHoursDoctor['time_from'].' - '.$openHoursDoctor['time_to'].'</span>';
                        $output .= '</div>';
                    }
                }
                $output .= '</div>';
                $output .= '<hr class="hr-style">';
                $output .= '</div>';
            }   
        }

        return $output;
    }

    /**
     * Prepare liks for footer block
     */
    public static function drawFooterLinks()
    {
        $output = '';

        if(!Doctors::isLogin() && !Patients::isLogin()){            
            $output .= CHtml::link(A::t('appointments', 'Doctor Login'), 'doctors/login', array('class'=>'doctors-login footer-login-link'));
            $output .= ' | '.CHtml::link(A::t('appointments', 'Patient Login'), 'patients/login', array('class'=>'patients-login footer-login-link'));
        }elseif(Doctors::isLogin()){
            $output .= CHtml::link(A::t('appointments', 'Dashboard'), 'doctors/dashboard', array('class'=>'doctors-dashboard footer-login-link'));
            $output .= ' | '.CHtml::link(A::t('appointments', 'Logout'), 'doctors/logout', array('class'=>'doctors-logout footer-login-link'));
        }elseif(Patients::isLogin()){
            $output .= CHtml::link(A::t('appointments', 'Dashboard'), 'patients/dashboard', array('class'=>'patients-dashboard footer-login-link'));
            $output .= ' | '.CHtml::link(A::t('appointments', 'Logout'), 'patients/logout', array('class'=>'patients-logout footer-login-link'));
        }
		
        if(in_array(APPHP_MODE, array('debug', 'demo')) && !CAuth::isLoggedIn()){
			if(!empty($output)){
				$output .= ' | ';
			}
            $output .= CHtml::link(A::t('appointments', 'Admin Login'), 'backend/login', array('class'=>'admin-login footer-login-link'));
        }
        
        return $output;
    }

    /**
     * Draws share link
     * @param string $url
     * @param string $name
     * @return html
     */
    public static function shareLink($url, $name)
    {
        $output = '';
        $output .= CHtml::openTag('div', array('class'=>'one_half first_column'));
        $output .= CHtml::openTag('aside', array('class'=>'share_posts'));
            $output .= CHtml::openTag('div', array('class'=>'fl'));
                $output .= CHtml::openTag('a', array('href'=> 'http://twitter.com/share?text='.CHtml::encode($name).'&url='.CHtml::encode($url), 'onclick'=> 'window.open(this.href, this.title, \'toolbar=0, status=0, width=548, height=325\'); return false', 'target'=>'_parent'));
                    $output .= CHtml::tag('img', array('src'=>'images/social_networks/twitter.png', 'alt' => 'Twitter'));
                $output .= CHtml::closeTag('a').self::NL; /* a */
            $output .= CHtml::closeTag('div').self::NL; /* fl */

            $output .= CHtml::openTag('div', array('class'=>'fl'));
                $output .= CHtml::openTag('a', array('href'=> 'https://plus.google.com/share?url='.CHtml::encode($url), 'onclick'=> 'window.open(this.href, this.title, \'toolbar = 0, status = 0, width = 548, height = 325\'); return false', 'target'=>'_parent'));
                    $output .= CHtml::tag('img', array('src'=>'images/social_networks/google-plus.png', 'alt' => 'Google+'));
                $output .= CHtml::closeTag('a').self::NL; /* a */
            $output .= CHtml::closeTag('div').self::NL; /* fl */

            $output .= CHtml::openTag('div', array('class'=>'fl'));
                $output .= CHtml::openTag('a', array('href'=> 'http://www.facebook.com/sharer.php?s=100&p[url]='.CHtml::encode($url).'&p[title]='.CHtml::encode($name), 'onclick'=> 'window.open(this.href, this.title, \'toolbar = 0, status = 0, width = 548, height = 325\'); return false', 'title'=>'Поделиться ссылкой на Фейсбук', 'target'=>'_parent'));
                    $output .= CHtml::tag('img', array('src'=>'images/social_networks/facebook.png', 'alt' => 'Facebook+'));
                $output .= CHtml::closeTag('a').self::NL; /* a */
            $output .= CHtml::closeTag('div').self::NL; /* fl */

            $output .= CHtml::tag('div', array('class' => 'cl'), '');
            $output .= CHtml::openTag('a', array('href'=> 'javascript:void(0);', 'class' => 'cmsms_share button_small'));
                $output .= CHtml::tag('span', array(), A::t('appointments', 'More sharing options'));
            $output .= CHtml::closeTag('a').self::NL; /* a */
            $output .= CHtml::tag('div', array('class' => 'cmsms_social cl'), '');
            $output .= CHtml::tag('br', array(), '');
            $output .= CHtml::tag('br', array(), '');

        $output .= CHtml::closeTag('aside').self::NL; /* aside */
        $output .= CHtml::closeTag('div').self::NL; /* aside */

        return $output;
    }

    /**
     * Price formatting
     */
    public function priceFormating($record, $params)
    {
        $output = '';

        $fieldName = isset($params['field_name']) ? $params['field_name'] : 'price';

        if(isset($record[$fieldName])){
            $output = CCurrency::format($record[$fieldName]);
        }

        return $output;
    }

    /**
     * Get Confirm Link
     * @param array $record
     * @return string
     */
    public function getConfirmLink($record = array())
    {
        $output = '';

        if($record['status'] == 0){
            $output = '[ <a href="doctors/confirmAppointment/id/'.$record['id'].'" onclick="return onConfirmRecord(this);">'.A::t('appointments', 'Confirm').'</a> ]';
        }

        return $output;
    }

    /**
     * Get Confirm Link
     * @param array $record
     * @return string
     */
    public function getAdminChangeLink($record = array())
    {
        $output = '';

        $currentDate = CLocale::date('Y-m-d');
        $currentTime = CLocale::date('H:i:s');

        if(($record['appointment_date'] > $currentDate) || (($record['appointment_date'] == $currentDate) && ($record['appointment_time'] >= $currentTime))){
            $output = '[ <a href="appointments/changeAppointment/id/'.$record['id'].'" onclick="return onConfirmRecord(this);">'.A::t('appointments', 'Change').'</a> ]';
        }

        return $output;
    }

    /**
     * Get Doctor IDs by specialty ID
     * @param int $specialtyId
     * @return array
     */
    public static function getDoctorIdsBySpecialty($specialtyId)
    {
        $arrOutput = array();

        $tableDoctorSpecialties = CConfig::get('db.prefix').DoctorSpecialties::model()->getTableName();
        $arrDoctorSpecialties = DoctorSpecialties::model()->findAll($tableDoctorSpecialties.'.specialty_id = :specialty_id', array(':specialty_id'=>$specialtyId));

        if(!empty($arrDoctorSpecialties) && is_array($arrDoctorSpecialties)){
            foreach($arrDoctorSpecialties as $doctorSpecialty){
                if(!in_array($doctorSpecialty['doctor_id'], $arrOutput)){
                    $arrOutput[] = (int)$doctorSpecialty['doctor_id'];
                }
            }
        }

        return $arrOutput;
    }

    /**
     * Get Doctor IDs by Clinic ID
     * @param int $locationId
     * @return array
     */
    public static function getDoctorIdsByLocation($locationId)
    {
        $arrOutput = array();

        $tableDoctorScheduleTimeBlocks = CConfig::get('db.prefix').DoctorScheduleTimeBlocks::model()->getTableName();
        $arrDoctorScheduleTimeBlocks = DoctorScheduleTimeBlocks::model()->findAll($tableDoctorScheduleTimeBlocks.'.address_id = :address_id', array(':address_id'=>$locationId));

        if(!empty($arrDoctorScheduleTimeBlocks) && is_array($arrDoctorScheduleTimeBlocks)){
            foreach($arrDoctorScheduleTimeBlocks as $doctorScheduleTimeBlocks){
                if(!in_array($doctorScheduleTimeBlocks['doctor_id'], $arrOutput)){
                    $arrOutput[] = (int)$doctorScheduleTimeBlocks['doctor_id'];
                }
            }
        }

        return $arrOutput;
    }
}