/**
 * Validates form fields and submit the form
 * @param object el form element
 */
function appointments_RestorePasswordForm(el)
{
    if(el == null) return false;
    // define this to prevent name overlapping
    var $ = jQuery;

    var frm = $(el).closest('form');
    var re = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,7})+$/;

    var email = $('#email').val();

    $('.alert').hide();
    $('.error').hide();
    $('.success').hide();
    $('#messageError').hide();
    $('#messageInfo').show();

    if(!email){
        $('#email').focus();
        $('#emailErrorEmpty').show();
    }else if(email && !re.test(email)){
        $('#email').focus();
        $('#emailErrorValid').show();
    }else{
        $(el).html($(el).data('sending'));
        $(el).addClass('hover');
        $(el).attr('disabled','disabled');

        frm.submit();
        return true;
    }
    // prevent the default form submission occurring
    return false;
}

/**
 * Finds longitude and attitude by address
 * @param frm
 */
function clinic_FindCoordinates(frm)
{
    // Define this to prevent name overlapping
    var $ = jQuery;

    var language = $('#find_longitude_latitude').data('language');
    var token = $('#'+frm).find('input[name=APPHP_CSRF_TOKEN]').val();
    var address = $('#'+frm).find('input[name=address_'+language+']').val();
    var longitude = $('#'+frm).find('input[name=longitude]');
    var latitude = $('#'+frm).find('input[name=latitude]');
    if(address == null || address == ''){
        apAlert("Address cannot be empty! Please enter an address before you're trying to find coordinates.", 'warning');
        $('#'+frm).find('input[name=address]').focus();
        return;
    }

    $.ajax({
        url: 'clinics/ajaxFindCoordinates',
        global: false,
        type: 'POST',
        data: {
            APPHP_CSRF_TOKEN: token,
            act: 'send',
            address: address
        },
        dataType: 'html',
        async: true,
        error: function(html){
            if(globalDebug){
                console.error("AJAX: cannot connect to the server or server response error!");
            }
        },
        success: function(html){
            console.log(html);
            try{
                var obj = $.parseJSON(html);
                var changeValues = false;

                if(obj.status == "1"){
                    if(longitude.val() == '' && latitude.val() == '' ){
                        if(obj.longitude != '') longitude.val(obj.longitude);
                        if(obj.latitude != '') latitude.val(obj.latitude);
                    }else{
                        if(obj.longitude != longitude.val() || obj.latitude != latitude.val()){
                            apConfirm('The new result is different from the previous coordinates! Do you want to replace them anyway?', '', function(){
                                if(obj.longitude != '') longitude.val(obj.longitude);
                                if(obj.latitude != '') latitude.val(obj.latitude);
                            });
                        }else{
                            apAlert(obj.alert, obj.alert_type);
                        }
                    }
                }else{
                    apAlert(obj.alert, obj.alert_type);
                }
            }catch(err){
                //alert("An error occurred while receiving data! Please try again later.");
                if(globalDebug){
                    console.error("An error occurred while receiving data!");
                    console.error(err);
                }
            }
        }
    });
}

/**
 * Validates form fields and submit the form
 * @param object el form element
 */
function doctors_RegistrationForm(el)
{
    return appointments_RegistrationForm(el, 'frmDoctorRegistration', 'doctors');
}

/**
 * Validates form fields and submit the login form
 * @param object el form element
 */
function doctors_LoginForm(el)
{
    if(el == null) return false;
    // define this to prevent name overlapping
    var $ = jQuery;

    var username = $('#login_username').val();
    var password = $('#login_password').val();

    $('.alert').hide();
    $('.error').hide();
    $('.success').hide();
    $('#messageError').hide();
    $('#messageInfo').show();

    if(!username){
        $('#login_username').focus();
        $('#usernameErrorEmpty').show();
    }else if(!password){
        $('#login_password').focus();
        $('#passwordErrorEmpty').show();
    }else{
        return true;
    }
    // prevent the default form submission occurring
    return false;
}

/**
 * Raise error message
 * @param el
 * @param errorDescription
 * @param errorField
 */
function appointments_RaiseError(el, errorDescription, errorField)
{
    // define this to prevent name overlapping
    var $ = jQuery;

    $('.error').hide();
    $('#messageInfo').hide();
    if(errorDescription !== null) $('#messageError').html(errorDescription);
    if(errorField !== null) $('#'+errorField).focus();
    $('#messageError').show();

    $(el).html($(el).data('send'));
    $(el).removeClass('hover');
    $(el).removeAttr('disabled');
}

/**
 * Validates form fields and submit the form
 * @param object el form element
 */
function patients_RegistrationForm(el)
{
    return appointments_RegistrationForm(el, 'frmPatientRegistration', 'patients');
}

/**
 * Validates form fields and submit the form
 * @param object el form element
 * @param string formName
 * @param string type (doctors|patients)
 */
