<?php
	$this->_activeMenu = 'appointments/manage';
	$this->_breadCrumbs = array(
		array('label'=>A::t('appointments', 'Modules'), 'url'=>'modules/'),
		array('label'=>A::t('appointments', 'Doctor Appointments'), 'url'=>'modules/settings/code/appointments'),
		array('label'=>A::t('appointments', 'Appointments Management'), 'url'=>'appointments/manage'),
		array('label'=>A::t('appointments', 'Edit Appointment')),
	);
	
	$statusParam = ($status !== '' ? '/status/'.$status : '');
?>

<h1><?= A::t('appointments', 'Appointments Management'); ?></h1>

<div class="bloc">
    <?= $tabs; ?>
		<div class="sub-title">
        <?= $subTabs; ?>
    </div>

    <div class="sub-title"><?= A::t('appointments', 'Edit Appointment'); ?></div>
    <div class="content">
    <?php
        echo CWidget::create('CDataForm', array(
            'model'             => 'Modules\Appointments\Models\Appointments',
            'primaryKey'        => $id,
            'operationType'     => 'edit',
            'action'            => 'appointments/edit/id/'.$id.$statusParam,
            'successUrl'        => 'appointments/manage'.$statusParam,
            'cancelUrl'         => 'appointments/manage'.$statusParam,
            'passParameters'    => false,
            'method'            => 'post',
            'htmlOptions'       => array(
                'name'              => 'frmAppointmentsAdd',
                //'enctype'         => 'multipart/form-data',
                'autoGenerateId'    => true
            ),
            'requiredFieldsAlert' => true,
            'fields' => array(
                'separatorPatientInfo' =>array(
                    'separatorInfo'=>array('legend'=>A::t('appointments', 'Patient Info'), 'disabled'=>false),
                    'patient_name'  =>array('type'=>'label', 'title'=>A::t('appointments', 'Patient Name'), 'tooltip'=>'', 'default'=>'', 'definedValues'=>array(), 'htmlOptions'=>array(), 'format'=>'', 'stripTags'=>false, 'callback'=>array('function'=>'', 'params'=>'')),
                    'patient_phone' =>array('type'=>'label', 'title'=>A::t('appointments', 'Phone'), 'tooltip'=>'', 'default'=>'', 'definedValues'=>array(), 'htmlOptions'=>array(), 'format'=>'', 'stripTags'=>false, 'callback'=>array('function'=>'', 'params'=>'')),
                    //'patient_email' =>array('type'=>'label', 'title'=>A::t('appointments', 'Email'), 'tooltip'=>'', 'default'=>'', 'definedValues'=>array(), 'htmlOptions'=>array(), 'format'=>'', 'stripTags'=>false, 'callback'=>array('function'=>'', 'params'=>'')),
                ),
                'separatorDoctorInfo' =>array(
                    'separatorInfo'=>array('legend'=>A::t('appointments', 'Doctor Info'), 'disabled'=>false),
                    'doctor_name'               =>array('type'=>'label', 'title'=>A::t('appointments', 'Doctor Name'), 'tooltip'=>'', 'default'=>'', 'definedValues'=>array(), 'htmlOptions'=>array(), 'format'=>'', 'stripTags'=>false, 'callback'=>array('function'=>'', 'params'=>'')),
                    'specialty_name'            =>array('type'=>'label', 'title'=>A::t('appointments', 'Specialty'), 'tooltip'=>'', 'default'=>'', 'definedValues'=>array(), 'htmlOptions'=>array('class'=>"large"), 'format'=>'', 'stripTags'=>false, 'callback'=>array('function'=>'', 'params'=>'')),
                ),
                'separatorAppointmentInfo' =>array(
                    'separatorInfo'=>array('legend'=>A::t('appointments', 'Appointment Info'), 'disabled'=>false),
                    'appointment_number'        =>array('type'=>'label', 'title'=>A::t('appointments', 'Appointment ID'), 'tooltip'=>'', 'default'=>'', 'definedValues'=>array(), 'htmlOptions'=>array(), 'format'=>'', 'stripTags'=>false, 'callback'=>array('function'=>'', 'params'=>'')),
                    'appointment_date'          =>array('type'=>'label', 'title'=>A::t('appointments', 'Appointment Date'), 'tooltip'=>'', 'default'=>'', 'definedValues'=>array(), 'htmlOptions'=>array(), 'format'=>$dateFormat, 'stripTags'=>false, 'callback'=>array('function'=>'', 'params'=>'')),
                    'appointment_time'          =>array('type'=>'label', 'title'=>A::t('appointments', 'Appointment Time'), 'tooltip'=>'', 'default'=>'', 'definedValues'=>array(), 'htmlOptions'=>array(), 'format'=>$appointmentTimeFormat, 'stripTags'=>false, 'callback'=>array('function'=>'', 'params'=>'')),
                    'clinic_name'               =>array('type'=>'label', 'title'=>A::t('appointments', 'Clinic Name'), 'tooltip'=>'', 'default'=>'', 'definedValues'=>array(), 'htmlOptions'=>array(), 'format'=>'', 'stripTags'=>false, 'callback'=>array('function'=>'', 'params'=>'')),
                    'visit_duration'            =>array('type'=>'html', 'title'=>A::t('appointments', 'Visit Duration'), 'tooltip'=>'', 'default'=>'', 'definedValues'=>array(), 'htmlOptions'=>array('class'=>"width-15"), 'format'=>'', 'stripTags'=>false, 'callback'=>array('function'=>'', 'params'=>''), 'prependCode'=>'', 'appendCode'=>' '.A::t('appointments', 'min.')),
                    'visit_price'               =>array('type'=>'textbox',  'title'=>A::t('appointments', 'Visit Price'),          'tooltip'=>'', 'validation'=>array('required'=>true, 'type'=>'float', 'minValue'=>'0.00', 'maxValue'=>'', 'format'=>$typeFormat), 'htmlOptions'=>array('maxLength'=>11, 'class'=>'small'), 'prependCode'=>$pricePrependCode.' ', 'appendCode'=>$priceAppendCode),
                    'visit_reason'              =>array('type'=>'label', 'title'=>A::t('appointments', 'Visit Reason'), 'tooltip'=>'', 'default'=>$visitReason, 'definedValues'=>array(), 'htmlOptions'=>array('style'=>'width:50%;'), 'format'=>'', 'stripTags'=>false, 'callback'=>array('function'=>'', 'params'=>'')),
                    'for_is_whom'               =>array('type'=>'label', 'title'=>A::t('appointments', 'Who is this appointment for?'), 'tooltip'=>'', 'default'=>$forWhom, 'definedValues'=>array(), 'htmlOptions'=>array('style'=>'width:50%;'), 'format'=>'', 'stripTags'=>false, 'callback'=>array('function'=>'', 'params'=>'')),
                    'p_arrival_reminder_sent'   =>array('type'=>'label', 'title'=>A::t('appointments', 'Patient Arrival Reminder'), 'tooltip'=>'', 'default'=>'', 'definedValues'=>array('0'=>'<span class="label-red label-square">'.A::t('appointments', 'Not Sent').'</span>', '1'=>'<span class="label-green label-square">'.A::t('appointments', 'Sent').'</span>'), 'htmlOptions'=>array('style'=>'width:50%;'), 'format'=>'', 'stripTags'=>false, 'callback'=>array('function'=>'', 'params'=>'')),
                    //'p_confirm_reminder_sent'   =>array('type'=>'label', 'title'=>A::t('appointments', 'Patient Confirm Reminder'), 'tooltip'=>'', 'default'=>'', 'definedValues'=>array('0'=>'<span class="label-red label-square">'.A::t('appointments', 'Not Sent').'</span>', '1'=>'<span class="label-green label-square">'.A::t('appointments', 'Sent').'</span>'), 'htmlOptions'=>array('style'=>'width:50%;'), 'format'=>'', 'stripTags'=>false, 'callback'=>array('function'=>'', 'params'=>'')),
                    //'d_confirm_reminder_sent'   =>array('type'=>'label', 'title'=>A::t('appointments', 'Doctor Confirm Reminder'), 'tooltip'=>'', 'default'=>'', 'definedValues'=>array('0'=>'<span class="label-red label-square">'.A::t('appointments', 'Not Sent').'</span>', '1'=>'<span class="label-green label-square">'.A::t('appointments', 'Sent').'</span>'), 'htmlOptions'=>array('style'=>'width:50%;'), 'format'=>'', 'stripTags'=>false, 'callback'=>array('function'=>'', 'params'=>'')),
                    'status'                    => array('type'=>'select', 'title'=>A::t('appointments', 'Status'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'set', 'source'=>array_keys($editStatusAppointments)), 'data'=>$editStatusAppointments, 'emptyOption'=>true, 'emptyValue'=>'', 'viewType'=>'dropdownlist', 'multiple'=>false, 'storeType'=>'separatedValues', 'separator'=>';', 'htmlOptions'=>array('class'=>'chosen-select-filter')),
                    'status_arrival'            => array('type'=>'checkbox', 'title'=>A::t('appointments', 'Arrived'), 'default'=>'0', 'validation'=>array('type'=>'set', 'source'=>array(0,1)), 'viewType'=>'custom'),
                    'appointment_description'   =>array('type'=>'textarea', 'title'=>A::t('appointments', 'Description'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'any', 'maxLength'=>255), 'htmlOptions'=>array('maxLength'=>'255')),
                    'doctor_internal_notes'     =>array('type'=>'textarea', 'title'=>A::t('appointments', 'Doctor Comments (for medical card)'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'any', 'maxLength'=>1000), 'htmlOptions'=>array('maxLength'=>'1000')),
                    'doctor_external_notes'     =>array('type'=>'textarea', 'title'=>A::t('appointments', 'Comments for patient'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'any', 'maxLength'=>1000), 'htmlOptions'=>array('maxLength'=>'1000')),
                    'patient_internal_notes'    =>array('type'=>'textarea', 'title'=>A::t('appointments', 'Patient Comments (for internal use)'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'any', 'maxLength'=>1000), 'htmlOptions'=>array('maxLength'=>'1000')),
                    'patient_external_notes'    =>array('type'=>'textarea', 'title'=>A::t('appointments', 'Comments for doctor'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'any', 'maxLength'=>1000), 'htmlOptions'=>array('maxLength'=>'1000')),
                ),
			),
            'buttons' => array(
				'submitUpdateClose' =>array('type'=>'submit', 'value'=>'Update & Close', 'htmlOptions'=>array('name'=>'btnUpdateClose')),
				'submitUpdate'      =>array('type'=>'submit', 'value'=>'Update', 'htmlOptions'=>array('name'=>'btnUpdate')),
                'cancel'            => array('type'=>'button', 'value'=>A::t('app', 'Cancel'), 'htmlOptions'=>array('name'=>'', 'class'=>'button white')),
            ),
            'messagesSource'    => 'core',
            'alerts'            => array('type'=>'flash', 'itemName'=>A::t('appointments', 'Appointment')),
            'return'            => true,
        ));
    ?>
    </div>
</div>