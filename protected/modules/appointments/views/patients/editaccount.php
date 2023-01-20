<?php
    $this->_pageTitle = A::t('appointments', 'Edit Account');
    $this->_activeMenu = 'patients/myAccount';

    $this->_breadCrumbs = array(
        array('label' => A::t('appointments', 'Home'), 'url'=>Website::getDefaultPage()),
        array('label' => A::t('appointments', 'Dashboard'), 'url'=>'patients/dashboard'),
        array('label' => A::t('appointments', 'My Account'), 'url'=>'patients/myAccount'),
        array('label' => A::t('appointments', 'Edit Account'))
    );

    A::app()->getClientScript()->registerScriptFile('assets/modules/appointments/js/appointments.js', 2);
    A::app()->getClientScript()->registerScriptFile('assets/vendors/chosen/chosen.jquery.min.js',2);

    A::app()->getClientScript()->registerCssFile('assets/vendors/chosen/chosen.min.css');
    A::app()->getClientScript()->registerCssFile('assets/vendors/jquery/jquery-ui.min.css');
    A::app()->getClientScript()->registerScriptFile('assets/vendors/jquery/jquery-ui.min.js', 1);
?>
    <section id="content" role="main">
        <div class="entry">
            <div class="cmsms_cc">
                <div class="one_first first_column">
                    <div class="cmsms-form-builder">
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
                        $fields['separatorAddress']['zip_code']      = array('type'=>'textbox', 'title'=>A::t('appointments', 'Zip Code'), 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'zipCode', 'maxLength'=>32, 'unique'=>false), 'htmlOptions'=>array('maxlength'=>'32', 'autocomplete'=>'off', 'class'=>'small'));
                        $onchange = "appointments_ChangeCountry('frmPatientEdit',this.value)";
                        $fields['separatorAddress']['country_code']  = array('type'=>'select', 'title'=>A::t('appointments', 'Country'), 'tooltip'=>'', 'default'=>$countryCode, 'validation'=>array('required'=>false, 'type'=>'set', 'source'=>array_keys($countries)), 'data'=>$countries, 'htmlOptions'=>array('onchange'=>$onchange));
                        $fields['separatorAddress']['state']         = array('type'=>'textbox', 'title'=>A::t('appointments', 'State/Province'), 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'text', 'maxLength'=>64, 'unique'=>false), 'htmlOptions'=>array('maxlength'=>'64', 'autocomplete'=>'off'));

                        $fields['separatorAccount'] = array();
                        $fields['separatorAccount']['separatorInfo'] = array('legend'=>A::t('appointments', 'Account Information'));
                        $fields['separatorAccount']['email']         = array('type'=>'textbox', 'title'=>A::t('appointments', 'Email'), 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'email', 'maxLength'=>100, 'unique'=>true), 'htmlOptions'=>array('maxlength'=>'100', 'autocomplete'=>'off', 'class'=>'middle'));
                        $fields['separatorAccount']['username']      = array('type'=>'label', 'title'=>A::t('appointments', 'Username'));
                        $fields['separatorAccount']['password']      = array('type'=>'password', 'title'=>A::t('appointments', 'Password'), 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'password', 'minLength'=>6, 'maxlength'=>25), 'encryption'=>array('enabled'=>CConfig::get('password.encryption'), 'encryptAlgorithm'=>CConfig::get('password.encryptAlgorithm'), 'encryptSalt'=>$salt), 'htmlOptions'=>array('maxlength'=>'25', 'placeholder'=>'&#9679;&#9679;&#9679;&#9679;&#9679;'));
                        $fields['separatorAccount']['language_code'] = array('type'=>'select', 'title'=>A::t('appointments', 'Preferred Language'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'set', 'source'=>array_keys($langList)), 'data'=>$langList);
                        $fields['separatorAccount']['is_active']     = array('type'=>'checkbox', 'title'=>A::t('appointments', 'Active'), 'default'=>'1', 'validation'=>array('type'=>'set', 'source'=>array(0,1)));
                        if($removalType == 'logical' || $removalType == 'physical_and_logical') $fields['separatorAccount']['is_removed'] = array('type'=>'checkbox', 'title'=>A::t('appointments', 'Removed'), 'default'=>'0', 'validation'=>array('type'=>'set', 'source'=>array(0,1)));

                        $fields['separatorOther'] = array();
                        $fields['separatorOther']['separatorInfo']            = array('legend'=>A::t('appointments', 'Other'));
                        $fields['separatorOther']['notifications']            = array('type'=>'checkbox', 'title'=>A::t('appointments', 'Notifications'), 'default'=>'0', 'validation'=>array('type'=>'set', 'source'=>array(0,1)));

                        echo CWidget::create('CDataForm', array(
                            'model'=>'Modules\Appointments\Models\Patients',
                            'primaryKey'=>$id,
                            'operationType'=>'edit',
                            'action'=>'patients/editAccount',
                            'successUrl'=>'patients/myAccount',
                            'cancelUrl'=>'patients/myAccount',
                            'method'=>'post',
                            'htmlOptions'=>array(
                                'id'=>'frmPatientEdit',
                                'class'=>'patient-form',
                                'name'=>'frmPatientEdit',
                                'autoGenerateId'=>true,
                                'enctype' => 'multipart/form-data',
                            ),
                            'requiredFieldsAlert'=>false,
                            'fields'=>$fields,
                            'buttons'=>array(
                                'submitUpdateClose'=>array('type'=>'submit', 'value'=>A::t('appointments', 'Update & Close'), 'htmlOptions'=>array('name'=>'btnUpdateClose', 'class'=>'button')),
                                'submitUpdate'=>array('type'=>'submit', 'value'=>A::t('appointments', 'Update'), 'htmlOptions'=>array('name'=>'btnUpdate', 'class'=>'button')),
                                'reset'=>array('type'=>'reset', 'value'=>A::t('appointments', 'Reset'), 'htmlOptions'=>array('class'=>'button white')),
                                'cancel'=>array('type'=>'button', 'value'=>A::t('appointments', 'Cancel'), 'htmlOptions'=>array('name'=>'', 'class'=>'button white')),
                            ),
                            'messagesSource'=>'core',
                            'return'=>true,
                        ));

                    ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php
    A::app()->getClientScript()->registerScript(
        'patientsChangeCountry',
        '$(document).ready(function(){
            appointments_ChangeCountry(
                "frmPatientEdit","'.$countryCode.'", "'.$stateCode.'"
            );
            jQuery("#frmPatientEdit_languages_spoken").chosen();
        });'
    );
