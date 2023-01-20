<?php
    $this->_activeMenu = 'doctors/manage';
    $this->_breadCrumbs = array(
        array('label'=>A::t('appointments', 'Modules'), 'url'=>'modules/'),
        array('label'=>A::t('appointments', 'Doctor Appointments'), 'url'=>'modules/settings/code/appointments'),
        array('label'=>A::t('appointments', 'Doctors Management'), 'url'=>'doctors/manage'),
        array('label'=>A::t('appointments', 'Edit Doctor')),
    );

    // register module javascript
    A::app()->getClientScript()->registerScriptFile('assets/modules/appointments/js/appointments.js', 2);

    $formName = 'frmDoctorEdit';

?>

<h1><?= A::t('appointments', 'Doctors Management'); ?></h1>

<div class="bloc">
    <?= $tabs; ?>
    <div class="sub-title"><?= A::t('appointments', 'Edit Doctor'); ?></div>
    <div class="content">
    <?php
        $fields = array();

        $fields['separatorPersonal'] = array();
        $fields['separatorPersonal']['separatorInfo'] = array('legend'=>A::t('appointments', 'Personal Information'));
        $fields['separatorPersonal']['title_id']      = array('type'=>'select', 'title'=>A::t('appointments', 'Title'), 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'set', 'source'=>array_keys($titles)), 'data'=>$titles, 'emptyOption'=>true, 'emptyValue'=>'- '.A::t('appointments', 'select').' -', 'htmlOptions'=>array());
        $fields['separatorPersonal']['doctor_first_name']    = array('type'=>'textbox', 'title'=>A::t('appointments', 'First Name'), 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'text', 'maxLength'=>32), 'htmlOptions'=>array('maxlength'=>'32'));
        $fields['separatorPersonal']['doctor_middle_name']   = array('type'=>'textbox', 'title'=>A::t('appointments', 'Middle Name'), 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'text', 'maxLength'=>32), 'htmlOptions'=>array('maxlength'=>'32'));
        $fields['separatorPersonal']['doctor_last_name']     = array('type'=>'textbox', 'title'=>A::t('appointments', 'Last Name'), 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'text', 'maxLength'=>32), 'htmlOptions'=>array('maxlength'=>'32'));
        $fields['separatorPersonal']['gender']        = array('type'=>'select', 'title'=>A::t('appointments', 'Gender'), 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'set', 'source'=>array_keys($genders)), 'data'=>$genders, 'emptyOption'=>true, 'emptyValue'=>'- '.A::t('appointments', 'select').' -', 'htmlOptions'=>array());
        $fields['separatorPersonal']['birth_date']    = array('type'=>'datetime', 'title'=>A::t('appointments', 'Birth Date'), 'default'=>'', 'tooltip'=>'', 'validation'=>array('required'=>false, 'type'=>'date', 'maxLength'=>'10', 'minValue'=>(date('Y')-110).'-00-00', 'maxValue'=>date('Y-m-d')), 'htmlOptions'=>array('maxLength'=>'10', 'class'=>'medium'), 'viewType'=>'date', 'dateFormat'=>'yy-mm-dd', 'definedValues'=>array(), 'buttonTrigger'=>true, 'maxDate'=>'1', 'yearRange'=>'-100:+0');
        $fields['separatorPersonal']['avatar']        = array(
            'type'              => 'imageUpload',
            'title'             => A::t('appointments', 'Avatar'),
            'tooltip'           => '',
            'default'           => '',
            'validation'        => array('required'=>false, 'type'=>'image', 'targetPath'=>'assets/modules/appointments/images/doctors/', 'maxSize'=>'500k', 'maxWidth'=>'300px', 'maxHeight'=>'300px', 'mimeType'=>'image/jpeg, image/jpg, image/png, image/gif', 'fileName'=>'d_'.CHash::getRandomString(15), 'htmlOptions'=>array()),
            'imageOptions'      => array('showImage'=>true, 'showImageName'=>true, 'showImageSize'=>true, 'showImageDimensions'=>true, 'imageClass'=>'avatar'),
            'deleteOptions'     => array('showLink'=>true, 'linkUrl'=>'doctors/edit/id/'.$id.'/delete/avatar', 'linkText'=>A::t('appointments', 'Delete')),
            'thumbnailOptions'  => array('create'=>false),
			'watermarkOptions'	=> array('enable'=>$doctorsWatermark, 'text'=>$watermarkText),
            'fileOptions'       => array('showAlways'=>false, 'class'=>'file', 'size'=>'25', 'filePath'=>'assets/modules/appointments/images/doctors/')
        );

        $fields['separatorContact'] = array();
        $fields['separatorContact']['separatorInfo']     = array('legend'=>A::t('appointments', 'Contact Information'));
        $fields['separatorContact']['work_phone']        = array('type'=>'textbox', 'title'=>A::t('appointments', 'Work Phone'), 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'phoneString', 'maxLength'=>32, 'unique'=>false), 'htmlOptions'=>array('maxlength'=>32, 'autocomplete'=>'off'));
        $fields['separatorContact']['work_mobile_phone'] = array('type'=>'textbox', 'title'=>A::t('appointments', 'Work Mobile Phone'), 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'phoneString', 'maxLength'=>32, 'unique'=>false), 'htmlOptions'=>array('maxlength'=>32, 'autocomplete'=>'off'));
        $fields['separatorContact']['phone']             = array('type'=>'textbox', 'title'=>A::t('appointments', 'Phone'), 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'phoneString', 'maxLength'=>32, 'unique'=>false), 'htmlOptions'=>array('maxlength'=>'32', 'autocomplete'=>'off'));
        $fields['separatorContact']['fax']               = array('type'=>'textbox', 'title'=>A::t('appointments', 'Fax'), 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'phoneString', 'maxLength'=>32, 'unique'=>false), 'htmlOptions'=>array('maxlength'=>'32', 'autocomplete'=>'off'));

        $fields['separatorSocial'] = array();
        $fields['separatorSocial']['separatorInfo'] = array('legend'=>A::t('app', 'Social Networks'));
        $fields['separatorSocial']['show_social_networks'] = array('type'=>'checkbox', 'title'=>A::t('appointments', 'Show Social Networks'), 'default'=>'1', 'validation'=>array('type'=>'set', 'source'=>array(0,1)), 'viewType'=>'custom');
        $fields['separatorSocial']['social_network_facebook'] = array('type'=>'textbox', 'title'=>A::t('appointments', 'Facebook Link'), 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'url', 'maxLength'=>50, 'unique'=>false), 'htmlOptions'=>array('maxlength'=>50, 'autocomplete'=>'off'));
        $fields['separatorSocial']['social_network_twitter'] = array('type'=>'textbox', 'title'=>A::t('appointments', 'Twitter Link'), 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'url', 'maxLength'=>50, 'unique'=>false), 'htmlOptions'=>array('maxlength'=>50, 'autocomplete'=>'off'));
        $fields['separatorSocial']['social_network_youtube'] = array('type'=>'textbox', 'title'=>A::t('appointments', 'YouTube Link'), 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'url', 'maxLength'=>50, 'unique'=>false), 'htmlOptions'=>array('maxlength'=>50, 'autocomplete'=>'off'));
        $fields['separatorSocial']['social_network_instagram'] = array('type'=>'textbox', 'title'=>A::t('appointments', 'Instagram Link'), 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'url', 'maxLength'=>50, 'unique'=>false), 'htmlOptions'=>array('maxlength'=>50, 'autocomplete'=>'off'));

        $fields['separatorAddress'] = array();
        $fields['separatorAddress']['separatorInfo'] = array('legend'=>A::t('appointments', 'Address Information'));
        $fields['separatorAddress']['address']       = array('type'=>'textbox', 'title'=>A::t('appointments', 'Address'), 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'text', 'maxLength'=>64, 'unique'=>false), 'htmlOptions'=>array('maxlength'=>'64', 'autocomplete'=>'off'));
        $fields['separatorAddress']['address_2']     = array('type'=>'textbox', 'title'=>A::t('appointments', 'Address (line 2)'), 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'text', 'maxLength'=>64, 'unique'=>false), 'htmlOptions'=>array('maxlength'=>'64', 'autocomplete'=>'off'));
        $fields['separatorAddress']['city']          = array('type'=>'textbox', 'title'=>A::t('appointments', 'City'), 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'text', 'maxLength'=>64, 'unique'=>false), 'htmlOptions'=>array('maxlength'=>'64', 'autocomplete'=>'off'));
        $fields['separatorAddress']['zip_code']      = array('type'=>'textbox', 'title'=>A::t('appointments', 'Zip Code'), 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'zipCode', 'maxLength'=>32, 'unique'=>false), 'htmlOptions'=>array('maxlength'=>'32', 'autocomplete'=>'off', 'class'=>'medium'));
        $onchange = "editDoctorsChangeCountry(this.value,'')";
        $fields['separatorAddress']['country_code']  = array('type'=>'select', 'title'=>A::t('appointments', 'Country'), 'tooltip'=>'', 'default'=>$defaultCountryCode, 'validation'=>array('required'=>false, 'type'=>'set', 'source'=>array_keys($countries)), 'data'=>$countries, 'htmlOptions'=>array('onchange'=>$onchange));
        $fields['separatorAddress']['state']         = array('type'=>'textbox', 'title'=>A::t('appointments', 'State/Province'), 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'text', 'maxLength'=>64, 'unique'=>false), 'htmlOptions'=>array('maxlength'=>'64', 'autocomplete'=>'off'));

        $fields['separatorAccount'] = array();
        $fields['separatorAccount']['separatorInfo'] = array('legend'=>A::t('appointments', 'Account Information'));
        $fields['separatorAccount']['email']         = array('type'=>'textbox', 'title'=>A::t('appointments', 'Email'), 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'email', 'maxLength'=>100, 'unique'=>true), 'htmlOptions'=>array('maxlength'=>'100', 'autocomplete'=>'off', 'class'=>'middle'));
        $fields['separatorAccount']['username']      = array('type'=>'label', 'title'=>A::t('appointments', 'Username'));
        $fields['separatorAccount']['password']      = array('type'=>'password', 'title'=>A::t('appointments', 'Password'), 'disabled'=>$changePassword == false ? true : false, 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'password', 'minLength'=>6, 'maxlength'=>25), 'encryption'=>array('enabled'=>CConfig::get('password.encryption'), 'encryptAlgorithm'=>CConfig::get('password.encryptAlgorithm'), 'encryptSalt'=>$salt), 'htmlOptions'=>array('maxlength'=>'25', 'placeholder'=>'&#9679;&#9679;&#9679;&#9679;&#9679;'));
        $fields['separatorAccount']['language_code'] = array('type'=>'select', 'title'=>A::t('appointments', 'Preferred Language'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'set', 'source'=>array_keys($langList)), 'data'=>$langList);
        $fields['separatorAccount']['is_active']     = array('type'=>'checkbox', 'title'=>A::t('appointments', 'Active'), 'default'=>'1', 'validation'=>array('type'=>'set', 'source'=>array(0,1)), 'viewType'=>'custom');
        if($removalType == 'logical' || $removalType == 'physical_and_logical') $fields['separatorAccount']['is_removed'] = array('type'=>'checkbox', 'title'=>A::t('appointments', 'Removed'), 'default'=>'0', 'validation'=>array('type'=>'set', 'source'=>array(0,1)), 'viewType'=>'custom');

        $fields['separatorProfessional'] = array();
        $fields['separatorProfessional']['separatorInfo']           = array('legend'=>A::t('appointments', 'Professional Information'));
        $fields['separatorProfessional']['medical_degree_id']       = array('type'=>'select', 'title'=>A::t('appointments', 'Degree'), 'default'=>'0', 'validation'=>array('required'=>false, 'type'=>'set', 'source'=>array_keys($degrees)), 'data'=>$degrees, 'emptyOption'=>true, 'emptyValue'=>'- '.A::t('appointments', 'select').' -', 'htmlOptions'=>array());
        $fields['separatorProfessional']['additional_degree']       = array('type'=>'textbox', 'title'=>A::t('appointments', 'Additional Degree'), 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'text', 'maxLength'=>50), 'htmlOptions'=>array('maxlength'=>50, 'autocomplete'=>'off'));
        $fields['separatorProfessional']['license_number']          = array('type'=>'textbox', 'title'=>A::t('appointments', 'License Number'), 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'text', 'maxLength'=>30), 'htmlOptions'=>array('maxlength'=>30, 'autocomplete'=>'off'));
        $fields['separatorProfessional']['education']               = array('type'=>'textarea', 'title'=>A::t('appointments', 'Education'), 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'text', 'maxLength'=>255), 'htmlOptions'=>array('maxlength'=>255));
        $fields['separatorProfessional']['experience_years']        = array('type'=>'select', 'title'=>A::t('appointments', 'Experience'), 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'set', 'source'=>array_keys($experienceYears)), 'data'=>$experienceYears, 'emptyOption'=>true, 'emptyValue'=>'- '.A::t('appointments', 'select').' -', 'htmlOptions'=>array());
        $fields['separatorProfessional']['residency_training']      = array('type'=>'textarea', 'title'=>A::t('appointments', 'Residency Training'), 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'text', 'maxLength'=>255), 'htmlOptions'=>array('maxlength'=>255));
        $fields['separatorProfessional']['hospital_affiliations']   = array('type'=>'textarea', 'title'=>A::t('appointments', 'Hospital Affiliations'), 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'text', 'maxLength'=>255), 'htmlOptions'=>array('maxlength'=>255));
        $fields['separatorProfessional']['board_certifications']    = array('type'=>'textarea', 'title'=>A::t('appointments', 'Board Certifications'), 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'text', 'maxLength'=>255), 'htmlOptions'=>array('maxlength'=>255));
        $fields['separatorProfessional']['awards_and_publications'] = array('type'=>'textarea', 'title'=>A::t('appointments', 'Awards and Publications'), 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'text', 'maxLength'=>255), 'htmlOptions'=>array('maxlength'=>255));
        $fields['separatorProfessional']['languages_spoken']        = array('type'=>'select', 'title'=>A::t('appointments', 'Languages Spoken'), 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'text', 'maxlength'=>'125'), 'data'=>$localesList, 'viewType'=>'dropdownlist', 'multiple'=>true, 'storeType'=>'separatedValues', 'separator'=>';', 'htmlOptions'=>array('style'=>'width:590px'));
	    $fields['separatorProfessional']['insurances_accepted']        = array('type'=>'select', 'title'=>A::t('appointments', 'Insurances Accepted'), 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'text', 'maxlength'=>'125'), 'data'=>$insurancesList, 'viewType'=>'dropdownlist', 'multiple'=>true, 'storeType'=>'separatedValues', 'separator'=>';', 'htmlOptions'=>array('style'=>'width:590px'));

        $fields['separatorOther'] = array();
        $fields['separatorOther']['separatorInfo']            = array('legend'=>A::t('appointments', 'Other'));
        $fields['separatorOther']['created_at']               = array('type'=>'label', 'title'=>A::t('appointments', 'Created at'), 'default'=>'', 'tooltip'=>'', 'definedValues'=>array(''=>A::t('appointments', 'Never')), 'htmlOptions'=>array(), 'format'=>$dateTimeFormat, 'stripTags'=>false);
        $fields['separatorOther']['created_ip']               = array('type'=>'label', 'title'=>A::t('appointments', 'Created from IP'), 'default'=>'', 'tooltip'=>'', 'definedValues'=>array(''=>A::t('appointments', 'Unknown'), '000.000.000.000'=>A::t('appointments', 'Unknown')), 'htmlOptions'=>array(), 'format'=>'', 'stripTags'=>false);
        $fields['separatorOther']['last_visited_at']          = array('type'=>'label', 'title'=>A::t('appointments', 'Last visit at'), 'default'=>'', 'tooltip'=>'', 'definedValues'=>array(''=>A::t('appointments', 'Never')), 'htmlOptions'=>array(), 'format'=>$dateTimeFormat, 'stripTags'=>false);
        $fields['separatorOther']['last_visited_ip']          = array('type'=>'label', 'title'=>A::t('appointments', 'Last visit from IP'), 'default'=>'', 'tooltip'=>'', 'definedValues'=>array('000.000.000.000'=>A::t('appointments', 'Unknown')), 'htmlOptions'=>array(), 'format'=>'', 'stripTags'=>false);
		$fields['separatorOther']['password_changed_at'] 	  = array('type'=>'label', 'title'=>A::t('appointments', 'Last Password Changed'), 'default'=>'', 'tooltip'=>'', 'definedValues'=>array(''=>A::t('appointments', 'Never'), ''=>A::t('appointments', 'Never')), 'htmlOptions'=>array(), 'format'=>$dateTimeFormat, 'stripTags'=>false);
        $fields['separatorOther']['notifications']            = array('type'=>'checkbox', 'title'=>A::t('appointments', 'Notifications'), 'default'=>'0', 'validation'=>array('type'=>'set', 'source'=>array(0,1)), 'viewType'=>'custom');
        $fields['separatorOther']['notifications_changed_at'] = array('type'=>'label', 'title'=>A::t('appointments', 'Notifications changed at'), 'default'=>'', 'tooltip'=>'', 'definedValues'=>array(''=>A::t('appointments', 'Never')), 'htmlOptions'=>array(), 'format'=>$dateTimeFormat, 'stripTags'=>false);
        $fields['separatorOther']['comments']                 = array('type'=>'textarea', 'title'=>A::t('appointments', 'Comments'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'any', 'maxLength'=>2048), 'htmlOptions'=>array('maxLength'=>'2048'));

        $fields['separatorMembership'] = array();
        $fields['separatorMembership']['separatorInfo']                 = array('legend'=>A::t('appointments', 'Membership Plan'), 'disabled'=>false);
        if (CAuth::getLoggedRole() == 'owner') {
            $fields['separatorMembership']['membership_plan_id']            = array('type'=>'select', 'title'=>A::t('appointments', 'Membership Plan'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'set', 'source'=>array_keys($arrMembershipPlans)), 'data'=>$arrMembershipPlans, 'emptyOption'=>true, 'emptyValue'=>'', 'viewType'=>'dropdownlist', 'multiple'=>false, 'storeType'=>'separatedValues', 'separator'=>';', 'htmlOptions'=>array('class'=>'chosen-select-filter small', 'style'=>'width: 100px;'));
            $fields['separatorMembership']['membership_images_count']       = array('type'=>'select', 'title'=>A::t('appointments', 'Images Count'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'set', 'source'=>array_keys($arrMembershipImagesCount)), 'data'=>$arrMembershipImagesCount, 'emptyOption'=>false, 'emptyValue'=>'', 'viewType'=>'dropdownlist', 'multiple'=>false, 'storeType'=>'separatedValues', 'separator'=>';', 'htmlOptions'=>array('class'=>'chosen-select-filter', 'style'=>'width: 100px;'));
            $fields['separatorMembership']['membership_clinics_count']      = array('type'=>'select', 'title'=>A::t('appointments', 'Clinics Count'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'set', 'source'=>array_keys($arrMembershipClinicsCount)), 'data'=>$arrMembershipClinicsCount, 'emptyOption'=>false, 'emptyValue'=>'', 'viewType'=>'dropdownlist', 'multiple'=>false, 'storeType'=>'separatedValues', 'separator'=>';', 'htmlOptions'=>array('class'=>'chosen-select-filter', 'style'=>'width: 100px;'));
            $fields['separatorMembership']['membership_schedules_count']    = array('type'=>'select', 'title'=>A::t('appointments', 'Schedules Count'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'set', 'source'=>array_keys($arrMembershipSchedulesCount)), 'data'=>$arrMembershipSchedulesCount, 'emptyOption'=>false, 'emptyValue'=>'', 'viewType'=>'dropdownlist', 'multiple'=>false, 'storeType'=>'separatedValues', 'separator'=>';', 'htmlOptions'=>array('class'=>'chosen-select-filter', 'style'=>'width: 100px;'));
            $fields['separatorMembership']['membership_specialties_count']  = array('type'=>'select', 'title'=>A::t('appointments', 'Specialties Count'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'set', 'source'=>array_keys($arrMembershipSpecialtiesCount)), 'data'=>$arrMembershipSpecialtiesCount, 'emptyOption'=>false, 'emptyValue'=>'', 'viewType'=>'dropdownlist', 'multiple'=>false, 'storeType'=>'separatedValues', 'separator'=>';', 'htmlOptions'=>array('class'=>'chosen-select-filter', 'style'=>'width: 100px;'));
            //$fields['separatorMembership']['membership_images_count']       = array('type'=>'textbox', 'title'=>A::t('appointments', 'Images Count'),   'tooltip'=>'',  'default'=>'', 'validation'=>array('required'=>true, 'type'=>'any', 'minLength'=>0, 'maxLength'=>1,), 'htmlOptions'=>array('class'=>'xsmall'));
            //$fields['separatorMembership']['membership_clinics_count']      = array('type'=>'textbox', 'title'=>A::t('appointments', 'Clinics Count'),   'tooltip'=>'',  'default'=>'', 'validation'=>array('required'=>true, 'type'=>'any', 'minLength'=>0, 'maxLength'=>1,), 'htmlOptions'=>array('class'=>'xsmall'));
            //$fields['separatorMembership']['membership_schedules_count']    = array('type'=>'textbox', 'title'=>A::t('appointments', 'Schedules Count'),   'tooltip'=>'',  'default'=>'', 'validation'=>array('required'=>true, 'type'=>'any', 'minLength'=>0, 'maxLength'=>1,), 'htmlOptions'=>array('class'=>'xsmall'));
            //$fields['separatorMembership']['membership_specialties_count']  = array('type'=>'textbox', 'title'=>A::t('appointments', 'Specialties Count'),   'tooltip'=>'',  'default'=>'', 'validation'=>array('required'=>true, 'type'=>'any', 'minLength'=>0, 'maxLength'=>1,), 'htmlOptions'=>array('class'=>'xsmall'));
            $fields['separatorMembership']['membership_show_in_search']     = array('type'=>'checkbox', 'title'=>A::t('appointments', 'Show In Search'),   'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'set', 'source'=>array(0,1)), 'htmlOptions'=>array());
            $fields['separatorMembership']['membership_enable_reviews']     = array('type'=>'checkbox', 'title'=>A::t('appointments', 'Enable Reviews'),   'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'set', 'source'=>array(0,1)), 'htmlOptions'=>array());
            $fields['separatorMembership']['membership_expires']            = array('type'=>'datetime', 	'title'=>A::t('appointments', 'Membership Expires'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'date', 'maxLength'=>10), 'htmlOptions'=>array('maxlength'=>'10', 'class'=>'medium'), 'definedValues'=>array(), 'viewType'=>'date', 'dateFormat'=>'yy-mm-dd', 'timeFormat'=>'HH:mm:ss', 'buttonTrigger'=>true, 'minDate'=>'', 'maxDate'=>'', 'yearRange'=>'-0:+10');
        } elseif (in_array(CAuth::getLoggedRole(), array('mainadmin', 'admin'))) {
            $fields['separatorMembership']['membership_plan_id']        = array('type'=>'select', 'title'=>'Select', 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'set', 'source'=>array_keys($arrMembershipPlans)), 'data'=>$arrMembershipPlans, 'emptyOption'=>true, 'emptyValue'=>'', 'viewType'=>'dropdownlist', 'multiple'=>false, 'storeType'=>'separatedValues', 'separator'=>';', 'htmlOptions'=>array('class'=>'chosen-select-filter'));
            $fields['separatorMembership']['membership_images_count']       = array('type'=>'label', 'title'=>A::t('appointments', 'Images Count'), 'default'=>'', 'tooltip'=>'', 'definedValues'=>array(''=>'--'), 'htmlOptions'=>array(), 'stripTags'=>false);
            $fields['separatorMembership']['membership_clinics_count']      = array('type'=>'label', 'title'=>A::t('appointments', 'Clinics Count'), 'default'=>'', 'tooltip'=>'', 'definedValues'=>array(''=>'--'), 'htmlOptions'=>array(), 'stripTags'=>false);
            $fields['separatorMembership']['membership_schedules_count']    = array('type'=>'label', 'title'=>A::t('appointments', 'Schedules Count'), 'default'=>'', 'tooltip'=>'', 'definedValues'=>array(''=>'--'), 'htmlOptions'=>array(), 'stripTags'=>false);
            $fields['separatorMembership']['membership_specialties_count']  = array('type'=>'label', 'title'=>A::t('appointments', 'Specialties Count'), 'default'=>'', 'tooltip'=>'', 'definedValues'=>array(''=>'--'), 'htmlOptions'=>array(), 'stripTags'=>false);
            $fields['separatorMembership']['membership_show_in_search']     = array('type'=>'label', 'title'=>A::t('appointments', 'Show In Search'), 'default'=>'', 'tooltip'=>'', 'definedValues'=>array('0'=>'<span class="badge-red badge-square">'.A::t('appointments', 'No').'</span>', '1'=>'<span class="badge-green badge-square">'.A::t('appointments', 'Yes').'</span>'), 'htmlOptions'=>array(), 'stripTags'=>false);
            $fields['separatorMembership']['membership_enable_reviews']     = array('type'=>'label', 'title'=>A::t('appointments', 'Enable Reviews'), 'default'=>'', 'tooltip'=>'', 'definedValues'=>array('0'=>'<span class="badge-red badge-square">'.A::t('appointments', 'No').'</span>', '1'=>'<span class="badge-green badge-square">'.A::t('appointments', 'Yes').'</span>'), 'htmlOptions'=>array(), 'stripTags'=>false);
            $fields['separatorMembership']['membership_expires']            = array('type'=>'label', 'title'=>A::t('appointments', 'Membership Expires'), 'default'=>'', 'tooltip'=>'', 'definedValues'=>array(''=>'--'), 'htmlOptions'=>array(), 'format'=>$dateFormat, 'stripTags'=>false);
        } else {
            $fields['separatorMembership']['membership_plan_name']      = array('type'=>'label', 'title'=>A::t('appointments', 'Membership Plan'), 'default'=>'', 'tooltip'=>'', 'definedValues'=>array(''=>'--'), 'htmlOptions'=>array(), 'stripTags'=>false);
            $fields['separatorMembership']['membership_images_count']       = array('type'=>'label', 'title'=>A::t('appointments', 'Images Count'), 'default'=>'', 'tooltip'=>'', 'definedValues'=>array(''=>'--'), 'htmlOptions'=>array(), 'stripTags'=>false);
            $fields['separatorMembership']['membership_clinics_count']      = array('type'=>'label', 'title'=>A::t('appointments', 'Clinics Count'), 'default'=>'', 'tooltip'=>'', 'definedValues'=>array(''=>'--'), 'htmlOptions'=>array(), 'stripTags'=>false);
            $fields['separatorMembership']['membership_schedules_count']    = array('type'=>'label', 'title'=>A::t('appointments', 'Schedules Count'), 'default'=>'', 'tooltip'=>'', 'definedValues'=>array(''=>'--'), 'htmlOptions'=>array(), 'stripTags'=>false);
            $fields['separatorMembership']['membership_specialties_count']  = array('type'=>'label', 'title'=>A::t('appointments', 'Specialties Count'), 'default'=>'', 'tooltip'=>'', 'definedValues'=>array(''=>'--'), 'htmlOptions'=>array(), 'stripTags'=>false);
            $fields['separatorMembership']['membership_show_in_search']     = array('type'=>'label', 'title'=>A::t('appointments', 'Show In Search'), 'default'=>'', 'tooltip'=>'', 'definedValues'=>array('0'=>'<span class="badge-red badge-square">'.A::t('appointments', 'No').'</span>', '1'=>'<span class="badge-green badge-square">'.A::t('appointments', 'Yes').'</span>'), 'htmlOptions'=>array(), 'stripTags'=>false);
            $fields['separatorMembership']['membership_enable_reviews']     = array('type'=>'label', 'title'=>A::t('appointments', 'Enable Reviews'), 'default'=>'', 'tooltip'=>'', 'definedValues'=>array('0'=>'<span class="badge-red badge-square">'.A::t('appointments', 'No').'</span>', '1'=>'<span class="badge-green badge-square">'.A::t('appointments', 'Yes').'</span>'), 'htmlOptions'=>array(), 'stripTags'=>false);
            $fields['separatorMembership']['membership_expires']            = array('type'=>'label', 'title'=>A::t('appointments', 'Membership Expires'), 'default'=>'', 'tooltip'=>'', 'definedValues'=>array(''=>'--'), 'htmlOptions'=>array(), 'format'=>$dateFormat, 'stripTags'=>false);
        }


        echo $actionMessage;

        echo CWidget::create('CDataForm', array(
            'model'=>'Modules\Appointments\Models\Doctors',
			'resetBeforeStart' => true,
            'primaryKey'=>$id,
            'operationType'=>'edit',
            'action'=>'doctors/edit/id/'.$id,
            'successUrl'=>'doctors/manage',
            'cancelUrl'=>'doctors/manage',
            'method'=>'post',
            'htmlOptions'=>array(
                'id'=>$formName,
                'name'=>$formName,
                'autoGenerateId'=>true
            ),
            'requiredFieldsAlert'=>true,
            'fields'=>$fields,
            'buttons'=>array(
                'submitUpdateClose'=>array('type'=>'submit', 'value'=>A::t('appointments', 'Update & Close'), 'htmlOptions'=>array('name'=>'btnUpdateClose')),
                'submitUpdate'=>array('type'=>'submit', 'value'=>A::t('appointments', 'Update'), 'htmlOptions'=>array('name'=>'btnUpdate')),
                'cancel'=>array('type'=>'button', 'value'=>A::t('appointments', 'Cancel'), 'htmlOptions'=>array('name'=>'', 'class'=>'button white')),
            ),
            'buttonsPosition'	=> 'both',
			'messagesSource' 	=> 'core',
			'showAllErrors'     => false,
			'alerts'            => array('type'=>'flash', 'itemName'=>A::t('appointments', 'Doctor')),
			'return'            => false,
        ));
    ?>
    </div>
</div>
<?php
    A::app()->getClientScript()->registerScript(
        'doctorsChangeCountry',
        'editDoctorsChangeCountry = function (country,stateCode){
            var ajax = null;
            jQuery("select#'.$formName.'_state").chosen("destroy");
            ajax = appointments_ChangeCountry("'.$formName.'",country,stateCode);
            if(ajax == null){
                jQuery("select#'.$formName.'_state").chosen();
            }else{
                ajax.done(function (){
                    jQuery("select#'.$formName.'_state").chosen();
                });
            }
        }

        jQuery(document).ready(function(){
            var country = "'.$countryCode.'";
            var stateCode = "'.$stateCode.'";

            ajax = appointments_ChangeCountry("'.$formName.'",country,stateCode);
            if(ajax == null){
                jQuery("select#'.$formName.'_state").chosen("destroy");
                jQuery("select#'.$formName.'_state").chosen({disable_search_threshold: 10});
            }else{
                ajax.done(function (){
                    jQuery("select#'.$formName.'_state").chosen("destroy");
                    jQuery("select#'.$formName.'_state").chosen({disable_search_threshold: 10});
                });
            }
        });',
        1
    );
