<?php
    $this->_pageTitle = A::t('appointments', 'Appointment Details');
    $this->_breadCrumbs = array(
        array('label'=> A::t('appointments', 'Home'), 'url'=>Website::getDefaultPage()),
		array('label'=> $profileDoctor->getFullName(), 'url'=>Website::prepareLinkByFormat('appointments', 'profile_link_format', $profileDoctor->id, $profileDoctor->getFullName())),
		array('label'=> A::t('appointments' , 'Book Appointment'), 'url'=>'appointments/'.$profileDoctor->id),
        array('label' => A::t('appointments', 'Appointment Details'))
    );
?>
<section id="content" role="main">
    <div class="entry">
        <?php if(empty($noSpecialty)): ?>
            <div class="cmsms_cc">
                <div class="one_first">
                    <div class="entry">
                        <div class="one_fourth first_column margin-left-0">
                            <figure class="box">
                                <?php if($profileDoctor->avatar): ?>
                                    <img class="fullwidth" src="assets/modules/appointments/images/doctors/<?= CHtml::encode($profileDoctor->avatar); ?>" alt="<?= CHtml::encode($fullname); ?>" title="<?= CHtml::encode($fullname); ?>" />
                                <?php else: ?>
                                    <img src="assets/modules/appointments/images/doctors/<?= CHtml::encode($profileDoctor->avatar_by_gender); ?>" class="fullwidth" alt="<?= CHtml::encode($fullname); ?>" title="<?= CHtml::encode($fullname); ?>" />
                                <?php endif; ?>
                            </figure>
                            <div class="cl"></div>
                            <div class="cl"></div>
                        </div>
                        <div class="three_fourth">
                            <div class="cmsms_features">
                                <div class="cmsms_features_item padding-5-0">
                                    <span class="cmsms_features_item_title"><?= A::t('appointments', 'Date'); ?>:</span>
                                    <span class="cmsms_features_item_desc margin-left-30-percent"><?= CLocale::date($dateFormat, $appointmnetnDate); ?></span>
                                </div>
                                <div class="cmsms_features_item padding-5-0">
                                    <span class="cmsms_features_item_title"><?= A::t('appointments', 'Time'); ?>:</span>
                                    <span class="cmsms_features_item_desc margin-left-30-percent"><?= CLocale::date($appointmentTimeFormat, $appointmnetnDate); ?> <?= $clinicTime['offset']; ?></span>
                                </div>
                                <div class="cmsms_features_item padding-5-0">
                                    <span class="cmsms_features_item_title"><?= A::t('appointments', 'With'); ?>:</span>
                                    <span class="cmsms_features_item_desc margin-left-30-percent"><?= CHtml::encode($fullname); ?></span>
                                </div>
                                <div class="cmsms_features_item padding-5-0">
                                    <span class="cmsms_features_item_title"><?= A::t('appointments', 'Where'); ?>:</span>
                                    <span class="cmsms_features_item_desc margin-left-30-percent"><?= $address; ?></span>
                                </div>
                                <div class="cmsms_features_item padding-5-0">
                                    <span class="cmsms_features_item_title"><?= A::t('appointments', 'Duration of visit'); ?>:</span>
                                    <span class="cmsms_features_item_desc margin-left-30-percent"><?= $timeVisit.' '.A::t('appointments', 'min.'); ?></span>
                                </div>
                                <div id="price" class="cmsms_features_item padding-5-0">
                                    <span class="cmsms_features_item_title"><?= A::t('appointments', 'Visit Price'); ?>:</span>
                                    <span class="cmsms_features_item_desc margin-left-30-percent"><?= CCurrency::format($profileDoctor->default_visit_price); ?></span>
                                </div>
                            </div>
                            <div class="cl"></div>
                        </div>
                    </div>
                </div>
                <div class="one_first">
                    <aside class="box error_box" style="display:none" id="message_error"><table><tbody>
                            <tr>
                                <td>&nbsp;</td>
                                <td><p id="message_error_text"><?= A::t('appointments', 'An error occurred! Please try again later.'); ?></p></td>
                            </tr>
                            </tbody></table>
                    </aside>
                </div>
                <div id="appointment_content" class="one_first first_column">
                    <div class="link tab">
                        <ul class=" tabs active">
                            <li id="tab_appointment_details" class="current"><a href="javascript:void(0);" data-type-tab="deactivate"><span><?= A::t('appointments', '1. Appointment Details'); ?></span></a></li>
                            <li id="tab_appointment_verify" class=""><a href="javascript:void(0);" data-type-tab="deactivate"><span><?= A::t('appointments', '2. Verify Appointment'); ?></span></a></li>
                            <li id="tab_appointment_complete" class=""><a href="javascript:void(0);" data-type-tab="deactivate"><span><?= A::t('appointments', '3. Completed!'); ?></span></a></li>
                        </ul>
                        <div class="tab_content">
                            <div id="appointment_details" class="tabs_tab" style="display: block;">
                                <div class="one_first">
                                    <?php
                                    echo CWidget::create('CFormView', array(
                                        'htmlOptions'	=> array(
                                            'name'			  => 'form-appointment',
                                            'id'			  => 'form-appointment',
                                            'enctype'		  => 'multipart/form-data',
                                            'autoGenerateId'  => false
                                        ),
                                        'requiredFieldsAlert'=>true,
                                        'fieldSets' 	=> array('type'=>'tabs', 'frameset'=>true),
                                        'fieldWrapper'  => array('tag'=>'div', 'class'=>'row'),
                                        'fields'		=> array(
                                            'specialty:'            =>array('type'=>'select', 'title'=>A::t('appointments', 'Doctor\'s Specialty'), 'tooltip'=>'', 'mandatoryStar'=>true, 'value'=>'', 'data'=>$arrDoctorSpecialties, 'emptyOption'=>true, 'emptyValue'=>'-- '.A::t('appointments', 'select').' --', 'viewType'=>'dropdownlist', 'multiple'=>false, 'htmlOptions'=>array('id'=>'specialty')),
                                            'visited_before:'       =>array('type'=>'select', 'title'=>A::t('appointments', 'Have you visited this doctor before?'), 'tooltip'=>'', 'mandatoryStar'=>true, 'value'=>0, 'data'=>$visitedBefore, 'emptyOption'=>true, 'emptyValue'=>'-- '.A::t('appointments', 'select').' --', 'viewType'=>'dropdownlist', 'multiple'=>false, 'htmlOptions'=>array('id'=>'visited_before')),
                                            'insurance:'            =>array('type'=>'select', 'title'=>A::t('appointments', 'Will you use insurance?'), 'tooltip'=>'', 'mandatoryStar'=>true, 'value'=>'', 'data'=>$insurance, 'emptyOption'=>true, 'emptyValue'=>'-- '.A::t('appointments', 'select').' --', 'viewType'=>'dropdownlist', 'multiple'=>false, 'htmlOptions'=>array('id'=>'insurance')),
                                            'reasons:'              =>array('type'=>'select', 'title'=>A::t('appointments', 'Visit Reasons'), 'tooltip'=>'', 'mandatoryStar'=>true, 'value'=>'', 'data'=>$visitReasons, 'emptyOption'=>true, 'emptyValue'=>'-- '.A::t('appointments', 'select').' --', 'viewType'=>'dropdownlist', 'multiple'=>false, 'htmlOptions'=>array('id'=>'reasons')),
                                            'other_reasons:'        => array('type'=>'textbox', 'title'=>false, 'tooltip'=>'', 'validation'=>array('required'=>false, 'maxLength'=>50, 'type'=>'text'), 'htmlOptions'=>array('maxLength'=>50, 'id'=>'other_reasons', 'placeholder'=>A::t('appointments', 'Enter Visit Reason'),  'style'=>'display:none;')),
                                            'appointment_for_whom:' =>array('type'=>'select', 'title'=>A::t('appointments', 'Who is this appointment for?'), 'tooltip'=>'', 'mandatoryStar'=>true, 'value'=>'', 'data'=>$appointmentForWhom, 'emptyOption'=>true, 'emptyValue'=>'-- '.A::t('appointments', 'select').' --', 'viewType'=>'dropdownlist', 'multiple'=>false, 'htmlOptions'=>array('id'=>'appointment_for_whom')),
                                            'for_whom_someone_else:'=> array('type'=>'textbox', 'title'=>false, 'tooltip'=>'', 'validation'=>array('required'=>false, 'maxLength'=>50, 'type'=>'text'), 'htmlOptions'=>array('maxLength'=>50, 'id'=>'for_whom_someone_else', 'placeholder'=>A::t('appointments', 'Enter whom this appointment is?'),  'style'=>'display:none;')),
                                        ),
                                        'buttons'=>array(
                                            'custom'=>array('type'=>'button', 'value'=>A::t('appointments', 'Book Now'), 'htmlOptions'=>array('id'=>'book_now', 'class'=>'button_small', 'onclick'=>'appointmentVerify(this);', 'data-date-time'=>$appointmnetnDate, 'data-doctor-id'=>$profileDoctor->id)),
                                        ),
                                        'buttonsPosition'=>'bottom',
                                        'return'=>true,
                                    ));
                                    ?>
                                </div>
                            </div>
                            <div id="appointment_verify" class="tabs_tab" style="display: none;">

                                <p class="padding-0"><strong><?=A::t('appointments', 'Doctor\'s Specialty'); ?></strong></p>
                                <p id="specialty_verify" class="margin-bottom-5"></p>

                                <p class="padding-0"><strong><?=A::t('appointments', 'Have you visited this doctor before?'); ?></strong></p>
                                <p id="visited_before_verify" class="margin-bottom-5"></p>

                                <p class="padding-0"><strong><?=A::t('appointments', 'Will you use insurance?'); ?></strong></p>
                                <p id="insurance_verify" class="margin-bottom-5"></p>

                                <p class="padding-0"><strong><?=A::t('appointments', 'Visit Reasons'); ?></strong></p>
                                <p id="reasons_verify" class="margin-bottom-5"></p>

                                <p class="padding-0"><strong><?=A::t('appointments', 'Who is this appointment for?'); ?></strong></p>
                                <p id="appointment_for_whom_verify" class="margin-bottom-5"></p>

                                <a class="button_small" href="javascript:void(0);" onclick="appointmentDetails(this);"><?= A::t('appointments', 'Back'); ?></a>
                                <a class="button_small" href="javascript:void(0);" onclick="appointmentComplete(this);" data-date-time="<?= $appointmnetnDate; ?>" data-doctor-id="<?= $profileDoctor->id; ?>"><?= A::t('appointments', 'Make an Appointment'); ?></a>

                            </div>
                            <div id="appointment_complete" class="tabs_tab" style="display: none;">
                                <aside class="box success_box" id="message_success"><table><tbody>
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td><p id="message_success_text"></p></td>
                                        </tr>
                                        </tbody></table>
                                </aside>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: echo $noSpecialty?>
        <?php endif; ?>
    </div>
</section>