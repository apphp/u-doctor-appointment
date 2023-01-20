<?php
    $this->_pageTitle = A::t('appointments', 'Doctor Registration');
    $this->_breadCrumbs = array(
        array('label' => A::t('appointments', 'Home'), 'url'=>Website::getDefaultPage()),
        array('label' => A::t('appointments', 'Doctor Login'), 'url'=>'doctors/login'),
        array('label' => A::t('appointments', 'Doctor Registration'))
    );

    //A::app()->getClientScript()->registerScriptFile('assets/vendors/chosen/chosen.jquery.min.js',2);
    //A::app()->getClientScript()->registerCssFile('assets/vendors/chosen/chosen.min.css');

    $requiredChar = ' &#42; ';
?>
    <section id="content" role="main">
        <div class="entry">
            <div class="cmsms_cc">
                <div class="one_first first_column">
                    <div class="cmsms-form-builder">
                    <?php
                        echo '<div style="display:none" id="messageSuccess">'.PHP_EOL;
                        echo '<p class="alert alert-success">'.$messageSuccess.'</p>'.PHP_EOL;
                        echo '<p class="alert alert-info">'.$messageInfo.'</p>'.PHP_EOL;
                        echo '</div>'.PHP_EOL;

                        $errorMsg = (APPHP_MODE == 'demo') ? A::t('appointments', 'This operation is blocked in Demo Mode!') : A::t('appointments', 'An error occurred while registration process! Please try again later.');
                        echo '<p style="display:none" class="alert alert-error" id="messageError">'.$errorMsg.'</p>'.PHP_EOL;

                        echo '<div class="registration-form-content">'.PHP_EOL;
                        echo CHtml::openForm('doctors/registration', 'post', array('name'=>'frmDoctorRegistration', 'id'=>'frmDoctorRegistration', 'class'=>'doctor-form')).PHP_EOL;
                        echo CHtml::hiddenField('act', 'send').PHP_EOL;

                        //echo CHtml::tag('p',array(),A::t('appointments', 'Please fill out the form below to perform registration.'));

                        $showTitles = array(''=>'- '.A::t('appointments', 'select').' -');
                        $showTitles = $showTitles + $titles;
                        $personalInfo = '';
                        $personalInfo .= '<div class="form_info cmsms_input">'.PHP_EOL;
                        $personalInfo .= '<label>'.$requiredChar.A::t('appointments', 'Title').'</label>'.PHP_EOL;
                        $personalInfo .= '<div class="form_field_wrap">'.PHP_EOL;
                        $personalInfo .= CHtml::dropDownList('title_id', '', $showTitles, array('data-required'=>true));
                        $personalInfo .= '<p class="error" style="display:none" id="titleErrorEmpty">'.A::t('appointments', 'The field title cannot be empty!').'</p>'.PHP_EOL;
                        $personalInfo .= '</div>'.PHP_EOL;
                        $personalInfo .= '</div>'.PHP_EOL;

                        $personalInfo .= '<div class="form_info cmsms_input">'.PHP_EOL;
                        $personalInfo .= '<label>'.$requiredChar.A::t('appointments', 'First Name').'</label>'.PHP_EOL;
                        $personalInfo .= '<div class="form_field_wrap">'.PHP_EOL;
                        $personalInfo .= '<input name="first_name" type="text" data-required="true" maxlength="32" autocomplete="off" />'.PHP_EOL;
                        $personalInfo .= '<p class="error" style="display:none" id="firstNameErrorEmpty">'.A::t('appointments', 'The field first name cannot be empty!').'</p>'.PHP_EOL;
                        $personalInfo .= '</div>'.PHP_EOL;
                        $personalInfo .= '</div>'.PHP_EOL;

                        //$personalInfo .= '<div class="form_info cmsms_input">'.PHP_EOL;
                        //$personalInfo .= '<label>'.A::t('appointments', 'Middle Name').'</label>'.PHP_EOL;
                        //$personalInfo .= '<div class="form_field_wrap">'.PHP_EOL;
                        //$personalInfo .= '<input name="middle_name" type="text" data-required="false" maxlength="32" autocomplete="off" />'.PHP_EOL;
                        ////$personalInfo .= '<p class="error" style="display:none" id="middleNameErrorEmpty">'.A::t('appointments', 'The field middle name cannot be empty!').'</p>'.PHP_EOL;
                        //$personalInfo .= '</div>'.PHP_EOL;
                        //$personalInfo .= '</div>'.PHP_EOL;

                        $personalInfo .= '<div class="form_info cmsms_input">'.PHP_EOL;
                        $personalInfo .= '<label>'.$requiredChar.A::t('appointments', 'Last Name').'</label>'.PHP_EOL;
                        $personalInfo .= '<div class="form_field_wrap">'.PHP_EOL;
                        $personalInfo .= '<input name="last_name" type="text" data-required="true" maxlength="32" autocomplete="off" />'.PHP_EOL;
                        $personalInfo .= '<p class="error" style="display:none" id="lastNameErrorEmpty">'.A::t('appointments', 'The field last name cannot be empty!').'</p>'.PHP_EOL;
                        $personalInfo .= '</div>'.PHP_EOL;
                        $personalInfo .= '</div>'.PHP_EOL;

                        $showGenders = array(''=>'- '.A::t('appointments', 'select').' -');
                        $showGenders = $showGenders + $genders;
                        $personalInfo .= '<div class="form_info cmsms_input">'.PHP_EOL;
                        $personalInfo .= '<label>'.$requiredChar.A::t('appointments', 'Gender').'</label>'.PHP_EOL;
                        $personalInfo .= '<div class="form_field_wrap">'.PHP_EOL;
                        $personalInfo .= CHtml::dropDownList('gender', '', $showGenders, array('data-required'=>true));
                        $personalInfo .= '<p class="error" style="display:none" id="genderErrorEmpty">'.A::t('appointments', 'The field gender cannot be empty!').'</p>'.PHP_EOL;
                        $personalInfo .= '</div>'.PHP_EOL;
                        $personalInfo .= '</div>'.PHP_EOL;

                        //$personalInfo .= '<div class="form_info cmsms_input">'.PHP_EOL;
                        //$personalInfo .= '<label>'.A::t('appointments', 'Birth Date').'</label>'.PHP_EOL;
                        //$personalInfo .= '<div class="form_field_wrap">'.PHP_EOL;
                        //$personalInfo .= '<input name="birth_date" type="text" data-required="false" maxlength="9" autocomplete="off" />'.PHP_EOL;
                        //// $personalInfo .= '<p class="error" style="display:none" id="birthDateErrorEmpty">'.A::t('appointments', 'The field birth date cannot be empty!').'</p>'.PHP_EOL;
                        //$personalInfo .= '</div>'.PHP_EOL;
                        //$personalInfo .= '</div>'.PHP_EOL;

                        //$format = 'yy-mm-dd';
                        //A::app()->getClientScript()->registerCssFile('assets/vendors/jquery/jquery-ui.min.css');
                        //A::app()->getClientScript()->registerScriptFile('assets/vendors/jquery/jquery-ui.min.js', 1);
                        //[> formats: dd/mm/yy | d M, y | mm/dd/yy  | yy-mm-dd  | <]
                        //A::app()->getClientScript()->registerScript(
                        //    'datepicker',
                        //    '$("#birth_date").datepicker({
                        //        showOn: "button",
                        //        buttonImage: "assets/vendors/jquery/images/calendar.png",
                        //        buttonImageOnly: true,
                        //        showWeek: false,
                        //        firstDay: 1,
                        //        maxDate: -1,
                        //        dateFormat: "'.$format.'",
                        //        changeMonth: true,
                        //        changeYear: true,
                        //        appendText : "'.A::t('appointments', 'Format').': yyyy-mm-dd"
                        //    });'
                        //);

                        echo CHtml::openTag('fieldset');
                        echo CHtml::tag('legend', '', A::t('appointments', 'Personal Information'));
                        echo $personalInfo;
                        echo CHtml::closeTag('fieldset');


                        //$contactInfo = '';
                        //$contactInfo .= CHtml::openTag('div', array('class'=>'form_info cmsms_input'));
                        //$contactInfo .= CHtml::tag('label', array(), A::t('appointments', 'Work Phone'));
                        //$contactInfo .= '<div class="form_field_wrap">'.PHP_EOL;
                        //$contactInfo .= CHtml::textField('work_phone', '', array('data-required'=>'false', 'maxlength'=>'32', 'autocomplete'=>'off'));
                        ////$contactInfo .= CHtml::tag('p', array('class'=>'error', 'style'=>'display:none', 'id'=>'workPhoneErrorEmpty'), A::t('appointments', 'The field work phone cannot be empty!'));
                        //$contactInfo .= CHtml::closeTag('div');
                        //$contactInfo .= CHtml::closeTag('div');

                        //$contactInfo .= CHtml::openTag('div', array('class'=>'form_info cmsms_input'));
                        //$contactInfo .= CHtml::tag('label', array(), A::t('appointments', 'Work Mobile Phone'));
                        //$contactInfo .= '<div class="form_field_wrap">'.PHP_EOL;
                        //$contactInfo .= CHtml::textField('work_mobile_phone', '', array('data-required'=>'false', 'maxlength'=>'32', 'autocomplete'=>'off'));
                        ////$contactInfo .= CHtml::tag('p', array('class'=>'error', 'style'=>'display:none', 'id'=>'workMobilePhoneErrorEmpty'), A::t('appointments', 'The field work mobile phone cannot be empty!'));
                        //$contactInfo .= CHtml::closeTag('div');
                        //$contactInfo .= CHtml::closeTag('div');

                        //$contactInfo .= CHtml::openTag('div', array('class'=>'form_info cmsms_input'));
                        //$contactInfo .= CHtml::tag('label', array(), A::t('appointments', 'Phone'));
                        //$contactInfo .= '<div class="form_field_wrap">'.PHP_EOL;
                        //$contactInfo .= CHtml::textField('phone', '', array('data-required'=>'false', 'maxlength'=>'32', 'autocomplete'=>'off'));
                        ////$contactInfo .= CHtml::tag('p', array('class'=>'error', 'style'=>'display:none', 'id'=>'phoneErrorEmpty'), A::t('appointments', 'The field phone cannot be empty!'));
                        //$contactInfo .= CHtml::closeTag('div');
                        //$contactInfo .= CHtml::closeTag('div');

                        //$contactInfo .= CHtml::openTag('div', array('class'=>'form_info cmsms_input'));
                        //$contactInfo .= CHtml::tag('label', array(), A::t('appointments', 'Fax'));
                        //$contactInfo .= '<div class="form_field_wrap">'.PHP_EOL;
                        //$contactInfo .= CHtml::textField('fax', '', array('data-required'=>false, 'maxlength'=>'32', 'autocomplete'=>'off'));
                        ////$contactInfo .= CHtml::tag('p', array('class'=>'error', 'style'=>'display:none', 'id'=>'faxErrorEmpty'), A::t('appointments', 'The field fax cannot be empty!'));
                        //$contactInfo .= CHtml::closeTag('div');
                        //$contactInfo .= CHtml::closeTag('div');

                        //echo CHtml::openTag('fieldset');
                        //echo CHtml::tag('legend', '', A::t('appointments', 'Contact Information'));
                        //echo $contactInfo;
                        //echo CHtml::closeTag('fieldset');


                        //$addressInfo = '';
                        //$addressInfo .= CHtml::openTag('div', array('class'=>'form_info cmsms_input'));
                        //$addressInfo .= CHtml::tag('label', array(), $requiredChar.A::t('appointments', 'Address'));
                        //$addressInfo .= '<div class="form_field_wrap">'.PHP_EOL;
                        //$addressInfo .= CHtml::textField('address', '', array('data-required'=>true, 'maxlength'=>'64', 'autocomplete'=>'off'));
                        //$addressInfo .= CHtml::tag('p', array('class'=>'error', 'style'=>'display:none', 'id'=>'addressErrorEmpty'), A::t('appointments', 'The field address cannot be empty!'));
                        //$addressInfo .= CHtml::closeTag('div');
                        //$addressInfo .= CHtml::closeTag('div');

                        //$addressInfo .= CHtml::openTag('div', array('class'=>'form_info cmsms_input'));
                        //$addressInfo .= CHtml::tag('label', array(), A::t('appointments', 'Address (line 2)'));
                        //$addressInfo .= '<div class="form_field_wrap">'.PHP_EOL;
                        //$addressInfo .= CHtml::textField('address_2', '', array('data-required'=>false, 'maxlength'=>'64', 'autocomplete'=>'off'));
                        ////$addressInfo .= CHtml::tag('p', array('class'=>'error', 'style'=>'display:none', 'id'=>'address2ErrorEmpty'), A::t('appointments', 'The field address (line 2) cannot be empty!'));
                        //$addressInfo .= CHtml::closeTag('div');
                        //$addressInfo .= CHtml::closeTag('div');

                        //$addressInfo .= CHtml::openTag('div', array('class'=>'form_info cmsms_input'));
                        //$addressInfo .= CHtml::tag('label', array(), $requiredChar.A::t('appointments', 'City'));
                        //$addressInfo .= '<div class="form_field_wrap">'.PHP_EOL;
                        //$addressInfo .= CHtml::textField('city', '', array('data-required'=>true, 'maxlength'=>'64', 'autocomplete'=>'off'));
                        //$addressInfo .= CHtml::tag('p', array('class'=>'error', 'style'=>'display:none', 'id'=>'cityErrorEmpty'), A::t('appointments', 'The field city cannot be empty!'));
                        //$addressInfo .= CHtml::closeTag('div');
                        //$addressInfo .= CHtml::closeTag('div');

                        //$addressInfo .= CHtml::openTag('div', array('class'=>'form_info cmsms_input'));
                        //$addressInfo .= CHtml::tag('label', array(), $requiredChar.A::t('appointments', 'Zip Code'));
                        //$addressInfo .= '<div class="form_field_wrap">'.PHP_EOL;
                        //$addressInfo .= CHtml::textField('zip_code', '', array('data-required'=>true, 'maxlength'=>'32', 'autocomplete'=>'off'));
                        //$addressInfo .= CHtml::tag('p', array('class'=>'error', 'style'=>'display:none', 'id'=>'zipcodeErrorEmpty'), A::t('appointments', 'The field zip code cannot be empty!'));
                        //$addressInfo .= CHtml::closeTag('div');
                        //$addressInfo .= CHtml::closeTag('div');

                        //$addressInfo .= CHtml::openTag('div', array('class'=>'form_info cmsms_select'));
                        //$addressInfo .= CHtml::tag('label', array(), A::t('appointments', 'Country'));
                        //$onchange = "appointments_ChangeCountry('frmDoctorRegistration',this.value)";
                        //$addressInfo .= '<div class="form_field_wrap">'.PHP_EOL;
                        //$addressInfo .= CHtml::dropDownList('country_code', $defaultCountryCode, $countries, array('data-required'=>false, 'onchange'=>$onchange));
                        ////$addressInfo .= CHtml::tag('p', array('class'=>'error', 'style'=>'display:none', 'id'=>'countryCodeErrorEmpty'), A::t('appointments', 'The field country cannot be empty!'));
                        //$addressInfo .= CHtml::closeTag('div');
                        //$addressInfo .= CHtml::closeTag('div');

                        //$addressInfo .= CHtml::openTag('div', array('class'=>'form_info cmsms_input'));
                        //$addressInfo .= CHtml::tag('label', array(), A::t('appointments', 'State/Province'));
                        //$addressInfo .= '<div class="form_field_wrap">'.PHP_EOL;
                        //$addressInfo .= CHtml::textField('state', '', array('data-required'=>false, 'maxlength'=>'64', 'autocomplete'=>'off'));
                        ////$addressInfo .= CHtml::tag('p', array('class'=>'error', 'style'=>'display:none', 'id'=>'stateErrorEmpty'), A::t('appointments', 'The field state cannot be empty!'));
                        //$addressInfo .= CHtml::closeTag('div');
                        //$addressInfo .= CHtml::closeTag('div');

                        //echo CHtml::openTag('fieldset');
                        //echo CHtml::tag('legend', '', A::t('appointments', 'Address Information'));
                        //echo $addressInfo;
                        //echo CHtml::closeTag('fieldset');


                        $accountInfo = '';
                        $accountInfo .= CHtml::openTag('div', array('class'=>'form_info cmsms_input'));
                        $accountInfo .= CHtml::tag('label', array(), $requiredChar.A::t('appointments', 'Email'));
                        $accountInfo .= '<div class="form_field_wrap">'.PHP_EOL;
                        $accountInfo .= CHtml::textField('email', '', array('data-required'=>true, 'maxlength'=>'100', 'autocomplete'=>'off'));
                        $accountInfo .= CHtml::tag('p', array('class'=>'error', 'style'=>'display:none', 'id'=>'emailErrorEmpty'), A::t('appointments', 'The field email cannot be empty!'));
                        $accountInfo .= CHtml::tag('p', array('class'=>'error', 'style'=>'display:none', 'id'=>'emailErrorValid'), A::t('appointments', 'You must provide a valid email address!'));
                        $accountInfo .= CHtml::closeTag('div');
                        $accountInfo .= CHtml::closeTag('div');

                        $accountInfo .= CHtml::openTag('div', array('class'=>'form_info cmsms_input'));
                        $accountInfo .= CHtml::tag('label', array(), $requiredChar.A::t('appointments', 'Username'));
                        $accountInfo .= '<div class="form_field_wrap">'.PHP_EOL;
                        $accountInfo .= CHtml::textField('username', '', array('data-required'=>'true', 'maxlength'=>'32', 'autocomplete'=>'off'));
                        $accountInfo .= CHtml::tag('p', array('class'=>'error', 'style'=>'display:none', 'id'=>'usernameErrorEmpty'), A::t('appointments', 'The field username cannot be empty!'));
                        $accountInfo .= CHtml::closeTag('div');
                        $accountInfo .= CHtml::closeTag('div');

                        $accountInfo .= CHtml::openTag('div', array('class'=>'form_info cmsms_input'));
                        $accountInfo .= CHtml::tag('label', array(), $requiredChar.A::t('appointments', 'Password'));
                        $accountInfo .= '<div class="form_field_wrap">'.PHP_EOL;
                        $accountInfo .= CHtml::passwordField('password', '', array('data-required'=>'true', 'maxlength'=>'25', 'autocomplete'=>'off'));
                        $accountInfo .= CHtml::tag('p', array('class'=>'error', 'style'=>'display:none', 'id'=>'passwordErrorEmpty'), A::t('appointments', 'The field password cannot be empty!'));
                        $accountInfo .= CHtml::closeTag('div');
                        $accountInfo .= CHtml::closeTag('div');

                        $accountInfo .= CHtml::openTag('div', array('class'=>'form_info cmsms_input'));
                        $accountInfo .= CHtml::tag('label', array(), $requiredChar.A::t('appointments', 'Confirm Password'));
                        $accountInfo .= '<div class="form_field_wrap">'.PHP_EOL;
                        $accountInfo .= CHtml::passwordField('confirm_password', '', array('data-required'=>'true', 'maxlength'=>'25', 'autocomplete'=>'off'));
                        $accountInfo .= CHtml::tag('p', array('class'=>'error', 'style'=>'display:none', 'id'=>'confirmPasswordErrorEmpty'), A::t('appointments', 'The field confirm password cannot be empty!'));
                        $accountInfo .= CHtml::tag('p', array('class'=>'error', 'style'=>'display:none', 'id'=>'confirmPasswordErrorEqual'), A::t('appointments', 'The password field must match the password confirmation field!'));
                        $accountInfo .= CHtml::closeTag('div');
                        $accountInfo .= CHtml::closeTag('div');

                        echo CHtml::openTag('fieldset');
                        echo CHtml::tag('legend', '', A::t('appointments', 'Account Information'));
                        echo $accountInfo;
                        // Notifications
                        echo CHtml::openTag('div', array('class'=>'row'));
                        echo CHtml::tag('label', array('for'=>'notifications'), A::t('appointments', 'Send Notifications'));
                        echo CHtml::checkBox('notifications', false, array());
                        echo CHtml::closeTag('div');

                        echo CHtml::openTag('div', array('class'=>'form_info cmsms_input'));
                        echo CHtml::tag('div',array('id'=>'licensie-cap'), '&nbsp;');
                        echo CHtml::openTag('div', array('id'=>'licensie-block'));
                        echo '<input type="checkbox" id="i_agree" name="i_agree" data-required="true" value="1">'.A::t('appointments', 'By signing up, I agree to the {terms_and_conditions}',
                            array(
                                '{terms_and_conditions}'=>'<a target="_blank" rel="noopener noreferrer" id="linkTermCondition" href="doctors/termsConditions">'.A::t('appointments', 'Terms & Conditions').'</a>',
                            ));
                        echo '<p class="error" style="display:none; margin-top:10px; margin-bottom:0;" id="iAgreeError">'.A::t('appointments', 'You must agree with the terms and conditions before you create an account.').'</p>';
                        echo CHtml::closeTag('div');
                        echo CHtml::closeTag('div');
                        echo CHtml::closeTag('fieldset');

                        //$professionalInfo  = '';
                        //$showDegrees = array(''=>'- '.A::t('appointments', 'select').' -');
                        //$showDegrees = $showDegrees + $degrees;
                        //$professionalInfo .= CHtml::openTag('div', array('class'=>'form_info cmsms_input'));
                        //$professionalInfo .= CHtml::tag('label', array(), A::t('appointments', 'Degree'));
                        //$onchange = "appointments_ChangeCountry('frmDoctorRegistration',this.value)";
                        //$professionalInfo .= '<div class="form_field_wrap">'.PHP_EOL;
                        //$professionalInfo .= CHtml::dropDownList('degree_id', '', $showDegrees, array('data-required'=>false));
                        ////$addressInfo .= CHtml::tag('p', array('class'=>'error', 'style'=>'display:none', 'id'=>'degreeErrorEmpty'), A::t('appointments', 'The field degree cannot be empty!'));
                        //$professionalInfo .= CHtml::closeTag('div');
                        //$professionalInfo .= CHtml::closeTag('div');

                        //$professionalInfo .= CHtml::openTag('div', array('class'=>'form_info cmsms_input'));
                        //$professionalInfo .= CHtml::tag('label', array(), A::t('appointments', 'Additional Degree'));
                        //$professionalInfo .= '<div class="form_field_wrap">'.PHP_EOL;
                        //$professionalInfo .= CHtml::textField('additional_degree', '', array('data-required'=>false, 'maxlength'=>'50', 'autocomplete'=>'off'));
                        ////$professionalInfo .= CHtml::tag('p', array('class'=>'error', 'style'=>'display:none', 'id'=>'additionalDegreeErrorEmpty'), A::t('appointments', 'The field additional degree cannot be empty!'));
                        //$professionalInfo .= CHtml::closeTag('div');
                        //$professionalInfo .= CHtml::closeTag('div');

                        //$professionalInfo .= CHtml::openTag('div', array('class'=>'form_info cmsms_input'));
                        //$professionalInfo .= CHtml::tag('label', array(), A::t('appointments', 'License Number'));
                        //$professionalInfo .= '<div class="form_field_wrap">'.PHP_EOL;
                        //$professionalInfo .= CHtml::textField('license_number', '', array('data-required'=>false, 'maxlength'=>'30', 'autocomplete'=>'off'));
                        ////$professionalInfo .= CHtml::tag('p', array('class'=>'error', 'style'=>'display:none', 'id'=>'licenseNumberErrorEmpty'), A::t('appointments', 'The field license number cannot be empty!'));
                        //$professionalInfo .= CHtml::closeTag('div');
                        //$professionalInfo .= CHtml::closeTag('div');

                        //$professionalInfo .= CHtml::openTag('div', array('class'=>'form_info cmsms_input'));
                        //$professionalInfo .= CHtml::tag('label', array(), A::t('appointments', 'Education'));
                        //$professionalInfo .= '<div class="form_field_wrap">'.PHP_EOL;
                        //$professionalInfo .= CHtml::textArea('education', '', array('data-required'=>false, 'maxlength'=>'255'));
                        ////$professionalInfo .= CHtml::tag('p', array('class'=>'error', 'style'=>'display:none', 'id'=>'educationErrorEmpty'), A::t('appointments', 'The field education cannot be empty!'));
                        //$professionalInfo .= CHtml::closeTag('div');
                        //$professionalInfo .= CHtml::closeTag('div');

                        //$showExperience = array(''=>'- '.A::t('appointments', 'select').' -');
                        //$showExperience = $showExperience + $experienceYears;
                        //$professionalInfo .= CHtml::openTag('div', array('class'=>'form_info cmsms_input'));
                        //$professionalInfo .= $requiredChar.CHtml::tag('label', array(), A::t('appointments', 'Experience'));
                        //$professionalInfo .= '<div class="form_field_wrap">'.PHP_EOL;
                        //$professionalInfo .= CHtml::dropDownList('experience', '', $showExperience, array('data-required'=>true));
                        //$professionalInfo .= CHtml::tag('p', array('class'=>'error', 'style'=>'display:none', 'id'=>'experienceErrorEmpty'), A::t('appointments', 'The field experience cannot be empty!'));
                        //$professionalInfo .= CHtml::closeTag('div');
                        //$professionalInfo .= CHtml::closeTag('div');

                        //$professionalInfo .= CHtml::openTag('div', array('class'=>'form_info cmsms_input'));
                        //$professionalInfo .= CHtml::tag('label', array(), A::t('appointments', 'Residency Training'));
                        //$professionalInfo .= '<div class="form_field_wrap">'.PHP_EOL;
                        //$professionalInfo .= CHtml::textArea('residency_training', '', array('data-required'=>false, 'maxlength'=>'255'));
                        ////$professionalInfo .= CHtml::tag('p', array('class'=>'error', 'style'=>'display:none', 'id'=>'residencyTrainingErrorEmpty'), A::t('appointments', 'The field residency training cannot be empty!'));
                        //$professionalInfo .= CHtml::closeTag('div');
                        //$professionalInfo .= CHtml::closeTag('div');

                        //$professionalInfo .= CHtml::openTag('div', array('class'=>'form_info cmsms_input'));
                        //$professionalInfo .= CHtml::tag('label', array(), A::t('appointments', 'Hospital Affiliations'));
                        //$professionalInfo .= '<div class="form_field_wrap">'.PHP_EOL;
                        //$professionalInfo .= CHtml::textArea('hospital_affiliations', '', array('data-required'=>false, 'maxlength'=>'255'));
                        ////$professionalInfo .= CHtml::tag('p', array('class'=>'error', 'style'=>'display:none', 'id'=>'hospitalAffiliationsErrorEmpty'), A::t('appointments', 'The field hospital affiliations cannot be empty!'));
                        //$professionalInfo .= CHtml::closeTag('div');
                        //$professionalInfo .= CHtml::closeTag('div');

                        //$professionalInfo .= CHtml::openTag('div', array('class'=>'form_info cmsms_input'));
                        //$professionalInfo .= CHtml::tag('label', array(), A::t('appointments', 'Board Certifications'));
                        //$professionalInfo .= '<div class="form_field_wrap">'.PHP_EOL;
                        //$professionalInfo .= CHtml::textArea('board_certifications', '', array('data-required'=>false, 'maxlength'=>'255'));
                        ////$professionalInfo .= CHtml::tag('p', array('class'=>'error', 'style'=>'display:none', 'id'=>'boardCertificationsErrorEmpty'), A::t('appointments', 'The field board certifications cannot be empty!'));
                        //$professionalInfo .= CHtml::closeTag('div');
                        //$professionalInfo .= CHtml::closeTag('div');

                        //$professionalInfo .= CHtml::openTag('div', array('class'=>'form_info cmsms_input'));
                        //$professionalInfo .= CHtml::tag('label', array(), A::t('appointments', 'Awards and Publications'));
                        //$professionalInfo .= '<div class="form_field_wrap">'.PHP_EOL;
                        //$professionalInfo .= CHtml::textArea('awards_and_publications', '', array('data-required'=>false, 'maxlength'=>'255'));
                        ////$professionalInfo .= CHtml::tag('p', array('class'=>'error', 'style'=>'display:none', 'id'=>'awardsAndPublicationsErrorEmpty'), A::t('appointments', 'The field awards and publications cannot be empty!'));
                        //$professionalInfo .= CHtml::closeTag('div');
                        //$professionalInfo .= CHtml::closeTag('div');

                        //$professionalInfo .= CHtml::openTag('div', array('class'=>'form_info cmsms_input'));
                        //$professionalInfo .= CHtml::tag('label', array(), A::t('appointments', 'Languages Spoken'));
                        //$professionalInfo .= '<div class="form_field">'.PHP_EOL;
                        //$professionalInfo .= CHtml::dropDownList('languages_spoken', '', $localesList, array('data-required'=>true, 'multiple'=>true));
                        ////$addressInfo .= CHtml::tag('p', array('class'=>'error', 'style'=>'display:none', 'id'=>'languagesSpokenErrorEmpty'), A::t('appointments', 'The field languages spoken cannot be empty!'));
                        //$professionalInfo .= CHtml::closeTag('div');
                        //$professionalInfo .= CHtml::closeTag('div');

                        //$professionalInfo .= CHtml::openTag('div', array('class'=>'form_info cmsms_input'));
                        //$professionalInfo .= CHtml::tag('label', array(), A::t('appointments', 'Insurances Accepted'));
                        //$professionalInfo .= '<div class="form_field_wrap">'.PHP_EOL;
                        //$professionalInfo .= CHtml::textArea('insurances_accepted', '', array('data-required'=>false, 'maxlength'=>'255', 'autocomplete'=>'off'));
                        ////$professionalInfo .= CHtml::tag('p', array('class'=>'error', 'style'=>'display:none', 'id'=>'insurancesAcceptedErrorEmpty'), A::t('appointments', 'The field insurances accepted cannot be empty!'));
                        //$professionalInfo .= CHtml::closeTag('div');
                        //$professionalInfo .= CHtml::closeTag('div');

                        //$professionalInfo .= CHtml::openTag('div', array('class'=>'row'));
                        //$professionalInfo .= CHtml::tag('label', array('for'=>'notifications'), A::t('appointments', 'Send Notifications'));
                        //$professionalInfo .= CHtml::checkBox('notifications', false, array());
                        //$professionalInfo .= CHtml::closeTag('div');

                        //$professionalInfo .= CHtml::openTag('div', array('class'=>'form_info cmsms_input'));
                        //$professionalInfo .= CHtml::tag('div',array('id'=>'licensie-cap'), '&nbsp;');
                        //$professionalInfo .= CHtml::openTag('div', array('id'=>'licensie-block'));
                        //$professionalInfo .= '<input type="checkbox" id="i_agree" name="i_agree" data-required="true" value="1">'.A::t('appointments', 'By signing up, I agree to the {terms_and_conditions}',
                        //    array(
                        //        '{terms_and_conditions}'=>'<a target="_blank" rel="noopener noreferrer" id="linkTermCondition" href="doctors/termsConditions">'.A::t('appointments', 'Terms & Conditions').'</a>',
                        //    ));
                        //$professionalInfo .= '<p class="error" style="display:none; margin-top:10px; margin-bottom:0;" id="iAgreeError">'.A::t('appointments', 'You must agree with the terms and conditions before you create an account.').'</p>';
                        //$professionalInfo .= CHtml::closeTag('div');
                        //$professionalInfo .= CHtml::closeTag('div');

                        //echo CHtml::openTag('fieldset');
                        //echo CHtml::tag('legend', '', A::t('appointments', 'Professional Information'));
                        //echo $professionalInfo;
                        //echo CHtml::closeTag('fieldset');


                        if($verificationCaptcha){
                            echo CHtml::openTag('fieldset');
                            echo CHtml::tag('legend', '', A::t('appointments', 'Human Verification'));
                            echo CHtml::openTag('div', array('class'=>'form_info cmsms_input'));
                            echo CWidget::create('CCaptcha', array('math', true, array('return'=>true)));
                            echo CHtml::tag('p', array('class'=>'error', 'style'=>'display:none', 'id'=>'captchaError'), A::t('appointments', 'The field captcha cannot be empty!'));
                            echo CHtml::closeTag('div');
                            echo CHtml::closeTag('fieldset');
                        }

                        echo CHtml::openTag('div', array('class'=>'row'));
                        echo CHtml::tag('button', array('type'=>'button', 'class'=>'button', 'data-sending'=>A::t('appointments', 'Sending...'), 'data-send'=>A::t('appointments', 'Send'), 'onclick'=>'javascript:doctors_RegistrationForm(this)'), A::t('appointments', 'Register'));
                        echo CHtml::closeTag('div');
                        echo CHtml::closeForm();
                        echo CHtml::closeTag('div');


                        A::app()->getClientScript()->registerScript(
                            'doctorsChangeCountry',
                            '$(document).ready(function(){
                                appointments_ChangeCountry(
                                    "frmDoctorRegistration","'.$defaultCountryCode.'"
                                );
                            });'
                        );
                    ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php
    // Prepare Terms & Conditions
    $title = str_replace(array('"',"\n\r","\r\n","\r","\n","\t"), array("'",'','','','',''), A::t('appointments', 'Terms & Conditions'));
    $text = str_replace(array('"',"\n\r","\r\n","\r","\n","\t"), array("'",'','','','',''), $textTermsConditions);
    A::app()->getClientScript()->registerScript(
        'openModalRegistration',
        'jQuery(document).ready(function(){
            jQuery("#linkTermCondition").click(function(){
                modal({
                    type:alert,
                    title:"'.$title.'",
                    text: "'.$text.'"
                });
                return false;
            });
            jQuery("#languages_spoken").chosen();
        });'
    );