function appointments_RegistrationForm(el, formName, type)
{
    if(el == null || jQuery(el).hasClass('hover') || formName == null) return false;
    // define this to prevent name overlapping
    var $ = jQuery;
    type = type == 'doctors' ? 'doctors' : 'patients';

    var token = $(el).closest('form').find('input[name=APPHP_CSRF_TOKEN]').val();
    var re = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,7})+$/;

    var titleId               = $('#'+formName+' select[name=title_id]').val();
    var firstName             = $('#'+formName+' input[name=first_name]').val();
    var middleName            = $('#'+formName+' input[name=middle_name]').val();
    var lastName              = $('#'+formName+' input[name=last_name]').val();
    var gender                = $('#'+formName+' select[name=gender]').val();
    var birthDate             = $('#'+formName+' input[name=birth_date]').val();
    var workPhone             = $('#'+formName+' input[name=work_phone]').val();
    var workMobilePhone       = $('#'+formName+' input[name=work_mobile_phone]').val();
    var phone                 = $('#'+formName+' input[name=phone]').val();
    var fax                   = $('#'+formName+' input[name=fax]').val();
    var address               = $('#'+formName+' input[name=address]').val();
    var address2              = $('#'+formName+' input[name=address_2]').val();
    var city                  = $('#'+formName+' input[name=city]').val();
    var zipCode               = $('#'+formName+' input[name=zip_code]').val();
    var countryCode           = $('#'+formName+' select[name=country_code]').val();
    var state                 = $('#'+formName+' *[name=state]').val();
    var email                 = $('#'+formName+' input[name=email]').val();
    var username              = $('#'+formName+' input[name=username]').val();
    var password              = $('#'+formName+' input[name=password]').val();
    var confirmPassword       = $('#'+formName+' input[name=confirm_password]').val();
    var degreeId              = $('#'+formName+' input[name=degree_id]').val();
    var additionalDegree      = $('#'+formName+' input[name=additional_degree]').val();
    var licenseNumber         = $('#'+formName+' input[name=license_number]').val();
    var education             = $('#'+formName+' input[name=education]').val();
    var experience            = $('#'+formName+' select[name=experience]').val();
    var residencyTraining     = $('#'+formName+' input[name=residency_training]').val();
    var hospitalAffiliations  = $('#'+formName+' input[name=hospital_affiliations]').val();
    var boardCertifications   = $('#'+formName+' input[name=board_certifications]').val();
    var awardsAndPublications = $('#'+formName+' input[name=awards_and_publications]').val();
    var languagesSpoken       = $('#'+formName+' submit[name=languages_spoken]').val();
    var insurancesAccepted    = $('#'+formName+' input[name=insurances_accepted]').val();
    var notifications         = $('#'+formName+' input[name=notifications]:checked').val();
    var objCaptcha            = $('#'+formName+' input[name=captcha_validation]');
    var captcha               = $('#'+formName+' input[name=captcha_validation]').val();
    var iAgree                = $('#i_agree:checked').val();

    var requiredTitleId               = $('#'+formName+' select[name=title_id]').data('required');
    var requiredFirstName             = $('#'+formName+' input[name=first_name]').data('required');
    var requiredMiddleName            = $('#'+formName+' input[name=middle_name]').data('required');
    var requiredLastName              = $('#'+formName+' input[name=last_name]').data('required');
    var requiredGender                = $('#'+formName+' select[name=gender]').data('required');
    var requiredBirthDate             = $('#'+formName+' input[name=birth_date]').data('required');
    var requiredWorkPhone             = $('#'+formName+' input[name=work_phone]').data('required');
    var requiredWorkMobilePhone       = $('#'+formName+' input[name=work_mobile_phone]').data('required');
    var requiredPhone                 = $('#'+formName+' input[name=phone]').data('required');
    var requiredFax                   = $('#'+formName+' input[name=fax]').data('required');
    var requiredAddress               = $('#'+formName+' input[name=address]').data('required');
    var requiredAddress2              = $('#'+formName+' input[name=address_2]').data('required');
    var requiredCity                  = $('#'+formName+' input[name=city]').data('required');
    var requiredZipCode               = $('#'+formName+' input[name=zip_code]').data('required');
    var requiredCountryCode           = $('#'+formName+' select[name=country_code]').data('required');
    var requiredState                 = $('#'+formName+' *[name=state]').data('required');
    var requiredEmail                 = $('#'+formName+' input[name=email]').data('required');
    var requiredConfirmPassword       = $('#'+formName+' input[name=confirm_password]').data('required');
    var requiredDegreeId              = $('#'+formName+' input[name=degree_id]').data('required');
    var requiredAdditionalDegree      = $('#'+formName+' input[name=additional_degree]').data('required');
    var requiredLicenseNumber         = $('#'+formName+' input[name=license_number]').data('required');
    var requiredEducation             = $('#'+formName+' input[name=education]').data('required');
    var requiredExperience            = $('#'+formName+' select[name=experience]').data('required');
    var requiredResidencyTraining     = $('#'+formName+' input[name=residency_training]').data('required');
    var requiredHospitalAffiliations  = $('#'+formName+' input[name=hospital_affiliations]').data('required');
    var requiredBoardCertifications   = $('#'+formName+' input[name=board_certifications]').data('required');
    var requiredAwardsAndPublications = $('#'+formName+' input[name=awards_and_publications]').data('required');
    var requiredLanguagesSpoken       = $('#'+formName+' submit[name=languages_spoken]').data('required');
    var requiredInsurancesAccepted    = $('#'+formName+' input[name=insurances_accepted]').data('required');
    var requiredNotifications         = $('#'+formName+' input[name=notifications]:checked').data('required');

    $('.error').hide();
    $('.success').hide();
    $('#messageError').hide();
    $('#messageInfo').show();


    if(requiredTitleId && !titleId){
        $('#'+formName+' select[name=title_id]').focus();
        $('#titleErrorEmpty').show();
    }else if(requiredFirstName && !firstName){
        $('#'+formName+' input[name=first_name]').focus();
        $('#firstNameErrorEmpty').show();
    }else if(requiredMiddleName && !middleName){
        $('#'+formName+' input[name=middle_name]').focus();
        $('#middleNameErrorEmpty').show();
    }else if(requiredLastName && !lastName){
        $('#'+formName+' input[name=last_name]').focus();
        $('#lastNameErrorEmpty').show();
    }else if(requiredGender && !gender){
        $('#'+formName+' select[name=gender]').focus();
        $('#genderErrorEmpty').show();
    }else if(requiredBirthDate && !birthDate){
        $('#'+formName+' input[name=birth_date]').focus();
        $('#birthDateErrorEmpty').show();
    }else if(requiredWorkPhone && !workPhone){
        $('#'+formName+' input[name=work_phone]').focus();
        $('#workPhoneErrorEmpty').show();
    }else if(requiredWorkMobilePhone && !workMobilePhone){
        $('#'+formName+' input[name=work_mobile_phone]').focus();
        $('#workMobilePhoneErrorEmpty').show();
    }else if(requiredPhone && !phone){
        $('#'+formName+' input[name=phone]').focus();
        $('#phoneErrorEmpty').show();
    }else if(requiredFax && !fax){
        $('#'+formName+' input[name=fax]').focus();
        $('#faxErrorEmpty').show();
    }else if(requiredEmail && !email){
        $('#'+formName+' input[name=email]').focus();
        $('#emailErrorEmpty').show();
    }else if(email && !re.test(email)){
        $('#'+formName+' input[name=email]').focus();
        $('#emailErrorValid').show();
    }else if(requiredAddress && !address){
        $('#'+formName+' input[name=address]').focus();
        $('#addressErrorEmpty').show();
    }else if(requiredAddress2 && !address2){
        $('#'+formName+' input[name=address_2]').focus();
        $('#address2ErrorEmpty').show();
    }else if(requiredCity && !city){
        $('#'+formName+' input[name=city]').focus();
        $('#cityErrorEmpty').show();
    }else if(requiredZipCode && !zipCode){
        $('#'+formName+' input[name=zip_code]').focus();
        $('#zipcodeErrorEmpty').show();
    }else if(requiredCountryCode && !countryCode){
        $('#'+formName+' input[name=country_code]').focus();
        $('#countryCodeErrorEmpty').show();
    }else if(requiredState && !state){
        $('#'+formName+' *[name=state]').focus();
        $('#stateErrorEmpty').show();
    }else if(!username){
        $('#'+formName+' input[name=username]').focus();
        $('#usernameErrorEmpty').show();
    }else if(!password){
        $('#'+formName+' input[name=password]').focus();
        $('#passwordErrorEmpty').show();
    }else if(requiredConfirmPassword && !confirmPassword){
        $('#'+formName+' input[name=confirm_password]').focus();
        $('#confirmPasswordErrorEmpty').show();
    }else if(requiredConfirmPassword && confirmPassword != password){
        $('#'+formName+' input[name=confirm_password]').focus();
        $('#confirmPasswordErrorEqual').show();
    }else if(requiredDegreeId && !degreeId){
        $('#'+formName+' input[name=degree_id]').focus();
        $('#degreeErrorEmpty').show();
    }else if(requiredAdditionalDegree && !additionalDegree){
        $('#'+formName+' input[name=additional_degree]').focus();
        $('#additionalDegreeErrorEmpty').show();
    }else if(requiredLicenseNumber && !licenseNumber){
        $('#'+formName+' input[name=license_number]').focus();
        $('#licenseNumberErrorEmpty').show();
    }else if(requiredExperience && !experience){
        $('#'+formName+' select[name=experience]').focus();
        $('#experienceErrorEmpty').show();
    }else if(requiredEducation && !education){
        $('#'+formName+' input[name=education]').focus();
        $('#educationErrorEmpty').show();
    }else if(requiredResidencyTraining && !residencyTraining){
        $('#'+formName+' input[name=residency_training]').focus();
        $('#residencyTrainingErrorEmpty').show();
    }else if(requiredHospitalAffiliations && !hospitalAffiliations){
        $('#'+formName+' input[name=hospital_affiliations]').focus();
        $('#hospitalAffiliationsErrorEmpty').show();
    }else if(requiredBoardCertifications && !boardCertifications){
        $('#'+formName+' input[name=board_certifications]').focus();
        $('#boardCertificationsErrorEmpty').show();
    }else if(requiredAwardsAndPublications && !awardsAndPublications){
        $('#'+formName+' input[name=awards_and_publications]').focus();
        $('#awardsAndPublicationsErrorEmpty').show();
    }else if(requiredLanguagesSpoken && !languagesSpoken){
        $('#'+formName+' select[name=languages_spoken]').focus();
        $('#languagesSpokenErrorEmpty').show();
    }else if(requiredInsurancesAccepted && !insurancesAccepted){
        $('#'+formName+' input[name=insurances_accepted]').focus();
        $('#insurancesAcceptedErrorEmpty').show();
    }else if(!iAgree){
        $('#'+formName+' input[name=i_agree]').focus();
        $('#iAgreeError').show();
    }else if(objCaptcha.length > 0 && !captcha){
        $('#'+formName+' input[name=captcha_validation]').focus();
        $('#captchaError').show();
    }else{

        $(el).html($(el).data('sending'));
        $(el).addClass('hover');
        $(el).attr('disabled','disabled');

        $.ajax({
            url: type + '/registration',
            global: false,
            type: 'POST',
            data: ({
                APPHP_CSRF_TOKEN        : token,
                act                     : "send",
                title_id                : titleId,
                first_name              : firstName,
                middle_name             : middleName,
                last_name               : lastName,
                gender                  : gender,
                birth_date              : birthDate,
                work_phone              : workPhone,
                work_mobile_phone       : workMobilePhone,
                phone                   : phone,
                fax                     : fax,
                address                 : address,
                address_2               : address2,
                city                    : city,
                zip_code                : zipCode,
                country_code            : countryCode,
                state                   : state,
                email                   : email,
                username                : username,
                password                : password,
                confirm_password        : confirmPassword,
                degree_id               : degreeId,
                additional_degree       : additionalDegree,
                license_number          : licenseNumber,
                education               : education,
                experience              : experience,
                residency_training      : residencyTraining,
                hospital_affiliations   : hospitalAffiliations,
                board_certifications    : boardCertifications,
                awards_and_publications : awardsAndPublications,
                languages_spoken        : languagesSpoken,
                insurances_accepted     : insurancesAccepted,
                notifications           : notifications,
                captcha                 : captcha,
                i_agree                 : iAgree
            }),
            dataType: 'html',
            async: true,
            error: function(html){
                $('#messageInfo').hide();
                $('#messageError').show();
            },
            success: function(html){
                try{
                    var obj = $.parseJSON(html);
                    if(obj.status == '1'){
                        $('.error').hide();
                        //$('#'+formName+' select[name=title_id]').val('');
                        $('#'+formName+' input[name=first_name]').val('');
                        $('#'+formName+' input[name=middle_name]').val('');
                        $('#'+formName+' input[name=last_name]').val('');
                        //$('#'+formName+' input[name=gender]').val('');
                        $('#'+formName+' input[name=birth_date]').val('');
                        $('#'+formName+' input[name=work_phone]').val('');
                        $('#'+formName+' input[name=work_mobile_phone]').val('');
                        $('#'+formName+' input[name=phone]').val('');
                        $('#'+formName+' input[name=fax]').val('');
                        $('#'+formName+' input[name=address]').val('');
                        $('#'+formName+' input[name=address_2]').val('');
                        $('#'+formName+' input[name=zip_code]').val('');
                        //$('#'+formName+' input[name=country_code]').val('');
                        //$('#'+formName+' input[name=state]').val('');
                        $('#'+formName+' input[name=email]').val('');
                        $('#'+formName+' input[name=username]').val('');
                        $('#'+formName+' input[name=password]').val('');
                        $('#'+formName+' input[name=confirm_password]').val('');
                        //$('#'+formName+' input[name=degree_id]').val('');
                        $('#'+formName+' input[name=additional_degree]').text('');
                        $('#'+formName+' input[name=license_number]').val('');
                        $('#'+formName+' input[name=education]').val('');
                        //$('#'+formName+' input[name=experience]').val('');
                        $('#'+formName+' input[name=residency_training]').text('');
                        $('#'+formName+' input[name=hospital_affiliations]').text('');
                        $('#'+formName+' input[name=board_certifications]').text('');
                        $('#'+formName+' input[name=awards_and_publications]').text('');
                        //$('#'+formName+' input[name=languages_spoken]').val('');
                        $('#'+formName+' input[name=insurances_accepted]').val('');
                        $('#'+formName+' input[name=captcha_validation]').val('');
                        $('#'+formName+' input[name=notifications]').attr('checkbox', '');
                        $('#'+formName+' #i_agree').attr('checkbox', '');

                        $('#'+formName).slideUp();
                        $('html, body').animate({
                            scrollTop: $('#messageSuccess').offset().top
                        }, 1000);
                        $('#messageSuccess').show();
                    }else{
                        if(globalDebug){
                            console.error('get ajax question error');
                        }
                        appointments_RaiseError(el, obj.error, obj.error_field);
                    }
                }catch(err){
                    if(globalDebug){
                        console.error(err.message);
                    }
                    appointments_RaiseError(el, err.message);
                }
            }
        });
    }
    return false;
}

