<?php
$this->_activeMenu = 'appointments/manage';
$this->_breadCrumbs = array(
    array('label'=>A::t('appointments', 'Modules'), 'url'=>'modules/'),
    array('label'=>A::t('appointments', 'Appointments'), 'url'=>'modules/settings/code/appointments'),
    array('label'=>A::t('appointments', 'Appointments Management'), 'url'=>'appointments/manage'),
    array('label'=>A::t('appointments', 'Book Appointment'), 'url'=>'appointments/'.$profileDoctor->id),
    array('label'=>A::t('appointments', 'Appointment Details')),

);

A::app()->getClientScript()->registerCssFile('assets/vendors/jquery/jquery-ui.min.css');

?>

<h1><?= A::t('appointments', 'Appointment Details'); ?></h1>
<div class="bloc">
    <?= $tabs; ?>
    <div class="sub-title">
        <h3><?= A::t('appointments', 'Appointment Details'); ?></h3>
    </div>
    <div class="content">
        <div class="cmsms_cc">
            <div class="book-appointment mb20">
                <table>
                    <tbody>
                    <tr>
                        <td width="220px">
                            <?php if($profileDoctor->avatar): ?>
                                <img src="assets/modules/appointments/images/doctors/<?= CHtml::encode($profileDoctor->avatar); ?>" alt="<?= CHtml::encode($fullname); ?>" title="<?= CHtml::encode($fullname); ?>" />
                            <?php else: ?>
                                <img src="assets/modules/appointments/images/doctors/<?= CHtml::encode($profileDoctor->avatar_by_gender); ?>" alt="<?= CHtml::encode($fullname); ?>" title="<?= CHtml::encode($fullname); ?>" />
                            <?php endif; ?>
                        </td>
                        <td>
                            <p><strong><?= A::t('appointments', 'Date'); ?>: </strong><?= CLocale::date($dateFormat, $appointmnetnDate); ?></p>
                            <p><strong><?= A::t('appointments', 'Time'); ?>: </strong><?= CLocale::date($appointmentTimeFormat, $appointmnetnDate); ?> <?= $clinicTime['offset']; ?></p>
                            <p><strong><?= A::t('appointments', 'With'); ?>: </strong><?= CHtml::encode($fullname); ?></p>
                            <p><strong><?= A::t('appointments', 'Where'); ?>: </strong><?= !empty($address) ? CHtml::encode($address) : '--'; ?></p>
                            <p><strong><?= A::t('appointments', 'Duration of visit'); ?>: </strong><?= !empty($timeVisit) ? $timeVisit.' '.A::t('appointments', 'min.') : '--'; ?></p>
                            <p><strong><?= A::t('appointments', 'Visit Price'); ?>: </strong><?= $profileDoctor->default_visit_price ? CCurrency::format($profileDoctor->default_visit_price) : '--'; ?></p>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div style="display:none" id="message_error">
                <div class="alert alert-error" id="message_error_text"></div>
            </div>
            <div id="appointment_content">
                <div class="sub-title">
                    <a class="sub-tab active" id="tab_appointment_details" ><?= A::t('appointments', '1. Appointment Details'); ?></a>
                    <a class="sub-tab" id="tab_appointment_verify" ><?= A::t('appointments', '2. Verify Appointment'); ?></a>
                    <a class="sub-tab" id="tab_appointment_complete" ><?= A::t('appointments', '3. Completed!'); ?></a>
                </div>

                <div id="appointment_details" style="display: block;">
                    <?php
                    echo CWidget::create('CFormView', array(
                        'htmlOptions'	=> array(
                            'name'			  => 'form-appointment',
                            'id'			  => 'form-appointment',
                            'enctype'		  => 'multipart/form-data',
                            'autoGenerateId'  => false
                        ),
                        'requiredFieldsAlert'=>true,
                        'fieldSets' 	=> array(),
                        'fieldWrapper'  => array('tag'=>'div', 'class'=>'row mb10'),
                        'fields'		=> array(
                            'patient_id'           => array('type'=>'hidden', 'value'=>'', 'htmlOptions'=>array('id'=>'patient_id')),
                            'patient_name'         => array('type'=>'textbox', 'title'=>A::t('appointments', 'Patient'), 'tooltip'=>'', 'validation'=>array('required'=>true, 'type'=>'text'), 'mandatoryStar'=>true, 'appendCode'=>'<a id="create-patient" href="javascript:void(0);">'.A::t('appointments', 'Create New Patient').'</a>', 'htmlOptions'=>array('id'=>'patient_name')),
                            'specialty'            => array('type'=>'select', 'title'=>A::t('appointments', 'Doctor\'s Specialty'), 'tooltip'=>'', 'mandatoryStar'=>true, 'value'=>'', 'data'=>$arrDoctorSpecialties, 'emptyOption'=>true, 'emptyValue'=>'-- '.A::t('appointments', 'select').' --', 'viewType'=>'dropdownlist', 'multiple'=>false, 'htmlOptions'=>array('id'=>'specialty')),
                            'visited_before'       => array('type'=>'select', 'title'=>A::t('appointments', 'Have you visited this doctor before?'), 'tooltip'=>'', 'mandatoryStar'=>true, 'value'=>0, 'data'=>$visitedBefore, 'emptyOption'=>true, 'emptyValue'=>'-- '.A::t('appointments', 'select').' --', 'viewType'=>'dropdownlist', 'multiple'=>false, 'htmlOptions'=>array('id'=>'visited_before')),
                            'insurance'            => array('type'=>'select', 'title'=>A::t('appointments', 'Will you use insurance?'), 'tooltip'=>'', 'mandatoryStar'=>true, 'value'=>'', 'data'=>$insurance, 'emptyOption'=>true, 'emptyValue'=>'-- '.A::t('appointments', 'select').' --', 'viewType'=>'dropdownlist', 'multiple'=>false, 'htmlOptions'=>array('id'=>'insurance')),
                            'reasons'              => array('type'=>'select', 'title'=>A::t('appointments', 'Visit Reasons'), 'tooltip'=>'', 'mandatoryStar'=>true, 'value'=>'', 'data'=>$visitReasons, 'emptyOption'=>true, 'emptyValue'=>'-- '.A::t('appointments', 'select').' --', 'viewType'=>'dropdownlist', 'multiple'=>false, 'htmlOptions'=>array('id'=>'reasons')),
                            'other_reasons'        => array('type'=>'textbox', 'title'=>false, 'tooltip'=>'', 'validation'=>array('required'=>false, 'maxLength'=>50, 'type'=>'text'), 'htmlOptions'=>array('maxLength'=>50, 'id'=>'other_reasons', 'placeholder'=>A::t('appointments', 'Enter Visit Reason'),  'style'=>'display:none;')),
                            'appointment_for_whom' => array('type'=>'select', 'title'=>A::t('appointments', 'Who is this appointment for?'), 'tooltip'=>'', 'mandatoryStar'=>true, 'value'=>'', 'data'=>$appointmentForWhom, 'emptyOption'=>true, 'emptyValue'=>'-- '.A::t('appointments', 'select').' --', 'viewType'=>'dropdownlist', 'multiple'=>false, 'htmlOptions'=>array('id'=>'appointment_for_whom')),
                            'for_whom_someone_else'=> array('type'=>'textbox', 'title'=>false, 'tooltip'=>'', 'validation'=>array('required'=>false, 'maxLength'=>50, 'type'=>'text'), 'htmlOptions'=>array('maxLength'=>50, 'id'=>'for_whom_someone_else', 'placeholder'=>A::t('appointments', 'Enter whom this appointment is?'),  'style'=>'display:none;')),
                        ),
                        'buttons'=>array(
                            'custom'=>array('type'=>'button', 'value'=>A::t('appointments', 'Book Now'), 'htmlOptions'=>array('id'=>'book_now', 'class'=>'button_small', 'onclick'=>'appointmentVerify(this);', 'data-type-account'=>'admin', 'data-date-time'=>$appointmnetnDate, 'data-doctor-id'=>$profileDoctor->id)),
                        ),
                        'buttonsPosition'=>'bottom',
                        'return'=>true,
                    ));
                    ?>
                </div>
                <div id="appointment_verify" style="display: none;">

                    <p class="padding-0"><strong><?= A::t('appointments', 'Patient Name'); ?></strong></p>
                    <p id="patient_name_verify" class="mb5"></p>

                    <p class="padding-0"><strong><?= A::t('appointments', 'Doctor\'s Specialty'); ?></strong></p>
                    <p id="specialty_verify" class="mb5"></p>

                    <p class="padding-0"><strong><?= A::t('appointments', 'Have you visited this doctor before?'); ?></strong></p>
                    <p id="visited_before_verify" class="mb5"></p>

                    <p class="padding-0"><strong><?= A::t('appointments', 'Will you use insurance?'); ?></strong></p>
                    <p id="insurance_verify" class="mb5"></p>

                    <p class="padding-0"><strong><?= A::t('appointments', 'Visit Reasons'); ?></strong></p>
                    <p id="reasons_verify" class="mb5"></p>

                    <p class="padding-0"><strong><?= A::t('appointments', 'Who is this appointment for?'); ?></strong></p>
                    <p id="appointment_for_whom_verify" class="mb5"></p>

                    <form>
                        <div class="buttons-wrapper bw-bottom">
                            <input class="button_small" onclick="appointmentDetails(this);" data-type-account="admin" value="<?= A::t('appointments', 'Back'); ?>" type="button" name="ap0">
                            <input class="button_small" onclick="appointmentComplete(this);" data-date-time="<?= $appointmnetnDate; ?>" data-type-account="admin" data-doctor-id="<?= $profileDoctor->id; ?>" value="<?= A::t('appointments', 'Make an Appointment'); ?>" type="button" name="ap0">
                        </div>
                    </form>
                </div>
                <div id="appointment_complete" style="display: none;">
                    <div id="message_success">
                        <div class="alert alert-success" id="message_success_text"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $createPatientPopup; ?>

