<!-- Appointment -->
<div class="appointment">
    <div class="container back-arrow">
        <h4><a href="mobile/doctorView/<?= $profileDoctor->id; ?>"> < <?= $fullName; ?></a></h4>
    </div>
    <div class="container">
        <?php if(!empty($errorMessage)): ?>
            <div class="alert alert-danger mt20"><?= $errorMessage; ?></div>
        <?php endif; ?>
        <?php if($showAppointmentForm): ?>
            <div class="form-agileits">
                <?php if(!empty($successMessage)): ?>
                    <div class="alert alert-success mt20"><?= $successMessage; ?></div>
                <?php endif; ?>
                <h3><?= A::t('appointments', 'Book Appointment'); ?></h3>
                <div id="error_message" class="alert alert-danger mt20" style="display: none;"><?= A::t('appointments', 'Please fill all the fields!'); ?></div>
                <div id="success_message" class="alert alert-success mt20" style="display: none;"><?= A::t('appointments', 'The email was successfully sent!'); ?></div>
                <?php
                    echo CWidget::create('CFormView', array(
                        'action' 	=> 'mobile/bookAppointment/id/'.$profileDoctor->id,
                        'method' 	=> 'post',
                        'htmlOptions' => array(
                            'name'	         => 'frmBookAppointment',
                            'id'	         => 'frmBookAppointment',
                            'autoGenerateId' => false
                        ),
                        'fieldWrapper' => array('tag'=>'div', 'class'=>''),
                        'fields'=>array(
                            'act'           => array('type'=>'hidden', 'value'=>'send', 'htmlOptions'=>array('id'=>'act')),
                            'date'          => array('type'=>'textbox', 'value'=>(!empty($date) ? $date : ''), 'title'=>'', 'htmlOptions'=>array('id'=>'date', 'class'=>'', 'onfocus'=>'this.value = \'\';', 'placeholder'=>A::t('appointments', 'Date'), 'maxlength'=>'10')),
                            'time'          => array('type'=>'textbox', 'value'=>(!empty($time) ? $time : ''), 'title'=>'', 'htmlOptions'=>array('id'=>'time', 'class'=>'', 'placeholder'=>A::t('appointments', 'Time'), 'maxlength'=>'5')),
                            'department'    => array('type'=>'select', 'title'=>'', 'tooltip'=>'', 'value'=>$department, 'data'=>$specialty, 'emptyOption'=>true, 'emptyValue'=>A::t('appointments', 'Department'), 'viewType'=>'dropdownlist', 'multiple'=>false, 'htmlOptions'=>array('id'=>'department', 'class'=>'form-control name', 'placeholder'=>A::t('appointments', 'Department'))),
                            'patient_name'  => array('type'=>'textbox', 'value'=>$patient_name, 'title'=>'', 'htmlOptions'=>array('id'=>'patient_name', 'class'=>'name', 'placeholder'=>A::t('appointments', 'Patient Name'), 'maxlength'=>'35')),
                            'phone'         => array('type'=>'textbox', 'value'=>$phone, 'title'=>'', 'htmlOptions'=>array('id'=>'phone', 'class'=>'', 'placeholder'=>A::t('appointments', 'Phone'), 'maxlength'=>'15')),
                            'gender'        => array('type'=>'select', 'title'=>'', 'tooltip'=>'', 'value'=>$gender, 'data'=>$genders, 'emptyOption'=>true, 'emptyValue'=>A::t('appointments', 'Gender'), 'viewType'=>'dropdownlist', 'multiple'=>false, 'htmlOptions'=>array('id'=>'gender', 'class'=>'form-control name', 'placeholder'=>A::t('appointments', 'Gender'))),
                        ),
                        'buttons'	=> array('custom'=>array('type'=>'button', 'value'=>A::t('appointments', 'Make an Appointment'), 'htmlOptions'=>array('id'=>'make-appointment'))),
                        'events'	=> array('focus'=>array('field'=>$errorField)),
                        'return'	=> true,
                    ));
                ?>
            </div>
            <?php if(!empty($openHours)): ?>
                <div class="timings-w3ls">
                    <h5><?= A::t('appointments','Opening Hours'); ?></h5>
                    <ul>
                        <?php foreach($openHours as $openHour): ?>
                            <li><?= CHtml::encode($openHour['week_day_name'])?> <span><?= CHtml::encode($openHour['time_from']).' - '.CHtml::encode($openHour['time_to'])?></span></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <div class="clearfix"> </div>
</div>
<!-- //Appointment -->

<!-- Calendar -->
<?= CHtml::cssFile('templates/mobile/css/jquery-ui.css'); ?>
<?= CHtml::scriptFile("templates/mobile/js/jquery-ui.js"); ?>
<!-- //Calendar -->
<?php
A::app()->getClientScript()->registerScript(
    'loading',
    '$(function() {
        $( "#date" ).datepicker();
    });',
    2
);