/**
 * Change country from dropdown box
 * @param frm
 * @param country
 * @param state
 */
function appointments_ChangeCountry(frm, country, state)
{
    // define this to prevent name overlapping
    var $ = jQuery;

    var token = $('#'+frm).find('input[name=APPHP_CSRF_TOKEN]').val();
    var stateId = $('#'+frm).find('*[name=state]').attr('id');
    var countryCode = (country != null) ? country : '';
    var stateCode = (state != null) ? state : '';

    var ajax = $.ajax({
        url: 'locations/getSubLocations',
        global: false,
        type: 'POST',
        data: ({
            APPHP_CSRF_TOKEN: token,
            act: 'send',
            country_code: countryCode
        }),
        dataType: 'html',
        async: true,
        error: function(html){
            //alert("AJAX: cannot connect to the server or server response error! Please try again later.");
            console.error("AJAX: cannot connect to the server or server response error!");
        },
        success: function(html){
            try{
                var obj = $.parseJSON(html);
                if(obj[0].status == "1"){
                    if(obj.length > 1){
                        $("#"+stateId).replaceWith('<select id="'+stateId+'" name="state"></select>');
                        $("#"+stateId).empty();
                        // add empty option
                        $("<option />", {val: "", text: "--"}).appendTo("#"+stateId);
                        for(var i = 1; i < obj.length; i++){
                            if(obj[i].code == stateCode && stateCode != ''){
                                $("<option />", {val: obj[i].code, text: obj[i].name, selected: true}).appendTo("#"+stateId);
                            }else{
                                $("<option />", {val: obj[i].code, text: obj[i].name}).appendTo("#"+stateId);
                            }
                        }
                    }else{
                        $("#"+stateId).replaceWith('<input type="text" id="'+stateId+'" name="state" data-required="false" maxlength="64" value="'+stateCode+'" />');
                    }
                }else{
                    //alert("An error occurred while receiving data! Please try again later.");
                    if(globalDebug){
                        console.error("An error occurred while receiving data!");
                    }
                }
            }catch(err){
                //alert("An error occurred while receiving data! Please try again later.");
                //console.error("An error occurred while receiving data!");
                if(globalDebug){
                    console.error(err);
                }
            }
        }
    });

    return ajax;
}
/**
 * alert
 * @param string message
 * @param string type
 * @param int showTime
 * @return void
 */
