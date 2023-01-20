<div id="create-patient-modal" class="dialog-window" style="display: none;">
    <div style="display:none" id="messageSuccess">
        <p class="alert alert-success"><?= A::t('appointments', 'Account successfully created!'); ?></p>
    </div>
    <p style="display:none" class="alert alert-error" id="messageError"><?= (APPHP_MODE == 'demo') ? A::t('appointments', 'This operation is blocked in Demo Mode!') : A::t('appointments', 'An error occurred while registration process! Please try again later.'); ?></p>

    <?php

        $formName = 'createPatientPopupForm';
        $fields = array();

        $fields['separatorPersonal'] = array();
        $fields['separatorPersonal']['separatorInfo']      = array('legend'=>A::t('appointments', 'Personal Information'));
        $fields['separatorPersonal']['first_name'] = array('type'=>'textbox', 'title'=>A::t('appointments', 'First Name'), 'default'=>'', 'mandatoryStar'=>true, 'htmlOptions'=>array('maxlength'=>32, 'data-required'=>true));
        $fields['separatorPersonal']['first_name_error'] = array('type'=>'label',    	'title'=>'', 'value'=>A::t('appointments', 'The field first name cannot be empty!'), 'definedValues'=>array(), 'format'=>'', 'stripTags'=>false, 'htmlOptions'=>array('id'=>'firstNameErrorEmpty', 'class'=>'error', 'style'=>'display:none'));
        $fields['separatorPersonal']['last_name']  = array('type'=>'textbox', 'title'=>A::t('appointments', 'Last Name'), 'default'=>'', 'mandatoryStar'=>true, 'htmlOptions'=>array('maxlength'=>32, 'data-required'=>true));
        $fields['separatorPersonal']['last_name_error'] = array('type'=>'label',    	'title'=>'', 'value'=>A::t('appointments', 'The field last name cannot be empty!'), 'definedValues'=>array(), 'format'=>'', 'stripTags'=>false, 'htmlOptions'=>array('id'=>'lastNameErrorEmpty', 'class'=>'error', 'style'=>'display:none'));
        $fields['separatorPersonal']['gender']             = array('type'=>'select', 'title'=>A::t('appointments', 'Gender'), 'default'=>'', 'mandatoryStar'=>true, 'data'=>$genders, 'emptyOption'=>true, 'emptyValue'=>'- '.A::t('appointments', 'select').' -', 'htmlOptions'=>array('maxlength'=>32, 'style'=>'width:210px;', 'data-required'=>true));
        $fields['separatorPersonal']['gender_error'] = array('type'=>'label',    	'title'=>'', 'value'=>A::t('appointments', 'The field gender cannot be empty!'), 'definedValues'=>array(), 'format'=>'', 'stripTags'=>false, 'htmlOptions'=>array('id'=>'genderErrorEmpty', 'class'=>'error', 'style'=>'display:none'));

        $fields['separatorContact'] = array();
        $fields['separatorContact']['separatorInfo'] = array('legend'=>A::t('appointments', 'Contact Information'));
        $fields['separatorContact']['phone'] = array('type'=>'textbox', 'title'=>A::t('appointments', 'Phone'), 'default'=>'', 'mandatoryStar'=>false, 'htmlOptions'=>array('maxlength'=>32, 'autocomplete'=>'off', 'data-required'=>false));
        $fields['separatorContact']['phone_error'] = array('type'=>'label',    	'title'=>'', 'value'=>A::t('appointments', 'The field phone cannot be empty!'), 'definedValues'=>array(), 'format'=>'', 'stripTags'=>false, 'htmlOptions'=>array('id'=>'phoneErrorEmpty', 'class'=>'error', 'style'=>'display:none'));

        $fields['separatorAccount'] = array();
        $fields['separatorAccount']['separatorInfo'] = array('legend'=>A::t('appointments', 'Account Information'));
        $fields['separatorAccount']['email'] = array('type'=>'textbox', 'title'=>A::t('appointments', 'Email'), 'default'=>'', 'mandatoryStar'=>true, 'htmlOptions'=>array('maxlength'=>100, 'autocomplete'=>'off', 'class'=>'middle', 'data-required'=>true));
        $fields['separatorAccount']['email_error'] = array('type'=>'label',    	'title'=>'', 'value'=>A::t('appointments', 'The field email cannot be empty!'), 'definedValues'=>array(), 'format'=>'', 'stripTags'=>false, 'htmlOptions'=>array('id'=>'emailErrorEmpty', 'class'=>'error', 'style'=>'display:none'));
        $fields['separatorAccount']['email_error_valid'] = array('type'=>'label',    	'title'=>'', 'value'=>A::t('appointments', 'You must provide a valid email address!'), 'definedValues'=>array(), 'format'=>'', 'stripTags'=>false, 'htmlOptions'=>array('id'=>'emailErrorValid', 'class'=>'error', 'style'=>'display:none'));
        $fields['separatorAccount']['username'] = array('type'=>'textbox', 'title'=>A::t('appointments', 'Username'), 'default'=>'', 'mandatoryStar'=>true, 'htmlOptions'=>array('maxlength'=>32, 'autocomplete'=>'off', 'class'=>'middle', 'data-required'=>true));
        $fields['separatorAccount']['username_error'] = array('type'=>'label',    	'title'=>'', 'value'=>A::t('appointments', 'The field username cannot be empty!'), 'definedValues'=>array(), 'format'=>'', 'stripTags'=>false, 'htmlOptions'=>array('id'=>'usernameErrorEmpty', 'class'=>'error', 'style'=>'display:none'));
        $fields['separatorAccount']['password'] = array('type'=>'password', 'title'=>A::t('appointments', 'Password'), 'default'=>'', 'mandatoryStar'=>true, 'encryption'=>array('enabled'=>CConfig::get('password.encryption'), 'encryptAlgorithm'=>CConfig::get('password.encryptAlgorithm'), 'encryptSalt'=>$salt), 'htmlOptions'=>array('maxlength'=>25, 'class'=>'middle', 'placeholder'=>'&#9679;&#9679;&#9679;&#9679;&#9679;', 'data-required'=>true));
        $fields['separatorAccount']['password_error'] = array('type'=>'label',    	'title'=>'', 'value'=>A::t('appointments', 'The field password cannot be empty!'), 'definedValues'=>array(), 'format'=>'', 'stripTags'=>false, 'htmlOptions'=>array('id'=>'passwordErrorEmpty', 'class'=>'error', 'style'=>'display:none'));
        $fields['separatorAccount']['confirm_password'] = array('type'=>'password', 'title'=>A::t('appointments', 'Confirm Password'), 'default'=>'', 'mandatoryStar'=>true, 'htmlOptions'=>array('maxlength'=>25, 'class'=>'middle', 'placeholder'=>'&#9679;&#9679;&#9679;&#9679;&#9679;', 'data-required'=>true));
        $fields['separatorAccount']['confirm_password_error'] = array('type'=>'label',    	'title'=>'', 'value'=>A::t('appointments', 'The field confirm password cannot be empty!'), 'definedValues'=>array(), 'format'=>'', 'stripTags'=>false, 'htmlOptions'=>array('id'=>'confirmPasswordErrorEmpty', 'class'=>'error', 'style'=>'display:none'));
        $fields['separatorAccount']['confirm_password_error_equal'] = array('type'=>'label',    	'title'=>'', 'value'=>A::t('appointments', 'The password field must match the password confirmation field!'), 'definedValues'=>array(), 'format'=>'', 'stripTags'=>false, 'htmlOptions'=>array('id'=>'confirmPasswordErrorEqual', 'class'=>'error', 'style'=>'display:none'));
        $fields['separatorAccount']['salt'] = array('type'=>'hidden', 'default'=>$salt);
        $fields['i_agree'] = array('type'=>'checkbox', 	'title'=>'', 'tooltip'=>'', 'mandatoryStar'=>false, 'value'=>'', 'checked'=>true, 'htmlOptions'=>array('id'=>'i_agree', 'style'=>'display:none'), 'viewType'=>'custom');

        echo CWidget::create('CFormView', array(
            'action'=>'',
            'successUrl'=>'',
            'cancelUrl'=>'',
            'method'=>'post',
            'htmlOptions'=>array(
                'id'=>$formName,
                'name'=>$formName,
                'autoGenerateId'=>true
            ),
            'requiredFieldsAlert'=>true,
            'fields'=>$fields,
            'buttons'=>array(
                'custom' =>array('type'=>'button', 'value'=>A::t('appointments', 'Create'), 'htmlOptions'=>array('class'=>'button_small', 'id'=>'create-patient-button', 'data-sending'=>A::t('appointments', 'Sending...'), 'data-send'=>A::t('appointments', 'Create'))),
            ),
            'buttonsPosition'   =>  'bottom',
            'return'            => true,
        ));
    ?>
</div>
<?php
A::app()->getClientScript()->registerScript(
    'createPatientPopup',
    "jQuery(document).ready(function() {
        jQuery('#create-patient').on('click', function(){
            jQuery('#create-patient-modal').dialog({height:720,width:600,zIndex:9999999});
        });
        
        jQuery('#create-patient-button').on('click', function(){
            return appointments_RegistrationForm(this, '".$formName."', 'patients');
        });
        
    });",
    2
);
?>