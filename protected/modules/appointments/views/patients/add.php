<?php
    $this->_activeMenu = 'patients/manage';
    $this->_breadCrumbs = array(
        array('label'=>A::t('appointments', 'Modules'), 'url'=>'modules/'),
        array('label'=>A::t('appointments', 'Doctor Appointments'), 'url'=>'modules/settings/code/appointments'),
        array('label'=>A::t('appointments', 'Patients Management'), 'url'=>'patients/manage'),
        array('label'=>A::t('appointments', 'Add Patient')),
    );

    // register module javascript
    A::app()->getClientScript()->registerScriptFile('assets/modules/appointments/js/appointments.js', 2);

    $formName = 'frmPatientAdd';
?>

<h1><?= A::t('appointments', 'Patients Management'); ?></h1>

<div class="bloc">
    <?= $tabs; ?>
    <div class="sub-title"><?= A::t('appointments', 'Add Patient'); ?></div>
    <div class="content">
    <?php
        $fields = array();

        $fields['separatorPersonal'] = array();
        $fields['separatorPersonal']['separatorInfo'] = array('legend'=>A::t('appointments', 'Personal Information'));
        $fields['separatorPersonal']['patient_first_name']    = array('type'=>'textbox', 'title'=>A::t('appointments', 'First Name'), 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'text', 'maxLength'=>32), 'htmlOptions'=>array('maxlength'=>32));
        $fields['separatorPersonal']['patient_last_name']     = array('type'=>'textbox', 'title'=>A::t('appointments', 'Last Name'), 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'text', 'maxLength'=>32), 'htmlOptions'=>array('maxlength'=>32));
        $fields['separatorPersonal']['gender']        = array('type'=>'select', 'title'=>A::t('appointments', 'Gender'), 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'set', 'source'=>array_keys($genders)), 'data'=>$genders, 'emptyOption'=>true, 'emptyValue'=>'- '.A::t('appointments', 'select').' -', 'htmlOptions'=>array('maxlength'=>32));
        $fields['separatorPersonal']['birth_date']    = array('type'=>'datetime', 'title'=>A::t('appointments', 'Birth Date'), 'default'=>'', 'tooltip'=>'', 'validation'=>array('required'=>false, 'type'=>'date', 'maxLength'=>'10', 'minValue'=>(date('Y')-110).'-00-00', 'maxValue'=>date('Y-m-d')), 'htmlOptions'=>array('maxLength'=>'10', 'class'=>'medium'), 'viewType'=>'date', 'dateFormat'=>'yy-mm-dd', 'definedValues'=>array(), 'buttonTrigger'=>true, 'maxDate'=>'1', 'yearRange'=>'-100:+0');

        $fields['separatorContact'] = array();
        $fields['separatorContact']['separatorInfo'] = array('legend'=>A::t('appointments', 'Contact Information'));
        $fields['separatorContact']['phone'] = array('type'=>'textbox', 'title'=>A::t('appointments', 'Phone'), 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'phoneString', 'maxLength'=>32, 'unique'=>false), 'htmlOptions'=>array('maxlength'=>32, 'autocomplete'=>'off'));
        $fields['separatorContact']['fax'] = array('type'=>'textbox', 'title'=>A::t('appointments', 'Fax'), 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'phoneString', 'maxLength'=>32, 'unique'=>false), 'htmlOptions'=>array('maxlength'=>32, 'autocomplete'=>'off'));

        $fields['separatorAddress'] = array();
        $fields['separatorAddress']['separatorInfo'] = array('legend'=>A::t('appointments', 'Address Information'));
        $fields['separatorAddress']['address'] = array('type'=>'textbox', 'title'=>A::t('appointments', 'Address'), 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'text', 'maxLength'=>64, 'unique'=>false), 'htmlOptions'=>array('maxlength'=>64, 'autocomplete'=>'off'));
        $fields['separatorAddress']['address_2'] = array('type'=>'textbox', 'title'=>A::t('appointments', 'Address (line 2)'), 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'text', 'maxLength'=>64, 'unique'=>false), 'htmlOptions'=>array('maxlength'=>64, 'autocomplete'=>'off'));
        $fields['separatorAddress']['city'] = array('type'=>'textbox', 'title'=>A::t('appointments', 'City'), 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'text', 'maxLength'=>64, 'unique'=>false), 'htmlOptions'=>array('maxlength'=>64, 'autocomplete'=>'off'));
        $fields['separatorAddress']['zip_code'] = array('type'=>'textbox', 'title'=>A::t('appointments', 'Zip Code'), 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'text', 'maxLength'=>32, 'unique'=>false), 'htmlOptions'=>array('maxlength'=>32, 'autocomplete'=>'off', 'class'=>'medium'));
        $onchange = "addPatientsChangeCountry(this.value,'')";
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

        $fields['separatorOther'] = array();
        $fields['separatorOther']['separatorInfo'] = array('legend'=>A::t('appointments', 'Other'));
        $fields['separatorOther']['notifications'] = array('type'=>'checkbox', 'title'=>A::t('appointments', 'Notifications'), 'default'=>'0', 'validation'=>array('type'=>'set', 'source'=>array(0,1)), 'viewType'=>'custom');
        $fields['separatorOther']['comments'] = array('type'=>'textarea', 'title'=>A::t('appointments', 'Comments'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'any', 'maxLength'=>2048), 'htmlOptions'=>array('maxLength'=>2048));

        echo CWidget::create('CDataForm', array(
            'model'=>'Modules\Appointments\Models\Patients',
            'operationType'=>'add',
            'action'=>'patients/add/',
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
                'submit' => array('type'=>'submit', 'value'=>A::t('appointments', 'Create'), 'htmlOptions'=>array('name'=>'')),
                'cancel' => array('type'=>'button', 'value'=>A::t('appointments', 'Cancel'), 'htmlOptions'=>array('name'=>'', 'class'=>'button white')),
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
        'addPatientsChangeCountry = function (country,stateCode){
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
                jQuery("#'.$formName.'_state").chosen("destroy");
                jQuery("select#'.$formName.'_state").chosen({disable_search_threshold: 10});
            }else{
                ajax.done(function (){
                    jQuery("#'.$formName.'_state").chosen("destroy");
                    jQuery("select#'.$formName.'_state").chosen({disable_search_threshold: 10});
                });
            }
        });',
        1
    );