<?php
A::app()->getClientScript()->registerScriptFile('assets/modules/appointments/js/appointments.js', 2);

A::app()->getClientScript()->registerScript(
    'autocompletePatientNames',
    'jQuery("#patient_name").autocomplete({
        source: function(request, response){
            $.ajax({
                url: "patients/ajaxGetPatientNames",
                global: false,
                type: "POST",
                data: ({
                    search : jQuery("#patient_name").val(),
                }),
                dataType: "json",
                async: true,
                error: function(html){
                    '.((APPHP_MODE == 'debug') ? 'console.error("AJAX: cannot connect to the server or server response error! Please try again later.");' : '').'
                },
                success: function(data){
                    if(data.length == 0){
                        jQuery("#patient_id").val("");
                        response({label: "'.A::te('core', 'No matches found').'"});
                    }else{
                        response($.map(data, function(item){
                            if(item.label !== undefined){
                                return {id: item.id, label: item.label}
                            }else{
                                // Empty search value if nothing found
                                jQuery("#patient_id").val("");
                            }
                        }));
                    }
                }
            });
        },
        minLength: 3,
        select: function(event, ui) {
            jQuery("#patient_id").val(ui.item.id);
            if(typeof(ui.item.id) == "undefined"){
                jQuery("#patient_name").val("");
                return false;
            }
        }
    });',
    5
);
?>