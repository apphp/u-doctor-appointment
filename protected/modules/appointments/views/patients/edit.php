<?php
    $this->_activeMenu = 'patients/manage';
    $this->_breadCrumbs = array(
        array('label'=>A::t('appointments', 'Modules'), 'url'=>'modules/'),
        array('label'=>A::t('appointments', 'Doctor Appointments'), 'url'=>'modules/settings/code/appointments'),
        array('label'=>A::t('appointments', 'Patients Management'), 'url'=>'patients/manage'),
        array('label'=>A::t('appointments', 'Edit Patient')),
    );

    // register module javascript
    A::app()->getClientScript()->registerScriptFile('assets/modules/appointments/js/appointments.js', 2);

    $formName = 'frmPatientEdit';
?>

<h1><?= A::t('appointments', 'Patients Management'); ?></h1>

<div class="bloc">
    <?= $tabs; ?>
    <div class="sub-title"><?= A::t('appointments', 'Edit Patient'); ?></div>
    <div class="content">
        <?php
        $fields = array();

        $fields['separatorPersonal'] = array();
        $fields['separatorPersonal']['separatorInfo'] = array('legend'=>A::t('appointments', 'Personal Information'));
        $fields['separatorPersonal']['patient_first_name']    = array('type'=>'textbox', 'title'=>A::t('appointments', 'First Name'), 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'text', 'maxLength'=>32), 'htmlOptions'=>array('maxlength'=>'32'));
        $fields['separatorPersonal']['patient_last_name']     = array('type'=>'textbox', 'title'=>A::t('appointments', 'Last Name'), 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'text', 'maxLength'=>32), 'htmlOptions'=>array('maxlength'=>'32'));
        $fields['separatorPersonal']['gender']        = array('type'=>'select', 'title'=>A::t('appointments', 'Gender'), 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'set', 'source'=>array_keys($genders)), 'data'=>$genders, 'emptyOption'=>true, 'emptyValue'=>'- '.A::t('appointments', 'select').' -', 'htmlOptions'=>array());
        $fields['separatorPersonal']['birth_date']    = array('type'=>'datetime', 'title'=>A::t('appointments', 'Birth Date'), 'default'=>'', 'tooltip'=>'', 'validation'=>array('required'=>false, 'type'=>'date', 'maxLength'=>'10', 'minValue'=>(date('Y')-110).'-00-00', 'maxValue'=>date('Y-m-d')), 'htmlOptions'=>array('maxLength'=>'10', 'class'=>'medium'), 'viewType'=>'date', 'dateFormat'=>'yy-mm-dd', 'definedValues'=>array(), 'buttonTrigger'=>true, 'maxDate'=>'1', 'yearRange'=>'-100:+0');

        $fields['separatorContact'] = array();
        $fields['separatorContact']['separatorInfo']     = array('legend'=>A::t('appointments', 'Contact Information'));
        $fields['separatorContact']['phone']             = array('type'=>'textbox', 'title'=>A::t('appointments', 'Phone'), 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'phoneString', 'maxLength'=>32, 'unique'=>false), 'htmlOptions'=>array('maxlength'=>'32', 'autocomplete'=>'off'));
        $fields['separatorContact']['fax']               = array('type'=>'textbox', 'title'=>A::t('appointments', 'Fax'), 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'phoneString', 'maxLength'=>32, 'unique'=>false), 'htmlOptions'=>array('maxlength'=>'32', 'autocomplete'=>'off'));

        $fields['separatorAddress'] = array();
        $fields['separatorAddress']['separatorInfo'] = array('legend'=>A::t('appointments', 'Address Information'));
        $fields['separatorAddress']['address']       = array('type'=>'textbox', 'title'=>A::t('appointments', 'Address'), 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'text', 'maxLength'=>64, 'unique'=>false), 'htmlOptions'=>array('maxlength'=>'64', 'autocomplete'=>'off'));
        $fields['separatorAddress']['address_2']     = array('type'=>'textbox', 'title'=>A::t('appointments', 'Address (line 2)'), 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'text', 'maxLength'=>64, 'unique'=>false), 'htmlOptions'=>array('maxlength'=>'64', 'autocomplete'=>'off'));
        $fields['separatorAddress']['city']          = array('type'=>'textbox', 'title'=>A::t('appointments', 'City'), 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'text', 'maxLength'=>64, 'unique'=>false), 'htmlOptions'=>array('maxlength'=>'64', 'autocomplete'=>'off'));
        $fields['separatorAddress']['zip_code']      = array('type'=>'textbox', 'title'=>A::t('appointments', 'Zip Code'), 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'zipCode', 'maxLength'=>32, 'unique'=>false), 'htmlOptions'=>array('maxlength'=>'32', 'autocomplete'=>'off', 'class'=>'medium'));
        $onchange = "editPatientsChangeCountry(this.value,'')";
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

        $fields['separatorMedicalCard'] = array();
        $fields['separatorMedicalCard']['separatorInfo'] = array('legend'=>A::t('appointments', 'Medical Card'));
        $fields['separatorMedicalCard']['weight'] = array('type'=>'textbox', 'title'=>A::t('appointments', 'Weight'), 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'text', 'maxLength'=>32), 'htmlOptions'=>array('maxlength'=>32));
        $fields['separatorMedicalCard']['height'] = array('type'=>'textbox', 'title'=>A::t('appointments', 'Height'), 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'text', 'maxLength'=>32), 'htmlOptions'=>array('maxlength'=>32));
        $fields['separatorMedicalCard']['blood_type'] = array('type'=>'textbox', 'title'=>A::t('appointments', 'Blood Type'), 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'text', 'maxLength'=>32), 'htmlOptions'=>array('maxlength'=>32));
        $fields['separatorMedicalCard']['allergies'] = array('type'=>'textbox', 'title'=>A::t('appointments', 'Allergies'), 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'text', 'maxLength'=>100), 'htmlOptions'=>array('maxlength'=>100));
        $fields['separatorMedicalCard']['high_blood_presure'] = array('type'=>'textbox', 'title'=>A::t('appointments', 'High Blood Pressure'), 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'text', 'maxLength'=>32), 'htmlOptions'=>array('maxlength'=>32));
        $fields['separatorMedicalCard']['low_blood_presure'] = array('type'=>'textbox', 'title'=>A::t('appointments', 'Low Blood Pressure'), 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'text', 'maxLength'=>32), 'htmlOptions'=>array('maxlength'=>32));
        $fields['separatorMedicalCard']['cardiac_rythm'] = array('type'=>'textbox', 'title'=>A::t('appointments', 'Cardiac Rhythm'), 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'text', 'maxLength'=>32), 'htmlOptions'=>array('maxlength'=>32));
        $fields['separatorMedicalCard']['smoking'] = array('type'=>'checkbox', 'title'=>A::t('appointments', 'Smoking'), 'default'=>'0', 'validation'=>array('type'=>'set', 'source'=>array(0,1)), 'viewType'=>'custom');
        $fields['separatorMedicalCard']['tried_drugs'] = array('type'=>'checkbox', 'title'=>A::t('appointments', 'Tried Drugs'), 'default'=>'0', 'validation'=>array('type'=>'set', 'source'=>array(0,1)), 'viewType'=>'custom');

        $fields['separatorOther'] = array();
        $fields['separatorOther']['separatorInfo']            = array('legend'=>A::t('appointments', 'Other'));
        $fields['separatorOther']['created_at']               = array('type'=>'label', 'title'=>A::t('appointments', 'Created at'), 'default'=>'', 'tooltip'=>'', 'definedValues'=>array(''=>A::t('appointments', 'Never')), 'htmlOptions'=>array(), 'format'=>$dateTimeFormat, 'stripTags'=>false);
        $fields['separatorOther']['created_ip']               = array('type'=>'label', 'title'=>A::t('appointments', 'Created from IP'), 'default'=>'', 'tooltip'=>'', 'definedValues'=>array(''=>A::t('appointments', 'Unknown'), '000.000.000.000'=>A::t('appointments', 'Unknown')), 'htmlOptions'=>array(), 'format'=>'', 'stripTags'=>false);
        $fields['separatorOther']['last_visited_at']          = array('type'=>'label', 'title'=>A::t('appointments', 'Last visit at'), 'default'=>'', 'tooltip'=>'', 'definedValues'=>array(''=>A::t('appointments', 'Never')), 'htmlOptions'=>array(), 'format'=>$dateTimeFormat, 'stripTags'=>false);
        $fields['separatorOther']['last_visited_ip']          = array('type'=>'label', 'title'=>A::t('appointments', 'Last visit from IP'), 'default'=>'', 'tooltip'=>'', 'definedValues'=>array('000.000.000.000'=>A::t('appointments', 'Unknown')), 'htmlOptions'=>array(), 'format'=>'', 'stripTags'=>false);
        $fields['separatorOther']['password_changed_at']      = array('type'=>'label', 'title'=>A::t('appointments', 'Last Password Changed'), 'default'=>'', 'tooltip'=>'', 'definedValues'=>array(''=>A::t('appointments', 'Never')), 'htmlOptions'=>array(), 'format'=>$dateTimeFormat, 'stripTags'=>false);
        $fields['separatorOther']['notifications']            = array('type'=>'checkbox', 'title'=>A::t('appointments', 'Notifications'), 'default'=>'0', 'validation'=>array('type'=>'set', 'source'=>array(0,1)), 'viewType'=>'custom');
        $fields['separatorOther']['notifications_changed_at'] = array('type'=>'label', 'title'=>A::t('appointments', 'Notifications changed at'), 'default'=>'', 'tooltip'=>'', 'definedValues'=>array(''=>A::t('appointments', 'Never')), 'htmlOptions'=>array(), 'format'=>$dateTimeFormat, 'stripTags'=>false);
        $fields['separatorOther']['comments']                 = array('type'=>'textarea', 'title'=>A::t('appointments', 'Comments'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'any', 'maxLength'=>2048), 'htmlOptions'=>array('maxLength'=>'2048'));

        echo $actionMessage;

        echo CWidget::create('CDataForm', array(
            'model'=>'Modules\Appointments\Models\Patients',
            'primaryKey'=>$id,
            'operationType'=>'edit',
            'action'=>'patients/edit/id/'.$id,
            'successUrl'=>'patients/manage',
            'cancelUrl'=>'patients/manage',
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
            'buttonsPosition'   =>  'both',
            'messagesSource' 	=> 'core',
            'showAllErrors'     => false,
            'alerts'            => array('type'=>'flash', 'itemName'=>A::t('appointments', 'Patient')),
            'return'            => true,
        ));
    ?>
    </div>
</div>
<?php
    A::app()->getClientScript()->registerScript(
        'patientsChangeCountry',
        'editPatientsChangeCountry = function (country,stateCode){
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
