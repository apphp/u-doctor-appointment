<?php
    $this->_activeMenu = 'doctors/manage';
    $this->_breadCrumbs = array(
        array('label'=>A::t('appointments', 'Modules'), 'url'=>'modules/'),
        array('label'=>A::t('appointments', 'Doctor Appointments'), 'url'=>'modules/settings/code/appointments'),
        array('label'=>A::t('appointments', 'Doctors Management'), 'url'=>'doctors/manage'),
        array('label'=>A::t('appointments', 'Add Doctor')),
    );

    // register module javascript
    A::app()->getClientScript()->registerScriptFile('assets/modules/appointments/js/appointments.js', 2);

    $formName = 'frmDoctorAdd';
?>

<h1><?= A::t('appointments', 'Doctors Management'); ?></h1>

<div class="bloc">
    <?= $tabs; ?>
    <div class="sub-title"><?= A::t('appointments', 'Add Doctor'); ?></div>
    <div class="content">
    <?php
        $fields = array();
        $fields['separatorPersonal'] = array();
        $fields['separatorPersonal']['separatorInfo'] = array('legend'=>A::t('appointments', 'Personal Information'));
        $fields['separatorPersonal']['title_id']      = array('type'=>'select', 'title'=>A::t('appointments', 'Title'), 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'set', 'source'=>array_keys($titles)), 'data'=>$titles, 'emptyOption'=>true, 'emptyValue'=>'- '.A::t('appointments', 'select').' -', 'htmlOptions'=>array('maxlength'=>32));
        $fields['separatorPersonal']['doctor_first_name']    = array('type'=>'textbox', 'title'=>A::t('appointments', 'First Name'), 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'text', 'maxLength'=>32), 'htmlOptions'=>array('maxlength'=>32));
        $fields['separatorPersonal']['doctor_middle_name']   = array('type'=>'textbox', 'title'=>A::t('appointments', 'Middle Name'), 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'text', 'maxLength'=>32), 'htmlOptions'=>array('maxlength'=>32));
        $fields['separatorPersonal']['doctor_last_name']     = array('type'=>'textbox', 'title'=>A::t('appointments', 'Last Name'), 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'text', 'maxLength'=>32), 'htmlOptions'=>array('maxlength'=>32));
        $fields['separatorPersonal']['gender']        = array('type'=>'select', 'title'=>A::t('appointments', 'Gender'), 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'set', 'source'=>array_keys($genders)), 'data'=>$genders, 'emptyOption'=>true, 'emptyValue'=>'- '.A::t('appointments', 'select').' -', 'htmlOptions'=>array('maxlength'=>32));
        $fields['separatorPersonal']['birth_date']    = array('type'=>'datetime', 'title'=>A::t('appointments', 'Birth Date'), 'default'=>'', 'tooltip'=>'', 'validation'=>array('required'=>false, 'type'=>'date', 'maxLength'=>'10', 'minValue'=>(date('Y')-110).'-00-00', 'maxValue'=>date('Y-m-d')), 'htmlOptions'=>array('maxLength'=>'10', 'class'=>'medium'), 'viewType'=>'date', 'dateFormat'=>'yy-mm-dd', 'definedValues'=>array(), 'buttonTrigger'=>true, 'maxDate'=>'1', 'yearRange'=>'-100:+0');
		$fields['separatorPersonal']['avatar']        = array(
			'type'          => 'imageupload',
			'title'         => A::t('appointments', 'Avatar'),
            'validation'    => array('required'=>false, 'type'=>'image', 'maxSize'=>'500k', 'maxWidth'=>'300px', 'maxHeight'=>'300px', 'targetPath'=>'assets/modules/appointments/images/doctors/', 'mimeType'=>'image/jpeg, image/jpg, image/png, image/gif', 'fileName'=>'d_'.CHash::getRandomString(15)),
			'imageOptions'  => array('showImage'=>false),
			'deleteOptions' => array('showLink'=>false),
            'watermarkOptions'	=> array('enable'=>$doctorsWatermark, 'text'=>$watermarkText),
			'fileOptions'   => array('showAlways'=>false, 'class'=>'file', 'size'=>'25')
		);
		
        $fields['separatorContact'] = array();
        $fields['separatorContact']['separatorInfo'] = array('legend'=>A::t('appointments', 'Contact Information'));
        $fields['separatorContact']['work_phone'] = array('type'=>'textbox', 'title'=>A::t('appointments', 'Work Phone'), 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'phoneString', 'maxLength'=>32, 'unique'=>false), 'htmlOptions'=>array('maxlength'=>32, 'autocomplete'=>'off'));
        $fields['separatorContact']['work_mobile_phone'] = array('type'=>'textbox', 'title'=>A::t('appointments', 'Work Mobile Phone'), 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'phoneString', 'maxLength'=>32, 'unique'=>false), 'htmlOptions'=>array('maxlength'=>32, 'autocomplete'=>'off'));
        $fields['separatorContact']['phone'] = array('type'=>'textbox', 'title'=>A::t('appointments', 'Phone'), 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'phoneString', 'maxLength'=>32, 'unique'=>false), 'htmlOptions'=>array('maxlength'=>32, 'autocomplete'=>'off'));
        $fields['separatorContact']['fax'] = array('type'=>'textbox', 'title'=>A::t('appointments', 'Fax'), 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'phoneString', 'maxLength'=>32, 'unique'=>false), 'htmlOptions'=>array('maxlength'=>32, 'autocomplete'=>'off'));

        $fields['separatorAddress'] = array();
        $fields['separatorAddress']['separatorInfo'] = array('legend'=>A::t('appointments', 'Address Information'));
        $fields['separatorAddress']['address'] = array('type'=>'textbox', 'title'=>A::t('appointments', 'Address'), 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'text', 'maxLength'=>64, 'unique'=>false), 'htmlOptions'=>array('maxlength'=>64, 'autocomplete'=>'off'));
        $fields['separatorAddress']['address_2'] = array('type'=>'textbox', 'title'=>A::t('appointments', 'Address (line 2)'), 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'text', 'maxLength'=>64, 'unique'=>false), 'htmlOptions'=>array('maxlength'=>64, 'autocomplete'=>'off'));
        $fields['separatorAddress']['city'] = array('type'=>'textbox', 'title'=>A::t('appointments', 'City'), 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'text', 'maxLength'=>64, 'unique'=>false), 'htmlOptions'=>array('maxlength'=>64, 'autocomplete'=>'off'));
        $fields['separatorAddress']['zip_code'] = array('type'=>'textbox', 'title'=>A::t('appointments', 'Zip Code'), 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'text', 'maxLength'=>32, 'unique'=>false), 'htmlOptions'=>array('maxlength'=>32, 'autocomplete'=>'off', 'class'=>'medium'));
        $onchange = "addDoctorsChangeCountry(this.value,'')";
        $fields['separatorAddress']['country_code'] = array('type'=>'select', 'title'=>A::t('appointments', 'Country'), 'tooltip'=>'', 'default'=>$defaultCountryCode, 'validation'=>array('required'=>false, 'type'=>'set', 'source'=>array_keys($countries)), 'data'=>$countries, 'htmlOptions'=>array('onchange'=>$onchange));
        $fields['separatorAddress']['state'] = array('type'=>'textbox', 'title'=>A::t('appointments', 'State/Province'), 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'text', 'maxLength'=>64, 'unique'=>false), 'htmlOptions'=>array('maxlength'=>64, 'autocomplete'=>'off'));

        $fields['separatorAccount'] = array();
        $fields['separatorAccount']['separatorInfo'] = array('legend'=>A::t('appointments', 'Account Information'));
        $fields['separatorAccount']['email'] = array('type'=>'textbox', 'title'=>A::t('appointments', 'Email'), 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'email', 'maxLength'=>100, 'unique'=>true), 'htmlOptions'=>array('maxlength'=>100, 'autocomplete'=>'off', 'class'=>'middle'));
        $fields['separatorAccount']['username'] = array('type'=>'textbox', 'title'=>A::t('appointments', 'Username'), 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'username', 'minLength'=>6, 'maxLength'=>25, 'unique'=>true), 'htmlOptions'=>array('maxlength'=>25, 'autocomplete'=>'off'));
        $fields['separatorAccount']['password'] = array('type'=>'password', 'title'=>A::t('appointments', 'Password'), 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'password', 'minLength'=>6, 'maxLength'=>20), 'encryption'=>array('enabled'=>CConfig::get('password.encryption'), 'encryptAlgorithm'=>CConfig::get('password.encryptAlgorithm'), 'encryptSalt'=>$salt), 'htmlOptions'=>array('maxlength'=>20, 'placeholder'=>'&#9679;&#9679;&#9679;&#9679;&#9679;'));
        $fields['separatorAccount']['passwordRetype'] = array('type'=>'password', 'title'=>A::t('appointments', 'Confirm Password'), 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'confirm', 'confirmField'=>'password', 'minLength'=>6, 'maxlength'=>20), 'htmlOptions'=>array('maxlength'=>20, 'placeholder'=>'&#9679;&#9679;&#9679;&#9679;&#9679;'));
        $fields['separatorAccount']['salt'] = array('type'=>'hidden', 'default'=>$salt);
        $fields['separatorAccount']['language_code'] = array('type'=>'select', 'title'=>A::t('appointments', 'Preferred Language'), 'tooltip'=>'', 'default'=>A::app()->getLanguage(), 'validation'=>array('required'=>true, 'type'=>'set', 'source'=>array_keys($langList)), 'data'=>$langList);
        $fields['separatorAccount']['is_active'] = array('type'=>'checkbox', 'title'=>A::t('appointments', 'Active'), 'default'=>'1', 'validation'=>array('type'=>'set', 'source'=>array(0,1)), 'viewType'=>'custom');

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
        $fields['separatorOther']['separatorInfo'] = array('legend'=>A::t('appointments', 'Other'));
        $fields['separatorOther']['notifications'] = array('type'=>'checkbox', 'title'=>A::t('appointments', 'Notifications'), 'default'=>'0', 'validation'=>array('type'=>'set', 'source'=>array(0,1)), 'viewType'=>'custom');
        $fields['separatorOther']['comments'] = array('type'=>'textarea', 'title'=>A::t('appointments', 'Comments'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'any', 'maxLength'=>2048), 'htmlOptions'=>array('maxLength'=>2048));

        CWidget::create('CDataForm', array(
            'model'=>'Modules\Appointments\Models\Doctors',
			'resetBeforeStart' => true,
            'operationType'=>'add',
            'action'=>'doctors/add/',
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
                'submit' => array('type'=>'submit', 'value'=>A::t('appointments', 'Create'), 'htmlOptions'=>array('name'=>'')),
                'cancel' => array('type'=>'button', 'value'=>A::t('appointments', 'Cancel'), 'htmlOptions'=>array('name'=>'', 'class'=>'button white')),
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
        'addDoctorsChangeCountry = function (country,stateCode){
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
                jQuery("select#'.$formName.'_state").chosen();
            }else{
                ajax.done(function (){
                    jQuery("select#'.$formName.'_state").chosen("destroy");
                    jQuery("select#'.$formName.'_state").chosen();
                });
            }
        });',
        1
    );