function apAlert(message, type, showTime){
    if(message === null){
        return false;
    }

    // Toastr not defined
    if(typeof toastr === "undefined"){
        alert(message);
        return false;
    }

    // Making the translation if possible (to be defined variable langVocab)
    if(typeof langVocab !== "undefined"){
        message = langVocab.get(message);
    }

    type = type == null ? 'info' : type;
    showTime = showTime == null ? '5000' : showTime;

    // Init toastr
    // See: http://codeseven.github.io/toastr/demo.html
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": false,
        "positionClass": "toast-bottom-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": showTime,
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }

    toastr[type](message);

    return true;
}

/**
 * My confirm
 * @param string message
 * @param string title
 * @param function functionSuccess
 * @return void
 */
function apConfirm(message, title, functionSuccess){
    if(message === null){
        var nameFunction = arguments.callee.toString().match(/function ([^(]*)\(/)[1];
        console.error("function '" + nameFunction + "'. Empty message");
        return false;
    }
    if(typeof functionSuccess != "function"){
        var nameFunction = arguments.callee.toString().match(/function ([^(]*)\(/)[1];
        console.error("function '" + nameFunction + "'. Input incorect");
        return false;
    }

    // Making the translation if possible (to be defined variable langVocab)
    if(typeof langVocab !== "undefined"){
        message = langVocab.get(message);
    }

    // Toastr not defined
    if(typeof toastr === "undefined"){
        if(confirm(message)){
            functionSuccess();
        }
        return false;
    }


    message = message + "<br /><br /><button type='button' class='btn btn-warning clear yes'>" + langVocab.get('Yes') + "</button><button type='button' class='btn btn-info clear no' style='margin-left:5px;'>" + langVocab.get('No') + "</button>"

    // Init toastr
    // See: http://codeseven.github.io/toastr/demo.html
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": false,
        "positionClass": "toast-top-center-wish-margin-top",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": 0,
        "extendedTimeOut": 0,
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut",
        "allowHtml": true,
        "tapToDismiss": false
    }

    // Close all toastr message
    toastr.remove();

    var toastrResult = toastr.info(message, title);

    toastrResult.delegate('.clear', 'click', function(){
        toastr.clear(toastrResult, {force: true });
        if(jQuery(this).hasClass("yes")){
            functionSuccess();
        }
    });

    return false;
}

/**
 *   Hide element
 */
function appHideElement(key){
    if(key.indexOf('#') !=-1 || key.indexOf('.') !=-1){
        jQuery(key).hide('fast');
    }else{
        jQuery('#'+key).hide('fast');
    }
}

/**
 *   Show element
 */
function appShowElement(key){
    if(key.indexOf('#') !=-1 || key.indexOf('.') !=-1){
        jQuery(key).show('fast');
    }else{
        jQuery('#'+key).show('fast');
    }
}

function prevPageBookAppointments(el) {
    var prevPage = $(el).data("prevPage");
    if(prevPage == 0){
        return false;
    }
}

function slidePageBookAppointments(el) {
    if(el == null) return false;
    // define this to prevent name overlapping
    var $ = jQuery;
    var page = $(el).data("page");
    var typePage = $(el).attr('id');
    var maxPage  = $(el).data("maxPage");
    //var prevPage = $('#prev_page').data("prevPage");
    var doctorId = $(el).data("doctorId");
    var clinicId = $(el).data("clinicId");

    if($(el).data("page") <= 0 || $(el).data("page")-1 >= maxPage){
        return false;
    }

    if($(el).attr('disabled')){
        return false;
    }

    $(el).attr('disabled', 'disabled');
    $(el).parent('div').css({'opacity':'0.6'});
    $(el).parent('div').prepend('<img class="img-ajax-loading" src="templates/default/images/ajax_loading.gif" class="img-ajax-loading" alt="loading" />');

    $.ajax({
        url: 'appointments/ajaxBookAppointment',
        global: false,
        type: 'GET',
        data: ({
            page: page,
            doctorId: doctorId,
            clinicId: clinicId
        }),
        dataType: 'html',
        async: true,
        error: function (html) {
            if(globalDebug){
                console.error("AJAX: cannot connect to the server or server response error!");
            }
        },
        success: function (html) {
            try {
                $(el).removeAttr('disabled');
                if(typePage == 'next_page' || typePage == 'page_in_nearest_schedule'){
                    $('#next_page').data("page", page + 1);
                    $('#prev_page').data("page", page - 1);
                }else if(typePage == 'prev_page'){
                    $('#next_page').data("page", page + 1);
                    $('#prev_page').data("page", page - 1);
                }

                $("div.book_appointment").detach();
                $('#book_appointment').html(html);
            }catch(err){
                if(globalDebug){
                    console.error(err);
                }
            }
        }
    });

    return false;
}

function appointmentDetails(el){

    if(el == null) return false;
    // define this to prevent name overlapping
    var $ = jQuery;

    var typeAccount          = $(el).data("typeAccount");
    var currentClass         = 'current';

    if(typeAccount === 'admin'){
        currentClass = 'active';
    }

    $('#book_now').removeAttr('disabled');

    $('#tab_appointment_details').addClass(currentClass);
    $('#appointment_details').show();

    $('#tab_appointment_verify').removeClass(currentClass);
    $('#appointment_verify').hide();

}

function appointmentVerify(el) {

    if (el == null) return false;
    // define this to prevent name overlapping
    var $ = jQuery;
    var specialty            = $('#specialty option:selected').text();
    var visitedBefore        = $('#visited_before option:selected').text();
    var insurance            = $('#insurance option:selected').text();
    var reasons              = $('#reasons option:selected').text();
    var appointmentForWhom   = $('#appointment_for_whom option:selected').text();
    var specialtyId          = $('#specialty option:selected').val();
    var visitedBeforeId      = $('#visited_before option:selected').val();
    var insuranceId          = $('#insurance option:selected').val();
    var reasonsId            = $('#reasons option:selected').val();
    var appointmentForWhomId = $('#appointment_for_whom option:selected').val();
    var forWhomSomeoneElse   = $('#for_whom_someone_else').val();
    var otherReasons         = $('#other_reasons').val();
    var patientName          = $('#patient_name').val();

    var dateTime             = $(el).data("dateTime");
    var doctorId             = $(el).data("doctorId");
    var typeAccount          = $(el).data("typeAccount");
    var currentClass         = 'current';
    var patientId            =  0;

    if(typeAccount === 'admin'){
        patientId =  $('#patient_id').val();
        currentClass = 'active';
    }else if(typeAccount === 'doctor'){
        patientId =  $('#patient_id').val();
    }

    if ($(el).attr('disabled')) {
        return false;
    }

    $(el).attr('disabled', 'disabled');

    $.ajax({
        url: 'appointments/ajaxVerifyAppointment',
        global: false,
        type: 'GET',
        data: ({
            specialtyId          : specialtyId,
            visitedBeforeId      : visitedBeforeId,
            insuranceId          : insuranceId,
            reasonsId            : reasonsId,
            appointmentForWhomId : appointmentForWhomId,
            dateTime             : dateTime,
            doctorId             : doctorId,
            patientId            : patientId
        }),
        dataType: 'html',
        async: true,
        error: function (html) {
            if(globalDebug){
                console.error("AJAX: cannot connect to the server or server response error!");
            }
        },
        success: function (html) {
            try {
                var obj = $.parseJSON(html);
                if(obj.status == '1'){
                    $('#message_error').hide();
                    $('#tab_appointment_details').removeClass(currentClass);
                    $('#appointment_details').hide();

                    $('#tab_appointment_verify').addClass(currentClass);
                    $('#appointment_verify').show();
                    $('#price').show();

                    if(typeAccount === 'admin' || typeAccount === 'doctor'){
                        $('#patient_name_verify').text(patientName);
                    }
                    $('#specialty_verify').text(specialty);
                    $('#visited_before_verify').text(visitedBefore);
                    $('#insurance_verify').text(insurance);
                    if (reasonsId === '11'){
                        $('#reasons_verify').text(reasons + ': ' + otherReasons);
                    } else {
                        $('#reasons_verify').text(reasons);
                    }
                    if (appointmentForWhomId === '2'){
                        $('#appointment_for_whom_verify').text(appointmentForWhom + ': ' + forWhomSomeoneElse);
                    } else {
                        $('#appointment_for_whom_verify').text(appointmentForWhom);
                    }
                }else{
                    $('#book_now').removeAttr('disabled');
                    $('#message_error_text').html(obj.error);
                    $('#message_error').show();
                    scroll_to('#message_error');
                    if(obj.status == '2'){
                        $('#appointment_content').hide();
                    }
                }
            }catch(err){
                if(globalDebug){
                    console.error(err);
                }
            }
        }
    });
}

function appointmentComplete(el){

    if(el == null) return false;
    // define this to prevent name overlapping
    var $        = jQuery;

    var specialty           = $('#specialty').val();
    var visitedBefore       = $('#visited_before').val();
    var insurance           = $('#insurance').val();
    var reasons             = $('#reasons').val();
    var appointmentForWhom  = $('#appointment_for_whom').val();
    var forWhomSomeoneElse  = $('#for_whom_someone_else').val();
    var otherReasons        = $('#other_reasons').val();
    var dateTime            = $(el).data("dateTime");
    var doctorId            = $(el).data("doctorId");
    var typeAccount          = $(el).data("typeAccount");
    var currentClass         = 'current';
    var patientId            =  0;
    var patientName          =  '';

    if(typeAccount === 'admin'){
        patientId =  $('#patient_id').val();
        patientName =  $('#patient_name').val();
        currentClass = 'active';
    }else if(typeAccount === 'doctor'){
        patientId =  $('#patient_id').val();
        patientName =  $('#patient_name').val();
    }

    if($(el).attr('disabled')){
        return false;
    }

    $(el).attr('disabled', 'disabled');

    $.ajax({
        url: 'appointments/ajaxAppointmentComplete',
        global: false,
        type: 'GET',
        data: ({
            specialty          :specialty,
            visitedBefore      :visitedBefore,
            insurance          :insurance,
            reasons            :reasons,
            appointmentForWhom :appointmentForWhom,
            otherReasons       :otherReasons,
            forWhomSomeoneElse :forWhomSomeoneElse,
            dateTime           :dateTime,
            doctorId           :doctorId,
            patientId          :patientId,
            patientName        :patientName
        }),
        dataType: 'html',
        async: true,
        error: function (html) {
            if(globalDebug){
                console.error("AJAX: cannot connect to the server or server response error!");
            }
        },
        success: function (html) {
            try {
                var obj = $.parseJSON(html);
                if(obj.status == '1'){
                    $('#tab_appointment_verify').removeClass(currentClass);
                    $('#message_success_text').html(obj.message);
                    $('#appointment_verify').hide();
                    $('#tab_appointment_complete').addClass(currentClass);
                    $('#appointment_complete').show();
                }else{
                    $('#message_error_text').html(obj.error);
                    $('#message_error').show();
                    $('#appointment_content').hide();
                    scroll_to('#message_error');
                }
            }catch(err){
                if(globalDebug){
                    console.error(err);
                }
            }
        }
    });
}

function findDoctorsShowMore(el){

    if(el == null) return false;
    // define this to prevent name overlapping
    var $        = jQuery;

    var doctorId    = $(el).data("doctorId");
    var locationId  = $(el).data("locationId");
    var specialtyId = $(el).data("specialtyId");
    var doctorName  = $(el).data("doctorName");
    var location    = $(el).data("location");
    var page        = $(el).data("page");
    var maxPage     = $(el).data("maxPage");


    if($(el).attr('disabled')){
        return false;
    }

    $(el).attr('disabled', 'disabled');

    $.ajax({
        url: 'appointments/ajaxShowMoreFindDoctors',
        global: false,
        type: 'GET',
        data: ({
            doctorId    :doctorId,
            locationId  :locationId,
            specialtyId :specialtyId,
            doctorName  :doctorName,
            location    :location,
            page        :page
        }),
        dataType: 'html',
        async: true,
        error: function (html) {
            if(globalDebug){
                console.error("AJAX: cannot connect to the server or server response error!");
            }
        },
        success: function (html) {
            try {
                $('#find_doctors_content').append(html);
                scroll_to('#page-'+page);
                page = page +1;
                $(el).data("page", page);
                if(page >= maxPage){
                    $('#show_more').hide();
                }else{
                    $(el).removeAttr('disabled');
                }
            }catch(err){
                if(globalDebug){
                    console.error(err);
                }
            }
        }
    });
}

/**
 * Change dropdown box “Week Day” for the selected of the clinic
 * @param formName
 * @param clinicId
 * @param weekDay
 */
function appointments_timeBlocks_changeWeekDay(formName, clinicId, weekDay)
{
    if(clinicId == 0 || clinicId == undefined){
        clinicId = $("#"+formName+"_address_id").val();
    }
    if(weekDay == undefined){
        weekDay = 0;
    }

    if(clinicId == "") return false;
    $("#error-message").remove();
    $.ajax({
        url: "workingHours/ajaxGetActiveWeekDays",
        global: false,
        type: "POST",
        data: ({
            clinicId : clinicId,
        }),
        dataType: "html",
        async: true,
        error: function (html) {
            if(globalDebug){
                console.error("AJAX: cannot connect to the server or server response error!");
            }
        },
        success: function (html) {
            try {
                var obj = $.parseJSON(html);
                var select = $("#"+formName+"_week_day");
                select.find("option").remove();
                if(obj.status == 1){
                    var count = 0;
                    $.each(obj.weekDays, function(index,value){
                        var selectedWeekDay = "";
                        if(weekDay == value.weekDayNumber){
                            selectedWeekDay = "selected";
                        }
                        select.append("<option value='"+value.weekDayNumber+"' "+selectedWeekDay+">"+value.weekDayName+"</option>");
                        count++;
                    });
                    $("#"+formName+"_row_1").show();
                    $("#"+formName+"_row_2").show();
                    $("#"+formName+"_row_3").show();
                    $("#"+formName+"_row_4").show();
                    firstDay = obj.weekDays[1];
                    $("#"+formName+"_time_from").timepicker("option","minTime", firstDay.startTime);
                    $("#"+formName+"_time_from").timepicker("option","maxTime", firstDay.endTime);
                    $("#"+formName+"_time_to").timepicker("option","minTime", firstDay.startTime);
                    $("#"+formName+"_time_to").timepicker("option","maxTime", firstDay.endTime);
                    $('input[type="submit"]').prop("disabled", false);
                    $(select).chosen("destroy");
                    $(select).chosen();
                }else{
                    $(".alert").remove();
                    var selectChosen = $("#"+formName+"_week_day_chosen");
                    selectChosen.remove();
                    $("#"+formName+"_row_1").hide();
                    $("#"+formName+"_row_2").hide();
                    $("#"+formName+"_row_3").hide();
                    $("#"+formName+"_row_4").hide();
                    $('input[type="submit"]').prop("disabled", true);
                    $(".content").prepend("<div id='error-message' class='alert alert-warning'>"+obj.message+"</div>");
                }
            }catch(err){
                console.error(err);
            }
        }
    });
}

/**
 * Change “From time” and “To Time” for the selected of the week day
 * @param formName
 */
function appointments_timeBlocks_changeTimepicker(formName)
{
    var weekDay = $("#"+formName+"_week_day").val();
    var clinicId = $("#"+formName+"_address_id").val();
    if(clinicId == "") return false;

    $.ajax({
        url: "workingHours/ajaxGetWorkingHours",
        global: false,
        type: "POST",
        data: ({
            weekDay     : weekDay,
            clinicId    : clinicId,
        }),
        dataType: "html",
        async: true,
        error: function (html) {
            if(globalDebug){
                console.error("AJAX: cannot connect to the server or server response error!");
            }
        },
        success: function (html) {
            try {
                var obj = $.parseJSON(html);
                if(obj.status == 1){
                    $("#error-message").remove();
                    $("#"+formName+"_row_3").show();
                    $("#"+formName+"_row_4").show();
                    $("#"+formName+"_time_from").val("");
                    $("#"+formName+"_time_to").val("");
                    $("#"+formName+"_time_from").timepicker("option","minTime", obj.startTime);
                    $("#"+formName+"_time_from").timepicker("option","maxTime", obj.endTime);
                    $("#"+formName+"_time_to").timepicker("option","minTime", obj.startTime);
                    $("#"+formName+"_time_to").timepicker("option","maxTime", obj.endTime);
                }else{
                    $("#error-message").remove();
                    $("#"+formName+"_row_3").hide();
                    $("#"+formName+"_row_4").hide();
                    $(".content").prepend("<div id='error-message' class='alert alert-warning'>"+obj.message+"</div>");
                }
            }catch(err){
                console.error(err);
            }
        }
    });
}


function scroll_to(id_el)
{
    $('html, body').animate({ scrollTop: $(id_el).offset().top-50 }, 500);
    // prevent the default form submission occurring
    return false;
}

$(document).ready(function() {
    $("#appointment_for_whom").on("change", function(){
        if($(this).val() == 2){
            $("#for_whom_someone_else").before( "<label id='empty-label-forwhow'> </label>");
            $("#for_whom_someone_else").show().focus();
        } else {
            $("#for_whom_someone_else").hide();
            $("#empty-label-forwhow").remove();
        }
    });

    $("#reasons").on("change", function(){
        if($(this).val() == 11){
            $("#other_reasons").before( "<label id='empty-label-other-reasons'> </label>");
            $("#other_reasons").show().focus();
        } else {
            $("#other_reasons").hide();
            $("#empty-label-other-reasons").remove();
        }
    });
});